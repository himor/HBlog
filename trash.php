<?php
	include_once ('header.php');
	include_once ('sidebar.php');
	if (!$userdata || $userdata['role'] > 0) {
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=login.php">';
		exit;
	}
	
	$post = new Post();
	
	if (isset($_GET['undelete']) && $_GET['undelete'] && $_GET['id']) {
		$post->publish($_GET['id'],0);
	}
		
	$result = $post->listTrash();
?>

<div class="first" style="min-height:700px;">
<h3>Posts</h3>
<div class="content">
<h4><a href="posts.php">Показать все посты</a></h4>
    
<?php
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
		echo "<td>".(($r['page'])?'<b>PAGE</b>, ':'')." удален</td>";
		echo "<td>".$r['category'].' '.$r['tag']."</td>";
		echo "<td>"."<a href=\"edit.php?id=".$r['id']."\"><strong>E</strong></a> ";
		echo "<a href=\"trash.php?undelete=1&id=".$r['id']."\"><strong>U</strong></a> ";		
		echo "</td></tr>";
	}
	echo "</tbody></table>";
	echo "<div class=\"clear-both\"></div>";
	
	echo "</ul></div>";
?>	
</div>
<?php
	include_once ('footer.php');
?>
</div><!-- wrap -->
</body>
</html>