<?php 
Header('Content-Type: vnd-ms.excel');
Header('Content-Disposition: attachment;filename='.$filename);
?>
<html>
<head>
<title><?=$page_title?> - <?=PRODUCT?> <?=VERSION?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/> 
</head>
<body>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr><td width="30">&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td><table border="0" cellpadding="0" cellspacing="0" width="100%">
			</table>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr><td bgcolor="#000066" align="center" height="20" valign="middle"><b><font size="3" color="#FFFFFF"><?=strtoupper($page_title);?></font></b></td></tr>
			</table>
			<br>