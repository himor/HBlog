<?php
	include_once ('header.php');
	include_once ('sidebar.php');
	
	if (!$userdata || $userdata['role'] > 0) {
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=login.php">';
		exit;
	}
	
	if (isset($_GET['id'])) {
		$id = $_GET['id'];
	} else {
		echo '<META HTTP-EQUIV="Refresh" Content="0; URL=posts.php">';
		exit;
	}
	
	$post = new Post();
	$result = $post->one($id);
	$r = mysql_fetch_array($result);
		
	$caption = $r['caption'];
	$page = $r['page'];
	$publish = ($r['public']==1?1:0);
	$text = $r['text'];
	$tag = $r['tag'];
	$status = $r['public'];
	if (isset($_POST['action'])) {
		// saving the post
		$caption = $_POST['caption'];
		$text = $_POST['text'];
		$tag = $_POST['tag'];
		if (isset($_POST['page'])) $page = 1; else $page = 0;
		if (isset($_POST['publish'])) $publish = 1;
		$status = $_POST['status'];
		if (!$caption) $caption = "Без заголовка";
		if ($text) {
			$data = array(
				'caption' => $caption,
				'text' => $text,
				'category' => null,
				'tag' => $tag,
				'page' => $page,
				'public' => $status,
				);
			$itog = $post -> update($id, $data);
			//$post -> dump();
			//exit;
			if (!$itog) {
				echo "<span class=\'error\'>INTERNAL ERROR: POST WASN'T UPDATED.</span>";
			} else {
				echo '<META HTTP-EQUIV="Refresh" Content="0; URL=posts.php">';
				exit;	
			}
			}
		}
?>

<div class="first" style="min-height:700px;">
<h3>Update the post</h3>

    <div class="content">
    <p><strong>Пост написан: </strong> <?php echo date("j F Y, H:i",$r['time']);?> GMT.</p>
    <form method="post" action="edit.php?id=<?php echo $id;?>">
    <input type="hidden" name="action" value="1" />
    <label class="w100">Заголовок </label>
    <input type="text" style="width:380px;" name="caption" value="<?php echo $caption;?>" />
	<label class="w100">Страница</label> <input type="checkbox" name="page" <?php echo $page ? 'checked=\'checked\'':''; ?> /><br />
    <label class="w100" style="float:left;">Пост </label>
    <textarea name="text" style="margin:5px; width: 520px; height: 450px;"><?php echo $text;?></textarea><br/>
    <div class="clear-both"></div>
    
   <?php
    //<label class="w100">Категория</label>
    //<select>
    //</select>
	?>
    <label class="w100">Теги </label>
    <input type="text" style="width:518px;" name="tag" value="<?php echo $tag;?>" />

    <label class="w100">Статус</label>
    <select name="status" id="status" style="margin-left:0px; margin-top:5px; height:22px; width:200px;">
    <option value="0" <?php echo $status==0 ? 'selected=\'selected\'':'';?>>Скрыт</option>
    <option value="1" <?php echo $status==1 ? 'selected=\'selected\'':'';?>>Опубликован</option>    
    <option value="-1" <?php echo $status==-1 ? 'selected=\'selected\'':'';?>>Удален</option>        
    </select>
        
    <div style="text-align:right;">
    <br />
	<label class="w100">Опубликовать</label> <input type="checkbox" id="publish" name="publish" <?php echo $publish ? 'checked=\'checked\'':''; ?> style="margin-right:20px;" />
    <button id="saveBt">Сохранить</button>
    </div>
    </form>
    
    </div>
    
</div>

<script type="text/javascript">
function encript() {
	$("#pass2").val(MD5($("#pass").val()));
	$("#capcha2").val(MD5($("#capcha").val()));
}

$(document).ready(function(){
	$("#publish").change(function() {
		if ($('#publish').is(':checked'))
			$('#status').val(1);
			else $('#status').val(0);
		});
	});
    
</script>
<?php
	include_once ('footer.php');
?>

</div><!-- wrap -->
</body>
</html>