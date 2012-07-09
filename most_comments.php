<?php
	include_once ('src/engine.php');
	$post = new Post();
	$result = $post->mostComments();
	
	while ($r = mysql_fetch_array($result)) {
	
		echo "<div class=\"last_cmt\">";
		echo "<span class='post_name'><a href='post.php?id=" . $r['post_id'] . "'>" . $r['postName'] . "</a></span><br />";
		echo "<span class='post_content'>" . cutComment(strip_tags($r['postText']), 150) . "</span><br />";
		?>
		<span class="post_cmt"><a href="post.php?id=<?php echo $r['post_id'];?>#comments"><?php $cmt = $post->countComments($r['post_id']); echo $cmt;?> комментари<?php if ($cmt%10==1) echo "й"; elseif($cmt%10>1 && $cmt%10<5) echo "я"; else echo "ев";?></a></span>
        <?
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