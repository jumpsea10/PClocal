<?php
session_start();
include_once("./inc/def.inc");
include_once("./inc/function.inc");
$is_exist = 0;
if (isset($_SESSION['operator_ac_key'])) {
    $ac_key = $_SESSION['operator_ac_key'];
    $rst = mysql_query("SELECT * FROM operator WHERE operator_ac_key = '$ac_key'");
    if ($rst->num_rows > 0) {
        $usr = mysqli_fetch_assoc($rst);
        $is_exist = 1;
    }
}
if ($is_exist === 0) {
    $_SESSION['login_err'] = '*再ログインしてください';
    header("Location: ". $_ENV['URL_MASTER_LOGIN']);
    exit();
}

//フィギュアの登録処理
$is_register = 0;
$is_register = $_POST['register'] ?? 0;
if (isset($_POST['register'])) {
    $i = 1;
    while (isset($_POST['figure_name'.$i])) {
        $name = isset($_POST['figure_name']) ? $_POST['figure_name'] : null;
        $anime = isset($_POST['anime_name']) ? $_POST['anime_name'] : '';
        $maker = isset($_POST['maker']) ? $_POST['maker'] : null;
        $sale_date = isset($_POST['sale_date']) ? $_POST['sale_date'] : null;
        $salling_price = isset($_POST['salling_price']) ? $_POST['salling_price'] : null;
        $buying_price = isset($_POST['buying_price']) ? $_POST['buying_price'] : null;
        $maker = isset($_POST['maker']) ? $_POST['maker'] : null;
        $figure_id = mysql_query("INSERT INTO figure (name,anime,maker,sale_date,salling_price,buying_price,image_filepath) VALUES ('$name','$anime','$maker','$sale_date','$salling_price','$buying_price','$image_filepath')");

        $year = date("Y",strtotime($sale_date));
        $month = date("m",strtotime($sale_date));
        $dir = "/var/www/img";
        if (!isdir($dir.'/'.$year.'/'.$month)) {
            if (!isdir($dir.'/'.$year) ){
                $newdir = $dir. '/'.$year; 
                mkdir($newdir,0777,true);
            }
        $newdir .= $dir.'/'.$year.'/'.$month;
        mkdir($newdir,0777,true);
        }
        $file_name = $figure_id.basename($_FILES["name"]);
        $file_path = $newdir.'/'.$file_name;
        move_upload_file($_FILES['temp_name'],$file_path);
        $update_path = '/img'.'/'.$year.'/'.$month.'/'.$file_name;
        mysql_query("UPDATE figure SET file_path = '{$update_path}' WHERE figure_id = '{$figure_id}'");
        $i++;
    }
}   
$cnt=1;

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ぷらいずせんたー/管理者</title>
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
        <li><a href="<?=$_ENV['URL_MASTER_REQUEST']?>">リクエスト登録</a></li>
        <li><a href="<?=$_ENV['URL_USER_INDEX']?>">フィギュア一覧</a></li>
        <li><a href="<?=$_ENV['URL_MASTER_LOGIN']?>">ログアウト</a></li>
    </ul>
</div>

    <!-- メインコンテンツ -->
    <div class="content" id="content">
        <h1 id="top">管理者 フィギュア登録</h1>

        <div id="figure_form" class='figure_form' style='display: block;'>
            <form action='' method="POST">
            <table>
                <tr>
                    <th>アニメ名</th>
                    <td><input type="text" name="anime_name<?=$cnt?>" class="search-input" value="<?php echo(isset($_COOKIE['anime_name'])? $_COOKIE['anime_name']:''); ?>"></td>
                </tr>
                <tr>
                    <th>フィギュア名</th>
                    <td><input type="text" name="figure_name<?=$cnt?>" class="search-input" value="<?php echo(isset($_COOKIE['figure_name'])? $_COOKIE['figure_name']:''); ?>"></td>
                </tr>
                <tr>
                    <th>メーカー</th>
                    <td><input type="text" name="maker<?=$cnt?>" class="search-input" value="<?php echo(isset($_COOKIE['maker_name'])? $_COOKIE['maker_name']:''); ?>"></td>
                </tr>
                <tr>
                    <th>始動日</th>
                    <td><input type="date" name="start_sale_date<?=$cnt?>" class="search-input"></td>
                </tr>
                <tr>
                    <th>売り相場</th>
                    <td><input type="number" name="start_selling_price<?=$cnt?>" class="search-input" >円</td>
                </tr>
                <tr>
                    <th>買い相場</th>
                    <td><input type="number" name="start_buying_price<?=$cnt?>" class="search-input">円</td>
                </tr>
                <tr>
                    <th>画像</th>
                    <td><input type="file" name="image<?=$cnt?>" class="register-input"></td>
                </tr>
            </table>

            <div id="exdom"></div>

            <input type="hidden" name="register" value="1">
            <button type="submit" class="register-button" style="background-color: #ff9800;">登録</button>
        </form>
        </div>
        <input type="hidden" id="cnt" value="<?=$cnt?>">
        <button id="add_button" class="register-button" style="background-color: #ff9800;">一行追加</button>
        <button id="delete_button" class="register-button" style="background-color: #ff9800;">一行減らす</button>

    </div>
<script>
    const sidebar = document.getElementById('sidebar');
    const menu_bar = document.getElementById('menu-icon');
    const content = document.getElementById('content');
    const figure_form = document.getElementById('figure_form');

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

    // スクロール位置をローカルストレージに保存
    function saveScrollPosition() {
        localStorage.setItem('scrollPosition', window.scrollY);
    }

    // ページ読み込み時に保存した位置に戻る
    window.onload = function() {
        const scrollPosition = localStorage.getItem('scrollPosition');
        if (scrollPosition) {
            window.scrollTo(0, scrollPosition);
            localStorage.removeItem('scrollPosition'); // 一度使ったら削除
        }
    };

    // ボタンを取得
    const add_Button = document.getElementById("add_button");
    add_Button.addEventListener("click", function() {
    // 現在のカウントを取得し、インクリメント
    let cnt = parseInt(document.getElementById("cnt").value);
    cnt++;

    // 新しいHTML要素を作成
    let html = '';
    html += '<table><tr><th>アニメ名</th><td><input type="text" name="anime_name'+cnt+'" class="register-input"></td></tr>';
    html += '<tr><th>フィギュア名</th><td><input type="text" name="figure_name'+cnt+'" class="register-input"></td></tr>';
    html += '<tr><th>メーカー</th><td><input type="text" name="maker'+cnt+'" class="register-input"></td></tr>';
    html += '<tr><th>始動日</th><td><input type="date" name="sale_date'+cnt+'" class="register-input"></td></tr>';
    html += '<tr><th>売り相場</th><td><input type="number" name="salling_price'+cnt+'" class="register-input"></td></tr>';
    html += '<tr><th>買い相場</th><td><input type="number" name="buying_price'+cnt+'" class="register-input"></td></tr>';
    html += '<tr><th>ファイルパス</th><td><input type="text" name="file_path'+cnt+'" class="register-input"></td></tr>';
    html += '<tr><th>画像</th><td><input type="file" name="image'+cnt+'" class="register-input"></td></tr></table>';

    // 作成したHTMLを新しい要素としてDOMに追加
    const input = document.getElementById("exdom");
    const newElement = document.createElement('div');
    newElement.id = "exdom" + cnt; 
    newElement.innerHTML = html;
    input.appendChild(newElement);

    // カウントを更新
    document.getElementById("cnt").value = cnt;
});

const delete_button = document.getElementById("delete_button");
delete_button.addEventListener("click", function() {
    let cnt = parseInt(document.getElementById("cnt").value);

    if (cnt > 1) {
        // 最後に追加された要素を削除
        const exdom = document.getElementById("exdom" + cnt);
        if (exdom) exdom.remove();
    
    // カウントをデクリメントして更新
    cnt--;
    document.getElementById("cnt").value = cnt;
}
});

</script>

<?php include_once("inc/foot.inc");?>