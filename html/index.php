<html>
	<head>
<?php $ogpt = 'CLIP PHOTO BETA'; $ogpd = 'UPLOAD'; require_once(__DIR__ . '/common/htmlhead.php'); ?>
		<link type="text/css" rel="stylesheet" href="./css/upload.css?a=<?=rand(100, 999)?>" />
		<script type="text/javascript" src="./js/upload.js?a=1"></script>
	</head>
	<body>
<?php require_once(__DIR__ . '/common/header.php'); ?>
		<div id="main">
			<div id="mainbox">
				<div id="dbox" class="d_off">
					<strong>ドラッグ＆ドロップでファイルを追加</strong><br />
					<input type="button" id="selbtn"　accept="image/*" value="ファイルを選択する" />
					<input type="file" id="selinput" multiple="multiple" /><br />
					<br />
					<span style="font-size:0.7em">最大10GBまで　10日間有効　フォルダ未対応</span>
				</div>
				<ul id="waitingList" style="max-height: 300px; overflow-y: auto; margin: 15px 0;">
				</ul>
				<input type="button" id="selbtn_big"　accept="image/*" value="ファイルを追加" /><br />
				<div id="spmsg" class="sp_only">10日間有効</div>
				<input type="text" id="msg" value="" placeholder="一言コメントやメモ（相手に表示されます）" /><br /><span class="pc_only"><br /></span>
				<input type="text" id="pass" value="" placeholder="パスワード（任意）" />　<span class="sp_only"><br /></span>
				<input type="button" id="clearWaitList" value="クリア">　<input type="button" id="upload" value="アップロード">　<span class="sp_only"><br /><br /></span><span id="copy_ret"></span>
				<input type="hidden" id="copy_url" value="">
				<audio id="beep_ok_src" src="./misc/ok.mp3" type="audio/mp3"></audio>
				<audio id="beep_ok_btn" style="opacity:0"></audio>
				<audio id="beep_ng_src" src="./misc/ng.mp3" type="audio/mp3"></audio>
				<audio id="beep_ng_btn" style="opacity:0"></audio>
			</div>
		</div>
<?php require_once(__DIR__ . '/common/footer.php'); ?>
	</body>
</html>
