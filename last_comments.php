<?php
	include_once ('src/engine.php');
	$post = new Post();
	$result = $post->lastComments();
	
	while ($r = mysql_fetch_array($result)) {
	
	echo "<div class=\"last_cmt\">";
	echo "<a href=\"http://www.gravatar.com/\">" . gravatar($r['email'], 30) . "</a>";
	echo "<span class='name'>" . $r['author'] . ": </span> ";
	echo ( strlen($r['comment'])>100 ? cutComment($r['comment'], 80) : $r['comment']);
	echo "<span class='for'>На сообщение <a href='post.php?id=" . $r['post_id'] . "'>" . $r['postName'] . "</a></span>";	
	
	echo "</div>";
	}
	
	
	function cutComment($str, $length) {
		$result = '';
		$k = 0;
		while ($k<$length || $str[$k]!=' ') {
			$result .= $str[$k++];
		}
		return $result . '...';
	}
?>