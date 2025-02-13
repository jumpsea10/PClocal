<?php
session_start();
include_once("inc/head.inc");
$login_error_msg = $_SESSION['login_error'] ?? null;
$register_error_msg = $_SESSION['register_error'] ?? null;
unset($_SESSION['register_error']);
unset($_SESSION['login_error']);
unset($_SESSION['user_ac_key']);
$previousPage = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '#';
?>
    <div class="login-container">
        <h1>ログイン</h1>
        <?php if (isset($login_error_msg)) { ?>
        <p style="color: red; font-size: 13px;"><?=$login_error_msg?></p>
           <?php } ?>
        <form action="load.php" method="POST">
            <input type="text" name="mail" placeholder="exam@mail.com" required>
            <input type="password" name="password" placeholder="パスワード" required>
            <button type="submit">ログイン</button>
        </form>
    <div class="sign_up <?php echo isset($register_error_msg) ? 'show' : '';?>" id="sign_up_form" >
        <h1>新規登録</h1>
        <?php if (isset($register_error_msg)) { ?>
        <p style="color: red; font-size: 13px;"><?=$register_error_msg?></p>
           <?php } ?>
        <form action="/register.php" method="POST" onsubmit="return validateForm()">
            <input type="text" name="mail" placeholder="example@mail.com" required>
            <input type="password" name="password" id="password" placeholder="パスワード" required>
            <input type="password" name="confirm_password" id="confirm_password" placeholder="パスワード確認" required>
            <button type="submit">新規登録</button>
            <div id="error" style="color: red; font-size: 13px;"></div>
        </form>
    </div>
        <div class="liner_str">
            <a id="sign_up_url">新規登録</a> /
            <a href="<?=$previousPage?>">そのままゲストとして閲覧する</a>
            <input type="hidden" name="guest" value="1">
        </div>
    </div>

    <script>
        const sign_up_url = document.getElementById("sign_up_url");
        const sign_up_form = document.getElementById("sign_up_form");

        sign_up_url.addEventListener('click',() => {
            sign_up_form.classList.toggle('show');
        });

        function validateForm() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const errorDiv = document.getElementById('error');

        // エラー表示をリセット
        errorDiv.textContent = "";

        // パスワード一致のバリデーション
        if (password !== confirmPassword) {
            errorDiv.textContent = "*パスワードが一致しません。";
            return false; // フォーム送信を無効化
        }

        // 必須項目がすべて埋まっている場合のみ送信許可
        return true;
        }


    </script>
<?php include_once("inc/foot.inc");?>
