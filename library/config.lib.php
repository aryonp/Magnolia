<?php 
/* -----------------------------------------------------
 * File name	: config.inc.php								
 * Created by 	: aryonp@gmail.com		
 * -----------------------------------------------------				            
 * Purpose		: Define configuration here											                 			
 * -----------------------------------------------------
 */

$db = @mysql_connect(DB_HOST, DB_USER, DB_PASS);
if (!$db){
	echo "<script language=\"Javascript\">alert(\"Unable to connect to DB server\")</script>";
	exit();
}
else { @mysql_select_db(DB_USE, $db); }

?>