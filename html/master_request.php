<?php
include_once("/var/www/html/inc/function.inc");
include_once("/var/www/html/inc/def.inc");
$is_exist = 0;
if (isset($_GET['ac_key'])) {
    $ac_key = mysqli_real_escape_string($con,$_GET['ac_key']);
    $rst = mysql_query("select * from operator where ac_key = '$ac_key'");
    if ($usr = mysqli_fetch_assoc($rst)) {
        $is_exist = 1;
    }
}

if (isset($_POST["way"])) {
    $way = $_POST["way"];
    if ($way === 1) {
        mysql_query("update figure_request set is_ok = 1 where request_id = '{$_POST["request_id"]}'");
    } else if($way === 2) {
        $i = $_POST['i'];
        setcookie('anime_name',$_POST['anime_name'.$i]);
        setcookie('figure_name',$_POST['figure_name'.$i]);
        setcookie('maker_name',$_POST['maker_name'.$i]);
        mysql_query("update figure_request set is_ok = 1 where request_id = '{$_POST["request_id"]}'");
        header('Location: '.$_ENV['URL_MASTER_INDEX'].'?ac_key='.$ac_key);
        exit();
    }
}

$rst = mysql_query("select * from figure_request where is_ok = 0");
?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>index of prize figures</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>

<!-- 左側のメニュー -->
<div class="sidebar" id="sidebar" style="background-color: #ff9800;">
    <h2>メニュー</h2>
    <!-- ハンバーガーメニューボタン -->
    <div class="menu-icon" id="menu-icon">
    ☰
    </div>
    <ul>
        <li><a href="<?=$_ENV['URL_MASTER_INDEX']?>?ac_key=<?=$ac_key?>">フィギュア登録</a></li>
    </ul>
</div>

<div class="content" id="content">
        <h1 id="top">リクエスト登録</h1>
        <?php
        $i=1; 
        while ($col = mysqli_fetch_assoc($rst)) { ?>
            <form action='' method="POST" id="form<?=$i?>" class="master-container">
            <table style="margin-bottom: 4px; margin-top: 6px;">
                <tr>
                    <th>アニメ名</th>
                    <td><input type="text" name="anime_name<?=$i?>" class="search-input" value="<?=$col["anime"]?>"></td>
                </tr>
                <tr>
                    <th>フィギュア名</th>
                    <td><input type="text" name="figure_name<?=$i?>" class="search-input" value="<?=$col["name"]?>"></td>
                </tr>
                <tr>
                    <th>メーカー</th>
                    <td><input type="text" name="maker_name<?=$i?>" class="search-input" value="<?=$col["maker"]?>"></td>
                </tr>
                <tr>
                    <th>詳細</th>
                    <td><input type="text" id="memo" name="memo<?=$i?>" value="<?=$col["memo"]?>"></td>
                </tr>
            </table>
            <input type="hidden" name="way" value='0' id="way">
            <input type="hidden" name="i" value=<?=$i?>>
            <input type="hidden" name="request_id" value="<?=$col["request_id"]?>">
            <div id="masterbutton-container">
            <button type="submit" id="delete<?=$i?>" class="master-button" style="background-color: red;" onclick="req_delete()">削除</bottun>
            <button type="submit" id="register<?=$i?>" class="master-button" style="background-color: orange;" onclick="req_register()">登録</bottun>
            </div>
        </form>
        <?php } ?>
        </div>

<script>
    const sidebar = document.getElementById('sidebar');
    const menu_bar = document.getElementById('menu-icon');
    const content = document.getElementById('content');
    const figure_form_show = document.getElementById('search_condition');
    const figure_form = document.getElementById('figure_form');
    const way = document.getElementById("way");

    // マウスがトリガーに乗ったときにサイドバーを表示
    menu_bar.addEventListener('mouseover', () => {
        sidebar.classList.add('visible');
        content.classList.add('show');
        menu_bar.classList.add('show');
    });

    // サイドバーからマウスが離れたら非表示にする
    sidebar.addEventListener('mouseleave', () => {
        sidebar.classList.remove('visible');
        content.classList.remove('show');
        menu_bar.classList.remove('show');
    });

    figure_form_show.addEventListener('click',() => {
        figure_form.classList.toggle('show');
    });

    // スクロール位置をローカルストレージに保存
    function saveScrollPosition() {
        localStorage.setItem('scrollPosition', window.scrollY);
    };

    // ページ読み込み時に保存した位置に戻る
    window.onload = function() {
        const scrollPosition = localStorage.getItem('scrollPosition');
        if (scrollPosition) {
            window.scrollTo(0, scrollPosition);
            localStorage.removeItem('scrollPosition'); // 一度使ったら削除
        }
    };

    function req_delete(){
        way.value = 1;
    };

    function req_register(){
        way.value = 2;
    };

</script>

<?php include_once("inc/foot.inc");?>