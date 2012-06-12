<?php
/* -----------------------------------------------------
 * File name	: logout.php	
 * Created by 	: M. Aryo N. Pratama				
 * -----------------------------------------------------			
 * Purpose		: Do logout using auth.class.php	
 * -----------------------------------------------------						                 			
 */

require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once CONT_PATH.'auth.class.php';

$logout = new Auth;

$logout->doLogout();

if(count($logout->_errors) > 0) {
	
		$err_msg ="<ul>";
		
		foreach($logout->_errors as $errors) { 
			$err_msg .= "<li>$errors</li>"; 
		}
		
		$err_msg .="</ul>";
		$status = "<div class=\"redbox\">$err_msg</div>";
}

echo $status;

?>
<html>
	<head>
		<title>&copy 2008 - Aryo</title>
	</head>
<body>
</body>
</html>