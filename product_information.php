<?php
    /**
    * FileName   : product_information.php
    * ScreenName : 商品詳細画面
    * DateTime   : 2022年4月
    **/ 

    include("pdo.php");
    session_start();

    //変数を定義
    $message = "";         //エラーメッセージ
    $id = $_GET['product_id'];
    $login_status = false; //ログインしていない状態 (false)

    //ログインしたかを確認
    $selectAdminSql = "SELECT * FROM t_admin WHERE admin_id = ?"; //ID指定admin検索用SQL
    $stmt           = $conn->prepare($selectAdminSql);

    $stmt->bindValue(1, intval($_SESSION['admin_id']));
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    if($admin) {
        if($admin['admin_name'] == $_SESSION['admin_name']) {
            $login_status = true; //ログインしている状態 (true)
        }
    }


    //商品情報を取得
    $selectProductSql = "SELECT * FROM t_product WHERE product_id = ?"; //ID指定商品検索用SQL
    $stmt      = $conn->prepare($selectProductSql);

    $stmt->bindValue(1, $id);

    if($stmt->execute()) {
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } else {
        header("Location: ${shop_url}");
    }

    //コメントリストを取得
    $selectCommentSql = "SELECT * FROM t_comment WHERE product_id = ?"; //ID指定コメント検索用SQL
    $stmt = $conn->prepare($selectCommentSql);

    $stmt->bindValue(1, $id);
    $stmt->execute();
    $command_list = $stmt->fetchAll(PDO::FETCH_ASSOC);

    //コメントをDBに追加
    if (isset($_POST['submit-comment'])) {
        if ($_POST['comment_body'] == "" || $_POST['commenter_name'] == "") {
            $message = "コメント内容と名前を両方入力してください";
        } else {
            $insertSql = "INSERT INTO t_comment (product_id, commenter_name, comment_body) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertSql);

            $stmt->bindValue(1, $id);
            $stmt->bindValue(2, $_POST['commenter_name']);
            $stmt->bindValue(3, $_POST['comment_body']);
            if ( $stmt->execute()) {
                header("Refresh:0");
            } else {
                $message = "コメント追加が失敗しました";
            }
        }
    }

?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="test-php.css">
    <title>商品詳細</title>
</head>
<body>
    <div id="wrapper">
    <p class="shop-information-title">　</p>
        <div id="header">
            <h1 class="shop-title">ショップタイトル</h1>
            <div class="right-icon">
                <a href="./shop_information.php">
                    <img src="./images/shop_top.png" alt="ショップトップ画面へ戻る">
                </a>
            </div>
        </div>
        <div id="contents">
            <div class="product-details">
                <h2>商品詳細</h2>
                <!-- 商品の画像 -->
                <div class="productdetailList">
                    <div class="product-details-left">
                        <img src="data:<?php echo htmlspecialchars($product["product_photo_type"], ENT_QUOTES, "UTF-8"); ?>;base64,
                        <?php echo htmlspecialchars(base64_encode($product["product_photo"]), ENT_QUOTES, "UTF-8"); ?>" 
                        alt="<?php echo htmlspecialchars($product["product_name"], ENT_QUOTES, "UTF-8"); ?>" class="product-img">
                    </div>
                    <div class="product-details-right">
                        <!-- 商品名 -->
                        <h3><?php echo htmlspecialchars($product["product_name"], ENT_QUOTES, "UTF-8"); ?></h3>
                        <?php if(!$login_status) { ?>
                            <!-- 購入ボタン -->
                            <form action="./buy_success.php" method="post">
                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product["product_id"], ENT_QUOTES, "UTF-8"); ?>">
                                <input type="submit" name="buy_product" value="購入" onClick="return confirm('商品を購入しますか?')">
                            </form>
                        <?php } ?>
                        <!-- 商品の値段 -->
                        <p class="productPrice">¥<?php echo htmlspecialchars($product["product_price"], ENT_QUOTES, "UTF-8"); ?></p>     
                        <!-- 販売数 -->
                        <p class="numberOfSale">累計販売数：<span><?php echo htmlspecialchars($product["perchase_count"], ENT_QUOTES, "UTF-8"); ?></span>個</p>
                        <!-- 商品の詳細情報  -->
                        <p class="productDetail"><?php echo htmlspecialchars($product["product_overview"], ENT_QUOTES, "UTF-8"); ?></p>
                    </div>
                </div>
            </div>
            <h2>コメント一覧</h2>
            <div class="comment-list">
                <p><?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?></p>
                <?php if(count($command_list) < 1) { ?>
                    <p>コメントがありません</p>
                <?php } else { ?>
                    <?php foreach ($command_list as $comment) { ?>
                        <div class="comment-details">
                            <div class="commentArea">
                                <p class="commentBody"><?php echo htmlspecialchars($comment["comment_body"], ENT_QUOTES, "UTF-8"); ?></p>
                                <ul>
                                    <li class="commentName"><?php echo htmlspecialchars($comment["commenter_name"], ENT_QUOTES, "UTF-8"); ?></li>
                                    <li class="commentData"><?php echo htmlspecialchars($comment["comment_date"], ENT_QUOTES, "UTF-8"); ?></li>
                                </ul>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <?php if(!$login_status) { ?>
                <div class="comment-box">
                <form action="" method="post">
                    <textarea name="comment_body" id="comment_body" cols="30" rows="10" placeholder="コメントを入力"></textarea><br>
                    <input type="text" name="commenter_name" id="commenter_name" placeholder="名前"><br>
                    <input type="submit" name="submit-comment" value="送信">
                </form>
            </div>
            <?php } ?>
        </div>
        <div id="footer">
        </div>
    </div>
    <script type="text/javascript" src="script.js"></script>
</body>
</html>