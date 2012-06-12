<?php
/* -----------------------------------------------------
 * File name : reminder.php								
 * Created by: aryonp@gmail.com		
 * -----------------------------------------------------				            
 * Purpose	 : Generate new random password and send 
 * it to specific email if every step passed correctly											                 			
 * -----------------------------------------------------
 */

require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'notif.lib.php';

function step_1(){ ?>
<div style="height:100%;">
<small>Please insert your email to authenticate</small>
<br><br>
<form method="POST" action="<?=$_SERVER['PHP_SELF']?>" class="form-inline">
<label><b>EMAIL</b></label>
<input type="text" size="50" name="email" value="<?=DEFAULT_MAIL_DOMAIN?>">
<input type="submit" name="f_pass_1" class="btn-info btn-small" value="  NEXT  ">
</form>
</div>
<?php }

function step_2(){
   $email = mysql_real_escape_string(strtolower(strip_tags(trim($_POST['email']))));
   if(!empty ($email) ){
		if(!$email){
			echo "<br><div class=\"alert\">";
			echo "You did not enter a valid e-mail address.<br/><br/>";
			echo "".back_button()."";
			echo "</div>";
		} 
		else {
			$check_username_query 	= "SELECT u.id FROM user u WHERE u.email = '$email' AND u.active = '1' AND u.del = '0';";
			$check_username_SQL 	= @mysql_query($check_username_query) or die(mysql_error());
			$check_username_array 	= mysql_fetch_array($check_username_SQL,MYSQL_ASSOC);
			if (mysql_num_rows($check_username_SQL) >= 1) {
				step_3($check_username_array["id"]);
				log_hist(7,$email);
			}
			else {
				echo "<br><div class=\"alert\">";
				echo "Wrong e-mail address! <br/><br/>";
				echo "".back_button()."";
				echo "</div>";
			} 
		}
	}	
	else {		echo "<br><div class=\"alert\">";
				echo "You cannot insert an empty email address <br/><br/>";
				echo "".back_button()."";
				echo "</div>";
	}
}

function step_3($uid){
   $userid 				= mysql_real_escape_string(strtolower(strip_tags(trim($uid))));
   $check_answer_query 	= "SELECT u.username AS uname FROM user u WHERE u.id = '$userid' AND u.active = '1' AND u.del = '0';";
   $check_answer_SQL 	= @mysql_query($check_answer_query) or die(mysql_error());
   $check_answer_array 	= mysql_fetch_array($check_answer_SQL,MYSQL_ASSOC);
   $new_pass 			= randomKeys(8);
   $new_pass_md5 		= md5($new_pass);
		if (mysql_num_rows($check_answer_SQL) >= 1) {
			$update_pass_q 		= "UPDATE user u SET u.password = '$new_pass_md5' WHERE u.id = '$userid' AND u.active = '1' AND u.del = '0';";
			$update_pass_SQL 	= @mysql_query($update_pass_q) or die(mysql_error());
			notify_pass_reset($userid, $new_pass);
			echo "<br><div class=\"alert alert-success\">";
			echo "STORIX has sent an email to your mailbox <br/><br/>";
			echo "<input type=\"button\" class=\"btn-info btn-small\" name=\"close\" value=\"  CLOSE  \" onclick=\"javascript:window.close()\" >";
			echo "</div>";
			log_hist(8,$check_answer_array["uname"]);
		}	
}

?>
<!DOCTYPE html>
<html>
	<head>
		<title>Forgot Password</title>
		<link rel="stylesheet" type="text/css" media="all" href="<?=CSS_PATH?>bootstrap.css" />
		<link rel="shortcut icon" href="<?=IMG_PATH?>favicon.ico" type="image/x-icon" />
	</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" oncontextmenu="return false">
<div class="container-fluid">
	<div class="row-fluid">&nbsp;</div>
	<div class="row-fluid">
		<div class="span12">
			<div class="well">
<//-----------------CONTENT-START-------------------------------------------------//>
<?php
if(isset($_POST['f_pass_1'])){
	step_2();
}
else {
	step_1();
}
?>
<//-----------------CONTENT-END-------------------------------------------------//>
			</div>
		</div>
	</div>
	<div class="row-fluid">&nbsp;</div>
</div>
<script language="javascript" type="text/javascript" src="<?=JS_PATH?>functions.js"></script></body>
</html>