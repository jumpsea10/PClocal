<?php
session_start();
include_once("inc/head.inc");
$is_exist = 0;
if (isset($_SESSION['user_ac_key'])) {
    $ac_key = $_SESSION['user_ac_key'];
    $rst = mysql_query("SELECT * FROM user WHERE user_ac_key = '$ac_key'");
    if ($rst->num_rows > 0) {
        $usr = mysqli_fetch_assoc($rst);
        $is_exist = 1;
    }
}

//重心の投票後の処理
if (isset($_POST["gravity_point"])) {
    $gp = $_gravity_to_english[$_gravity_point[$_POST["gravity_point"]]];
    $figure_id = mysqli_real_escape_string($_POST["vote_figure_id"]);
    $_SESSION[$figure_id] = 1;
    $rst = mysql_query("SELECT '$gp' FROM figure WHERE figure_id = '$figure_id'");
    $col = mysqli_fetch_assoc($rst);
    $new_gp_cnt = (int)$col[$gp] + 1;
    mysql_query("UPDATE figure SET $gp = '{$new_gp_cnt}' WHERE figure_id = '$figure_id'");
}

//お気に入り登録後の処理
if (isset($_POST["fav_usr"])) {
    $now = date("Y-m-d H:i:s");
    $fav_usr = mysqli_real_escape_string($con,$_POST['fav_usr']);
    $fav_figure = mysqli_real_escape_string($con,$_POST['fav_figure']);
    mysql_query("INSERT INTO user_favorite (user_id, figure_id, create_datetime) VALUES ($fav_user, $fav_figure, '{$now}')");
    $rst = mysql_query("SELECT * FROM figure WHERE figure_id='$fav_figure'");
    $col = mysqli_fetch_assoc($rst);
    $new_favorite = $col["favorite"] + 1;
    mysql_query("UPDATE figure SET favorite = '{$new_favorite}' WHERE figure_id = '$fav_figure'");
}

//order句の作成
$order_by = 'sale_date DESC';
// フィギュアデータを取得するクエリ
if (isset($_POST['sort'])) {
    switch($_POST['sort']) {
        case '1':
            $order_by = 'sale_date DESC';
        case '2':
            $order_by = 'buying_price DESC';
        case '3':
            $order_by = 'salling_price ASC';
        case '4':
            $order_by = 'favorite DESC';
    }
}

//WHERE句の作成
$where = '1=1';
$where .= isset($_POST["anime_name"]) ? " AND anime LIKE '%" . mysqli_real_escape_string($con,$_POST["anime_name"]) . "%'" : '';
$where .= isset($_POST["figure_name"]) ? " AND name LIKE '%" . mysqli_real_escape_string($con,$_POST["figure_name"]) . "%'" : '';
$where .= isset($_POST["start_sale_date"]) ? " AND sale_date >= '" . mysqli_real_escape_string($con,$_POST["start_sale_date"]) . "'" : '';
$where .= isset($_POST["end_sale_date"]) ? " AND sale_date <= '" . mysqli_real_escape_string($con,$_POST["end_sale_date"]) . "'" : '';
$where .= isset($_POST["start_salling_price"]) ? " AND salling_price >= '" . mysqli_real_escape_string($con,$_POST["start_salling_price"]) . "'" : '';
$where .= isset($_POST["end_salling_price"]) ? " AND salling_price <= '" . mysqli_real_escape_string($con,$_POST["end_salling_price"]) . "'" : '';
$where .= isset($_POST["start_buying_price"]) ? " AND buying_price >= '" . mysqli_real_escape_string($con,$_POST["start_buying_price"]) . "'" : '';
$where .= isset($_POST["end_buying_price"]) ? " AND buying_price <= '" . mysqli_real_escape_string($con,$_POST["end_buying_price"]) . "'" : '';

$nowpage = 1;
$offset = ($nowpage-1)*20;
if (isset($_POST["page"])) {
    $offset = ($_POST['page']-1)*20;
    $nowpage = $_POST['page'];
}

$figures_query = "SELECT * FROM figure WHERE $where ORDER BY $order_by LIMIT 20 offset $offset";
$figures_result = mysql_query($figures_query);

$max_pages = mysqli_num_rows($figures_result)/20+1;

?>
<!-- 左側のメニュー -->
<div class="sidebar" id="sidebar">
    <h2>メニュー</h2>
    <!-- ハンバーガーメニューボタン -->
    <div class="menu-icon" id="menu-icon">
    ☰
    </div>
    <ul>
        <li><a href="<?=$_ENV['URL_USER_REQUEST']?>">リクエスト</a></li>
        <?php if ($is_exist == 0) { ?>
        <li><a href="<?=$_ENV['URL_USER_LOGIN']?>">ログイン</a></li>
        <?php }  else if ($is_exist == 1) { ?>
        <li><a href="<?=$_ENV['URL_USER_FAVORITE']?>">お気に入り一覧</a></li>
        <li><a href="<?=$_ENV['URL_USER_LOGIN']?>">ログアウト</a></li>
        <?php } ?>
    </ul>
</div>
    <!-- メインコンテンツ -->
    <div class="content" id="content">
        <h1 id="top">フィギュア一覧</h1>
        <?php if ($is_exist == 1) { ?>
            <p style="color: green; font-size: 17px;">ログインユーザです。</p>
        <?php } else { ?>
            <p style="color: green; font-size: 17px;">ゲストユーザです。</p>
        <?php } ?>
        
        <div class="liner_str">
            <a id="search_condition">検索条件を表示/非表示</a>
        </div>

        <div id="figure_form" class='figure_form'>
            <form action='' method="POST">
            <table>
                <tr>
                    <th>アニメ名</th>
                    <td><input type="text" name="anime_name" class="search-input" value="<?php echo (!empty($_POST["anime_name"])? $_POST["anime_name"] : '');?>"></td>
                </tr>
                <tr>
                    <th>フィギュア名</th>
                    <td><input type="text" name="figure_name" class="search-input" value="<?php echo (!empty($_POST["figure_name"])? $_POST["figure_name"] : '');?>"></td>
                </tr>
                <tr>
                    <th>発売日</th>
                    <td><input type="date" name="start_sale_date" class="search-input" value="<?php echo (!empty($_POST["start_sale_sate"])? $_POST["start_sale_sate"] : '');?>">~<input type="date" name="end_sale_date" class="search-input" value="<?php echo (!empty($_POST["end_sale_date"])? $_POST["end_sale_date"] : '');?>"></td>
                </tr>
                <tr>
                    <th>売り相場</th>
                    <td><input type="number" name="start_selling_price" class="search-input" value="<?php echo (!empty($_POST["start_selling_price"])? $_POST["start_selling_price"] : '');?>">円~<input type="number" name="end_selling_price" class="search-input" value="<?php echo (!empty($_POST["end_selling_price"])? $_POST["end_selling_price"] : '');?>">円</td>
                </tr>
                <tr>
                    <th>買い相場</th>
                    <td><input type="number" name="start_buying_price" class="search-input" value="<?php echo (!empty($_POST["start_buying_price"])? $_POST["start_buying_price"] : '');?>">円~<input type="number" name="end_buying_price" class="search-input" value="<?php echo (!empty($_POST["end_buying_price"])? $_POST["end_buying_price"] : '');?>">円</td>
                </tr>
            </table>
            <button type="submit" class="search-button">検索</button>
        </form>
        </div>
        
        <form action='#' method='POST'>
        <div class="figure-sort">
            <label for="sort-select">並び替え：</label>
            <select id="sort-select" name="sort" onchange="this.form.submit()">
                <option value="1" <?= @$_POST['sort'] == 1 ? 'selected' : '' ?>>発売日時順</option>
                <option value="2" <?= @$_POST['sort'] == 2 ? 'selected' : '' ?>>相場高い順</option>
                <option value="3" <?= @$_POST['sort'] == 3 ? 'selected' : '' ?>>相場安い順</option>
                <option value="4" <?= @$_POST['sort'] == 4 ? 'selected' : '' ?>>人気順</option>
            </select>
        </div>
        </form>

        <div class="figure-list">
        <?php while ($col = mysqli_fetch_assoc($figures_result)) { ?>
        <div class="figure-item">
            <img src="<?=$col["image_filepath"]?>" alt="Figure Image" class="figure-image">
            <div class="figure-details">
                <h2 class="figure-name"><?=$col["name"]?></h2>
                <p>稼働日: <span><?=$col["sale_date"]?></span></p>
                <p>メーカー: <span><?=$col["maker"]?></span></p>
                <p>売り相場: <span><?=$col["salling_price"]?></span></p>
                <p>買い相場: <span><?=$col["buying_price"]?></span></p>
                <div class="Element_vertical">
            <!-- 重心の位置投票フォーム -->
            <form action="#" method="POST" class="vote-form">
                <p>重心の位置:</p>
                <div class="bar-chart-vertical">

                <?php 
                $total = 0;
                foreach ($_gravity_point as $key => $val) {
                    $total += $col[$_gravity_to_english[$val]];
                }
                foreach ($_gravity_point as $key => $val) {
                    $percent = $col[$_gravity_to_english[$val]]/$total*100; ?>
                    <div class="bar">
                    <p><?=$col[$_gravity_to_english[$val]]?></p>
                    <div class="bar-inner" style="<?php echo("height: ".$percent.'%;');?>"></div>
                    <input type="radio" name="gravity_point" value="<?=$key?>"> 
                    <p><?=$val?></p>
                    </div>
                <?php } ?>
                </div>
                <input type="hidden" name="vote_figure_id" value="<?=$col["figure_id"]?>">
            <?php if (!isset($_SESSION[$col["figure_id"]])) { ?>
                <button type="submit" class="vote-button" onclick="saveScrollPosition()">投票する</button>
            <?php } ?>
            </form>
                </div>
                <div class="Element_vertical">
                <label>お気に入り数: <span><?=$col["favorite"]?></span></label>
                <?php
                if ($is_exist) {
                $rst = mysql_query("SELECT * FROM user_favorite WHERE user_id = '{$usr["user_id"]}' AND figure_id = '{$col["figure_id"]}'");
                $fav_flag = mysqli_num_rows($rst)? 0: 1;                
                if ($fav_flag) { ?>
                    <form action="" method="POST" class="favorite-form">
                        <input type="hidden" name="fav_usr" value="<?=$usr["user_id"]?>">
                        <input type="hidden" name="fav_figure" value="<?=$col["figure_id"]?>">
                        <button type="submit" class="favorite-button" onclick="saveScrollPosition()">お気に入りに登録</button>
                    </form>
                <?php }} ?>
            </div>
            </div>
        </div>
        <?php } ?>
        </div>

        <!-- ページナビゲーション（ボタン形式） -->
        <div class="pagination">
            <form method="POST" action="#" onchange="this.form.submit()">
                <button type="submit" name="page" value="<?= max(1, $nowpage - 1) ?>" <?= $nowpage == 1 ? 'disabled' : '' ?>>前へ</button>
                <?php for ($i = 1; $i <= $max_pages; $i++) {?>
                    <button type="submit" name="page" value="<?= $i ?>" <?php if($i == $nowpage){ echo('class="active"');}?>><?= $i ?></button>
                <?php } ?>
                <button type="submit" name="page" value="<?= min($max_pages, $nowpage + 1) ?>" <?= $nowpage == $max_pages ? 'disabled' : ''?>>次へ</button>
            </form>
        </div>
    </div>



<script>
    const sidebar = document.getElementById('sidebar');
    const menu_bar = document.getElementById('menu-icon');
    const content = document.getElementById('content');
    const figure_form_show = document.getElementById('search_condition');
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

    figure_form_show.addEventListener('click',() => {
        figure_form.classList.toggle('show');
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





</script>
<?php include_once("inc/foot.inc");?>