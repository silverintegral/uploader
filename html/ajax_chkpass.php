<?php
session_start();

header('content-type: application/json; charset=utf-8');

if (!preg_match('/^[0-9a-zA-Z]+$/', $_POST['id']) || !$_POST['pass']) {
	echo json_encode(array('check' => 'ng'));
	exit();
}

$pfile = __DIR__ . '/__data/' . $_POST['id'] . '.pass';

if (!file_exists($pfile)) {
	echo json_encode(array('check' => 'ng'));
	exit();
}

if (file_get_contents($pfile) === hash('sha512', $_POST['pass'])) {
	$_SESSION['pass'][$_POST['id']] = true;
	echo json_encode(array('check' => 'ok'));
	exit();	
}

echo json_encode(array('check' => 'ng'));
