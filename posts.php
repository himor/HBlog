<?php
	include_once ('header.php');
	include_once ('sidebar.php');
	if (!$userdata || $userdata['role'] > 0) {
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=login.php">';
		exit;
	}
	
	$post = new Post();
	
	if (isset($_GET['delete']) && $_GET['delete'] && $_GET['id']) {
		$post->delete($_GET['id']);
	}
	if (isset($_GET['status']) && $_GET['status'] && $_GET['id']) {
		$post->publish($_GET['id'], $_GET['new']);
	}
	
	$num = $post->countPosts();
	
	// pagination
	$pages = floor($num / 20);
	if (isset($_GET['page']))
		$page = floor($_GET['page']);
	else $page = 0;	
	$result = $post->listing($page);
?>

<div class="first" style="min-height:700px;">
<h3>Posts</h3>
<div class="content">
<h4>Всего: <?php echo $num;?> постов. <a href="trash.php">Показать удалённые посты</a></h4>
    
<?php
	if ($num > 0) {
	$i = 0;	
	
	echo "<table id=\"comments\">";
	echo "<thead>";
	echo "<th style=\"width:200px;\">Пост</th>";
	echo "<th style=\"width:120px;\">Дата</th>";
	echo "<th style=\"width:120px;\">Статус</th>";
	echo "<th>Категория, теги</th>";
	echo "<th>Опции</th>";
	echo "</thead><tbody>";
	while ($r = mysql_fetch_array($result)) {
		echo "<tr>";
		echo "<td><a href=\"post.php?id=".$r['id']."\">".$r['caption']."</a></td>";
		echo "<td>".date("j/m/y, H:i",$r['time'])."</td>";
		echo "<td>".(($r['page'])?'<b>PAGE</b>, ':'').(($r['public']==1)?"опубликован":"<b>скрыт</b>")."</td>";
		echo "<td>".$r['category'].' '.$r['tag']."</td>";
		echo "<td>"."<a href=\"edit.php?id=".$r['id']."\"><strong>E</strong></a> ";
		echo "<a href=\"posts.php?status=1&new=".(($r['public'])?0:1)."&id=".$r['id']."\"><strong>S</strong></a> ";
		echo "<a href=\"posts.php?delete=1&id=".$r['id']."\"><strong>D</strong></a> ";		
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
		echo "<li><a href=\"posts.php?page=$i\" class=\"".(($i==$page)?"active":"")."\">".($i+1)."</a></li>";
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