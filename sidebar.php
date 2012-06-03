
<div id="sidebar">
	
	<h3>Облако тэгов</h3>
    <div id="tagCloud">
	<?php 
	$p = new Post();
	$set = $p->tagCloud();
	$result = '';
	$color = 1;
	if ($set)
	foreach($set as $s) {
		$color = ($color ? 0 : 1);
		if ($s['counter']>20) $result .= '<a class="tag_5 color'.$color.'" href="bytag.php?search='.$s['tag'].'">'.$s['tag'].'</a> ';
		elseif ($s['counter']>15) $result .= '<a class="tag_4 color'.$color.'" href="bytag.php?search='.$s['tag'].'">'.$s['tag'].'</a> ';
		elseif ($s['counter']>10) $result .= '<a class="tag_3 color'.$color.'" href="bytag.php?search='.$s['tag'].'">'.$s['tag'].'</a> ';
		elseif ($s['counter']>5) $result .= '<a class="tag_2 color'.$color.'" href="bytag.php?search='.$s['tag'].'">'.$s['tag'].'</a> ';
		elseif ($s['counter']>0) $result .= '<a class="tag_1 color'.$color.'" href="bytag.php?search='.$s['tag'].'">'.$s['tag'].'</a> ';
		}
	echo $result;
	?>
    </div>
    

    <h3>Пикаса</h3>
    <div id="picasa"></div>
    <h4>Много других фоток на <a href="https://picasaweb.google.com/101311003421553582752">Google Picasa</a></h4>

    
	<h3>Комментарии</h3>
    <div id="last_comments"></div>


    
</div><!-- sidebar -->
  
  <script>
	
	$.ajax({
		url: "last_comments.php",
		cache: false,
		success: function(html){	
			$("#last_comments").html(html);	
		},
	});

	$.ajax({
		url: "picgenerator.php",
		cache: false,
		success: function(html){	
			$("#picasa").html(html);	
		},
	});

  </script>