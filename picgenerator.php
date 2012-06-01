<?php

$number = 		26;
$pick = 		12;
$prefix = 		'img_';
$extension = 	'.jpg';
$path = 		'upload/sidebar/';

/* THIS PART RETURNS SET OF IMAGES */
$picNames = array();
$picArray = array();
for ($i = 0; $i<$number; $i++)
	$picNames[] = $path . $prefix . $i;
$array = array_rand($picNames, $pick);
foreach($array as $a)
	$picArray[] = $picNames[$a];
shuffle($picArray);
$html = '';$next = null;
foreach ($picArray as $p) {
	if (!file_exists($p . '_thumb' . $extension)) {
		buildThumb($p);
	}
	$html .= '<a href="display.php?image=' . $p . $extension . ($next ? '&next=' . $next : '') . '">';
	$html .= '<img src="' . $p . '_thumb' . $extension . '" />';
	$html .= '</a>';
	$next = $p . $extension;
}
echo $html;
exit;

function buildThumb($s) {
	global $extension;
	//header('Content-type: image/jpeg');
	$image = imagecreatefromjpeg($s.$extension);
	list($width,$height) = getimagesize($s.$extension);
	if ($width > $height) {
		$szy = $height;
		$szx = $szy;
		if ($szx>$width) {
			$szx = $width;
			$szy = $szx;
		}
		} else {
			$szx = $width; 
			$szy = $szx; 
			}
	$new_size_x = 62;
	$new_size_y = 62;
	$image_p = imagecreatetruecolor($new_size_x,$new_size_y);
	imagecopyresampled($image_p,$image,0,0,0,0,$new_size_x,$new_size_y,$szx,$szy);
	imagejpeg($image_p,$s.'_thumb'.$extension,100);
}

?>