<?php
set_time_limit(0);
ignore_user_abort(true);

$dir = __DIR__ . '/__data';
$pre = '';

function img_save($src_name, $dst_name, $max, $fill = false) {
	list($src_w, $src_h) = getimagesize($src_name);
	
	$exif = exif_read_data($src_name);
	$rotate = 0;
	$flip = false;

	if (is_array($exif)) {
		switch ($exif['Orientation']) {
			case 8:		//右に90度（→）
				$rotate = 90;
				//$rotate = 270;
				break;
			case 3:		//180度回転（↓）
				$rotate = 180;
				break;
			case 6:		//右に270度回転（←）
				$rotate = 270;
				break;
			case 2:		//反転　（↑）
				$flip = IMG_FLIP_HORIZONTAL;
				break;
			case 7:		//反転して右90度（→）
				$rotate = 90;
				$flip = IMG_FLIP_HORIZONTAL;
				break;
			case 4:		//反転して180度なんだけど縦反転と同じ（↓）
				$flip = IMG_FLIP_VERTICAL;
				break;
			case 5:		//反転して270度（←）
				$rotate = 270;
				$flip = IMG_FLIP_HORIZONTAL;
				break;
		}
	}

	/*
	if ($src_w <= $max && $src_h <= $max) {
		// サイズ変換不要
		return @copy($src_name, $dst_name);
	}
	*/

	// ファイルを開く
	$src_img = @imagecreatefromstring(file_get_contents($src_name)); // この方法で開くと画像タイプの指定が不要？
	
	if ($src_img === null) {
		return false;
	}

	// アスペクト比を維持したまま縮小後（拡大後）のサイズを計算
	if ($src_w / $max > $src_h / $max) {
		$dst_w = $max;
		$dst_h = (int)($max / $src_w * $src_h);
		$dst_x = 0;
		$dst_y = ($max - $dst_h) / 2;
	} else {
		$dst_h = $max;
		$dst_w = (int)($max / $src_h * $src_w);
		$dst_x = ($max - $dst_w) / 2;
		$dst_y = 0;
	}

	// 新しい画像を作成する
	if ($fill) {
		$dst_img = @imagecreatetruecolor($max, $max);
	} else {
		$dst_img = @imagecreatetruecolor($dst_w, $dst_h);
		$dst_x = 0;
		$dst_y = 0;
	}

	if ($dst_img === null) {
		imagedestroy($src_img);
		return false;
	}

	imagealphablending($dst_img, false);
	imagesavealpha($dst_img, true);

	// 新しい画像の背景を塗りつぶす
	if ($fill) {
		if (!imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, 0, 0, 0, 0))) {
			imagedestroy($src_img);
			imagedestroy($dst_img);
			return false;
		}
	}

	// 新しい画像に元画像をコピーする
	if (!imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $src_w, $src_h)) {
		imagedestroy($src_img);
		imagedestroy($dst_img);
		return false;
	}

	//反転(2,7,4,5)
	if ($flip !== false) {
		$dst_img = imageflip($dst_img, $flip);
	}

	//回転(8,3,6,7,5)
	if ($rotate != 0) {
		$dst_img = imagerotate($dst_img, $rotate, 0);
	}

	// 新しい画像の保存
	if (!imagejpeg($dst_img, $dst_name, 75)) {
		imagedestroy($src_img);
		imagedestroy($dst_img);
		return false;
	}
	
	chmod($dst_name, 0777);

	// 終了処理
	imagedestroy($src_img);
	imagedestroy($dst_img);

	return true;
}

function mk_rand_str($len) {
    $str = array_merge(range('a', 'z'), range('0', '9'), range('A', 'Z'));
    $r_str = null;
    for ($i = 0; $i < $len; $i++) {
        $r_str .= $str[rand(0, count($str) - 1)];
    }
    return $r_str;
}

while (1) {
	$pre = mk_rand_str(8);
	if (!file_exists($dir . '/' . $pre)) {
		$dir .= '/' . $pre;
		break;
	}
}

file_put_contents($dir . '.dtime', time() + (10 * 24 * 60 * 60));
chmod($dir . '.dtime', 0666);

file_put_contents($dir . '.msg', $_POST['msg']);
chmod($dir . '.msg', 0666);

mkdir($dir);
chmod($dir, 0777);

$file = array();
if (!empty($_FILES['file'])) {
	foreach ($_FILES['file'] as $string => $naiyou) {
		foreach ($naiyou as $key => $val) {
			$file[$key][$string] = $val;
		}
	}

	foreach ($file as $key => $val) {
		$is = getimagesize($val['tmp_name']);
		if (is_array($is) && $is[0] != 0 && $is[1] != 0) {
			if (move_uploaded_file($val['tmp_name'], $dir . '/' . $val['name'])) {
				chmod($dir . '/' . $val['name'], 0777);
				img_save($dir . '/' . $val['name'], $dir . '/' . $val['name'] . '.s', 300, true);
				chmod($dir . '/' . $val['name'], $dir . '/' . $val['name'] . '.s', 0777);
				img_save($dir . '/' . $val['name'], $dir . '/' . $val['name'] . '.m', 2000, false);
				chmod($dir . '/' . $val['name'], $dir . '/' . $val['name'] . '.m', 0777);
			}
		}
	}
}  

if ($_POST['pass']) {
	file_put_contents($dir . '.pass', hash('sha512', $_POST['pass']));
	chmod($dir . '.pass', 0666);
}

if (connection_aborted() == 1)
	exit();

header('content-type: application/json; charset=utf-8');
echo json_encode(array('path' => $pre));
