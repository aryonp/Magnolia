<?php 
	$result = mysql_query("SELECT id, name, link, pid, permit FROM navigation ORDER BY pid, sort, name");
	$menu = array('items' => array(),
				  'parents' => array());
	while ($items = mysql_fetch_assoc($result)){
		$permit_array 	= explode(",",$items["permit"]);
		$compare_permit = in_array($_SESSION['level'],$permit_array);
		if ($compare_permit != 0) {
    		$menu['items'][$items['id']] = $items;
			$menu['parents'][$items['pid']][] = $items['id'];
		}
		else {
			echo "";
		}
	}

	function buildMenu($parent, $menu) {
   		$html = "";
   		if (isset($menu['parents'][$parent])){
      		$html .= "<ul>\n";
       		foreach ($menu['parents'][$parent] as $itemId)	{
          		if(!isset($menu['parents'][$itemId])) {
             		$html .= "<li><a href='".$menu['items'][$itemId]['link']."'><img src=\"".IMG_PATH."r-arrow.gif\" border=\"0\" width=\"13\" height=\"13\"> ".ucwords($menu['items'][$itemId]['name'])."</a></li>\n";
          		}
          		if(isset($menu['parents'][$itemId])){
             		$html .= "<li><a href='".$menu['items'][$itemId]['link']."'><img src=\"".IMG_PATH."r-arrow.gif\" border=\"0\" width=\"13\" height=\"13\"> ".ucwords($menu['items'][$itemId]['name'])."</a>";
             		$html .= "\t".buildMenu($itemId, $menu);
             		$html .= "</li> \n";
          		}
       		}
       	$html .= "</ul> \n";
   		}
   	return $html;
	}
?>
<!DOCTYPE html>
<html>
<head>
<script language="javascript" type="text/javascript" src="<?=JS_PATH?>jquery.js"></script>
<script language="javascript" type="text/javascript" src="<?=JS_PATH?>bootstrap.js"></script>
<script language="javascript" type="text/javascript" src="<?=JS_PATH?>bootstrap-tab.js"></script>
<script language="javascript" type="text/javascript" src="<?=JS_PATH?>functions.js"></script>
<link rel="stylesheet" type="text/css" media="all" href="<?=CSS_PATH?>bootstrap.css"/>
<link rel="stylesheet" type="text/css" media="all" href="<?=CSS_PATH?>bootstrap-responsive.css"/>
<link rel="stylesheet" type="text/css" media="all" href="<?=CSS_PATH?>tree.css"/>
<link rel="shortcut icon" href="<?=IMG_PATH?>favicon.ico" type="image/x-icon" />
<title><?=$page_title?> - <?=PRODUCT?> <?=VERSION?></title>
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" style='height:100%;'>
<table border="0" cellpadding="0" cellspacing="0" style='height:100%;min-hright:100%;max-height:100%;margin-top:0;margin-bottom:0;bottom:0;top:0;'>
<?=public_notif($notify,$notify_msg)?>
<tr valign="top">
	<td colspan="2">
	<table border="0" width="100%">
		<tr align="left">
			<td valign="top" width="250"><img src="<?=IMG_PATH?>storixlogo.png" border="0" width="237" alt="STORIX"/></td>
			<td valign="top"><br/>Logged in as "<b><?=$_SESSION['email']?></b>" on (<?=cplday('D, d M Y H:i O',$_SESSION['timestamp'])?>)
				<br />[<a href="javascript:logout()">Sign Out</a>,
					   <a href="<?=SYSPATH?>settings.php">My Settings</a>,
				   		<a href="javascript:openW('<?=SYSPATH?>about.php','About',300,400,'scrollbars=0,toolbar=0,status=0,fullscreen=no,menubar=0,resizable=0')">About</a>]</td>
			<td valign="middle" height="40"><a href="<?=COMP_URL?>"><img align="right" src="<?=IMG_PATH?>logo.gif"/></a>&nbsp;&nbsp;</td>
		</tr>
	</table>
</td></tr>
<tr><td colspan="2" height="7" bgcolor="#000066"></td></tr>
<tr><td valign="top" bgcolor="#6699cc" width="170">
		<table border="0" cellpadding="0" cellspacing="0" width="170">
  			<tr><td height="24" bgcolor="#000066" align="center">&nbsp;</td></tr>
  			<tr><td height="7" bgcolor="#000066" align="center"></td></tr>
    		<tr><td height="1" bgcolor="#a7a7a7"></td></tr>
			<tr><td><div class="menuku"><?php echo buildMenu(1,$menu);?></div></td></tr>
 		</table>	
	</td>
	<td width="100%">
		<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
  			<tbody>
    		<tr height="7" bgcolor="#ccccff">
				<td width="7"></td>
				<td></td>
				<td width="10"></td></tr>
    		<tr><td bgcolor="#ccccff">&nbsp;</td>
      			<td align="left" valign="top">
       				<table border="0" cellpadding="0" cellspacing="0" height="100%" width="100%">
           				<tbody>
           				<tr><td width="3">&nbsp;</td>
                			<td align="left" valign="top">