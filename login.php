<?php
    /**
     * FileName   : login.php
     * ScreenName : ログイン画面
     * DateTime   : 2022年4月
     **/ 
    try {
        session_start();
        include("pdo.php");

        $message = "";
        $login_success_url = "./shop_information.php";

        $dbh = $conn;

        //ログインボタンが押されたら
        if (isset($_POST['login'])) {
            //IDとパスワードが入ってなければメッセージを表示
            if (empty($_POST['id']) || empty($_POST['pass'])) {
                $message = "全ての項目に値を入れてください。";
            } else {
                //入力値を保存
                $name = $_POST['id'];
                $password = $_POST['pass'];
                
                //DBから管理ユーザー情報を取得
                $loginsql = "SELECT * FROM t_admin WHERE admin_name = ? AND admin_password = ?";
                $stmt = $dbh->prepare($loginsql);
                $stmt->bindValue(1, $name);
                $stmt->bindValue(2, $password);
                $stmt->execute();
                $admin = $stmt->FETCH();

                if($admin){
                    $_SESSION['admin_id']   = $admin['admin_id'];
                    $_SESSION['admin_name'] = $admin['admin_name'];
                    header("Location: ${login_success_url}");
                    exit();
                } else {
                    $message = "IDまたはパスワードが違います";
                }
            }
        }
        $dbh = null;
    }catch(PDOException $e){
        $message = "DB接続に失敗しました";
    }
?>

<!DOCTYPE html>
<html lang="ja">
     <head>
         <!-- 文字コード -->
         <meta charset="utf-8">
         <!-- ページタイトル -->
         <title>ショップタイトル</title>
         <!-- cssファイル読み込み -->
         <link rel="stylesheet" href="test-php.css">
     </head>

     <body>
         <!-- ヘッダー部分 -->
         <header>
             <!-- 4.～管理者ログイン画面まで -->
             <p class="shop-information-title">　</p>
             <a href="./shop_information.php">
             <img src="./images/shop_top.png" alt="ショップトップ画面に戻る" class="back-icon">
             </a>
             <h1>ショップタイトル</h1>
             <h2>管理者ログイン画面</h2>
             
         </header>

         <div class="login-main">
             <div class="result-message">
                <p><?php echo $message; ?></p>
             </div>
            <!-- ログインとパスワードの入力場所 -->
             <form action="./login.php" method="POST">
                 <div class="login">
                     <div class="log-id">
                         <b>ログインID</b><br>
                         <input type="text" name="id">
                     </div>

                     <div class="log-pass">
                         <b>パスワード</b><br>
                         <input type="password" name="pass">
                     </div>
                 </div>
                <br>
                <!-- ログインボタン -->
                 <nav>
                     <div>
                         <input class="log-bnt" type="submit" name="login" value="ログイン"></input>
                     </div>
                 </nav>
             </form>
         </div>
     </body>
</html>
