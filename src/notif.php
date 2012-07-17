<?php

class Notif {
	private $sys = null;
	private $notif_table = "notif";
	private $register = "register";	
	private $picom = "picom";	
	private $comment_table;
	private $post_table;		
	private $users_table;
		
	function __construct() {
		$this->sys = new System();
		$this->post_table = $this->sys->posts;
		$this->comment_table = $this->sys->comments;
		$this->users_table = $this->sys->users;
	}
	
	/**
	*	Returns number of new notifications for this user
	*/
	public function getNewNotifNumber($uid) {
		$query = "SELECT COUNT(id) FROM `".$this->notif_table."` WHERE ";
		$query .= "`to` = '$uid' AND `read` = '0' ;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$result = mysql_fetch_array($result);		
		$this->sys -> close();
		return $result[0];	
	}

	/**
	*	Returns array of all notifications
	*/
	public function getAllNotif($uid) {
		$query1 = "SELECT ".$this->notif_table.".*, ".$this->comment_table.".author, ".$this->post_table.".caption, " .$this->post_table.".id as post_id " .
			" FROM ".$this->notif_table.", ".$this->comment_table.", ".$this->post_table." WHERE " .
			$this->comment_table.".cid = ".$this->notif_table.".msg AND " .
			$this->post_table.".id = ".$this->notif_table.".target AND " .
			"`to` = '$uid' AND `type` = 2 ORDER BY `read`, `time` DESC LIMIT 20;";
		
		$query2 = "SELECT ".$this->notif_table.".*, ".$this->picom.".author " .
			" FROM ".$this->notif_table.", ".$this->picom." WHERE " .
			$this->picom.".id = ".$this->notif_table.".msg AND " .
			"`to` = '$uid' AND `type` IN (1,3) ORDER BY `read`, `time` DESC LIMIT 20;";
		
		$this->sys -> connect();
		$result1 = $this->sys->query($query1);
		$result2 = $this->sys->query($query2);
		$this->sys -> close();
		$result = array();
		while($r = mysql_fetch_array($result1)) {
			$result[] = array(
				'author' => $r['author'],
				'type' => 2,
				'read' => $r['read'],
				'target' => $r['caption'],
				'targetId' => $r['post_id'],
				'time' => $r['time'],
				);
		}
		while($r = mysql_fetch_array($result2)) {
			$result[] = array(
				'author' => $r['author'],
				'type' => $r['type'],
				'read' => $r['read'],
				'target' => null,
				'targetId' => $r['target'],
				'time' => $r['time'],
				);
		}
		function cmpr($a, $b) {
			return $a['time'] > $b ['time'] ? -1 : 1;
			}
		usort($result, "cmpr");
		
		return $result;
	}

	public function markAllRead($uid) {
		$query = "UPDATE `".$this->notif_table."` SET `read` = 1 WHERE ";
		$query .= "`to` = '$uid';";
		$this->sys -> connect();
		$this->sys->query($query);
		$this->sys -> close();
	}
	
	/* 
	*	Updates list of users who must be notified in case of event
	*/
	public function updateRegister($target, $type, $uid) {
		// just add userId if not exists
		$query = "SELECT COUNT(id) FROM `".$this->register."` WHERE ";
		$query .= "`uid` = '$uid' AND `type` = '$type' AND `target` = '$target';";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$result = mysql_fetch_array($result);		
		if (!$result[0]) {
			$query = "INSERT INTO `".$this->register."` (`type`, `target`, `uid`) ";
			$query .= " VALUES('$type','$target','$uid');";
			$result = $this->sys->query($query);
		}
		$this->sys -> close();
	}
	
	/*
	*	Returns array of users who must be notified in case of event
	*/
	public function getRegister($target, $type) {
		$query = "SELECT * FROM `".$this->register."` WHERE ";
		$query .= " `type` = '$type' AND `target` = '$target';";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		$return = array();
		while ($r = mysql_fetch_array($result)) {
			$return[] = $r['uid'];
		}
		return $return;
	}

	/**
	*	Sends notifications to all users, who must be informed about the event
	*/
	public function informUsers($type, $target, $msg, $except = null) {
		$recipients = $this->getRegister($target, $type);
		$this->sys -> connect();
		$time = time();
		foreach	($recipients as $to) {
			if ($to == $except) continue;
			$query = "INSERT INTO `notif` (`to`, `target`, `msg`, `type`, `time`, `read`) " .
				"VALUES ($to, $target, $msg, $type, '$time', 0);";
			$this->sys->query($query);
		}
		$this->sys->close();		
	}
	
	
}
?>