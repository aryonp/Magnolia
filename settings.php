<?php

/* -----------------------------------------------------
 * File name	: config.inc.php								
 * Created by 	: aryonp@gmail.com		
 * -----------------------------------------------------				            
 * Purpose		: Change individual settings										                 			
 * -----------------------------------------------------
 */

require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title 	= "Settings Page";
$page_id_left 	= "15";
$page_id_right 	= "43";
$category_page 	= "settings";
chkSecurity($page_id_right);

$status 		= "&nbsp;";
$old_data_q 	= "SELECT u.sign FROM user u WHERE u.id = '".$_SESSION['uid']."';";
$old_data_SQL 	= @mysql_query($old_data_q) or die(mysql_error());
$old_data_array = mysql_fetch_array($old_data_SQL);

if (isset($_POST['upd_pass'])) {
	
	$email		= trim($_POST['email']);
	$oldpass 	= md5($_POST['old_pwd']);
	$new_pwd1 	= md5($_POST['new_pwd1']);
	$new_pwd2 	= md5($_POST['new_pwd2']);
	$q_cek1		= "SELECT email, password FROM user WHERE email = '$email' AND password = '$oldpass';";
	$cek1 		= @mysql_query($q_cek1) or die(mysql_error());
	$num 		= mysql_num_rows($cek1);
	if ($num >= 1) {
		if ($new_pwd1 == $new_pwd2) {
			$query 	= "UPDATE user SET password = '$new_pwd2' WHERE email = '$email';";
			@mysql_query($query) or die(mysql_error());
			$status = "<br/><p class=\"alert alert-success span8\">Your password has been successfully changed</p>";
			log_hist(6,"");
		} 
		else { 
			$status ="<br/><p class=\"alert alert-error span8\">Failed to change password. Make sure you have entered the correct information</p>"; 
		}
	} 
	else { 
		$status ="<br/><p class=\"alert alert-error span8\">Failed to change password. Make sure you have entered the correct old password</p>"; 
	} 
}

if(isset($_POST['upd_sign'])) {
	
	$uid 			= $_SESSION['uid'];
	$sign_target 	= file_target("sign",$_FILES['sign-file']['name']);
	
	if(move_uploaded_file($_FILES['sign-file']['tmp_name'], $sign_target)) {
		$update_sign_q = "UPDATE user SET user.sign = '$sign_target' WHERE user.id = '$uid';";
		@mysql_query($update_sign_q) or die(mysql_error());
		chmod($sign_target, 0777);
		log_hist(10,"");
		//$status = "<p class=\"alert alert-success\">Your signature has been updated.</p>";
		header("location:".$_SERVER['PHP_SELF']);
		exit();
	}
	else { 
		$status = "<br/><p class=\"alert alert-error span8\">Sorry, there was a problem uploading your file.</p>";
	}
}

if (isset($_POST['del_sign'])){
	$location = $_POST['location'];
	$uid = $_SESSION['uid'];
	$empty_file_query  ="UPDATE user SET user.sign = '' WHERE user.id ='$uid';";
	
	if(file_exists($location)) {
		
		if(unlink($location)) {
			@mysql_query($empty_file_query) or die(mysql_error());
			log_hist(11);
			header("location:".$_SERVER['PHP_SELF']);
		} 
		 else { 
			$status = "<br/><p class=\"alert alert-error span8\">Failed to delete your signature.</p>";
		}
	} 
	else { 
		@mysql_query($empty_file_query) or die(mysql_error());
		log_hist(11);
		$status = "<br/><p class=\"alert alert-error span8\">Signature that you want to delete is not exist, but the link still exist in the system.\n STORIX system remove will remove it.</p>";
	}
}	

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="<?=$_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2>MY SETTINGS</h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr valign="top" align="left">
		<td><?=$status?><br />
			<div class="span8 well">
			<label><b>CHANGE PASSWORD</b></label>
				<table border="0">
					<tr valign=top>
						<td colspan=3 align="right">&nbsp;</td></tr>
					<tr valign=top>
						<td align="right"><b>OLD PASSWORD</b>&nbsp;<font color="Red">*</font></td>
						<td>:</td>
						<td><input type = "password" size="50" name="old_pwd" id="old_pwd">
						<script language="JavaScript" type="text/javascript">
					if(document.getElementById) document.getElementById('old_pwd').focus();</script><br />
						(<b><i><?=strtolower(DEFAULT_PASS)?></i></b>), if you never change your password.</td></tr>
					<tr valign=top>
						<td align="right"><b>NEW PASSWORD</b>&nbsp;<font color="Red">*</font></td>
						<td>:</td>
						<td><input type=password size="50" name='new_pwd1'> </td></tr>
					<tr valign=top>
						<td align="right"><b>RE-TYPE NEW PASSWORD</b>&nbsp;<font color="Red">*</font></td>
						<td>:</td>
						<td><input type=password size="50" name='new_pwd2'></td></tr>
					<tr valign=top>
						<td colspan=3 align="right">&nbsp;</td></tr>
					<tr valign=top>
						<td colspan=3 align="left">
							<input type=hidden name='email' value='<?=$_SESSION['email']?>'>
							<input type="submit" class="btn-info btn-small" name="upd_pass" value="  UPDATE PASSWORD  "></td></tr>
				</table>
			</div>
		</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr valign="top" align="left">
		<td>
			<div class="span8 well">
			<label><b>PERSONAL SIGNATURE</b></label>
				<table border="0">
<?php if(!empty($old_data_array[0])){ ?>
					<tr><td align="left">
						<img src="<?=$old_data_array[0]?>" border="0" width="240" height="100">
						<input type=hidden name="location" value="<?=$old_data_array[0]?>">
					</td></tr>
					<tr><td align="left"><input type="submit" name="del_sign" value="  DELETE SIGNATURE  "></td></tr>
<?php } else { ?>
					<tr><td align="left"><input type="file" name="sign-file" size="50"/>&nbsp;<font color="Red">*</font></td></tr>
					<tr><td align="left">(150x100 px)</td></tr>
					<tr><td align="left"><input type="submit" name="upd_sign" class="btn-info btn-small" value="  UPLOAD SIGNATURE  "></td></tr>
<?php } ?>
				</table>
			</div>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
</table>		
</form>
<//-----------------CONTENT-END---------------------------------------------------//>          					
<?php include THEME_DEFAULT.'footer.php';?>