<?php
	include_once ('header.php');
	include_once ('sidebar.php');
	
	$image = null;
	$next = null;
	
	if (isset($_GET['image']))
		$image = $_GET['image'];
	if (isset($_GET['next']))
		$next = $_GET['next'];
	
	if (!$image) {
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=index.php">';
		exit;		
	}
?>

<div class="first" style="min-height:700px;">

    <div class="content" style="padding:20px !important;margin:0px !important;">
    <?php
	if ($next)
		echo "<a href='display.php?image=$next'>";
		else echo "<a href='index.php'>";
	echo "<img style='width:100%;' src='$image' />";
	echo "</a>";
	
	?>
    
    </div>

</div>

<script type="text/javascript">
function encript() {
	$("#pass2").val(MD5($("#pass").val()));
	$("#capcha2").val(MD5($("#capcha").val()));
}
</script>
<?php
	include_once ('footer.php');
?>

</div><!-- wrap -->
</body>
</html>