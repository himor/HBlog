<?php
session_start('hblog');
include_once('src/engine.php');
$sys = new System();
if (isset($_SESSION['hblog'])) {
	$sys->connect();
	$userdata = $sys->userdata();
	$sys->close();
}

if (isset($_POST['name'])) { // saving the comment
	$picom = new Picom();
	$isStr = $_POST['isStr'];
	$id = $picom->postComment($_POST['img'], $_POST['name'], $_POST['email'], $_POST['text'], $_POST['uid']);	
	if (isset($_SESSION['hblog']) && isset($userdata['role']) && $userdata['role'] == '0') {
		// do nothing if current user is actually admin
	} else {
		if ($isStr)
			$picom->informAdminImgToPost($_POST['img'], $id);
		else
		$picom->informAdmin((int)$_POST['img'], $id);
	}
	$notif = new Notif();
	if (isset($_SESSION['hblog']) && isset($userdata['role']) && $userdata['role'] != '0') {
		$notif->updateRegister($isStr ? $_POST['img'] : (int)$_POST['img'], $isStr ? 3 : 1, $userdata['userId']);
	}
	$notif->informUsers($isStr ? 3 : 1, $isStr ? $_POST['img'] : (int)$_POST['img'], $id, (isset($_SESSION['hblog']) && isset($userdata['userId']) ? $userdata['userId'] : null));
	$return = array();
	$return['id'] = $id;
	$return['html'] = "<a href=\"http://www.gravatar.com/\">" . gravatar($_POST['email'], 30) . "</a>" .
		"<span class='name'>" . $_POST['name'] . "</span> <br>" .
		nl2br($_POST['text']);
	echo json_encode($return);
}

if (isset($_GET['img']))
	$img = $_GET['img'];
	else return;
	
if (isset($_GET['str']))
	$isStr = 1;
	else $isStr = 0;
	
?>

<script>
function postComment() {
	if ($('#ccbutton').attr('disabled')) return;
	var name = $('#ccname').val();
	var email = $('#ccemail').val();
	var uid = $('#ccuid').val();
	var text = $('#cccomment').val();
	if (!name || !email || !text) return;
	if (name == 'Имя' || email == 'E-mail' || text=='Пишите комментарий...') return;
	$.post ("commentContainer.php", {img:'<?php echo $img;?>',name:name, email:email, uid:uid, text:text, isStr:<?php echo $isStr;?>}, function(data){
		var thediv = '<div id="dv'+data.id+'" class="comContOne"></div>';
		$('#holder').before(thediv);
		$('#dv'+data.id).html(data.html);
		$('#ccbutton').removeAttr('disabled');
		$('#cccomment').val('');
	}, "json");
	$('#ccbutton').attr('disabled','disabled');	
}
</script>

<?php

$picom = new Picom();
$res = $picom->getComments($img);

while ($r = mysql_fetch_array($res)) {
	echo "<div id=\"dv" . $r['id'] . "\" class=\"comContOne\">";
	echo "<a href=\"http://www.gravatar.com/\">" . gravatar($r['email'], 30) . "</a>" .
		"<span class='name'>" . $r['author'] . " <span class=\"date\">".date("j F Y, H:i",$r['time'])." GMT</span> </span> <br>" .
		nl2br($r['comment']);	
	echo "</div>";
}
?>

<div id="holder"></div>
