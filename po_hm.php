<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "PO Home";
$page_id_left 	= "8";
$category_page 	= "main";
chkSecurity($page_id_left);

$search = ($_GET['search']!="")?mysql_real_escape_string(trim($_GET['search'])):"";
$param 	= ($_GET['param']!="")?trim($_GET['param']):"";

switch($param){
	case "id":
		$query  ="SELECT p.id, p.po_nbr, p.kurs as curr, u.id AS uid, CONCAT(u.fname,' ',u.lname) AS rname, CONCAT(a.fname,' ',a.lname) AS aname, v.name as vname, p.date as pdate, p.authdate AS adate,p.status, p.project 
          		  FROM po p 
          	      lEFT JOIN user u ON (u.id = p.user_id_fk) 
          		  LEFT JOIN user a ON (a.id = p.auth_id_fk) 
          		  LEFT JOIN vdr v ON (v.id = p.vdr_id_fk) 
          		  WHERE p.del = '0' AND p.id = '$search'
          		  ORDER BY p.id DESC ";
		break;
	case "pno":
		$query  ="SELECT p.id, p.po_nbr, p.kurs as curr, u.id AS uid, CONCAT(u.fname,' ',u.lname) AS rname, CONCAT(a.fname,' ',a.lname) AS aname, v.name as vname, p.date as pdate, p.authdate AS adate ,p.status, p.project 
          		  FROM po p 
          	      lEFT JOIN user u ON (u.id = p.user_id_fk) 
          		  LEFT JOIN user a ON (a.id = p.auth_id_fk) 
          		  LEFT JOIN vdr v ON (v.id = p.vdr_id_fk) 
          		  WHERE p.del = '0' AND p.po_nbr LIKE '%$search%'
          		  ORDER BY p.id DESC ";
		break;
	case "req":
		$query  ="SELECT p.id, p.po_nbr, p.kurs as curr, u.id AS uid, CONCAT(u.fname,' ',u.lname) AS rname, CONCAT(a.fname,' ',a.lname) AS aname, v.name as vname, p.date as pdate, p.authdate AS adate,p.status, p.project 
          		  FROM po p 
          	      lEFT JOIN user u ON (u.id = p.user_id_fk) 
          		  LEFT JOIN user a ON (a.id = p.auth_id_fk) 
          		  LEFT JOIN vdr v ON (v.id = p.vdr_id_fk) 
          		  WHERE p.del = '0' AND CONCAT(u.fname,' ',u.lname) LIKE '%$search%'
          		  ORDER BY p.id DESC ";
		break;
	case "content":
		$query  ="SELECT p.id, p.po_nbr, p.kurs as curr, u.id AS uid, CONCAT(u.fname,' ',u.lname) AS rname, CONCAT(a.fname,' ',a.lname) AS aname, v.name as vname, p.date as pdate, p.authdate AS adate,p.status, p.project 
          		  FROM po p 
          	      lEFT JOIN user u ON (u.id = p.user_id_fk) 
          		  LEFT JOIN user a ON (a.id = p.auth_id_fk) 
          		  LEFT JOIN vdr v ON (v.id = p.vdr_id_fk) 
          		  LEFT JOIN po_det pd ON (pd.po_id_fk = p.id)
          		  WHERE p.del = '0' AND pd.description LIKE '%$search%'
          		  ORDER BY p.id DESC ";
		break;
	case "vendor":
		$query  ="SELECT p.id, p.po_nbr, p.kurs as curr, u.id AS uid, CONCAT(u.fname,' ',u.lname) AS rname, CONCAT(a.fname,' ',a.lname) AS aname, v.name as vname, p.date as pdate, p.authdate AS adate,p.status, p.project 
          		  FROM po p 
          	      lEFT JOIN user u ON (u.id = p.user_id_fk) 
          		  LEFT JOIN user a ON (a.id = p.auth_id_fk) 
          		  LEFT JOIN vdr v ON (v.id = p.vdr_id_fk) 
          		  WHERE p.del = '0' AND v.name LIKE '%$search%'
          		  ORDER BY p.id DESC ";
		break;
	default:
		$query  ="SELECT p.id, p.po_nbr, p.kurs as curr, u.id AS uid, CONCAT(u.fname,' ',u.lname) AS rname, CONCAT(a.fname,' ',a.lname) AS aname, v.name as vname, p.date as pdate, p.authdate AS adate,p.status, p.project 
          		  FROM po p 
          	      lEFT JOIN user u ON (u.id = p.user_id_fk) 
          		  LEFT JOIN user a ON (a.id = p.auth_id_fk) 
          		  LEFT JOIN vdr v ON (v.id = p.vdr_id_fk) 
          		  WHERE p.del = '0' 
          		  ORDER BY p.id DESC ";
}

$pagingResult = new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";

if (isset($_GET['did'])){
	$did 		= trim($_GET['did']);
	$chk_q 		= "SELECT p.user_id_fk as uid, p.status as status FROM po p WHERE p.id ='$did';";
	$chk_SQL 	= @mysql_query($chk_q) or die(mysql_error());
	$chk_array 	= mysql_fetch_array($chk_SQL,MYSQL_ASSOC);
	if ($chk_array["uid"] == $_SESSION['uid'] AND $chk_array["status"] == "pending") {
			$del  = "UPDATE po SET del = '1' WHERE id ='$did';";
			@mysql_query($del) or die(mysql_error());
			log_hist(89,$did);
			header("location:$this_page");
			exit();
	} else {
		$status ="<p class=\"redbox\">You don't have privilege to delete the PO data or the data status doesn't allowed to be deleted</p>";
	}
}
include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>[<a href = "./po.php">CREATE NEW PO</a>]&nbsp;[<a href = "./po_org.php">ORGANIZE PO</a>]</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td align="center"><form action="" method="GET">SEARCH PO&nbsp;&nbsp;:&nbsp;&nbsp;
		<input type="text" name="search" size=50 value="<?=$search?>"/>&nbsp;&nbsp;
		BY:&nbsp;&nbsp;
		<select name="param" class="input-small">
			<option value="id">ID</option>
			<option value="pno">PO NO.</option>
			<option value="req">REQUESTER</option>
			<option value="content">CONTENT</option>
			<option value="vendor">VENDOR</option>
		</select>
		<input type="submit" name="sbutton" value="  GO  " class="btn-small btn-info" />
		</form></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
				<tr align="left" valign="top"> 
            		<th width="25">&nbsp;<b>NO.</b>&nbsp;</td>
            		<th width="*">&nbsp;<b>ID.</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>PO NO.</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>REQ BY</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>AUTH BY</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>VENDOR</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>CURR.</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>PO DATE</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>AUTH. DATE</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>STATUS</b>&nbsp;</td>
                 	<th colspan="3" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
			<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($array["id"])?"#".$array["id"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["po_nbr"])?$array["po_nbr"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["rname"])?ucwords($array["rname"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["aname"])?ucwords($array["aname"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["vname"])?ucwords($array["vname"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["curr"])?strtoupper($array["curr"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["pdate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["pdate"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["adate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["adate"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=strtoupper($array["status"])?>&nbsp;</td>
					<td align="center" width="25"><a title="View" href="./po_det.php?id=<?=$array["id"]?>">
							<img src="<?=IMG_PATH?>view.png"></a></td>
					<td align="center" width="25"><a title="Print" href="javascript:openW('./print_po.php?id=<?=$array["id"]?>','Print_PO',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');">
							<img src="<?=IMG_PATH?>print.png"></a></td>
					<td align="center" width="25">
<?php	if ($array["uid"] == $_SESSION['uid'] AND $array["status"] == "pending") { ?>
					<a title="Delete" href="<?=$this_page?>" onclick="return confirmBox(this,'del','PO ID #<?=$array["id"]?>','<?=$array["id"]?>')">
							<img src="<?=IMG_PATH?>delete.png"></a>
<?php	} else { ?><img src="<?=IMG_PATH?>d_delete.png">
<?php	}		?>
						</td>
			</tr>
<?php			$count++;  
				}
  	} else {
?>
				<tr><td colspan="13" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php } ?>	</tbody>
			</table>
		</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[<a href = "./po.php">CREATE NEW PO</a>]&nbsp;[<a href = "./po_org.php">ORGANIZE PO</a>]</td></tr>
		<tr><td>&nbsp;</td></tr>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>