<?php
	include_once ('header.php');
	include_once ('sidebar.php');

	$post = new Post();
	if (isset($_GET['id']))
		$id = $_GET['id'];
		else {
			echo '<META HTTP-EQUIV="Refresh" Content="0; URL=index.php">';
			exit;	
		}
	$result = $post->one($id);
	
	if (isset($_POST['author']))
		$author = $_POST['author'];
		else $author = null;
	if (isset($_POST['email']))
		$email = $_POST['email'];
		else $email = null;
	if (isset($_POST['user_id']))
		$user_id = $_POST['user_id'];
		else $user_id = 0;
	if (isset($_POST['comment']))
		$comment = addslashes(trim(strip_tags($_POST['comment'])));
		else $comment = '';
	
	if (isset($_POST['capcha'])) {
		$capcha = $_POST['capcha'];
		$capcha_must = $_POST['capcha_must'];		
	}
		else {
			$capcha = 123;
			$capcha_must = null;
		}
	$login = false;
	
	if ($author && $email && $comment && ($capcha == $capcha_must)) {
		$comment_id = $post->addComment($id,$author,$email,$comment,$user_id);
		if (isset($_SESSION['hblog']) && isset($userdata['role']) && $userdata['role'] == '0') {
			// do nothing if current user is actually admin
		} else
			$post->informAdmin($id, $comment_id);
		$notif = new Notif();
		if (isset($_SESSION['hblog']) && isset($userdata['role']) && $userdata['role'] != '0') {
			$notif->updateRegister($id, 2, $userdata['userId']);
		}
		$notif->informUsers(2, $id, $comment_id, (isset($_SESSION['hblog']) && isset($userdata['userId']) ? $userdata['userId'] : null));
		$capcha_must = null;$comment = '';
	}
	
?>


<?php
	$r = mysql_fetch_array($result);
?>
	<div class="first" style="min-height:700px;">
    <h2><?php echo $r['caption']?></h2>
    <h4><span class="dateblock"><?php echo date("j F Y, H:i",$r['time']);?></span><a href="#comments"><?php $cmt = $post->countComments($r['id']); echo $cmt;?> комментари<?php if ($cmt%10==1) echo "й"; elseif($cmt%10>1 && $cmt%10<5) echo "я"; else echo "ев";?></a></h4>
    <h4 class="tagblock">
    <?php if ($r['tag']) :
		$tags = explode(',', $r['tag']);
		$first = true;
		foreach($tags as $t) {
			echo "<a href=\"bytag.php?search=$t\">#".trim($t)."</a>";
			$first = false;
		}
		endif;?>
    </h4>
    <p></p>
	
    
    <div class="content_big">
    <?php echo nl2br($r['text']);?>
    </div>
    
    
    <a name="comments"></a>
    <div id="comments">
    <div id="c-top"><span>Комментарии</span></div>
        <div id="c-mid">
        <?php
        	$com = $post->allComments($id);
			while($r = mysql_fetch_array($com)){
				echo "<div>";
				echo "<a href=\"http://www.gravatar.com/\">" . gravatar($r['email']) . "</a>";
				echo "<span>".$r['author']."<span class=\"date\">".date("j F Y, H:i",$r['time'])." GMT</span></span><br>";
				echo "<p>".nl2br($r['comment'])."</p>";
				echo "</div>";
			}
		?>
        </div>
       
    </div>  
    
     <form style="width:600px;margin:auto;" method="post" action="post.php?id=<?php echo $id;?>#comments" onSubmit="javascript:encript();">
        <?php
		if($capcha_must && (!$author || !$comment || !$email || ($capcha != $capcha_must))) {
			echo "<span class=\"error\">Ошибка: проверьте правильность введенных данных!</span><br/><br/>";			
		}		
		?>
        <label>Имя:</label>
        <input name="author" type="text" value="<?php if (isset($userdata)) echo $userdata['name'];?>"/><br />
		<label>E-mail:</label>
        <input name="email" type="text" value="<?php if (isset($userdata)) echo $userdata['email'];?>" /><br />
        <input name="user_id" type="hidden" value="<?php if (isset($userdata)) echo $userdata['userId']; ?>"/>
        <label>Комментарий:</label><br />
		<textarea name="comment" style="margin-left:160px;width:400px;height:80px;"><?php echo $comment; ?></textarea><br />

        <label>Тест Тьюринга:</label><br/>
        <label><?php
            $a = rand(1,20);
            $b = rand(1,20);
            $c = $a + $b;
            $capcha = md5("$c");
            echo "$a + $b = ?";
        ?>
        </label>
        <input id="capcha" type="text" /><br />
        <input id="capcha2" name="capcha" type="hidden" />
        <input name="capcha_must" type="hidden" value="<?php echo $capcha; ?>"/>
        
        <div style="text-align:center;padding:10px;">
        <button type="submit">Отправить</button>
        </div>
        </form>
      
    <div class="clear-both"></div>
    <script type="text/javascript">
	function encript() {
		$("#capcha2").val(MD5($("#capcha").val()));
	}
	</script>
</div>
<?php
	echo "<div class=\"clear-both\"></div>";
	
?>	

<?php
	include_once ('footer.php');
?>

</div><!-- wrap -->
<script type="text/javascript">
	$(document).ready(function() {
		$('.content_big img').each(function() {
			$(this).wrapAll("<a href=\"javascript:display('"+$(this).attr('src')+"');\">");
		});
			
	});

</script>
</body>
</html>
