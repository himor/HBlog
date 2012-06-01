<?php
	include_once("src/engine.php");
	$sys = new System();
	$sys->logout();
	header("Location: index.php");
?>