<?php
class Post {
	private $sys = null;
	private $comment_table;
	private $post_table;		
	private $users_table;
	private $tags_table;
		
	function __construct() {
		$this->sys = new System();
		$this->post_table = $this->sys->posts;
		$this->comment_table = $this->sys->comments;
		$this->users_table = $this->sys->users;
		$this->tags_table = $this->sys->tags;
	}
	
	public function dump($error = '') {
		$this->sys->fatal(" 'post.php' dump exception $error");
	}
	
	/* 
		Function returns all the posts by category and/or tag name
	*/
	public function all($category = null, $tag = null, $page = 0, $limit = 9) {
		$page *= $limit;
		$query = "SELECT * FROM `".$this->post_table."` WHERE ";
		if ($category) {
			$query .= "`category` LIKE '%$category%'";
		}
		if ($category && $tag) {
			$query .= " AND `tag` LIKE '%$tag%'";
		} elseif (!$category && $tag) {
			$query .= "`tag` LIKE '%$tag%'";
		}
		if ($category || $tag)
			$query .= " AND ";
		$query .= "`public` = 1 ORDER BY `time` DESC";
		$query .= " LIMIT $page,$limit; ";
		
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	/* 
		Function returns list of posts
	*/
	public function listing($page = 0, $limit = 20) {
		$page *= $limit;
		$query = "SELECT * FROM `".$this->post_table."` WHERE `public` > -1";
		$query .= " ORDER BY `time` DESC LIMIT $page,$limit; ";		
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	/* 
		Function returns list of deleted posts
	*/
	public function listTrash() {
		$query = "SELECT * FROM `".$this->post_table."` WHERE `public` = -1";
		$query .= " ORDER BY `time` DESC ; ";		
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}

	/* 
		Function returns list of pages
	*/
	public function listPages() {
		$query = "SELECT * FROM `".$this->post_table."` WHERE `page` = 1 AND `public` = 1;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}

	/* 
		Function returns one posts by its id
	*/
	public function one($post_id) {
		$query = "SELECT * FROM `".$this->post_table."` WHERE `id` = $post_id;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	/*
		Function creates a new post
	*/
	public function write($data) {
		$this->sys -> connect();
		$data['caption'] = $this->sys->clear($data['caption']);
		$data['text'] = $this->sys->clear($data['text']);
		if ($data['category']) $data['category'] = $this->sys->clear($data['category']);
		if ($data['tag']) $data['tag'] = $this->sys->clear($data['tag']);		
		$query = "INSERT INTO `".$this->post_table."`(`caption`, `text`, `category`, `tag`, `time`, `page`, `public`) VALUES ";
		$query .= "('".$data['caption']."', '".$data['text']."',";
		if ($data['category'])
			$query .= "'".$data['category']."',";
		else $query .= "'',";
		if ($data['tag'])
			$query .= "'".$data['tag']."',";
		else $query .= "'',";
		$time = time();
		$query .= "$time, ".$data['page'].", ".$data['public'].");";		
		$result = $this->sys->query($query);
		$query = "SELECT `id` FROM `".$this->post_table."` WHERE `time` = $time;";
		$result = $this->sys->query($query);
		$result = mysql_fetch_array($result);		
		$this->sys -> close();
		$this->processTags($data['tag']);
		return $result['id'];
	}
	
	public function countPosts() {
		$query = "SELECT COUNT(`id`) FROM `".$this->post_table."` WHERE  `public` = 1;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		$result = mysql_fetch_row($result);
		return $result[0];
	}
	
	public function update($post_id, $data) {
		$this->sys -> connect();
		$data['caption'] = $this->sys->clear($data['caption']);
		$data['text'] = $this->sys->clear($data['text']);
		if ($data['category']) $data['category'] = $this->sys->clear($data['category']);
		if ($data['tag']) $data['tag'] = $this->sys->clear($data['tag']);		
		$query = "UPDATE `".$this->post_table."` SET `caption` = '".$data['caption']."', `text` = '".$data['text']."',";
		$query .= "`category` = '".$data['category']."',";
		$query .= " `tag` = '".$data['tag']."',";
		$query .= " `public` = '".$data['public']."',";
		$query .= " `page` = ".$data['page']." WHERE `id` = $post_id;";		
		$result = $this->sys->query($query);
		$this->sys -> close();
		$this->processTags($data['tag'], 0);
		return $result;
	}
	
	// flag = 0 means UNpublish
	public function publish($id, $flag = 1) {
		$query = "UPDATE `".$this->post_table."` SET `public` = $flag WHERE `id` = $id;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
	}
	
	public function delete($post_id) {
		$this -> publish($post_id, -1);
	}
	
	public function random() {
		$this->sys -> connect();
		$query = "SELECT MAX(`id`) FROM `".$this->post_table."` WHERE TRUE;";
		$result = $this->sys->query($query);
		$result = mysql_fetch_row($result);
		$max = rand(0, $result[0]);
		$query = "SELECT * FROM `".$this->post_table."` WHERE `id` >= $max LIMIT 1;";
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function countComments($post_id) {
		$query = "SELECT COUNT(`cid`) FROM `".$this->comment_table."` WHERE `post_id` = '$post_id';";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		$result = mysql_fetch_row($result);
		return $result[0];
	}

	public function countAllComments() {
		$query = "SELECT COUNT(`cid`) FROM `".$this->comment_table."` WHERE TRUE;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		$result = mysql_fetch_row($result);
		return $result[0];
	}
	
	public function allComments($post_id) {
		$query = "SELECT * FROM `".$this->comment_table."` WHERE `post_id` = $post_id ORDER BY `time` ASC;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function listComments($page = 0, $limit = 10) {
		$page *= $limit;
		$query = "SELECT *, ".$this->comment_table.".time AS ctime FROM ".$this->comment_table.", ".$this->post_table." WHERE ".$this->comment_table.".post_id = ".$this->post_table.".id ORDER BY `ctime` DESC LIMIT $page,$limit; ";		
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function userComments($user_id) {
		$query = "SELECT *, ".$this->comment_table.".time AS ctime FROM ".$this->comment_table.", ".$this->post_table." WHERE `user_id` = $user_id AND ".$this->comment_table.".post_id = ".$this->post_table.".id ORDER BY ".$this->comment_table.".time DESC;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function lastComments($number = 5) {
		$query = "SELECT ".$this->comment_table.".*, ".$this->post_table.".caption AS postName FROM ".$this->comment_table.", ".$this->post_table." WHERE ".$this->comment_table.".post_id = ".$this->post_table.".id ORDER BY `time` DESC LIMIT $number;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;		
	}
	
	public function addComment($post_id, $author=null, $email=null, $text=null, $user_id=null) {
		$this->sys ->connect();  
		if ($user_id) {
			// find that user. it always exists.
			$query = "SELECT * FROM `".$this->users_table."` WHERE `uid` = '$user_id';";
			$result = $this->sys->query($query);
			$r = mysql_fetch_array($result);
			$author = $r['name'];
			$email = $r['email'];
		} else {
			$author = $this->sys->clear($author);
			$email = $this->sys->clear($email);			
		}
		$text = $this->sys->clear($text);
		$time = time();
		$query = "INSERT INTO `".$this->comment_table."`(`post_id`,`author`,`email`,`comment`,`time`, `user_id`) VALUES ";
		$query .= "($post_id,'$author','$email','$text',$time, '".(($user_id)?$user_id:0)."');";
		$result = $this->sys->query($query);	
		$this->sys -> close();
	}
	
	public function deleteComment($id) {
		$query = "DELETE FROM `".$this->comment_table."` WHERE `cid` = $id;";
		$this->sys -> connect();
		$result = $this->sys->query($query);	
		$this->sys -> close();
	}
	
	public function search ($str, $limit = 11) {
		$this->sys -> connect();
		$str = $this->sys->clear($str);
		$query = "SELECT * FROM `".$this->post_table."` WHERE (`text` LIKE '%$str%' OR `caption` LIKE '%$str%' ";
		$query .= "OR `tag` LIKE '%$str%' OR `category` LIKE '%$str%') AND `public` = 1 ORDER BY `time` DESC LIMIT $limit; ";
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function searchByTag ($str, $limit = 11) {
		$this->sys -> connect();
		$str = $this->sys->clear($str);
		$query = "SELECT * FROM `".$this->post_table."` WHERE `tag` LIKE '%$str%' AND `public` = 1 ORDER BY `time` DESC  LIMIT $limit ; ";
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function processTags($str, $edit = 1) {  // if edit = 1 counters will be incremented
		if (!trim($str)) return;
		$new = explode(',',$str);
		$this->sys -> connect();
		$query = "SELECT * FROM `".$this->tags_table."` WHERE TRUE;";
		$result = $this->sys->query($query);
		if (!mysql_num_rows($result)) {
			$query = "INSERT INTO `".$this->tags_table."` (`tag`, `counter`) VALUES ('".trim($new[0])."', 1);";
			$this->sys->query($query);
		} else { 
			$set = array();
			while ($r = mysql_fetch_array($result))
				$set[] = $r['tag'];
			foreach($new as $n)	{
				if (!in_array(trim($n), $set)) {
					$query = "INSERT INTO `".$this->tags_table."` (`tag`, `counter`) VALUES ('".trim($n)."', 1);";
					$result = $this->sys->query($query);
				} elseif ($edit) {
					$query = "SELECT * FROM `".$this->tags_table."` WHERE `tag` = '".trim($n)."'";
					$result = $this->sys->query($query);
					$r = mysql_fetch_array($result);
					$query = "UPDATE `".$this->tags_table."` SET `counter` = ".($r['counter']+1)." WHERE `id` = ".$r['id'].";";
					$result = $this->sys->query($query);
				}
			} // foreach
		}
		$this->sys -> close();
	}
	
	public function tagCloud() {
		$this->sys -> connect();
		$query = "SELECT * FROM `".$this->tags_table."` WHERE TRUE;";
		$result = $this->sys->query($query);
		$this->sys->close();
		if (!mysql_num_rows($result)) return '';
		$total = 0;
		$set = array();
		while ($r = mysql_fetch_array($result)) {
			$total += $r['counter'];
			$set[] = array ( 'tag' => $r['tag'],
				'counter' => $r['counter']);
		}
		$result = array();
		foreach($set as $s)
			$result[] = array('tag' => $s['tag'],
				'counter' => $s['counter'] / $total * 100
			);			
		return (count($result) ? $result : null);
	}
	
	
	//////////////////////////////////////////////////////////////////
	// DELETE
	public function write_t($data) {
		$this->sys -> connect();
		$data['caption'] = $this->sys->clear($data['caption']);
		$data['text'] = $this->sys->clear($data['text']);
		if ($data['category']) $data['category'] = $this->sys->clear($data['category']);
		if ($data['tag']) $data['tag'] = $this->sys->clear($data['tag']);		
		$query = "INSERT INTO `$this->post_table`(`caption`, `text`, `category`, `tag`, `time`, `page`) VALUES ";
		$query .= "('".$data['caption']."', '".$data['text']."',";
		if ($data['category'])
			$query .= "'".$data['category']."',";
		else $query .= "'',";
		if ($data['tag'])
			$query .= "'".$data['tag']."',";
		else $query .= "'',";
		$time = $data['time'];
		$query .= "$time, ".$data['page'].");";		
		$result = $this->sys->query($query);
		$query = "SELECT `id` FROM `$this->post_table` WHERE `time` = $time;";
		$result = $this->sys->query($query);
		$result = mysql_fetch_array($result);		
		$this->sys -> close();
		return $result['id'];
	}
	
	
}
?>