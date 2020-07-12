<?php
session_start();

$name = __DIR__ . '/__data/' . $_GET['id'] . '/' . $_GET['p'];
$expires = 864000; // 10日間

if (!file_exists($name)) {
	header("HTTP/1.1 404 Not Found");
	exit;
}

$pfile = __DIR__ . '/__data/' . $_GET['id'] . '.pass';
if (file_exists($pfile) && !$_SESSION['pass'][$_GET['id']]) {
	header("HTTP/1.1 403 Forbidden");
	exit;
}

if (isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])) {
	header('HTTP/1.1 304 Not Modified');
	header('Cache-Control: max-age=' . $expires);
	header('Pragma: cache');
	exit;
}

header('Last-Modified: Fri Jan 01 2010 00:00:00 GMT');
header('Expires: ' . gmdate('D, d M Y H:i:s T', time() + $expires));
header('Cache-Control: private, max-age=' . $expires);
header('Pragma: cache');

header('Content-Length: ' . filesize($name));

if ($_GET['a'] == 'dl') {
	header('Content-Disposition: attachment; filename="' . $_GET['p'] . '"');

	if (preg_match('/iPhone|iPod|iPad/i', $_SERVER['HTTP_USER_AGENT'])) {
		header("Content-Type: application/force-download");
	} else {
//		header('Content-Type: image/jpeg; name="' . $_GET['p'] . '"');
		header("Content-Type: application/force-download");
	}
} else {
	header('Content-Type: image/jpeg; name="' . $_GET['p'] . '"');
}

readfile($name);
