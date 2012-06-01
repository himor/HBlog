<?php
$s = $_GET['im']; 					//This is path to the image
header('Content-type: image/jpeg');

$image = imagecreatefromjpeg($s);

if (!$image) {
	return false;
}

list($width,$height) = getimagesize($s);
if ($width > $height) {
	$szy = $height;
	$szx = $szy * 2;
	if ($szx>$width) {
		$szx = $width;
		$szy = $szx*.5;
	}
	} else {
		$szx = $width; 
		$szy = $szx*.5; 
		}
$new_size_x = 280;
$new_size_y = 140;
$image_p = imagecreatetruecolor($new_size_x,$new_size_y);
imagecopyresampled($image_p,$image,0,0,0,0,$new_size_x,$new_size_y,$szx,$szy);

imagejpeg($image_p,null,100);
?>