<?php 
/* -----------------------------------------------------
 * File name	: credits.php								
 * Created by 	: aryonp@gmail.com	
 * -----------------------------------------------------				            
 * Purpose		: Credits and Agreement.											                 			
 * -----------------------------------------------------
 */

require_once 'init.php'; 

?>
<!DOCTYPE html>
<html>
<head>
<title><?=PRODUCT?> <?=VERSION?></title>
<link rel="stylesheet" type="text/css" media="all" href="<?=CSS_PATH?>bootstrap.css" />
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" oncontextmenu="return false">
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="0" cellspacing="0" width ="100%" >
	<tr><td align="top" height="60"><img src="<?=IMG_PATH?>storixlogo.png"></td></tr>
	<tr><td align="top" height="10">&nbsp;</td></tr>
	<tr><td height="20" class="td_style_title">&nbsp;<b>STORIX Credits and Agreement Policy</b></td></tr>
	<tr><td align="top" align="center" height="200">
		<center>STORIX code was re-written from scratch by M. Aryo N. Pratama in 2008 using PHP Framework known as "Proto-M".<br/>
		By using STORIX, directly you are acknowledge and agree to the terms below.<br/><br/>
		<a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/">
		<img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-nc-nd/3.0/88x31.png" /></a><br /><br />
		<span xmlns:dct="http://purl.org/dc/terms/" property="dct:title">Proto-M</span> by <a xmlns:cc="http://creativecommons.org/ns#" href="http://www.halilintar.org" property="cc:attributionName" rel="cc:attributionURL">M. Aryo N. Pratama</a> is licensed under a <a rel="license" href="http://creativecommons.org/licenses/by-nc-nd/3.0/">Creative Commons Attribution-NonCommercial-NoDerivs 3.0 Unported License</a>.<br />Based on a work at <a xmlns:dct="http://purl.org/dc/terms/" href="http://www.halilintar.org" rel="dct:source">www.halilintar.org</a>.
	    </center>
	</td></tr>
	<tr><td height="20" class="td_style_title">&nbsp;</td></tr>
	<tr height="40"><td align="center"><input type="button" class="btn-inverse btn-small" name="close" value="      CLOSE      " onclick="javascript:window.close()" ></td></tr>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<script language="javascript" type="text/javascript" src="<?=JS_PATH?>functions.js"></script>
</body>
</html>