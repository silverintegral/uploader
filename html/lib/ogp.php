<?php
$img = (empty($_SERVER["HTTPS"]) ? "http://" : "https://") . $_SERVER["HTTP_HOST"] . $_SERVER["REQUEST_URI"];
$img = dirname($img) . '/img/ogp.png';
?>
		<meta name="twitter:title" content="<?php echo($ogpt) ?>" />
		<meta name="twitter:twitter:image:alt" content="CLIP PHOTO" />
		<meta name="twitter:description" content="<?php echo($ogpd) ?>" />
		<meta name="twitter:site" content="@xenncam" />
		<meta name="twitter:creator" content="@xenncam" />
		<meta name="twitter:card" content="summary" />
		<meta name="twitter:image" content="<?php echo($img) ?>" />
