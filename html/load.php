<?php
include_once("./inc/login_head.inc");
session_start();
$mail = $_POST["mail"];
$rst = mysql_query("select * from user where mail = '{$mail}'");
if(!mysqli_num_rows($rst)){
    header('Location: login.php');
    $_SESSION['login_error']='*このメールアドレスはまだ登録されていません。';
    exit();
}
extract(mysqli_fetch_assoc($rst));
if(password_verify($_POST["password"],$pwd_hash)){
    header("Location: http://localhost:8080/index.php?ac_key=".$ac_key);
    exit();
}else{
    header('Location: login.php');
    $_SESSION['login_error']='*パスワードが間違っています。';
    exit();
}

?>