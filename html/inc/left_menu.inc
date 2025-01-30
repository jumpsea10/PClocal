<!-- 左側のメニュー -->
<div class="sidebar" id="sidebar">
    <h2>メニュー</h2>
    <!-- ハンバーガーメニューボタン -->
    <div class="menu-icon" id="menu-icon">
    ☰
    </div>
    <ul>
        <li><a href="http://localhost:8080/index.php">トップ</a></li>
        <li><a href="#request">リクエスト</a></li>
        <?php if($is_exist==0){ ?>
        <li><a href="http://localhost:8080/login.php">ログイン</a></li>
        <?php } ?>
    </ul>
</div>