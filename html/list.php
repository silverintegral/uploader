<?php
//ini_set( 'display_errors', 1 );
session_start();

$dir = __DIR__ . '/__data';
if (!is_dir($dir . '/' . $_GET['id'])) {
	header("HTTP/1.1 404 Not Found");
?>
<html>
	<head>
<?php $ogpt = 'CLIP PHOTO BETA'; $ogpd = $msg; require_once(__DIR__ . '/common/htmlhead.php'); ?>
		<link type="text/css" rel="stylesheet" href="./list.css?a=<?=rand(100, 999)?>" />
	</head>
	<body style="text-align:center;">
		<div id="sp_title" class="sp_only"><img src="./img/top_sp.jpg" /></div>
		<div id="pc_title" class="pc_only"><img src="./img/top_pc.jpg" /></div>
		<br />
		<br />
		<div style="color:gray;font-size:1.6em;font-weight:bold">404 File Not Found</div><br />
		<br />
		<div style="line-height:1.6em;">期限切れ、もしくは、<br />削除された可能性があります</div>
	</body>
</html>

<?php
	exit();
}

if (!$_GET['id'])
	die('ERR 01');


$pfile = __DIR__ . '/__data/' . $_REQUEST['id'] . '.pass';
if (file_exists($pfile)) {
	if (isset($_POST['pass'])) {
		if (file_get_contents($pfile) === hash('sha512', $_POST['pass'])) {
			$_SESSION['pass'][$_POST['id']] = true;
		}
		header('Location: /cp/' . $_POST['id']);
		exit();
	} else if (!$_SESSION['pass'][$_GET['id']]) {
?>
<html>
	<head>
<?php $ogpt = 'CLIP PHOTO BETA'; $ogpd = $msg; require_once(__DIR__ . '/common/htmlhead.php'); ?>
		<link type="text/css" rel="stylesheet" href="./list.css?a=<?=rand(100, 999)?>" />
<script>
	$(function() {
		$('form').submit(function() {
			$.ajax({
				url: "./ajax_chkpass.php",
				type: "POST",
				dataType: "json",
				async: false,
				cache: false,
				data: 'id=<?=$_GET['id']?>&pass=' + $('#pass').val(),
				success: function(data) {
					if (data['check'] == 'ok') {
						location.reload();
					} else {
						alert('パスワードが異なります');
					}
				},
				error: function(data) {
					alert('ネットワークエラー');
				}
			});
			return false;
		});
	});
</script>
	</head>
	<body style="text-align:center;">
<?php require_once(__DIR__ . '/common/header.php'); ?>
		<div id="main">
			<br />
			<br />
			<div style="color:gray;font-size:1.6em;font-weight:bold">パスワード入力</div><br />
			<br />
			<form id="login" method="POST">
				<input type="text" id="pass" name="pass" />
				<input type="hidden" name="id" value="<?=$_REQUEST['id']?>" />
				<input type="submit" value="認証" />
			</form>
		</div>
<?php require_once(__DIR__ . '/common/footer.php'); ?>
	</body>
</html>

<?php
		exit();
	}
}


$msg = @file_get_contents($dir . '/' . $_GET['id'] . '.msg');
if (!$msg)
	$msg = '画像共有 <b>CLIP PHOTO</b>';
?>
<html>
	<head>
<?php $ogpt = 'CLIP PHOTO BETA'; $ogpd = $msg; require_once(__DIR__ . '/common/htmlhead.php'); ?>
		<link type="text/css" rel="stylesheet" href="./js/lightbox/simplelightbox.min.css" >
		<link type="text/css" rel="stylesheet" href="./css/list.css?a=<?=rand(100, 999)?>" />
		<script type="text/javascript" src="./js/lightbox/simple-lightbox.js"></script>
		<script type="text/javascript" src="./js/list.js"></script>
	</head>
	<body>
<?php require_once(__DIR__ . '/common/header.php'); ?>
		<div id="main">
<?php
if (preg_match('/iPhone|iPod|iPad/i', $_SERVER['HTTP_USER_AGENT'])) {
?>
		<div style="text-align:center;margin:10px">
			<a class="btn_orange" href="#" id="sel_start">選択DL</a>&nbsp;
			<a class="btn_orange" href="./<?=$_GET['id']?>/Z">全てDL</a>&nbsp;
			<span class="sp_only">長押し原寸表示</span>
		</div>
<?php
} else {
?>
		<div style="text-align:center;margin:10px">
			<a class="btn_orange" href="#" id="sel_start">選択DL</a>&nbsp;
			<a class="btn_orange" href="./<?=$_GET['id']?>/Z">全てDL</a>&nbsp;
			<span class="sp_only">長押し原寸DL</span>
		</div>
<?php
}
?>
		<hr />
			<div style="color:gray;margin:10px;text-align:center;word-break:keep-all;overflow-wrap:break-word;"><?=$msg?></div>
			<table id="lightbox-margin-table"><tr><td id="lightbox-margin"></td><td><div id="lightbox" class="lightbox">
<?php
$dt = date('m月d日H時',(int)file_get_contents($dir . '/' . $_GET['id'] . '.dtime'));
$cou = 0;
$files = array();
foreach (glob($dir . '/' . $_GET['id'] . '/*.s') as $file) {
	if (is_file($file)) {
		$file = basename($file);
		$file_m =  substr($file, 0, strlen($file) - 2);
		$files[] = '"' . $file_m . '"';
		$cou++;
		echo '<div data-long-press-delay="500" class="sbox" url="' . $_GET['id'] . '/' . $file_m . '">'
			. '<a m="./' . $_GET['id'] . '/' . $file_m . '.m">'
			. '<img title="' . substr($file, 0, strlen($file) - 2) . '" class="simg" style="opacity:0" '
			. 'b="./' . $_GET['id'] . '/' . $file_m . '" '
			. 's="./' . $_GET['id'] . '/' . $file . '" /></a>'
			. '<div class="bbox" fname="' . $file_m . '"></div>'
			. '</div>';
	}
}

if ($cou) {
	$file = basename($file);
	$file_m =  substr($file, 0, strlen($file) - 2);

	echo '<div class="sbox">'
		. '<img class="simg" '
		. 's="./qr/' . $_GET['id'] . '/' . $_GET['f'] . '/S" />'
		. '<div class="qbox"></div>'
		. '</div>';
}
?>
			</div></td></tr></table>
<?php
echo '<script>var sel = -1; var sels = [' . implode(',', $files) . '];</script>';
echo '<div style="color:gray;margin:10px;text-align:center">' . $cou . '枚　' . $dt . 'まで</div>';
?>
		<hr />
		<div style="text-align:center;margin-top:10px;line-height:1.5em">
			<span style="color:orange;font-size:0.8em">"全てDL"の初回は時間がかかる事があります</span><span class="sp_only"><br /></span><span class="pc_only">　</span>
			<span style="color:orange;font-size:0.8em">"選択DL"は数が多いと時間がかかります</span><br />
		</div>
		</div>
<?php require_once(__DIR__ . '/common/footer.php'); ?>
		<div id="dlpanel">
			<a href="#" id="sel_cancel">キャンセル</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<a href="#" id="sel_reset">リセット</a>&nbsp;&nbsp;
			<a href="#" id="sel_dl">ダウンロード</a>
		</div>
		<div id="dlpanel_e"><span style="color:black">選択数が多いと時間がかかります</span></div>
	</body>
</html>
