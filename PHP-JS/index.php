<?php

error_reporting(E_ALL); //E_STRICTレベル以外のエラーを報告する
ini_set('display_errors','On'); //画面にエラーを表示させるか

//1.post送信されていた場合
if(!empty($_POST)){

  //エラーメッセージを定数に設定
  define('MSG01','入力必須です');
  define('MSG02', 'Emailの形式で入力してください');
  define('MSG03','パスワード（再入力）が合っていません');
  define('MSG04','半角英数字のみご利用いただけます');
  define('MSG05','6文字以上で入力してください');

  //配列$err_msgを用意
  $err_msg = array();

  //2.フォームが入力されていない場合
  if(empty($_POST['email'])){

    $err_msg['email'] = MSG01;

  }
  if(empty($_POST['pass'])){

    $err_msg['pass'] = MSG01;

  }
  if(empty($_POST['pass_retype'])){

    $err_msg['pass_retype'] = MSG01;

  }

  if(empty($err_msg)){

    //変数にユーザー情報を代入
    $email = $_POST['email'];
    $pass = $_POST['pass'];
    $pass_re = $_POST['pass_retype'];

    //3.emailの形式でない場合
    if(!preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/", $email)){
      $err_msg['email'] = MSG02;
    }

    //4.パスワードとパスワード再入力が合っていない場合
    if($pass !== $pass_re){
      $err_msg['pass'] = MSG03;
    }

    if(empty($err_msg)){

      //5.パスワードとパスワード再入力が半角英数字でない場合
      if(!preg_match("/^[a-zA-Z0-9]+$/", $pass)){
        $err_msg['pass'] = MSG04;

      }elseif(mb_strlen($pass) < 6){
      //6.パスワードとパスワード再入力が6文字以上でない場合

        $err_msg['pass'] = MSG05;
      }

      if(empty($err_msg)){

        //DBへの接続準備
        $dsn = 'mysql:dbname=php_sample01;host=localhost;charset=utf8';
        $user = 'root';
        $password = 'root';
        $options = array(
                // SQL実行失敗時に例外をスロー
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                // デフォルトフェッチモードを連想配列形式に設定
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // バッファードクエリを使う(一度に結果セットをすべて取得し、サーバー負荷を軽減)
                // SELECTで得た結果に対してもrowCountメソッドを使えるようにする
                PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
            );

        // PDOオブジェクト生成（DBへ接続）
        $dbh = new PDO($dsn, $user, $password, $options);

        //SQL文（クエリー作成）
        $stmt = $dbh->prepare('INSERT INTO users (email,pass,login_time) VALUES (:email,:pass,:login_time)');

        //プレースホルダに値をセットし、SQL文を実行
        $stmt->execute(array(':email' => $email, ':pass' => $pass, ':login_time' => date('Y-m-d H:i:s')));

        header("Location:mypage.php"); //マイページへ
      }

    }
  }
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>ホームページのタイトル</title>
    <link rel="stylesheet" href="./index.css">
  </head>
  <body>

        <h1>ユーザー登録</h1>
            <form method="post">
        <span class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
                <input type="text" name="email" placeholder="email" value="<?php if(!empty($_POST['email'])) echo $_POST['email'];?>">

        <span class="err_msg"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span>
                <input type="password" name="pass" placeholder="パスワード" value="<?php if(!empty($_POST['pass'])) echo $_POST['pass'];?>">

        <span class="err_msg"><?php if(!empty($err_msg['pass_retype'])) echo $err_msg['pass_retype']; ?></span>
                <input type="password" name="pass_retype" placeholder="パスワード（再入力）" value="<?php if(!empty($_POST['pass_retype'])) echo $_POST['pass_retype'];?>">

                <input type="submit" value="送信">
            </form>
            <a href="mypage.php">マイページへ</a>
  </body>
</html>