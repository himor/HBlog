<?php
class Picom {
	private $sys = null;
	private $picom_table = "picom";
	private $users_table;

	function __construct() {
		$this->sys = new System();
		$this->users_table = $this->sys->users;		
	}
	
	/* 
		Function returns comments for the picture
	*/
	public function getComments($img) {
		$query = "SELECT * FROM `".$this->picom_table."` WHERE ";
		$query .= "`pic_id` = '$img'";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	public function postComment($img, $name, $email, $text, $uid = null) {
		$time = time();
		$this->sys -> connect();
		if ($uid) {
			// find that user. it always exists.
			$query = "SELECT * FROM `".$this->users_table."` WHERE `uid` = '$uid';";
			$result = $this->sys->query($query);
			$r = mysql_fetch_array($result);
			$name = $r['name'];
			$email = $r['email'];
		} else {
			$name = $this->sys->clear($name);
			$email = $this->sys->clear($email);			
		}
		$text = $this->sys->clear($text);
		$query = "INSERT INTO `".$this->picom_table."` (`pic_id`, `author`, `email`, `comment`, `time`" . ($uid ? ", `user_id`) ":")") .
		"VALUES ('$img', '$name', '$email', '$text', '$time'" . ($uid ? ", $uid);" : ");");
		$result = $this->sys->query($query);
		$query = "SELECT `id` FROM `".$this->picom_table."` WHERE `time` = $time;";
		$result = $this->sys->query($query);
		$result = mysql_fetch_array($result);		
		$this->sys -> close();
		return $result['id'];
	}
	
	/*
		Make a notification about new comment for picture
		target - id of the picture
		msg - id of the comment
	*/
	public function informAdmin($target, $msg) {
		$type = 1; // comment for picture in gallery
		$time = time();
		$this->sys -> connect();
		$to = $this->sys->getAdmin();
		$query = "INSERT INTO `notif` (`to`, `target`, `msg`, `type`, `time`, `read`) " .
			"VALUES ($to, $target, $msg, $type, '$time', 0);";
		$this->sys->query($query);
		$this->sys->close();		
	}
	
	public function informUser($to, $target, $msg) {
		$type = 1; // comment for picture
		$time = time();
		$query = "INSERT INTO `notif` (`to`, `target`, `msg`, `type`, `time`, `read`) " .
			"VALUES ($to, $target, $msg, $type, '$time', 0);";
		$this->sys -> connect();
		$this->sys->query($query);
		$this->sys->close();		
	}	

}
?>