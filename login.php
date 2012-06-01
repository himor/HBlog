<?php
	include_once ('header.php');
	include_once ('sidebar.php');
	
	if (isset($_POST['username']))
		$username = $_POST['username'];
		else $username = null;
	if (isset($_POST['password']))
		$password = $_POST['password'];
		else $password = null;
	
	if (isset($_POST['capcha'])) {
		$capcha = $_POST['capcha'];
		$capcha_must = $_POST['capcha_must'];		
	}
		else {
			$capcha = null;
			$capcha_must = 123345;
		}
	$login = false;
	
	if ($username && $password && ($capcha == $capcha_must)) {
		$sys = new System();
		$sys -> connect();
		$login = $sys -> login($username, $password);		
		$sys -> close();		
	}
	
	if ($username && $password && $login && ($capcha == $capcha_must)) {
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=index.php">';
		exit;		
	}
?>

<div class="first" style="min-height:700px;">
<h3>Login</h3>
<br/><br/><br/><br/>
    <div class="content">
    <form style="width:350px;margin:auto;" method="post" action="login.php" onSubmit="javascript:encript();">
    
    <?php
	if (($username || $password) && !$login) {
		echo "<span class=\"error\">Неправильное имя пользователя или пароль</span><br/>";
	}
	if (($username || $password) && ($capcha != $capcha_must)) {
		echo "<span class=\"error\">Неправильный результат сложения</span><br />";
	}
	?>
    
    <p><em>Пожалуйста введите имя пользователя и пароль:</em></p>
    <label>Имя пользователя:</label>
    <input type="text" name="username" /><br />
    
    <label>Пароль:</label>
    <input id="pass" type="password" /><br /><br />
    <input id="pass2" name="password" type="hidden" />
    
    <label>Тест Тьюринга:</label><br/>
    <label><?php
    	$a = rand(1,20);
    	$b = rand(1,20);
		$c = $a + $b;
		$capcha = md5("$c");
		echo "$a + $b = ?";
	?>
    </label>
    <input id="capcha" type="text" /><br /><br /><br />
    <input id="capcha2" name="capcha" type="hidden" />
    <input name="capcha_must" type="hidden" value="<?php echo $capcha; ?>"/>
    
    <div style="text-align:center;">
    <button type="submit">Войти</button>
    </div>
    </form>
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