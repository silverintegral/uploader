<?php
//ini_set( 'display_errors', 1 );

$dir = __DIR__ . '/__data';
if (!is_dir($dir . '/' . $_GET['id'])) {
	header("HTTP/1.1 404 Not Found");
?>
<html>
	<head>
<?php $ogpt = 'CLIP PHOTO BETA'; $ogpd = $msg; require_once(__DIR__ . '/common/htmlhead.php'); ?>
		<link type="text/css" rel="stylesheet" href="./list.css?a=<?=rand(100, 999)?>" />
		<script type="text/javascript" src="./list.js"></script>
	</head>
	<body style="text-align:center;">
		<div id="sp_title" class="sp_only"><img src="./img/top_sp.jpg" /></div>
		<div id="pc_title" class="pc_only"><img src="./img/top_pc.jpg" /></div>
		<br />
		<br />
		<div style="color:gray;font-size:1.6em;font-weight:bold">404 File Not Found</div><br />
		<br />
		<div style="line-height:1.6em;">期限切れ、もしくは、<br />運営に削除された可能性があります</div>
	</body>
</html>

<?php
	exit;
}
?>
<html>
	<head>
<?php $ogpt = 'CLIP PHOTO BETA'; $ogpd = $msg; require_once(__DIR__ . '/common/htmlhead.php'); ?>
		<link type="text/css" rel="stylesheet" href="./js/lightbox/simplelightbox.min.css" >
		<link type="text/css" rel="stylesheet" href="./css/view.css" />
		<script type="text/javascript" src="./js/view.js"></script>
	</head>
	<body>
<?php require_once(__DIR__ . '/common/header.php'); ?>
		<div id="main">
			<div id="view"><img src="./<?=$_GET['id']?>/<?=$_GET['p']?>/V"></div>
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
