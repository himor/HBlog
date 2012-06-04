<?php
	include_once ('header.php');
	include_once ('sidebar.php');

	$post = new Post();
	$num = $post->countPosts();
	
	// pagination
	$pages = floor($num / 9);
	if (isset($_GET['page']))
		$page = floor($_GET['page']);
	else $page = 0;	
	$result = $post->all(null, null, $page);
?>


<?php
	if ($num > 0) {
	$i = 0;	
	while ($r = mysql_fetch_array($result)) {
?>

	<div class="<?php echo (($i==0)?'first':'second');?>" id="div_<?php echo $i+1;?>">
    <h3><a href="post.php?id=<?php echo $r['id'];?>"><?php echo $r['caption']?></a></h3>
    <h4><?php echo date("j F Y, H:i",$r['time']);?> <a href="post.php?id=<?php echo $r['id'];?>#comments"><?php $cmt = $post->countComments($r['id']); echo $cmt;?> комментари<?php if ($cmt%10==1) echo "й"; elseif($cmt%10>1 && $cmt%10<5) echo "я"; else echo "ев";?></a></h4>
	<a href="post.php?id=<?php echo $r['id'];?>">
    <?php
	
	$img = extractImage($r['text']);
	
	if ($img) {
		echo "<img src=\"" . $cachePath . md5($img) . '.jpg' . "\" class=\"postimg\" />";
		$length = 200;
	} else {
		$length = 500;
	}	
	?>    
    </a>
	<div class="content">
    <p><?php echo (($i==0)?cutForFirst($r['text']):cutForSecond($r['text'], $length));?></p>
    <?php if ($r['tag']) :
	    echo "<p class=\"tagsList\"> ";
		$tags = explode(',', $r['tag']);
		$first = true;
		foreach($tags as $t) {
			if (!$first) echo ', ';
			echo "<a href=\"bytag.php?search=$t\">#".trim($t)."</a>";
			$first = false;
		}
		echo "</p>";
		endif;?>
    </div>
    <div class="clear-both"></div>
    
</div>
<?php
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
		echo "<li><a href=\"./?page=$i\" class=\"".(($i==$page)?"active":"")."\">".($i+1)."</a></li>";
	}
	
	
	echo "</ul></div>";
	}
?>	

<?php
	include_once ('footer.php');
?>

</div><!-- wrap -->
<script type="text/javascript">
	function align(a, b) {
		var h1 = $('#div_'+a).height();
		var h2 = $('#div_'+b).height();
		var m = ((h1>h2)?parseInt(h1):parseInt(h2));
		$('#div_'+a).height(m);
		$('#div_'+b).height(m);
	}
	
	$(document).ready(function() {
		align(2,3);
		align(4,5);
		align(6,7);
		align(8,9);
	});

</script>
</body>
</html>
