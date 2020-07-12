<?php
set_time_limit(0);
$now = time();

// UP失敗を削除対象にする
foreach (glob(dirname(__DIR__) . '/__data/*') as $dir) {
	if (is_dir($dir)) {
		if (!file_exists($dir . '.dtime') || !file_exists($dir . '.msg')) {
			file_put_contents($dir . '.dtime', '0');
		}
	}
}

// 期限切れを削除する
foreach (glob(dirname(__DIR__) . '/__data/*.dtime') as $file) {
	if (is_file($file)) {
		$dtime = (int)@file_get_contents($file);

		if ($dtime <= $now) {
			$sdir = substr($file, 0, strlen($file) - 6);

			foreach (glob($sdir . '/*') as $sfile) {
				if (is_file($sfile)) {
					@unlink($sfile);
				}
			}
			rmdir($sdir);

			foreach (glob($sdir . '.*') as $sfile) {
				if (is_file($sfile)) {
					@unlink($sfile);
				}
			}
		}
	}
}
