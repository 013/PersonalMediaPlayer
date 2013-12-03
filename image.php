<?php

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $_GET['url']);
curl_setopt($ch, CURLOPT_HEADER, 1);
//curl_setopt($ch, CURLOPT_REFERER, "http://");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_BINARYTRANSFER, 1);
$return = curl_exec($ch);
curl_close($ch);

list($header, $image) = explode("\r\n\r\n", $return, 2);


header('Content-Type: image/jpeg');
//echo $header;

echo $image;


?>

