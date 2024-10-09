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
  $email = $_POST['user_email'];
  $pass = $_POST['user_password'];
  $pass_re = $_POST['user_password_retype'];

  //2.フォームが入力されていない場合
  if(empty($email)){

    $err_msg['email'] = MSG01;

  }
  if(empty($pass)){

    $err_msg['pass'] = MSG01;

  }
  if(empty($pass_re)){

    $err_msg['pass_retype'] = MSG01;

  }

  if(empty($err_msg)){

    //変数にユーザー情報を代入

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
        // $dsn = 'mysql:dbname=php_sample01;host=localhost;charset=utf8';
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
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="./index.css">
</head>
<body>
  <h1>ユーザー登録</h1>
  <form method="get">
  <p>
    <label for="email">
      <span>Email:</span>
      <span class="err_msg"><?php if(!empty($err_msg['email'])) echo $err_msg['email']; ?></span>
    </label>
    <input type="email" id="email" name="user_email" placeholder = "example@gmail.com"/>
  </p>
  <p>
    <label for="pass">
    <span>Password:</span>
    <span class="err_msg"><?php if(!empty($err_msg['pass'])) echo $err_msg['pass']; ?></span>
    </label>
    <input type="password" id="pass" name="user_password" placeholder="Password" />
  </p>
  <p>
    <label for="pass_retype">
    <span>Password-Retype:</span>
      <span class="err_msg"><?php if(!empty($err_msg['pass_retype'])) echo $err_msg['pass_retype']; ?></span>
    </label>
    <input type="password" id="pass_retype" name="user_password_retype" placeholder = "Retype Password"></input>
  </p>
  <input type="submit" value="送信">
</form>
<a href="mypage.php">マイページへ</a>
</body>
</html>