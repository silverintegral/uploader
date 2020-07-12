<?php
//ini_set('display_errors', 1);
session_start();

set_time_limit(0);
ignore_user_abort(true);

if (!$_GET['id'])
	die('ERR 01');

$pfile = __DIR__ . '/__data/' . $_POST['id'] . '.pass';
if (file_exists($pfile) && !$_SESSION['pass'][$_GET['id']]) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}

$zip_dir = __DIR__ . '/__data';
$zip_name = $_GET['id'] . '.zip';
$file_list = array();

$log = __DIR__ . '/__log/' . $_GET['id'] . '_' . date('Ymd-His-') . rand(10, 99) . '.txt';
//touch($log);
chmod($log, 0666);
function putlog($str) {
	global $log;
//	file_put_contents($log, $str . "\n", FILE_APPEND);
}

if (isset($_GET['f']) && $_GET['f'] != '') {
	foreach (explode(',', $_GET['f']) as $file) {
		$file_list[] = $zip_dir . '/' . $_GET['id'] . '/' . $file;
	}

	$zip_name = $_GET['id'] . '-' . rand(100, 9999) . '.zip';

} else if (!file_exists($zip_dir . '/' . $zip_name)) {
	$tmp_name = glob($zip_dir . '/' . $_GET['id'] . '.zip.*')[0];
	$tmp_size = filesize($tmp_name);
	$tmp_cou = 0;

	putlog($tmp_name);

	while (file_exists($tmp_name)) {
		sleep(3);
		putlog("1");

		$ts = filesize($tmp_size);
		if ($ts === false)
			continue;

		putlog("2 loop");

		if ($tmp_size == $ts) {
			$tmp_cou++;
			putlog("3 size " . $tmp_size . '==' . $ts);
			putlog("4 cou " . $tmp_cou);
			if ($tmp_cou == 5) {
				@unlink($tmp_name);
				putlog("5");
				break;
			}
		} else {
			$tmp_cou = 0;
			putlog("6 loop safe");
		}
		$tmp_size = $ts;

		if (file_exists($zip_dir . '/' . $zip_name))
			break;
		
		if (connection_aborted() == 1)
			exit();
		
		clearstatcache();
	}

	putlog("7");

	//if (!file_exists($zip_dir . '/' . $_GET['id']))
	//	die('ERR 02');
	
	$file_list = glob($zip_dir . '/' . $_GET['id'] . '/*');
}

if (!file_exists($zip_dir . '/' . $zip_name)) {
	if (count($file_list)) {

		$zip = new ZipArchive();
		if ($zip->open($zip_dir . '/' . $zip_name, ZIPARCHIVE::CREATE | ZIPARCHIVE::OVERWRITE) === true) {

			$cou = 0;

			foreach ($file_list as $file) {
				if (is_file($file) && substr($file, -2) != '.s' && substr($file, -2) != '.m') {
					$cou++;
					if (!$zip->addFile($file, basename($file)))
						die('ERR 04');
				}
			}

			@$zip->close();
			if (!$cou) {
				die('ERR 05');
			}

			chmod($zip_dir . '/' . $zip_name, 0666);
		} else {
			die('ERR 03');
		}

		@unlink(glob($zip_dir . '/' . $zip_name . '.*')[0]);
	}
}

if (connection_aborted() == 0) {
	$size = filesize($zip_dir . '/' . $zip_name);
	$fp = fopen($zip_dir . '/' . $zip_name, 'rb');
	$start = 0;
	$end = 0;

	if (!$_GET['f']) {
		if ($range = @$_SERVER["HTTP_RANGE"]) {
			list($start, $end) = sscanf($_SERVER['HTTP_RANGE'], 'bytes=%d-%d');
			$start = (int)$start;
			$end = (int)$end;
		}

		if ($start) {
			if ($end) {
				$rsize = $size - ($end - $start);
			} else {
				$rsize = $size - $start;
			}

			if ($rsize < 1 || $rsize > $size - $start) {
				header('HTTP/1.1 416 Requested Range Not Satisfiable');
				exit();
			} else {
				$size = $rsize;
				header('HTTP/1.1 206 Partial Content');
			}

			fseek($fp ,$start);

			header('Content-Range: bytes ' . $size);
			header('Content-Length: ' . $size);
		} else {
			header('Content-Length: ' . $size);
		}
	} else {
		header('Content-Length: ' . $size);
	}

	header('Content-Type: application/zip; name="clip_' . $zip_name . '"');
	header('Content-Disposition: attachment; filename="clip_' . $zip_name . '"');
	header("Content-Transfer-Encoding: binary");

	while (!feof($fp)) {
	    echo fread($fp, 8000);
	}

	fclose();
}

if (isset($_GET['f']) && $_GET['f'] != '') {
	@unlink($zip_dir . '/' . $zip_name);
}
