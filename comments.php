<?php
	include_once ('header.php');
	include_once ('sidebar.php');
	if (!$userdata || $userdata['role'] > 0) {
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=login.php">';
		exit;
	}
	
	$post = new Post();
	
	if (isset($_GET['delete']) && $_GET['delete'] && $_GET['id']) {
		$post->deleteComment($_GET['id']);
	}
	
	$num = $post->countAllComments();
	
	// pagination
	$pages = floor($num / 10);
	if (isset($_GET['page']))
		$page = floor($_GET['page']);
	else $page = 0;	
	$result = $post->listComments($page);
?>

<div class="first" style="min-height:700px;">
<h3>Comments</h3>
<div class="content">
<h4>Всего: <?php echo $num;?> комментариев.</h4>

<?php
	if ($num > 0) {
	$i = 0;	
	
	echo "<table id=\"comments\" cellpadding=\"10px;\">";
	echo "<tbody>";
	while ($r = mysql_fetch_array($result)) {
		echo "<tr><td>";
		echo "<span style=\"font-size:1.2em;font-weight:bold;\">Пост: <a href=\"post.php?id=".$r['id']."\">".$r['caption']."</a></span><br />";
		echo "Дата комментария: ".date("j/m/y, H:i",$r['ctime'])."<br />";
		echo "Автор: <strong>".$r['author']."</strong> | " .$r['email']." | [user_id: ".$r['user_id']."]<br />";
		echo "<em>".$r['comment']."</em><br />";
		echo "Опции: ";
		echo "<a href=\"comments.php?delete=1&id=".$r['cid']."\"><strong>Удалить</strong></a> ";		
		echo "</td></tr>";
	}
	echo "</tbody></table>";
	
	$i++; 
	}
	echo "<div class=\"clear-both\"></div>";
	// pagination
	echo "<div style=\"text-align:center;width:700px;\">";
	echo "<ul id=\"nav-bar\">";
	$p_start = $page - 4;
	$p_end =   $page + 4;
	if ($p_end > $pages) {
		$p_start -= ($p_end - $pages);
		$p_end = $pages;
	} 
	if ($p_start < 0) {
		$p_end += (-$p_start);
		$p_start = 0;
	}
	if ($p_end > $pages) {
		$p_end = $pages;
	}
	for($i = $p_start; $i <= $p_end; $i ++) {
		echo "<li><a href=\"comments.php?page=$i\" class=\"".(($i==$page)?"active":"")."\">".($i+1)."</a></li>";
	}
	echo "</ul></div>";
?>	
</div></div>
<?php
	include_once ('footer.php');
?>
</div><!-- wrap -->
</body>
</html>