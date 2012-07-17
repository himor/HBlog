<?php
	include_once ('header.php');
	
	$notif = new Notif();
	echo $notif->getNewNotifNumber(3);
	$result = $notif -> getAllNotif(3);
	
	$picom = new Picom();
	echo $picom -> postComment('0', 'james', 'email','text');
	
?>