<?php

$url = $_GET['url'];
$ext = explode(".", $url);
$ext = end($ext);

if ($ext == 'png') {
	$image = imagecreatefrompng($url);
} else {
	$image = imagecreatefromjpeg($url);
}

list($width, $height) = getimagesize($url);

header('Content-Type: image/jpeg');

$image_p = imagecreatetruecolor(125, 185);
imagecopyresampled($image_p, $image, 0, 0, 0, 0, 125, 185, $width, $height);
imagedestroy($image);
imagejpeg($image_p);
imagedestroy($image_p);

?>

