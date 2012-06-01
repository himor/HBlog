<?php
	include_once ('header.php');
	include_once ('sidebar.php');
	if (!$userdata) {
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=login.php">';
		exit;
	}
	
	$sys = new System();
	$sys -> connect();
	
	// if UPDATE
	$name = null;
	$password = null;
	$new_password = null;
	$error = null;
	if (isset($_POST['name']))
		$name = $_POST['name'];
	if (isset($_POST['password']))
		$password = $_POST['password'];
	if (isset($_POST['new_password']))
		$new_password = $_POST['new_password'];
	if ($name && $password && $new_password) {
		if ($sys -> userByPassword($userdata['userId'], $password) > 0) {
			$sys -> update($userdata['userId'], $userdata['username'], $new_password, $name, $userdata['email']);
		} else {
			// wrong old password
			$error = 'Неправильный пароль!';
		}
	}
	
	$result = $sys -> profile($userdata['userId']);
	$r = mysql_fetch_array($result);	
?>

<div class="first" style="min-height:700px;">
<h3>My profile</h3>
    <div class="content">
    <p><strong>Зарегистрирован: </strong> <?php echo date("j F Y, H:i",$r['registered']);?> GMT.</p>
    <p><strong>Роль: </strong> <?php echo(($r['role']==0)?"Администратор":"Читатель");?></p>
    <form style="width:350px;" method="post" action="profile.php" onSubmit="javascript:encript();">
    
    
    <label>Имя пользователя:</label>
    <input maxlength="30" type="text" name="username" disabled="disabled" value="<?php echo $r['username'];?>" /><br />
    
    <label style="width:400px; text-align:center;">Реальное имя <small>(подпись под комментарием)</small>:</label><br />
    <label>&nbsp;</label>
    <input maxlength="30" type="text" name="name" value="<?php echo $r['name'];?>" /><br />    
    
    <label>Старый пароль:</label>
    <input maxlength="30" id="pass" type="password" /><br />
    <input id="pass2" name="password" type="hidden" />
     <?php
	if ($error) {
		echo "<span class=\"error\" style=\"margin-left:160px;\">$error</span><br/>";
	}
	?>
    
    <label>Новый пароль:</label>
    <input maxlength="30" id="newpass" type="password" /><br />
    <input id="newpass2" name="new_password" type="hidden" />
    
    <label>E-mail:</label>
    <input maxlength="30" type="text" name="email" value="<?php echo $r['email'];?>" /><br /><br />
    <div style="text-align:center;">
    <button type="submit">Сохранить</button>
    </div>    
    </form>
    
    <?php
	$post = new Post();
	$result = $post ->userComments($userdata['userId']);
	echo "<br/><p><strong>Мои комментарии</strong>:</p>";
	echo "<table id=\"comments\">";
	echo "<thead>";
	echo "<th>Пост</th>";
	echo "<th>Комментарий</th>";
	echo "<th>Дата</th>";	
	echo "</thead><tbody>";
	while ($r = mysql_fetch_array($result)) {
		echo "<tr>";
		echo "<td><a href=\"post.php?id=".$r['id']."\">".$r['caption']."</a></td>";		
		echo "<td><a href=\"post.php?id=".$r['id']."#comments\">".$r['comment']."</a></td>";
		echo "<td>".date("j F Y, H:i",$r['ctime'])."</td>";
		
	}
	echo "</tbody></table>";
	?>
    
    
    </div>
</div>

<script type="text/javascript">
function encript() {
	$("#pass2").val(MD5($("#pass").val()));
	if ($("#newpass").val())
		$("#newpass2").val(MD5($("#newpass").val()));
	$("#capcha2").val(MD5($("#capcha").val()));
}
</script>
<?php
	include_once ('footer.php');
?>

</div><!-- wrap -->
</body>
</html>