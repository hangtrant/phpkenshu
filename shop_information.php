<?php
    /**
    * FileName   : shop_information.php
    * ScreenName : ショップトップ画面
    * DateTime   : 2022年4月
    **/ 
    include("pdo.php"); //pdoファイルをインポート
    session_start();    //session開始

    //変数を定義
    $login_status  = false; //ログインしていない状態 (false)
    $product_count = 0;    //商品数

    //定数を定義
    Define("SHOP_URL","./shop_information.php"); //ショップトップ画面のリンク

    //ログインしたかを確認
    $selectAdminSql = "SELECT * FROM t_admin WHERE admin_id = ?"; //ID指定admin検索用SQL
    $stmt           = $conn->prepare($selectAdminSql);

    $stmt->bindValue(1, intval($_SESSION["admin_id"])); //SESSIONの値を取得する
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC); //fetch連想配列

    if($admin) {
        if($admin["admin_name"] == $_SESSION["admin_name"]) {
            $login_status = true; //ログインしている状態 (true)
        }
    }

    //DBから商品リストを取得
    $selectSql = "SELECT * FROM t_product"; //全部商品検索用SQL
    $stmt      = $conn->prepare($selectSql);

    if($stmt->execute()) {
        $product_list  = $stmt->fetchAll();
        $product_count = count($product_list); //商品数を取得
    }

    //商品を削除
    if (isset($_POST["delete_product"])) {
        $deleteProductSql = "DELETE FROM t_product WHERE product_id = ?"; //ID指定商品削除用SQL
        $stmt      = $conn->prepare($deleteProductSql);
        $stmt->bindValue(1, $_POST["product_id"]);  
        $result =  $stmt->execute();
        if ($result) {
            header("Refresh:0");
        } else {
            $message = "商品削除が失敗しました";            //DBに新商品追加失敗したらエラーメッセージが表示
        }
    }
   
    //ログアウトする
    if (isset($_POST["admin_logout"])) { 
        $_SESSION["admin_name"] = "";
        $_SESSION["admin_id"] = "";
        $login_status = false;
        header("Location: " . SHOP_URL); //ショップトップ画面へ遷移
    }
?>

<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./stylesheet.css">
    <div class="headerLine">　</div>
    <title>ショップトップ</title>
</head>
<body id="wrapper">
    <div>
        <div id="shop-top-header">
            <h1 class="shop-title">ショップタイトル</h1>
            <p class="shop-information-message">(ショップ説明)私の好きなものを販売しています。
                <br>なるべく低価格でお届けます。
            </p>
            <div class="left-icon">
                <?php  if ($login_status) { ?>
                    <!-- ログインされた場合 -->
                   <form action="" method="post" class="logout-btn-position">
                        <input type="submit" name="admin_logout" alt="Logout" class="logout-btn" value="" onClick="return confirm('ログアウトしますか?')">
                   </form>
                <?php } else { ?>
                    <!-- ログインされない場合 -->
                    <a href="./login.php" id="admin-login">
                        <img src="./images/admin_login.png" alt="管理者ログイン">
                    </a>
                <?php } ?>

                <a href="./index.html">
                    <img src="./images/shop_list.png" alt="店舗一覧">
                </a>
            </div>
        </div>
        <!-- /#header -->

        <div id="contents">
            <h2>店長情報</h2>
            <div class="shop-info">
                <img src="./images/IMG-3110.jpg" alt="ショップの画像" class="shop-img">
                <div class="owner-info">
                    <p class="owner-name">赤坂　花子</p>
                    <ul class="owner-list">
                        <li>出身地  　：　東京都</li>
                        <li>誕生日  　：　11月12日</li>
                        <li>血液型  　：　A型</li>
                        <li>好きな物  ：　ご飯、旅行、音楽</li>
                    </ul>
                </div>
            </div>
            <!-- /shop-info -->
            <div class="product-list">
                <h2>商品一覧</h2>
                <!-- 商品追加ボタン -->
                <?php if ($login_status) {?>
                    <div class="product-list-add">
                        <a href="./add_new_product.php">+ 商品を追加する</a>
                    </div>
                <?php } ?>
                <?php if ($product_count == 0) { ?>
                    <p>商品がありません</p>
                <?php } else { ?>
                    <div class="product-list-position">
                        <?php foreach (array_reverse($product_list) as $product) {?>
                            <a class="product-box-list" href="./product_information.php?product_id=<?php echo htmlspecialchars($product["product_id"], ENT_QUOTES, "UTF-8"); ?>">
                                <div class="product-box">
                                    <!-- 商品の画像 -->
                                    <img src="data:<?php echo htmlspecialchars($product["product_photo_type"], ENT_QUOTES, "UTF-8"); ?>;base64,
                                    <?php echo htmlspecialchars(base64_encode($product["product_photo"]), ENT_QUOTES, "UTF-8"); ?>" 
                                        alt="<?php echo htmlspecialchars($product["product_name"], ENT_QUOTES, "UTF-8"); ?>" class="product-img">
                                    <!-- 商品名 -->
                                    <p><?php echo htmlspecialchars($product["product_name"], ENT_QUOTES, "UTF-8"); ?></p>
                                    <!-- 商品の値段 -->
                                    <p>¥<?php echo htmlspecialchars($product["product_price"], ENT_QUOTES, "UTF-8"); ?></p>                
                                    <?php if ($login_status) {?>
                                        <!-- 商品を編集 -->
                                        <div class="owner-bnt">
                                            <form action="./change_product_information.php" method="post">
                                                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product["product_id"], ENT_QUOTES, "UTF-8"); ?>">
                                                <input type="submit" id="change_product" name="change_product" value="編集">
                                            </form>

                                            <!-- 商品を削除 -->
                                            <form action="" method="post">
                                                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product["product_id"], ENT_QUOTES, "UTF-8"); ?>">
                                                    <input type="submit" id="change_product" name="delete_product" value="削除" onClick="return confirm('商品を削除しますか?')">
                                            </form>
                                        </div>
                                    <?php } ?>
                                </div>
                                <!-- /product-box -->
                            </a>
                        <?php }?>   
                    </div>    
                <?php }?> 
            </div>
            <!-- /product-list -->
        </div>
        <!-- /#contents -->

        <div id="footer">
        </div>
        <!-- /#footer -->
    </div>
</body>
</html>
