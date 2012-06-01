<?php
class Post {
	private $sys = null;
	
	function __construct() {
		$this->sys = new System();
	}
	
	public function dump($error = '') {
		$this->sys->fatal(" post dump exception $error");
	}
	
	/* 
		Function returns all the posts by category and/or tag name
	*/
	public function all($category = null, $tag = null, $page = 0, $limit = 9) {
		$page *= $limit;
		$query = "SELECT * FROM `post` WHERE ";
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
		$query = "SELECT * FROM `post` WHERE `public` > -1";
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
		$query = "SELECT * FROM `post` WHERE `public` = -1";
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
		$query = "SELECT * FROM `post` WHERE `page` = 1;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}

	/* 
		Function returns one posts by its id
	*/
	public function one($post_id) {
		$query = "SELECT * FROM `post` WHERE `id` = $post_id;";
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
		$query = "INSERT INTO `post`(`caption`, `text`, `category`, `tag`, `time`, `page`, `public`) VALUES ";
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
		$query = "SELECT `id` FROM `post` WHERE `time` = $time;";
		$result = $this->sys->query($query);
		$result = mysql_fetch_array($result);		
		$this->sys -> close();
		$this->processTags($data['tag']);
		return $result['id'];
	}
	
	public function countPosts() {
		$query = "SELECT COUNT(`id`) FROM `post` WHERE TRUE;";
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
		$query = "UPDATE `post` SET `caption` = '".$data['caption']."', `text` = '".$data['text']."',";
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
		$query = "UPDATE `post` SET `public` = $flag WHERE `id` = $id;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
	}
	
	public function delete($post_id) {
		$this -> publish($post_id, -1);
	}
	
	public function random() {
		$this->sys -> connect();
		$query = "SELECT MAX(`id`) FROM `post` WHERE TRUE;";
		$result = $this->sys->query($query);
		$result = mysql_fetch_row($result);
		$max = rand(0, $result[0]);
		$query = "SELECT * FROM `post` WHERE `id` >= $max LIMIT 1;";
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function countComments($post_id) {
		$query = "SELECT COUNT(`cid`) FROM `comment` WHERE `post_id` = '$post_id';";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		$result = mysql_fetch_row($result);
		return $result[0];
	}

	public function countAllComments() {
		$query = "SELECT COUNT(`cid`) FROM `comment` WHERE TRUE;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		$result = mysql_fetch_row($result);
		return $result[0];
	}
	
	public function allComments($post_id) {
		$query = "SELECT * FROM `comment` WHERE `post_id` = $post_id ORDER BY `time` ASC;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function listComments($page = 0, $limit = 10) {
		$page *= $limit;
		$query = "SELECT *, comment.time AS ctime FROM comment, post WHERE comment.post_id = post.id ";
		$query .= " ORDER BY `ctime` DESC LIMIT $page,$limit; ";		
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function userComments($user_id) {
		$query = "SELECT *, comment.time AS ctime FROM comment, post WHERE `user_id` = $user_id AND comment.post_id = post.id ORDER BY comment.time DESC;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function lastComments($number = 5) {
		$query = "SELECT comment.*, post.caption AS postName FROM comment, post WHERE comment.post_id = post.id ORDER BY `time` DESC LIMIT $number;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;		
	}
	
	public function addComment($post_id, $author=null, $email=null, $text=null, $user_id=null) {
		$this->sys ->connect();  
		if ($user_id) {
			// find that user. it always exists.
			$query = "SELECT * FROM `users` WHERE `uid` = '$user_id';";
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
		$query = "INSERT INTO `comment`(`post_id`,`author`,`email`,`comment`,`time`, `user_id`) VALUES ";
		$query .= "($post_id,'$author','$email','$text',$time, '".(($user_id)?$user_id:0)."');";
		$result = $this->sys->query($query);	
		$this->sys -> close();
	}
	
	public function deleteComment($id) {
		$query = "DELETE FROM `comment` WHERE `cid` = $id;";
		$this->sys -> connect();
		$result = $this->sys->query($query);	
		$this->sys -> close();
	}
	
	public function search ($str, $limit = 11) {
		$this->sys -> connect();
		$str = $this->sys->clear($str);
		$query = "SELECT * FROM `post` WHERE (`text` LIKE '%$str%' OR `caption` LIKE '%$str%' ";
		$query .= "OR `tag` LIKE '%$str%' OR `category` LIKE '%$str%') AND `public` = 1 ORDER BY `time` DESC LIMIT $limit; ";
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function searchByTag ($str, $limit = 11) {
		$this->sys -> connect();
		$str = $this->sys->clear($str);
		$query = "SELECT * FROM `post` WHERE `tag` LIKE '%$str%' AND `public` = 1 ORDER BY `time` DESC  LIMIT $limit ; ";
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function processTags($str, $edit = 1) {  // if edit = 1 counters will be incremented
		if (!trim($str)) return;
		$new = explode(',',$str);
		$this->sys -> connect();
		$query = "SELECT * FROM `tags` WHERE TRUE;";
		$result = $this->sys->query($query);
		if (!mysql_num_rows($result)) {
			$query = "INSERT INTO `tags` (`tag`, `counter`) VALUES ('".trim($new[0])."', 1);";
			$this->sys->query($query);
		} else { 
			$set = array();
			while ($r = mysql_fetch_array($result))
				$set[] = $r['tag'];
			foreach($new as $n)	{
				if (!in_array(trim($n), $set)) {
					$query = "INSERT INTO `tags` (`tag`, `counter`) VALUES ('".trim($n)."', 1);";
					$result = $this->sys->query($query);
				} elseif ($edit) {
					$query = "SELECT * FROM `tags` WHERE `tag` = '".trim($n)."'";
					$result = $this->sys->query($query);
					$r = mysql_fetch_array($result);
					$query = "UPDATE `tags` SET `counter` = ".($r['counter']+1)." WHERE `id` = ".$r['id'].";";
					$result = $this->sys->query($query);
				}
			} // foreach
		}
		$this->sys -> close();
	}
	
	public function tagCloud() {
		$this->sys -> connect();
		$query = "SELECT * FROM `tags` WHERE TRUE;";
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
		return $result;
	}
	
	
	//////////////////////////////////////////////////////////////////
	// DELETE
	public function write_t($data) {
		$this->sys -> connect();
		$data['caption'] = $this->sys->clear($data['caption']);
		$data['text'] = $this->sys->clear($data['text']);
		if ($data['category']) $data['category'] = $this->sys->clear($data['category']);
		if ($data['tag']) $data['tag'] = $this->sys->clear($data['tag']);		
		$query = "INSERT INTO `post`(`caption`, `text`, `category`, `tag`, `time`, `page`) VALUES ";
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
		$query = "SELECT `id` FROM `post` WHERE `time` = $time;";
		$result = $this->sys->query($query);
		$result = mysql_fetch_array($result);		
		$this->sys -> close();
		return $result['id'];
	}
	
	
}
?>