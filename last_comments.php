<?php
	include_once ('src/engine.php');
	$post = new Post();
	$result = $post->lastComments();
	
	while ($r = mysql_fetch_array($result)) {
	
	echo "<div class=\"last_cmt\">";
	echo "<span class='name'>" . $r['author'] . ": </span> ";
	echo ( strlen($r['comment'])>100 ? substr($r['comment'],0,100).'...' : $r['comment']);
	echo "<span class='for'>На сообщение <a href='post.php?id=" . $r['post_id'] . "'>" . $r['postName'] . "</a></span>";	
	
	echo "</div>";
	}
?>