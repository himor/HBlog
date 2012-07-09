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
	$id = $picom->postComment($_POST['img'], $_POST['name'], $_POST['email'], $_POST['text'], $_POST['uid']);	
	if (isset($_SESSION['hblog']) && isset($userdata['role']) && $userdata['role'] == '0') {
		// do nothing
	} else
		$picom->informAdmin((int)$_POST['img'], $id);
	if (isset($_SESSION['hblog']) && isset($userdata['role']) && $userdata['role'] != '0') {
		$notif = new Notif();
		$notif->updateRegister((int)$_POST['img'], 1, $userdata['userId']);
	}
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
?>
<!--<span style="display:block;height:10px;"></span>
<div id="comCont">
<span>Имя:</span>
<input name="author" id="ccname" type="text" value="<?php if (isset($userdata)) echo $userdata['name'];?>"/><br />
<span>E-mail:</span>
<input name="email" id="ccemail" type="text" value="<?php if (isset($userdata)) echo $userdata['email'];?>" /><br />
<input name="user_id"  id="ccuid" type="hidden" value="<?php if (isset($userdata)) echo $userdata['userId']; ?>"/>
<textarea name="comment"  id="cccomment" style="width:275px;height:80px;"></textarea><br />
<button type="button" id="ccbutton" onclick="postComment();" style="margin-top:10px;float:right;">Сказать</button>
<div class="clear-both"></div>
</div>
-->
<script>
function postComment() {
	if ($('#ccbutton').attr('disabled')) return;
	var name = $('#ccname').val();
	var email = $('#ccemail').val();
	var uid = $('#ccuid').val();
	var text = $('#cccomment').val();
	if (!name || !email || !text) return;
	if (name == 'Имя' || email == 'E-mail' || text=='Пишите комментарий...') return;
	$.post ("commentContainer.php", {img:'<?php echo $img;?>',name:name, email:email, uid:uid, text:text}, function(data){
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
