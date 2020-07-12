<?

/**
 * 画像変換＆保存関数
 * $src_name	編集前のファイル名
 * $dst_name	編集後のファイル名
 * $max_w		最大横幅
 * $max_h		最大縦幅
 * $fixed		trueで自動的に縦向き横向きの修正を行わない（デフォルトはfalse）
 * $bg_r		背景色（赤、0-255、デフォルトは0）
 * $bg_g		背景色（緑、0-255、デフォルトは0）
 * $bg_b		背景色（青、0-255、デフォルトは0）
 * $bg_a		背景色（透明度、0-127、127で完全な透明、デフォルトは0）
 * $type		画像タイプ（png、gif、もしくはjpg、デフォルトはjpg）
 * 
 * 元画像のサイズが最大サイズに達していない場合は引き伸ばす
 **/
function img_save($src_name, $dst_name, $max_w, $max_h, $fixed = false, $bg_r = 0, $bg_g = 0, $bg_b = 0, $bg_a = 0, $type = '') {
	list($src_w, $src_h) = getimagesize($src_name);

	if ($src_w == $max_w && $src_h == $max_h) {
		// サイズ変換不要
		return @copy($src_name, $dst_name);
	}

	if (!$fixed) {
		// 縦向き横向きの決定
		if ($max_w < $max_h) {
			if ($src_w > $src_h)
				list($max_w, $max_h) = array($max_h, $max_w);
		} else {
			if ($src_w < $src_h)
				list($max_w, $max_h) = array($max_h, $max_w);
		}
	}

	// ファイルを開く
	$src_img = @imagecreatefromstring(file_get_contents($src_name)); // この方法で開くと画像タイプの指定が不要？
	if ($src_img === null) {
		return false;
	}

	// 新しい画像を作成する
	$dst_img = @imagecreatetruecolor($max_w, $max_h);
	if ($dst_img === null) {
		imagedestroy($src_img);
		return false;
	}

	imagealphablending($dst_img, false);
	imagesavealpha($dst_img, true);

	// アスペクト比を維持したまま縮小後（拡大後）のサイズを計算
	if ($src_w / $max_w > $src_h / $max_h) {
		$dst_w = $max_w;
		$dst_h = (int)($max_w / $src_w * $src_h);
		$dst_x = 0;
		$dst_y = ($max_h - $dst_h) / 2;
	} else {
		$dst_h = $max_h;
		$dst_w = (int)($max_h / $src_h * $src_w);
		$dst_x = ($max_w - $dst_w) / 2;
		$dst_y = 0;
	}

	// 新しい画像の背景を塗りつぶす
	$bg_r = min(max((int)$bg_r, 0x00), 0xFF);
	$bg_g = min(max((int)$bg_g, 0x00), 0xFF);
	$bg_b = min(max((int)$bg_b, 0x00), 0xFF);
	$bg_a = min(max((int)$bg_a, 0x00), 0x7F);
	if (!imagefill($dst_img, 0, 0, imagecolorallocatealpha($dst_img, $bg_r, $bg_g, $bg_b, $bg_a))) {
		imagedestroy($src_img);
		imagedestroy($dst_img);
		return false;
	}

	// 新しい画像に元画像をコピーする
	if (!imagecopyresampled($dst_img, $src_img, $dst_x, $dst_y, 0, 0, $dst_w, $dst_h, $src_w, $src_h)) {
		imagedestroy($src_img);
		imagedestroy($dst_img);
		return false;
	}

	// 新しい画像の保存
	$type = strtolower($type);
	if ($type == 'png') {
		if (!imagepng($dst_img, $dst_name, 9)) {
			imagedestroy($src_img);
			imagedestroy($dst_img);
			return false;
		}
	} else if ($type == 'gif') {
		if (!imagegif($dst_img, $dst_name)) {
			imagedestroy($src_img);
			imagedestroy($dst_img);
			return false;
		}
	} else {
		if (!imagejpeg($dst_img, $dst_name, 90)) {
			imagedestroy($src_img);
			imagedestroy($dst_img);
			return false;
		}
	}

	// 終了処理
	imagedestroy($src_img);
	imagedestroy($dst_img);

	return true;
}


// インストールされているphpが対応する画像フォーマットかどうか
function is_img($name) {
	$img = @imagecreatefromstring(file_get_contents($name));
	if ($img === false) {
		return false;
	}

	imagedestroy($img);
	return true;
}

// pdfかどうか
function is_pdf($name) {
	$mime = shell_exec('file -bi '.escapeshellcmd($name));
	$mime = preg_replace('/^.*?([-\/0-9a-zA-Z]+).*$/s', '$1', $mime);

	if ($mime == 'application/pdf' || $mime == 'application/x-pdf' || $mime == 'application/acrobat'
		|| $mime == 'applications/vnd.pdf' || $mime == 'text/pdf' || $mime == 'text/x-pdf') {
		return true;
	} else {
		return false;
	}
}
