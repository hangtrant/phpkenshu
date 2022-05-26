<?php 
    /**
    * FileName   : buy_success.php
    * ScreenName : 購入完了画面
    * DateTime   : 2022年4月
    **/
    include("pdo.php");

    //変数を定義
    $message = "";      //メッセージ

    if (isset($_POST['buy_product'])) {
        $updatePerchaseCountSql = "UPDATE t_product SET perchase_count = perchase_count + 1 WHERE product_id = ?";
        $stmt = $conn->prepare($updatePerchaseCountSql);

        $stmt->bindValue(1, intval($_POST['product_id']));
       if($stmt->execute()) {
           $message = "購入完了しました。";
       } else {
           $message = "購入失敗しました。s";
       }
    }
?>
<!DOCTYPE html>
<html lang="ja">
     <head>
         <!-- 文字コード -->
         <meta charset="utf-8">
         <!-- ページタイトル -->
         <title>購入完了画面</title>
         <!-- cssファイル読み込み -->
         <link rel="stylesheet" href="test-php.css">
     </head>

     <body>
         <!-- ヘッダー部分 -->
         <header>
             <!-- 3.購入完了画面(ユーザー) -->
             <p class="shop-information-title">　</p>
             <a href="./shop_information.php">
             <img src="./images/shop_top.png" alt="ショップトップ画面に戻る" class="back-icon">
             </a>
             <h1>ショップタイトル</h1>
             <h2>購入完了画面</h2>
         </header>

         <div class="main">
             <div class="finish">
             <p class="PurchaseCompletedText"><?php echo htmlspecialchars($message, ENT_QUOTES, "UTF-8"); ?></p>
             </div>
         </div>
     </body>
</html>