<?php 
    /**
    * FileName   : change_product_information.php
    * ScreenName : 商品編集画面
    * DateTime   : 2022年4月
    **/ 
    include("pdo.php");
    session_start();

    //変数を定義
    $message        = "";        //メッセージ
    
    //定数を定義
    // $image_max_size = 16777215;  //画像の最大サイズ（16777215 Bytes）
    $image_max_size = 1048576;     //画像の最大サイズ（1MB）
    $login_url = "./login.php";
    $shop_url  = "./shop_information.php";

    //ログインがされていない場合はログインページに遷移
    $selectAdminSql = "SELECT * FROM t_admin WHERE admin_id = ?"; //ID指定admin検索用SQL
    $stmt           = $conn->prepare($selectAdminSql);

    $stmt->bindValue(1, intval($_SESSION['admin_id']));
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if($admin) {
        if($admin['admin_name'] != $_SESSION['admin_name']) {
            header("Location: ${login_url}");  //ログイン画面へ遷移
        }
    } else {
        header("Location: ${login_url}");     //ログイン画面へ遷移
    }
    
    //商品情報を取得
    if (isset($_POST['change_product'])) {
        $id = $_POST['product_id'];

        $selectSql = "SELECT * FROM t_product WHERE product_id = ?"; //ID指定商品検索用SQL
        $stmt      = $conn->prepare($selectSql);

        $stmt->bindValue(1, $id);

        if($stmt->execute()) {
            $product = $stmt->fetch(PDO::FETCH_ASSOC);
        } else {
            header("Location: ${shop_url}");
        }
    }

    //商品情報をDBに編集
    if (isset($_POST['submit'])) { // submitボタンを押した場合
        $name       = $_POST['product_name'];
        $price      = $_POST['product_price'];
        $overview   = $_POST['product_overview'];
        $id         = $_POST['product_id'];
        $photo      = base64_decode($_POST['product_photo_pre']);
        $photo_type = $_POST['product_photo_type_pre'];


        if ($name == "" || $price  == "" || $overview  == "" ) {  //全部項目を入力したかを確認
            $message = '必須な情報を入力してください！';
        } else if (filesize($_FILES['product_photo']['tmp_name']) >= $image_max_size) { //画像サイズを確認
            $message ='画像のサイズは1MBが超えた!';
        } else {
            if (is_uploaded_file($_FILES['product_photo']['tmp_name'])) { //商品の画像を選択したかを確認
                $photo            = file_get_contents($_FILES['product_photo']['tmp_name']); //画像を取得
                $photo_properties = getimageSize($_FILES['product_photo']['tmp_name']);      //画像の1データを取得
                $photo_type       = $photo_properties['mime'];                               //画像の拡張子を取得
                
                $photo_name = $_FILES["product_photo"]['name'];

                //max width and height
                $width = 350;
                $height = 350;
            } 

            // 商品編集用SQL
            $insertSql = "UPDATE t_product SET
            product_name       = ?, 
            update_at          = NOW(),
            product_photo      = ?,
            product_price      = ?,
            product_overview   = ?,
            product_photo_type = ?
            WHERE product_id   = ?";
        
            $stmt = $conn->prepare($insertSql);

            $stmt->bindValue(1, $name);
            $stmt->bindValue(2, $photo);
            $stmt->bindValue(3, intval($price));
            $stmt->bindValue(4, $overview);
            $stmt->bindValue(5, $photo_type);
            $stmt->bindValue(6, intval($id));

            $result = $stmt->execute();

            if ($result) {
                header("Location: ${shop_url}");  //DBに新商品編集成功したらショップトップ画面へ遷移
            } else {
                $message = "商品編集が失敗しました";          //DBに新商品編集失敗したらエラーメッセージが表示
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
    <title>商品編集</title>
</head>
<body>
    <div id="wrapper">
        <div id="header">
            <h1>ショップタイトル</h1>
            <div class="right-icon">
                <a href="./shop_information.php">
                    <img src="./images/shop_top.png" alt="ショップトップ画面へ戻る">
                </a>
            </div>
        </div>
        <!-- /#header -->

        <div id="contents">
            <h2>商品を編集する</h2>
            <p><?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?></p>
            <div class="upload-product-info">
                <form action="" method="post" enctype="multipart/form-data" id="product-form">
                    <div class="upload-product-img">
                        <p><img src="data:<?php echo htmlspecialchars($product["product_photo_type"], ENT_QUOTES, "UTF-8"); ?>;base64,
                            <?php echo htmlspecialchars(base64_encode($product["product_photo"]), ENT_QUOTES, "UTF-8"); ?>" id="output" ></p>
                        <p><input type="file"  accept="image/*" name="product_photo" id="file"  onchange="loadFile(event)" style="display: none;"></p>
                        <p><label for="file">画像をアップロードする</label></p>
                        <p>※ 画像サイズが1MB以下を選択してください</p>
                    </div>
                    <div class="upload-product-description">
                        商品名<br>
                        <input type="text" name="product_name" id="product_name" value="<?php echo htmlspecialchars($product["product_name"], ENT_QUOTES, "UTF-8"); ?>"><br>
                       
                        詳細<br>
                        <textarea name="product_overview" id="product_overview" cols="30" rows="10"><?php echo htmlspecialchars($product["product_overview"], ENT_QUOTES, "UTF-8"); ?>
                        </textarea><br>
                       
                        値段<br>
                        <input type="text" name="product_price" id="product_price" value="<?php echo htmlspecialchars($product["product_price"], ENT_QUOTES, "UTF-8"); ?>"><br>
                        <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product["product_id"], ENT_QUOTES, "UTF-8"); ?>">
                        <input type="hidden" name="product_photo_pre" value="<?php echo htmlspecialchars(base64_encode($product["product_photo"]), ENT_QUOTES, "UTF-8"); ?>">
                        <input type="hidden" name="product_photo_type_pre" value="<?php echo htmlspecialchars($product["product_photo_type"], ENT_QUOTES, "UTF-8"); ?>">
                    </div>
                    <div class="submit-button">
                        <input type="submit" name="submit" value="編集を確定する">
                    </div>
                </form>
                <!-- /form -->
            </div>
        <!-- /upload-product-info -->
        </div>
        <!-- /#contents -->

        <div id="footer">
        </div>
        <!-- footer なし -->
    </div>
    <!-- /#wrapper -->
    <script type="text/javascript" src="script.js"></script>
</body>
</html>

