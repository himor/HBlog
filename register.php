<?php
	include_once ('header.php');
	include_once ('sidebar.php');
	
	if (isset($_POST['username']))
		$username = $_POST['username'];
		else $username = null;
	if (isset($_POST['password']))
		$password = $_POST['password'];
		else $password = null;
	if (isset($_POST['name']))
		$name = $_POST['name'];
		else $name = null;		
	if (isset($_POST['email']))
		$email = $_POST['email'];
		else $email = null;		
	
	if (isset($_POST['capcha'])) {
		$capcha = $_POST['capcha'];
		$capcha_must = $_POST['capcha_must'];		
	}
		else {
			$capcha = null;
			$capcha_must = 123345;
		}
	$success = false;
	
	if ($username && $password && ($capcha == $capcha_must) && $name && $email) {
		$sys = new System();
		$sys -> connect();
		$success = $sys -> register($username, $password, $name, $email);
		$sys -> close();		
	}
	
	if ($success) {
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=login.php">';
		exit;		
	}
?>

<div class="first" style="min-height:700px;">
<h3>Registration</h3>
<br/><br/><br/><br/>
    <div class="content">
    <form style="width:350px;margin:auto;" method="post" action="register.php" onSubmit="javascript:encript();">
    
    <?php
	if (($username && $password && $name && $email && ($capcha == $capcha_must)) && !$success) {
		echo "<span class=\"error\">Пользователь с этим адресом уже существует</span><br/>";
	}
	if (($username && $password && $name && $email) && ($capcha != $capcha_must)) {
		echo "<span class=\"error\">Неправильный результат сложения</span><br />";
	}
	?>
    
    <p><em>Пожалуйста заполните все поля для регистрации:</em><br />
	Запомните ваш пароль и имя пользователя.</p>
    <label>Имя пользователя:</label>
    <input maxlength="30" type="text" name="username" /><br />
    
    <label style="width:400px; text-align:center;">Реальное имя <small>(подпись под комментарием)</small>:</label><br />
    <label>&nbsp;</label>
    <input maxlength="30" type="text" name="name" /><br />    
    
    <label>Пароль:</label>
    <input maxlength="30" id="pass" type="password" /><br />
    <input id="pass2" name="password" type="hidden" />
    
    <label>E-mail:</label>
    <input maxlength="30" type="text" name="email" /><br /><br />

    <label><a href="http://goo.gl/sTkAI">Тест Тьюринга</a>:</label><br/>
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
    <button type="submit">Регистрация</button>
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