<?php
/* -----------------------------------------------------
 * File name	: login.php	
 * Created by 	: M. Aryo N. Pratama				
 * -----------------------------------------------------			
 * Purpose		: Do login using auth.class.php	
 * -----------------------------------------------------						                 			
 */

require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once CONT_PATH.'auth.class.php';
session_start();

if (isset($_POST['submit'])) {
	$login = new Auth;
	$login->setLogin($_POST['email'],$_POST['password']);
	$login->getLogin();
	if(count($login->_errors) > 0) {
		$err_msg ="<ul>";
		foreach($login->_errors as $errors) { 
			$err_msg .= "<li>$errors</li>"; 
		}
		$err_msg .="</ul>";
		$status = "<div class=\"alert alert-error span6\">$err_msg</div>";
	}
}

?>
<!DOCTYPE html>
<html>
	<head>
		<script language="javascript" type="text/javascript" src="<?=JS_PATH?>functions.js"></script>
		<link rel="stylesheet" type="text/css" href="<?=CSS_PATH?>bootstrap.css">
		<link rel="shortcut icon" href="<?=IMG_PATH?>favicon.ico" type="image/x-icon" />
		<title><?=PRODUCT?> <?=VERSION?> :: Login Page</title>
	</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<?=public_notif($notify,$notify_msg)?>
<noscript><center><div class="alert" align="center">You must activate your Javascript support on your browser<br>because some of facilities in this portal are using Javascript</div></center></noscript>
	<form action="" method="POST" class="form">
		<div class="container-fluid">
			<div class="row-fluid">
				<?=$status?><br/>
				<div class="span6">
					<div class="row-fluid">
						<img src="<?=IMG_PATH?>storixlogo.png"><br/><br/>
					</div>
					<div class="row-fluid">
						<div class="well">
							<label name="email">Email</label>
							<input type="text" name="email" id="email" size="40">
							<script language="JavaScript" type="text/javascript">if(document.getElementById) document.getElementById('email').focus();</script>
							<label name="password">Password</label>
							<input name="password" type="password" size="40"><br/><br/>
							[<a href="javascript:openW('./forgot_pass.php','Forgot_Pass',500,230,'scrollbars=1,toolbar=0,status=0,fullscreen=no,menubar=0,resizable=0')">Forgot/Reset Password</a>]<br/><br/>
							<input type="submit" name="submit" class="btn-info" value="  LOGIN  ">&nbsp;&nbsp;
							<input type="reset" class="btn-info" value="  RESET  ">
						</div>
					</div>
					<div class="row-fluid" style="text-align:center;">						
						<a href="javascript:openW('./credits.php','About',350,500,'toolbar=0,status=0,fullscreen=no,menubar=0, scrollbars=1')"><?=PRODUCT?> Project</a>&nbsp;&copy&nbsp;<?=date('Y') == '2007'?'2007':'2007 - '.date('Y');?>. All rights reserved
					</div>
				</div>
			</div>
		</div>	
	</form>
</body>
</html>