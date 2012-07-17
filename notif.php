<?php
	include_once ('header.php');
	include_once ('sidebar.php');
	$notif = new Notif();
	$result = $notif->getAllNotif($userdata['userId']);
	$notif->markAllRead($userdata['userId']);
?>
<style>
.bold {
	font-weight:bold;
}
</style>
<div class="first" style="min-height:700px;">
<h3>Новости</h3>
<br/>
    <div class="content notif">

<?php
	echo "<table id=\"comments\" cellpadding=\"8\">";
	echo "<tbody>";
	foreach($result as $r) {
		echo "<tr>";
		echo "<td" . ($r['read'] == 0 ? ' class="bold"' : '') . ">";
		echo "<span class=\"date\">".date("j/m/y, H:i",$r['time'])."</span>";
		if ($r['type']==1) {
			echo "Новый комментарий от пользователя \"".$r['author']."\" к <a href=\"javascript:displayGallery(".$r['targetId'].");\">фотографии</a>";
		} elseif ($r['type']==2) {
			echo "Новый комментарий от пользователя \"".$r['author']."\" к посту <a href=\"post.php?id=".$r['targetId']."\">".$r['target']."</a>";
		} elseif ($r['type']==3) {
			echo "Новый комментарий от пользователя \"".$r['author']."\" к <a href=\"javascript:display('".$r['targetId']."');\">фотографии</a> к посту";
		}
		echo "</td>";
		echo "</tr>";
	}
	echo "</tbody></table>";
?>

    </div>

</div>

<?php
	include_once ('footer.php');
?>

</div><!-- wrap -->
</body>
</html>