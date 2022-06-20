<?php
    /**
     * FileName   : login.php
     * ScreenName : ログイン画面
     * DateTime   : 2022年4月
     **/ 
    include("pdo.php"); //pdoファイルをインポート
    session_start();    //session開始

    //変数を定義
    $message = "";

    //定数を定義
    Define("SHOP_URL","./shop_information.php"); //ショップトップ画面のリンク

    //ログインボタンが押されたら
    if (isset($_POST["login"])) {
        //IDとパスワードが入ってなければメッセージを表示
        if (empty($_POST["id"]) || empty($_POST["pass"])) {
            $message = "全ての項目に値を入れてください。";
        } else {
            //入力値を保存
            $name = $_POST["id"];
            $password = $_POST["pass"];
                
            //DBから管理ユーザー情報を取得
            $loginsql = "SELECT * FROM t_admin WHERE admin_name = ? AND admin_password = ?";
            $stmt = $conn->prepare($loginsql);
            $stmt->bindValue(1, $name);
            $stmt->bindValue(2, $password);
            $stmt->execute();
            $admin = $stmt->fetch(PDO::FETCH_ASSOC); //fetch連想配列

            if($admin){
                $_SESSION["admin_id"]   = $admin["admin_id"];
                $_SESSION["admin_name"] = $admin["admin_name"];
                header("Location: " . SHOP_URL);    //ログイン成功したらショップトップ画面へ遷移
                exit();
            } else {
                $message = "IDまたはパスワードが違います";
            }
        }
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
         <link rel="stylesheet" href="./stylesheet.css">
     </head>

     <body id="wrapper">
        <div>
            <!-- ヘッダー部分 -->
            <header>
                <!-- 4.～管理者ログイン画面まで -->
                <div class="headerLine">　</div>
                <a href="./shop_information.php">
                <img src="./images/shop_top.png" alt="ショップトップ画面に戻る" class="back-icon">
                </a>
                <h1>ショップタイトル</h1>
                <h2>管理者ログイン画面</h2>
                
            </header>

            <div>
                <div class="result-message">
                    <p class="login-error-message"><?php echo $message; ?></p>
                </div>
                <!-- ログインとパスワードの入力場所 -->
                <form action="./login.php" method="POST" class="login-main">
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
         </div>
     </body>
</html>
