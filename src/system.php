<?php

class System {
	
	public $users = "users";
	public $comments = "comment";
	public $posts = "post";
	public $tags = "tags";

	private $db 	= 'blog';
	private $host 	= 'localhost';
	private $user	= 'root';
	private $pasw	= 'mysql';
	
	private $link 	= null;
	private $last_query = '';
	
	public function connect($flag = 0) {
		$this->link = mysql_connect($this->host, $this->user, $this->pasw); 
		mysql_select_db($this->db, $this->link);
		$result = mysql_query('SET NAMES utf8');         // problem is because of Wordpress database type :-(
		$result = mysql_query('SET CHARACTER SET utf8');
		
	}
	
	public function query($query) {
		$this->last_query = $query;
		$result = mysql_query($query, $this->link);
		return $result;
	}
	
	public function close() {
		mysql_close($this->link);		
	}
	
	public function fatal($error = '') {
		echo "FATAL ERROR: $error<br>QUERY:<br>".$this->last_query;
	}
	
	public function clear($query) {
		return mysql_real_escape_string(stripslashes($query), $this->link); 	
	}
	
	public function login($username, $password) {
		//sets up SESSION
		$username = $this->clear($username);
		$password = $this->clear($password);
		$query = "SELECT COUNT(`uid`) FROM `".$this->users."` WHERE `username` = '$username' AND `password` = '$password' ;";
		$result = $this->query($query);
		$result = mysql_fetch_row($result);
		if ($result[0] == 0) return false;
		$query = "SELECT * FROM `".$this->users."` WHERE `username` = '$username' AND `password` = '$password' ;";
		$result = $this->query($query);
		$result = mysql_fetch_array($result);
		$_SESSION['name'] = $result['name'];
		$_SESSION['email'] = $result['email'];
		$_SESSION['username'] = $result['username'];
		$_SESSION['userId'] = $result['uid'];
		$_SESSION['role'] = $result['role'];
		$_SESSION['hblog'] = 1;
		return true;		
	}
	
	public function profile($user_id) {
		$query = "SELECT * FROM `".$this->users."` WHERE `uid` = '$user_id'; ";
		$result = $this->query($query);
		return $result;
	}
	
	public function userdata() {
		$data = array();
		$data['name'] = $_SESSION['name'];
		$data['username'] = $_SESSION['username'];
		$data['email'] = $_SESSION['email'];
		$data['userId'] = $_SESSION['userId'];
		$data['role'] = $_SESSION['role'];
		return $data;
	}
	
	public function logout() {
		unset($_SESSION['hblog']); 
		setcookie ("heml", "", time() - 2629743, "/", "himorblog.com");
		setcookie ("hpwd", "", time() - 2629743, "/", "himorblog.com");
		if (ini_get("session.use_cookies")) {
			$params = session_get_cookie_params();
			setcookie(session_name(), 'hblog', time() - 42000,
				$params["path"], $params["domain"],
				$params["secure"], $params["httponly"]
			);
		}
		// ryan, you are genius!
		if(session_is_registered("hblog"))
		 session_destroy();
	}
	
	/* function checks if user exists */
	public function userexist($email) {
		$query = "SELECT COUNT(`uid`) FROM `".$this->users."` WHERE `email` = '".$this->clear($email)."';";
		$result = $this->query($query);
		if (!$result) return 0;
		$result = mysql_fetch_row($result);
		return $result[0];
	}
	
	/* function checks user password exists */
	public function userByPassword($user_id, $password) {
		$query = "SELECT COUNT(`uid`) FROM `".$this->users."` WHERE `uid` = '$user_id' AND `password` = '$password' ;";
		$result = $this->query($query);
		if (!$result) return 0;
		$result = mysql_fetch_row($result);
		return $result[0];
	}
	
	public function register($username, $password, $name, $email) {
		if (!$this->userexist($email)) {
			$query = "INSERT INTO `".$this->users."` (`username`, `password`, `name`, `email`, `role`, `registered`, `reg_token`) VALUES (";
			$token = $this->genRandomString(16);
			$time = time();
			$query .= "'".$this->clear($username)."','".$this->clear($password)."','".$this->clear($name)."','".$this->clear($email)."', ";
			$query .= '1, '.$time.",'".$token."');";
			$this -> query($query);
			return true; // no error
		} else return false; // error - user exists		
	}
	
	public function update($user_id, $username, $password, $name, $email) {
			$query = "UPDATE `".$this->users."` SET `username` = '$username', `password` = '$password', `name` = '$name', `email` = '$email' WHERE `uid` = '$user_id'; ";
			$this -> query($query);
	}	
	
	public function genRandomString($length = 8) {
		$characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$string = "";    
		for ($p = 0; $p < $length; $p++) {
			$string .= $characters[mt_rand(0, strlen($characters)-1)];
		}
		return $string;
	}

	/* function changes the role of the user */
	/* role = 1 is regular users, role = 0 is admin*/
	public function setRole($user_id, $role) {
		$query = "UPDATE `".$this->users."` SET `role` = $role WHERE `uid` = $user_id;";
		$this -> query($query);
	}
	
	/*	Function returns ID of admin user
	*/
	public function getAdmin() {
		$query = "SELECT `uid` FROM `".$this->users."` WHERE `role` = 0 LIMIT 1;";
		$result = $this->query($query);
		if (!$result) return 0;
		$result = mysql_fetch_row($result);
		return $result[0];
	}
		
}

?>