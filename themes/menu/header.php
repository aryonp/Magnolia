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
             		$html .= "<li><a href='".$menu['items'][$itemId]['link']."'><img src=\"".IMG_PATH."arrow.gif\" border=\"0\" width=\"10\" height=\"10\"> ".ucwords($menu['items'][$itemId]['name'])."</a></li>\n";
          		}
          		if(isset($menu['parents'][$itemId])){
             		$html .= "<li><a href='".$menu['items'][$itemId]['link']."'><img src=\"".IMG_PATH."arrow.gif\" border=\"0\" width=\"10\" height=\"10\"> ".ucwords($menu['items'][$itemId]['name'])."</a>";
             		$html .= "\t".buildMenu($itemId, $menu);
             		$html .= "</li> \n";
          		}
       		}
       	$html .= "</ul> \n";
   		}
   	return $html;
	}
?>
<html>
<head>
<title><?=$page_title?> - <?=PRODUCT?> <?=VERSION?></title>
<link rel="stylesheet" type="text/css" media="all" href="<?=CSS_PATH?>style.css"/>
<link rel="stylesheet" type="text/css" media="all" href="<?=CSS_PATH?>main.css"/>
<style type="text/css">
.menuku ul {
	margin: 0;
	padding: 0;
	list-style: none;
	width: 150px; /* Width of Menu Items */
	border-bottom: 1px solid #ccc;
	}

.menuku ul li {
	position: relative;
	}
	
.menuku li ul {
	position: absolute;
	left: 149px; /* Set 1px less than menu width */
	top: 0;
	display: none;
	
	}

/* Styles for Menu Items */

.menuku ul li a {
	display: block;
	text-decoration: none;
	color: #777;
	background: #fff; 
	
	/* IE6 Bug */
	
	padding: 5px;
	border: 1px solid #ccc;
	border-bottom: 0;
	}

/* Fix IE. Hide from IE Mac \*/

* .menuku html ul li { float: left; height: 1%; }
* .menuku html ul li a { height: 1%; }

/* End */

.menuku ul li a:hover { color: #E2144A; background: #f9f9f9;background-color:#ff9;font-weight:bold;} /* Hover Styles */
		
.menuku li ul li a { padding: 5px 5px; } /* Sub Menu Styles */
		
.menuku li:hover ul, li.over ul { display: block; background-color:#ff9;} /* The magic */
</style>
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
			<tr><td><div class="menuku"><?php echo buildMenu(1,$menu);?></div></td></tr>
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