<?php
/* -----------------------------------------------------
 * File name	: auth.class.php	
 * Created by 	: aryonp@gmail.com				   
 * Created date	: 21.10.2008				
 * -----------------------------------------------------			
 * Purpose		: Handle authorization in STORIX		
 * -----------------------------------------------------						                 			
 */

class Auth {
	
	var $userid;
	var $uname;
	var $password;
	var $seccode;
	var $_errors;
		
	function __construct(){
		$this->_errors = array();
	}	
	
	function setLogin($uname,$password){
		$this->uname 	= (!empty($uname))?mysql_real_escape_string(strip_tags(trim($uname))):"";
		$this->password = (!empty($password))?mysql_real_escape_string(md5(strip_tags(trim($password)))):"";
	}	
	
	function getLogin(){
		if(empty($this->uname))
		array_push($this->_errors,"Empty email");
			
		if(empty($this->password))
		array_push($this->_errors,"Empty password");
		
		$query = "SELECT CONCAT(u.fname,' ',u.lname) AS fullname, u.email, u.username, b.id AS bid, u.id as uid, d.name AS department, d.id AS did, CONCAT(m.fname,' ',m.lname) AS mgrname, m.id AS mid, u.level_id_fk AS level, b.name AS branches 
		          FROM user u 
		          LEFT JOIN departments d ON (d.id = u.dept_id_fk) 
		          LEFT JOIN branch b ON (b.id = u.branch_id_fk) 
		          LEFT JOIN user m ON (m.id = u.mgr_id_fk) 
		          WHERE u.email = '$this->uname' AND u.password = '$this->password' AND u.active = '1' AND u.del = '0' ";			
		
		$sql = @mysql_query($query) or die(mysql_error());
		
		if(!mysql_num_rows($sql))
		array_push($this->_errors,"Wrong email or password");
		
		if(count($this->_errors) <= 0) {
			$this->setSession($sql);
			
			if (!empty($_SESSION['ctRedirect'])) {
				$page = $_SERVER['SERVER_NAME']."".$_SESSION['ctRedirect'];	
			}
			
			else {
				$page = $_SERVER['SERVER_NAME']."".LOGIN_OK;
			}
			
			log_hist(1);
			Header("Location:http://$page");
			unset($_SESSION['ctRedirect']);
			exit();
		}
	}
	
	function setSession($sql){
		session_start();
		$array = mysql_fetch_array($sql, MYSQL_ASSOC);
		$_SESSION['fullname'] 	= $array['fullname'];
		$_SESSION['bid'] 		= $array['bid'];
		$_SESSION['uid'] 		= $array['uid'];
		$_SESSION['department'] = $array['department'];
		$_SESSION['did'] 		= $array['did'];
		$_SESSION['branch'] 	= $array['branches'];
		$_SESSION['mgr_name'] 	= $array['mgrname'];
		$_SESSION['mid'] 		= $array['mid'];
		$_SESSION['level'] 		= $array['level'];
		$_SESSION['email'] 		= $array['email'];
		$_SESSION['timestamp'] 	= date('Y-m-d H:i:s');
		$_SESSION['auth_system']= SYS_CODE;
		//session_unregister($_SESSION['ctRedirect']);
		//$_SESSION['uname'] 	= $array['username'];
	}
	
	function setSU($userid){
		$this->userid = (is_numeric($userid))?(int) mysql_real_escape_string(strip_tags(trim($userid))):"";
	}	
	
	function getSU(){
		$userid = array(1);
		
		if(in_array($this->userid,$userid))
		array_push($this->_errors,"Unauthorized SU");
		
		if(empty($this->userid))
		array_push($this->_errors,"Empty SU");
		
		$query = "SELECT CONCAT(u.fname,' ',u.lname) AS fullname, b.id AS bid, u.email, u.id as uid, d.name AS department, d.id AS did, CONCAT(m.fname,' ',m.lname) AS mgrname, m.id AS mid, u.level_id_fk AS level, b.name AS branches 
		          FROM user u 
		          LEFT JOIN departments d ON (d.id = u.dept_id_fk) 
		          LEFT JOIN branch b ON (b.id = u.branch_id_fk) 
		          LEFT JOIN user m ON (m.id = u.mgr_id_fk) 
		          WHERE u.id = '$this->userid' AND u.del = 0 AND u.active = 1;";
		$sql 	= @mysql_query($query) or die(mysql_error());
		$array 	= mysql_fetch_array($sql, MYSQL_ASSOC);
		
		if(mysql_num_rows($sql) == 0)
		array_push($this->_errors,"Wrong user ID");
		
		if(count($this->_errors) == 0) {
			//log_hist(2,$array["username"]);
			$page = $_SERVER['SERVER_NAME'].LOGIN_OK;
			$this->setSession($sql);
			Header("Location:http://$page");
			exit();
		}
	}
	
	function doLogout(){
		session_start();
		
		$old_session = $_SESSION['auth_system'];
		
		if(!isset($old_session)) {
			session_unset();
			session_destroy();
			header("Location:".LOGIN_FAIL."");
			exit();
		}
		
		else {		
			log_hist(3);
			session_unset();
			session_destroy();
			if(!empty($old_session)) {
				header("Location:".LOGIN_OK."");
				exit();
			}
			else {
				array_push($this->_errors,"You cannot logout");
			}
		}
	}
}
?>