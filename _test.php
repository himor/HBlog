<?php
	include_once ('header.php');
	$post = new Post();
	$post->processTags('Тараканы, Здесь и там');
	$post->processTags('Тараканы, Где их только нет');
	$post->processTags('Бостон');
	$post->processTags('Студенческая жизнь, Бостон, Лето, Солнце');
	$post->processTags('Студенческая жизнь, Бостон, Лето, Солнце');
	$post->processTags('Солнце и Море');
	$set = $post->tagCloud();
	$result = '';
	foreach($set as $s) {
		if ($s['counter']>20) $result .= '<a class="tag_5" href="bytag.php?search='.$s['tag'].'">'.$s['tag'].'</a> ';
		elseif ($s['counter']>15) $result .= '<a class="tag_4" href="bytag.php?search='.$s['tag'].'">'.$s['tag'].'</a> ';
		elseif ($s['counter']>10) $result .= '<a class="tag_3" href="bytag.php?search='.$s['tag'].'">'.$s['tag'].'</a> ';
		elseif ($s['counter']>5) $result .= '<a class="tag_2" href="bytag.php?search='.$s['tag'].'">'.$s['tag'].'</a> ';
		elseif ($s['counter']>0) $result .= '<a class="tag_1" href="bytag.php?search='.$s['tag'].'">'.$s['tag'].'</a> ';
		}
	echo $result;
	
?>