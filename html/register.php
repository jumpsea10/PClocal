<?php
include_once("./inc/login_head.inc");
session_start();
if (isset($_POST['mail'])) {
    $mail = mysqli_real_escape_string($_POST["mail"]);
    $rst = mysql_query("SELECT * FROM user WHERE mail = '$mail' ");
    $col = mysqli_fetch_assoc($rst);
}
if ($col) {
    header('Location: '. $_ENV['URL_USER_LOGIN']);
    $_SESSION['register_error']='*このメールアドレスは既に登録されています。';
    exit();
} else {
    if (isset($_POST['password'])) {
        $pwd_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
    }
    $datetime = date("Y-m-d H:i:s");
    // ランダムな英数字の生成
    $str = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPUQRSTUVWXYZ';
    $ac_key = substr(str_shuffle($str), 0, 10);
    mysql_query("INSERT INTO user (mail,pwd_hash,register_datetime,ac_key) VALUES ('{$mail}','{$pwd_hash}','{$datetime}','{$ac_key}')");
    $_SESSION['user_ac_key'] = $ac_key;
}
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="5;url=<?=$_ENV['URL_USER_INDEX']?>">
    <title>ぷらいずせんたー/新規登録画面</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
<div class = 'login-container'>
<h2>登録が完了いたしました。</h2>
<div>