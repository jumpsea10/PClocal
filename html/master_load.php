<?php
include_once("./inc/login_head.inc");
session_start();
if (isset($_POST['id'])) {
    $ID = mysqli_real_escape_string($con,$_POST['id']);
}
// $pwd_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
// echo($pwd_hash);
$rst = mysql_query("select * from operator where login_id = '{$ID}'");
if (!mysqli_num_rows($rst)) {
    header('Location: master_login.php');
    $_SESSION['login_error']='*このIDは登録されていません。';
    exit();
}
extract(mysqli_fetch_assoc($rst));
if (password_verify($_POST["password"],$pwd_hash)) {
    header("Location: ".$_ENV['URL_MASTER_INDEX']."?ac_key=".$ac_key);
    exit();
} else {
    header('Location: master_login.php');
    $_SESSION['login_error']='*パスワードが間違っています。';
    exit();
}

?>