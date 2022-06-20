<?php
    /**
     * FileName   : add_new_product.php
     * ScreenName : 商品追加画面
     * DateTime   : 2022年4月
     **/ 
    include("pdo.php"); //pdoファイルをインポート
    session_start();    //session開始
   
    //変数を定義
    $message = "";               //メッセージ

    //定数を定義        
    Define("IMAGE_MAX_SIZE",1048576);            //画像の最大サイズ（1MB）
    Define("LOGIN_URL","./login.php");           //ログイン画面のリンク
    Define("SHOP_URL","./shop_information.php"); //ショップトップ画面のリンク

    //ログインがされていない場合はログインページに遷移
    $selectAdminSql = "SELECT * FROM t_admin WHERE admin_id = ?"; //ID指定admin検索用SQL
    $stmt           = $conn->prepare($selectAdminSql);

    $stmt->bindValue(1, intval($_SESSION["admin_id"])); //SESSIONの値を取得する
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);
    if($admin) {
        if($admin["admin_name"] != $_SESSION["admin_name"]) {
            header("Location: " . LOGIN_URL); //ログイン画面へ遷移
        }
    } else {
        header("Location: " . LOGIN_URL); //ログイン画面へ遷移
    }


    //商品情報をDBに追加
    if (isset($_POST["submit"])) { // submitボタンを押したことを確認
       if ($_POST["product_name"] == "" || $_POST["product_overview"] == "" || $_POST["product_price"] == "") {  //全部項目を入力したかを確認
           $message = '必須な情報を入力してください！';
       } else if (!is_uploaded_file($_FILES["product_photo"]["tmp_name"])) { //商品の画像を選択しなかった場合
           $message = '商品の画像を選択してください！';
       } else if (filesize($_FILES["product_photo"]["tmp_name"]) >= IMAGE_MAX_SIZE) { //画像サイズを確認
           $message ="画像のサイズは1MBが超えた!";
       } else {
            $photo = file_get_contents($_FILES["product_photo"]["tmp_name"]);       //画像を取得
            $photo_properties = getimageSize($_FILES["product_photo"]["tmp_name"]); //画像データを取得
            $photo_type       = $photo_properties["mime"];                          //画像の拡張子を取得
            $perchase_count   = 0;                                                  //購入数を初期化
            //商品追加用SQL
            $insertSql = "INSERT INTO t_product (
                product_name, create_at, update_at, 
                product_photo, product_price, product_overview, 
                perchase_count, product_photo_type)
                VALUES(?, NOW(), NOW(), ?, ?, ?, ?, ?)";
            
            $stmt = $conn->prepare($insertSql);

            $stmt->bindValue(1, $_POST["product_name"]);
            $stmt->bindValue(2, $photo);
            $stmt->bindValue(3, intval($_POST["product_price"]));
            $stmt->bindValue(4, $_POST["product_overview"]);
            $stmt->bindValue(5, $perchase_count );
            $stmt->bindValue(6, $photo_type);

            if ($stmt->execute()) {
                header("Location: " . SHOP_URL);  //DBに新商品追加成功したらショップトップ画面へ遷移
            } else {
                $message = "商品追加が失敗しました";          //DBに新商品追加失敗したらエラーメッセージが表示
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
    <title>商品追加</title>
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
        <!-- /#header -->

        <div id="contents">
        <h2>商品を追加する</h2>
            <!-- $messageをHTMLエンティティに変換する -->
            <p class="alertText"><?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?></p>
            <div class="upload-product-info">
                <!-- 送信 -->
                <form action="" method="post" enctype="multipart/form-data" id="product-form">
                    <div class="itemList">
                        <div class="upload-product-img">
                            <p><img id="output"></p>
                            <p><input type="file"  accept="image/*" name="product_photo" id="file"  onchange="loadFile(event)" style="display: none;"></p>
                            <p><label for="file">画像をアップロードする</label></p>
                            <p>※ 画像サイズが1MB以下を選択してください</p>
                        </div>
                        <div class="upload-product-description">
                            <div class="productName">
                                <p>商品名</p>
                                <input type="text" name="product_name" id="product_name" placeholder="商品名を入力してください">
                            </div>
                            <div class="detail">
                                <p>詳細</p>
                                <textarea name="product_overview" id="product_overview" cols="30" rows="10" placeholder="商品の詳細を入力してください"></textarea>
                            </div>
                            <div class="price">
                                <div class="priceTextArea">
                                    <p>値段</p>
                                </div>
                                <div class="priceArea">
                                    <input type="text" name="product_price" id="product_price" placeholder="商品の値段を入力してください">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="submitBtnArea">
                        <input type="submit" name="submit" value="+ 商品を追加する">
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

