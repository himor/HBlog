<?php

function cutForSecond($str, $length = 200){
	$str = strip_tags($str);
	$ar = explode(". ",$str);
	$str = '';
	$i = 0;
	while (strlen($str)<$length && $i<count($ar))
	   $str .= $ar[$i++] . ". ";	
	return $str;
}

	header("Content-Type: application/rss+xml; charset=utf-8");
 
 	$rssfeed = '<?xml version="1.0" encoding="ISO-8859-1"?>';
    $rssfeed .= '<rss version="2.0">';
    $rssfeed .= '<channel>';
    $rssfeed .= '<title>Блог Миши Гордо</title>';
    $rssfeed .= '<link>http://www.himorblog.com/blog</link>';
    $rssfeed .= '<description>This is an example RSS feed</description>';
    $rssfeed .= '<language>en-us</language>';
	$rssfeed .= '<copyright>Copyright (C) 2012 himorblog.com</copyright>';
 
	$db 	= 'mikgor2_db';
	$host 	= 'mysql4.freehostia.com';
	$user	= 'mikgor2_db';
	$pasw	= 'Gates*7237';
	
	$link = mysql_connect($host, $user, $pasw); 
	mysql_select_db($db, $link);
	$result = mysql_query('SET NAMES utf8');         // problem is because of Wordpress database type :-(
	$result = mysql_query('SET CHARACTER SET utf8');
	$query = "SELECT * FROM `post` WHERE `public` = 1 ORDER BY `time` DESC LIMIT 10;";
	$result = mysql_query($query, $link);
	mysql_close($link);		

	while($r = mysql_fetch_array($result)) {
        extract($r);
 
        $rssfeed .= '<item>';
        $rssfeed .= '<title>' . $caption . '</title>';
        $rssfeed .= '<description>' . cutForSecond($text, 200) . '[...] </description>';
        $rssfeed .= '<link>' . 'http://himorblog.com/blog/post.php?id=' . $id . '</link>';
        $rssfeed .= '<pubDate>' . date("D, d M Y H:i:s O", strtotime($time)) . '</pubDate>';
        $rssfeed .= '</item>';
    }
 
    $rssfeed .= '</channel>';
    $rssfeed .= '</rss>';
 
    echo $rssfeed;
	
?>