<?php $main_page_cat = "main";$main_page_pst = "left"; ?>	
<html>
<head>
<title><?=$page_title?> - <?=PRODUCT?> <?=VERSION?></title>
<link rel="stylesheet" type="text/css" media="all" href="<?=CSS_PATH?>style.css"/>
<link rel="stylesheet" type="text/css" media="all" href="<?=CSS_PATH?>main.css"/>
<link rel="shortcut icon" href="<?=IMG_PATH?>favicon.ico" type="image/x-icon" />
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >
<table border="0" cellpadding="0" cellspacing="0" width="100%" height="100%">
<?=public_notif($notify,$notify_msg)?>
<tr valign="top"><td height="30" colspan="2">
	<table border="0" width="100%">
		<tr><td valign="top" align="left" width="100"><img src="<?=IMG_PATH?>storixlogo.png" border="0" width="237" alt="STORIX"/></td>
			<td valign="top" align="left">&nbsp;</td>
			<td valign="top" align="left"><br/>Logged in as "<b><?=$_SESSION['email']?></b>" on (<?=cplday('D, d M Y H:i O',$_SESSION['timestamp'])?>)
				<br />[<a href="javascript:logout()" target="_top" >Sign Out</a>,
					   <a href="<?=SYSPATH?>settings.php" target="_top">My Settings</a>,
				   		<a href="javascript:openW('<?=SYSPATH?>about.php','About',300,400,'scrollbars=0,toolbar=0,status=0,fullscreen=no,menubar=0,resizable=0')">About</a>]</td>
			<td valign="middle" height="40"><a href="<?=COMP_URL?>"><img align="right" src="<?=IMG_PATH?>logo.gif"/></a></td>
			<td width="3">&nbsp;</td>
		</tr>
	</table>
</td></tr>
<tr><td colspan="2" height="7" bgcolor="#000066"></td></tr>
<tr><td valign="top" bgcolor="#6699cc" width="150">
		<table border="0" cellpadding="0" cellspacing="0" width="150">
  			<tr><td height="24" bgcolor="#000066" align="center"><font color="White"><?=date('D, d M Y')?></font></td></tr>
  			<tr><td height="7" bgcolor="#000066" align="center"></td></tr>
    		<tr><td height="1" bgcolor="#a7a7a7"></td></tr>
			<?=nav_menu($page_id_left, $main_page_cat, $main_page_pst)?>
 		</table>	
	</td>
	<td width="100%">
		<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
  			<tbody>
    		<tr height="7" bgcolor="#ccccff"><td width="7"></td><td></td><td width="10"></td></tr>
    		<tr><td bgcolor="#ccccff">&nbsp;</td>
      			<td align="left" valign="top">
       				<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
           				<tbody>
           				<tr><td width="3">&nbsp;</td>
                			<td align="left" valign="top">