<?php 
/* -----------------------------------------------------
 * File name	: about.php								
 * Created by 	: aryonp@gmail.com		
 * -----------------------------------------------------				            
 * Purpose		: About page											                 			
 * -----------------------------------------------------
 */

require_once 'init.php'; 

?>
<!DOCTYPE html>
<html>
<head>
<title>About</title>
<link rel="stylesheet" type="text/css" media="all" href="<?=CSS_PATH?>bootstrap.css" />
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" oncontextmenu="return false">
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" width="300" height="400" background="<?=IMG_PATH?>about1.jpg">
	<tr height="*"><td>&nbsp;</td></tr>
	<tr height="80"><td align="center">&nbsp;</td></tr>
	<tr height="80"><td valign="top" align="left">
		&nbsp;&copy  <?=date('Y') == '2007'?'2007':'2007 - '.date('Y');?>. All rights reserved.<br/>
		eRequest Portal and the eRequest Portal logos are trademarks of their respective owners. 
		Please read the Credits page for more information.</td></tr>
	<tr height="40"><td align="center"><input type="button" class="btn-inverse btn-small" name="close" value="       CLOSE       " onclick="javascript:window.close()" ></td></tr>
</table>	
<//-----------------CONTENT-END-------------------------------------------------//>
<script language="javascript" type="text/javascript" src="<?=JS_PATH?>functions.js"></script>
</body>
</html>