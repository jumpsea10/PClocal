<?php
session_start();
include_once("/var/www/html/inc/function.inc");
if (isset($_POST['id'])) {
    $ID = mysqli_real_escape_string($con,$_POST['id']);
}
// $pwd_hash = password_hash($_POST['password'], PASSWORD_DEFAULT);
// echo($pwd_hash);
// exit();
$rst = mysql_query("SELECT * FROM operator WHERE login_id = '{$ID}'");
if ($rst->num_rows <= 0) {
    header('Location: '. $_ENV['URL_MASTER_LOGIN']);
    $_SESSION['login_err']='*このIDは登録されていません。';
    exit();
}
$col = mysqli_fetch_assoc($rst);
if (password_verify($_POST["password"],$col['pwd_hash'])) {
    $_SESSION['operator_ac_key'] = $col['operator_ac_key'];
    header("Location: ".$_ENV['URL_MASTER_INDEX']);
    exit();
} else {
    header('Location: '. $_ENV['URL_MASTER_LOGIN']);
    $_SESSION['login_error']='*パスワードが間違っています。';
    exit();
}

?>