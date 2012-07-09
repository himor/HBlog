<?php
	session_start('hblog');
	include_once ('src/engine.php');
	$sys = new System();
	if (isset($_SESSION['hblog'])) {
		$sys->connect();
		$userdata = $sys->userdata();
		$sys->close();
	}
	// find out which page requested header;
	$self = explode('/',$_SERVER['PHP_SELF']);
	$self = $self[count($self)-1];
	if ($self == 'post.php') {
		$self_id = $_GET['id'];
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<link rel="stylesheet" type="text/css" href="src/css/base.css" />
<link rel="shortcut icon" href="img/logo.ico"></link>
<link rel="alternate" href="feed/" title="himorblog" type="application/rss+xml" />

<!--[if IE]>
	<link rel="stylesheet" type="text/css" href="src/css/base_ie.css" />
<![endif]-->

<script src="src/encript.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js" type="text/javascript"></script>
<script>
	$(document).ready(function(){
		$('#searchText').bind("keypress", function(e) {
                if (e.keyCode == 13) {
					window.location="search.php?search="+$(this).val();
                    return false;
                }
            });
	});
</script>
<title>Blog | himorblog.com</title>
</head>
<body>

<div id="wrap">

<div id="login-bar">
    <span>
    <?php if (isset($userdata)) : ?>
    <a href="logoff.php">LOG OUT</a>
    <?php else : ?>
    <a href="register.php">REGISTER</a>
    <?php endif; ?>
    </span>
    <span>&#8226;</span>
    <span>
    <?php if (isset($userdata)) : ?>
    <a href="profile.php"><?php echo $userdata['name'];?></a>
    <?php else : ?>
    <a href="login.php">LOGIN</a>
    <?php endif; ?>
    </span>
    <?php if (isset($userdata) && $userdata['role'] == '0' ) : ?>
    <span>&#8226;</span>
    <span><a href="posts.php">POSTS</a></span>
    
    <span>&#8226;</span>
    <span><a href="comments.php">COMMENTS</a></span>
    
    <span>&#8226;</span>
    <span><a href="write.php">WRITE</a></span>
    <?php endif; ?>
    <span><a href="feed/"><img src="img/rss.png" width="12" height="12" style="position:relative;top:3px;" alt="rss" /></a></span>
    
    <div class="clear-both"></div>
</div><!-- login bar -->

<div id="nav">
	<a href="http://www.himorblog.com"><img src="img/logo.png" border='0' alt="Logo" /></a>
    <div id="insert">
<table cellpadding="0" cellspacing="0"><tr><td><div class="search">Search: <input type="text" id="searchText" /></div></td></tr>
<tr><td style="vertical-align:bottom;"><ul class="menu"><li><a href="index.php" class="<?php echo(($self=='index.php')?'active':'');?>">Главная</a></li><li><a href="/robot/?language=ru">Работы</a></li><?php
		$post = new Post();
		$result = $post->listPages();
		while ($r = mysql_fetch_array($result)) {
			echo "<li>";
			echo "<a href=\"post.php?id=".$r['id']."\" " . (isset($self_id) ? ($self_id==$r['id']?"class='active'":"") : "") . ">".$r['caption']."</a>";
			echo "</li>";
		}
	?></ul></td></tr></table>
    </div>
<div class="clear-both"></div>
</div><!-- nav -->