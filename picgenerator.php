<?php

$number = 		82;
$pick = 		12;
$prefix = 		'img_';
$extension = 	'.jpg';
$path = 		'upload/sidebar/';

session_start('hblog');
include_once('src/engine.php');
$sys = new System();
if (isset($_SESSION['hblog'])) {
	$sys->connect();
	$userdata = $sys->userdata();
	$sys->close();
}
?>

<div id="mask">
</div>

<div id="field">
	<img src="img/closeImg.png" width="50" height="50" id="closeMask" />
    <table><tr><td>
    <a id="mainlink" href="#"><img src="" id="mainimg" /></a>
    </td>
    <td class="comment-block"><div id="comment-contain">
    <span style="display:block;height:10px;"></span>
    <div id="comCont">
    <input name="author" id="ccname" style="width:265px;"
    onfocus="if (this.value=='Имя') this.value = ''" 
    onblur="if (this.value=='') this.value = 'Имя'"
    type="text" value="<?php if (isset($userdata)) echo $userdata['name']; else echo "Имя"?>"/><br />
    <input name="email" id="ccemail" type="text" style="width:265px;"
    onfocus="if (this.value=='E-mail') this.value = ''" 
    onblur="if (this.value=='') this.value = 'E-mail'"
    value="<?php if (isset($userdata)) echo $userdata['email']; else echo "E-mail"?>" /><br />
    <input name="user_id"  id="ccuid" type="hidden" value="<?php if (isset($userdata)) echo $userdata['userId']; ?>"/>
    <textarea name="comment" id="cccomment" style="width:265px;height:60px;"
    onfocus="if (this.value=='Пишите комментарий...') this.value = ''" 
    onblur="if (this.value=='') this.value = 'Пишите комментарий...'" type="text">Пишите комментарий...</textarea><br />
    <button type="button" id="ccbutton" onclick="postComment();" style="margin-top:10px;float:right;">Сказать</button>
    <div class="clear-both"></div>
    </div>
    <div id="comment-container"></div>
    </div></td>
    </tr></table>    
</div>

<script type="text/javascript">
	var images = new Array();
	function displayGallery(id) {
		var next = 0;
	<?php 
		for($i=0; $i<$number; $i++) {
			echo "images[$i] = \"" . $path . $prefix . $i . $extension . "\";";
		}
	?>
		$("#mainimg").attr('src',images[id]);
		if (id == <?php echo ($number-1);?>)
			next = 0;
			else next = id + 1;
		$("#mainlink").attr('href', 'javascript:nextImage('+next+');');	
		$('#mask').fadeIn(300);
		$('#field').fadeIn(300);
		$("#comment-container").empty();
		$.post("commentContainer.php?img="+id+'gallery', {}, function(data){
			$("#comment-container").html(data);		
			});
}

	function display(src) {
		src = src.replace('http://www.himorblog.com','');
		src = src.replace('www.himorblog.com','');
		src = src.replace('http://himorblog.com','');
		$("#mainimg").attr('src',src);
		$("#mainlink").attr('href', 'javascript:closeMask();');	
		$('#mask').fadeIn(300);
		$('#field').fadeIn(300);
		$("#comment-container").empty();
		var src = src.replace(' ', '_');
		$.post("commentContainer.php?str=1&img="+src, {}, function(data){
			$("#comment-container").html(data);		
			});
}


function nextImage(id) {
	var next;
	$("#mainimg").attr('src',images[id]);
	$("#mainimg").css('maxWidth',$(window).width()-500);
	if (parseInt(id) == <?php echo ($number-1);?>)
		next = 0;
	else next = parseInt(id) + 1;
	$("#mainlink").attr('href', 'javascript:nextImage(' + next + ');');
	$("#comment-container").empty();
	$.post("commentContainer.php?img="+id+'gallery', {}, function(data){
		$("#comment-container").html(data);		
		});
}

$(document).ready(function(){
	$('#closeMask').click(function(){
		closeMask();
		});
	});

function closeMask() {
	$("#field").fadeOut(100);
	$("#mask").fadeOut(100)
}
$(document).keyup(function(e) {
  if (e.keyCode == 27) { 
  		$("#field").fadeOut(100);
		$("#mask").fadeOut(100);
   }   // esc
});

</script>









<?
/* THIS PART RETURNS SET OF IMAGES */
$picNames = array();
$picArray = array();
for ($i = 0; $i<$number; $i++)
	$picNames[] = $path . $prefix . $i;
$array = array_rand($picNames, $pick);
foreach($array as $a)
	$picArray[] = $picNames[$a];
shuffle($picArray);
$html = '';$next = null;
foreach ($picArray as $p) {
	if (!file_exists($p . '_thumb' . $extension)) {
		buildThumb($p);
	}
	$id = substr($p, strlen($path . $prefix));
	$html .= '<a href="javascript:displayGallery(' . $id . ');">';
	$html .= '<img src="' . $p . '_thumb' . $extension . '" />';
	$html .= '</a>';
	$next = $p . $extension;
}
echo $html;
exit;

function buildThumb($s) {
	global $extension;
	//header('Content-type: image/jpeg');
	$image = imagecreatefromjpeg($s.$extension);
	list($width,$height) = getimagesize($s.$extension);
	if ($width > $height) {
		$szy = $height;
		$szx = $szy;
		if ($szx>$width) {
			$szx = $width;
			$szy = $szx;
		}
		} else {
			$szx = $width; 
			$szy = $szx; 
			}
	$new_size_x = 62;
	$new_size_y = 62;
	$image_p = imagecreatetruecolor($new_size_x,$new_size_y);
	imagecopyresampled($image_p,$image,0,0,0,0,$new_size_x,$new_size_y,$szx,$szy);
	imagejpeg($image_p,$s.'_thumb'.$extension,100);
}

?>