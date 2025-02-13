<?php
session_start();
include_once("/var/www/html/inc/def.inc");
include_once("/var/www/html/inc/function.inc");
$login_error_msg = $_SESSION['login_err'] ?? null;
unset($_SESSION['login_err']); 
unset($_SESSION['operator_ac_key']);
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ぷらいずせんたー/管理者ログイン</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <div class="login-container">
        <h1>管理者ログイン</h1>
        <?php if (isset($login_error_msg)) { ?>
        <p style="color: red; font-size: 13px;"><?=$login_error_msg?></p>
           <?php } ?>
        <form action="master_load.php" method="POST">
            <input type="text" name="id" placeholder="ID" required>
            <input type="password" name="password" placeholder="パスワード" required>
            <button type="submit">ログイン</button>
        </form>

    <script>
        const sign_up_url = document.getElementById("sign_up_url");
        const sign_up_form = document.getElementById("sign_up_form");

        sign_up_url.addEventListener('click',() => {
            sign_up_form.classList.toggle('show');
        });


    </script>
<?php include_once("inc/foot.inc");?>
