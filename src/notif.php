<?php

class Notif {
	private $sys = null;
	private $notif_table = "notif";
	private $register = "register";	

	function __construct() {
		$this->sys = new System();
	}
	
	public function getNewNotifNumber($uid) {
		$query = "SELECT COUNT(id) FROM `".$this->notif_table."` WHERE ";
		$query .= "`to` = '$uid' AND `read` = '0' ;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$result = mysql_fetch_array($result);		
		$this->sys -> close();
		return $result[0];	
	}

	public function getAllNotif($uid) {
		$query = "SELECT * FROM `".$this->notif_table."` WHERE ";
		$query .= "`to` = '$uid' ORDER BY `read`, `time` DESC;";
		$this->sys -> connect();
		$result = $this->sys->query($query);
		$this->sys -> close();
		return $result;
	}
	
	/* List of users who must be notified in case of event
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
	



}
?>