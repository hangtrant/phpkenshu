<?php
    /**
    * FileName   : pdo.php
    * ScreenName : なし
    * DateTime   : 2022年4月
    * データベースに接続するファイル
    **/ 
    try {
        $pdo   = "mysql:dbname=test_db;host=localhost;charset=utf8";  //データベース名、ホスト名、文字コード設定
        $user  = "admin";                                             //ユーザ名
        $pass  = "password";                                          //パスワード
        $conn  = new PDO($pdo, $user, $pass);                         //PDOインスタンスを生成
        // echo "DB接続に成功しました"
    } catch  (PDOException $e)  {
        // echo "DB接続に失敗しました";
    }
?>
