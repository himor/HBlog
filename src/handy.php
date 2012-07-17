<?php

$cachePath = "upload/cache/";

function extractImage($str) {
	global $cachePath;
	$start = -1;$end = -1;
	for ($i = 0; $i < strlen($str)-5; $i++)
		if (substr($str, $i, 5) == '<img ') {
			$start = $i;
			break;
		}
	if ($start>-1) {
	for ($i = $start; $i < strlen($str); $i++)
		if ($str[$i]=='>') {
			$end = $i;
			break;
		}
	}
	$result = null;$flg = false;
	if ($end>$start)
	for ($i = $start; $i < $end; $i++) {
		if ($str[$i]!='"' && $flg) {
			$result .= $str[$i];
		}
		if ($str[$i]=='"' && $flg) {
			break;
		}
		if ($str[$i]=='"' && !$flg) {
			$flg = true;
		}
	}

	if ($result) {
		if (!file_exists($cachePath . md5($result) . '.jpg')) {
			buildPicture($result);
		}
		if (!file_exists($cachePath . md5($result) . '.jpg')) {
			$result = NULL;
		}
	}
	return $result;
}

function buildPicture($s) {
	global $cachePath;
	$image = @imagecreatefromjpeg($s);
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
	imagejpeg($image_p , $cachePath . md5($s) . '.jpg' , 90);	
}

// function removes spaces
function clearPath ($str) {
	return str_replace(' ','_',$str);
}

function cutForFirst($str){
	$str = strip_tags($str, '<p><a><b><i><strong><em><s>');
	$ar = explode(". ",$str);
	$str = '';
	$i = 0;
	while (strlen($str)<900 && $i<count($ar))
	   $str .= $ar[$i++] . ". ";	
	return $str;
}

function cutForSecond($str, $length = 200){
	$str = strip_tags($str, '<p><a><b><i><strong><em><s>');
	$ar = explode(". ",$str);
	$str = '';
	$i = 0;
	while (strlen($str)<$length && $i<count($ar))
	   $str .= $ar[$i++] . ". ";	
	return $str;
}

function shorted($str, $length) {
	if (strlen($str)<=$length) return $str;
	$result = '';
	for ($i = 0; $i < $length-3; $i ++)
		$result .= $str[$i];
	return $result.'...';
}

function gravatar($email, $size = 50) {
	$usersEmail = $email;
	$defaultImage = "img/transparent.png";
	$avatarSize = $size;
	$avatarRating = "G";
	$avatarBorder = null;
	$gravatarURL = "http://www.gravatar.com/avatar.php?gravatar_id=%s
	&default=%s&size=%s&border=%s&rating=%s";

$avatarURL = sprintf
(
	$gravatarURL, 
	md5($usersEmail), 
	$defaultImage,
	$avatarSize,
	$avatarBorder,
	$avatarRating
);

return "<img class=\"gravatar\" src=\"" . 
	$avatarURL . "\" width=\"" . 
	$avatarSize . "\" height=\"" . 
	$avatarSize . "\" />";
}

?>