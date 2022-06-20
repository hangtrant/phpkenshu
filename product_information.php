<?php
    /**
    * FileName   : product_information.php
    * ScreenName : 商品詳細画面
    * DateTime   : 2022年4月
    **/ 
    include("pdo.php"); //pdoファイルをインポート
    session_start();    //session開始

    //変数を定義
    $message = "";              //メッセージ
    $id = $_GET["product_id"]; //$idに必要な商品情報を受け渡す
    $login_status = false;     //ログインしていない状態 (false)

    //定数を定義
    Define("SHOP_URL","./shop_information.php"); //ショップトップ画面のリンク

    //ログインしたかを確認
    $selectAdminSql = "SELECT * FROM t_admin WHERE admin_id = ?"; //ID指定admin検索用SQL
    $stmt           = $conn->prepare($selectAdminSql);

    $stmt->bindValue(1, intval($_SESSION['admin_id'])); //SESSIONの値を取得する
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC); //fetch連想配列

    if($admin) {
        if($admin["admin_name"] == $_SESSION["admin_name"]) {
            $login_status = true; //ログインしている状態 (true)
        }
    }


    //商品情報を取得
    $selectProductSql = "SELECT * FROM t_product WHERE product_id = ?"; //ID指定商品検索用SQL
    $stmt      = $conn->prepare($selectProductSql);
    $stmt->bindValue(1, $id);
    $stmt->execute();
    $product = $stmt->fetch(PDO::FETCH_ASSOC); //fetch連想配列

    if(!$product) {
        header("Location: " . SHOP_URL); //ショップトップ画面へ遷移
    }

    //コメントリストを取得
    $selectCommentSql = "SELECT * FROM t_comment WHERE product_id = ?"; //ID指定コメント検索用SQL
    $stmt = $conn->prepare($selectCommentSql);

    $stmt->bindValue(1, $id);
    $stmt->execute();
    $command_list = $stmt->fetchAll();

    //コメントをDBに追加
    if (isset($_POST["submit-comment"])) {
        if ($_POST["comment_body"] == "" || $_POST["commenter_name"] == "") {
            $message = "コメント内容と名前を両方入力してください";
        } else {
            $insertSql = "INSERT INTO t_comment (product_id, commenter_name, comment_body) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($insertSql);

            $stmt->bindValue(1, $id);
            $stmt->bindValue(2, $_POST["commenter_name"]);
            $stmt->bindValue(3, $_POST["comment_body"]);
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
    <link rel="stylesheet" href="./stylesheet.css">
    <title>商品詳細</title>
</head>
<body id="wrapper">
    <div>
        <div id="header">
            <div class="headerLine">　</div>
            <div class="headerCenter">
                <h1>ショップタイトル</h1>
                <div class="right-icon">
                    <a href="./shop_information.php">
                        <img src="./images/shop_top.png" alt="ショップトップ画面へ戻る">
                    </a>
                </div>
            </div>
        </div>
        <div id="contents">
        <h2>商品詳細</h2>
            <div class="productDetailList">
                <!-- 商品の画像 -->
                <div class="product-details-left">
                    <img src="data:<?php echo htmlspecialchars($product["product_photo_type"], ENT_QUOTES, "UTF-8"); ?>;base64,
                    <?php echo htmlspecialchars(base64_encode($product["product_photo"]), ENT_QUOTES, "UTF-8"); ?>" 
                    alt="<?php echo htmlspecialchars($product["product_name"], ENT_QUOTES, "UTF-8"); ?>" class="product-img">
                </div>
                <div class="product-details-right">
                     <!-- 商品名 -->
                    <h3><?php echo htmlspecialchars($product["product_name"], ENT_QUOTES, "UTF-8"); ?></h3>
                    <div class="itemDtail">
                        <?php if(!$login_status) { ?>
                            <!-- 購入ボタン -->
                            <div class="buyButton">
                                <form action="./buy_success.php" method="post">
                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product["product_id"], ENT_QUOTES, "UTF-8"); ?>">
                                    <input type="submit" name="buy_product"  id="productBuyButton" value="購入" onClick="return confirm('商品を購入しますか?')">
                                </form>
                            </div>
                        <?php } ?>
                        <div class="productLabel">
                            <div class="productPrice">
                                <!-- 商品の値段 -->
                                <p>¥<?php echo htmlspecialchars($product["product_price"], ENT_QUOTES, "UTF-8"); ?></p>     
                            </div>
                            <div class="numberOfSale">
                                <!-- 販売数 -->
                                <p>累計販売数：<span><?php echo htmlspecialchars($product["perchase_count"], ENT_QUOTES, "UTF-8"); ?></span>個</p>
                            </div>
                        </div> 
                    </div>
                    <div class="productDetail">
                        <!-- 商品の詳細情報  -->
                        <p><?php echo htmlspecialchars($product["product_overview"], ENT_QUOTES, "UTF-8"); ?></p>
                    </div>
                </div>
            </div>
            <div class="comment-list">
                <h2>コメント一覧</h2>
                <p class="alertText"><?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?></p>
                <?php if(count($command_list) < 1) { ?>
                    <p class="alertText">コメントがありません</p>
                <?php } else { ?>
                    <?php foreach (array_reverse($command_list) as $comment) { ?>
                        <div class="comment-details">
                            <div class="commentArea">
                                <p class="commentBody"><?php echo htmlspecialchars($comment["comment_body"], ENT_QUOTES, "UTF-8"); ?></p>
                                <div class="commentAreaFoot">
                                    <p class="commentName"><?php echo htmlspecialchars($comment["commenter_name"], ENT_QUOTES, "UTF-8"); ?></p>
                                    <p class="commentDate"><?php echo htmlspecialchars($comment["comment_date"], ENT_QUOTES, "UTF-8"); ?></p>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } ?>
            </div>
            <?php if(!$login_status) { ?>
            <div class="comment-box">
                <form action="" method="post">
                    <div class="itemMain">
                        <textarea name="comment_body" id="comment_body" cols="30" rows="10" placeholder="コメントを入力"></textarea>
                        <div class="itemFoot">
                            <div class="commentNameArea">
                                <input type="text" name="commenter_name" id="commenter_name" placeholder="名前">
                            </div>
                            <div class="submitCommentArea">
                                <input type="submit" name="submit-comment" value="送信">
                            </div>
                        </div>
                    </div>
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
