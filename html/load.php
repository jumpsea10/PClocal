<?php
session_start();
include_once("/var/www/html/inc/function.inc");
unset($_SESSION['user_ac_key']);
if (isset($_POST['mail'])) {
    $mail = mysqli_real_escape_string($con,$_POST['mail']);
}
$rst = mysql_query("SELECT * FROM user WHERE mail = '{$mail}'");
if ($rst->num_rows <= 0) {
    header('Location: '. $_ENV['URL_USER_LOGIN']);
    $_SESSION['login_error']='*このメールアドレスはまだ登録されていません。';
    exit();
}
$col = mysqli_fetch_assoc($rst);
if (password_verify($_POST["password"],$col['pwd_hash'])) {
    $_SESSION['user_ac_key'] = $col['user_ac_key'];
    header("Location: ". $_ENV['URL_USER_INDEX']);
    exit();
} else {
    header('Location: '. $_ENV['URL_USER_LOGIN']);
    $_SESSION['login_error']='*パスワードが間違っています。';
    exit();
}

?>