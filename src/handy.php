<?php

function extractImage($str) {
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
	return $result;
}

function cutForFirst($str){
	$str = strip_tags($str);
	$ar = explode(". ",$str);
	$str = '';
	$i = 0;
	while (strlen($str)<900 && $i<count($ar))
	   $str .= $ar[$i++] . ". ";	
	return $str;
}

function cutForSecond($str, $length = 200){
	$str = strip_tags($str);
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

?>