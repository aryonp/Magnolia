<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();
	
$page_title 	= "Request Page";
$page_id_left 	= "3";
$category_page 	= "main";
chkSecurity($page_id_left);

$search = ($_GET['search']!="")?mysql_real_escape_string(trim($_GET['search'])):"";
$param 	= ($_GET['param']!="")?trim($_GET['param']):"";
$fltr 	= ($_SESSION['level'] <= 3)?"":"r.user_id_fk = '".$_SESSION['uid']."' AND";

switch($param){
	case "id":
			$where 	= "WHERE $fltr r.del = '0' AND r.id = '$search'";
		break;
	case "fno":
			$where 	= "WHERE $fltr r.del = '0' AND r.code LIKE '%$search%'";
		break;
	case "req":
			$where 	= "WHERE $fltr r.del = '0' AND CONCAT(u.fname,' ',u.lname) LIKE '%$search%'";
		break;
	default:
			$where 	= "WHERE $fltr r.del = '0'";
}

$query 	= "SELECT r.id as id, 
                  r.code as file, 
                  CONCAT(u.fname,' ',u.lname) AS fullname, 
                  r.req_type as type, 
                  r.req_date as rdate,
                  r.auth_date AS l2date, 
                  r.appr_date AS adate, 
                  r.status as status
			FROM req r 
            LEFT JOIN user u ON (u.id = r.user_id_fk) 
            $where 
            ORDER BY r.req_date DESC ";

$pagingResult = new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";

if (isset($_GET['did'])){
	$did 	= trim($_GET['did']);
	$chk_q 	= "SELECT r.user_id_fk as id, r.status as status, r.del 
			  FROM req r 
			  WHERE r.id = '$did' ;";
	$chk_SQL = @mysql_query($chk_q) or die(mysql_error());
	
	$chk_array = mysql_fetch_array($chk_SQL, MYSQL_ASSOC);
	if ($chk_array["id"] == $_SESSION['uid'] AND $chk_array["status"] == "pending" AND $chk_array["del"] == 0) {
		$del 	= "UPDATE req SET del = '1' WHERE id = '$did';";
		@mysql_query($del) or die(mysql_error());
		$del_d 	= "UPDATE req_det SET del = '1' WHERE req_id_fk = '$did';";
		@mysql_query($del_d) or die(mysql_error());
		log_hist("70",$did);
		header("location:$this_page");
		exit();		
	} 
	
	else {
		$status ="<p class=\"redbox\">You don't have privilege to delete the request data or the data status doesn't allowed to be deleted</p>";
	}
}

include THEME_DEFAULT.'header.php';?>              			
<//-----------------CONTENT-START-------------------------------------------------//>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h3>REQUEST HOME</h3></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td align="center"><form action="" method="GET">SEARCH&nbsp;&nbsp;:&nbsp;&nbsp;
		<input type="text" name="search" size=50 value="<?=$search?>"/>&nbsp;&nbsp;
		BY:&nbsp;&nbsp;
		<select name="param" class="input-small">
			<option value="id">ID</option>
			<option value="fno">FILE NO.</option>
			<option value="req">REQUESTER</option>
		</select>
		<input type="submit" name="sbutton" value="  GO  " class="btn-small btn-info" />
		</form></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><a href = "./req.php" class="btn">CREATE NEW REQUEST</a></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
        <tr><td>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
            	<tr valign="middle"> 
                 	<th width="25" align="left">&nbsp;<b>NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>FILE NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>REQUESTER</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>REQ. TYPE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>REQ. DATE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>L2 DATE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>IT APPR.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>STATUS</b>&nbsp;</td>
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
					<td align="left">&nbsp;<?=$count?>.</td>
					<td align="left">&nbsp;#<?=$array["id"];?></td>
					<td align="left">&nbsp;<?=$array["file"];?></td>
					<td align="left">&nbsp;<?=ucwords($array["fullname"])?></td>
					<td align="left">&nbsp;<?=ucwords($array["type"])?></td>
					<td align="left">&nbsp;<?=($array["rdate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["rdate"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["l2date"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["l2date"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["adate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["adate"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=strtoupper($array["status"])?></td>
					<td align="center" width="25"><a title="View" href="./req_det.php?id=<?=$array["id"]?>"><img src="<?=IMG_PATH?>view.png"></a></td>
					<td align="center" width="25"><a title="Print" href="javascript:openW('./print_req.php?id=<?=$array["id"]?>','Print_Request',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png"></a></td>
					<td align="center" width="25">
					<?php if ($array["status"] == "pending" OR ($_SESSION['level'] <= 7 AND $array["status"] == "authorized")) { ?>
					<a title="Delete" href="<?=$this_page?>" onclick="return confirmBox(this,'del','request ID #<?=$array["id"]?>', '<?=$array["id"]?>')"><img src="<?=IMG_PATH?>delete.png"></a>
					<?php } else {?><img src="<?=IMG_PATH?>d_delete.png"><?php } ?>
					</td>
				</tr>
				
			<?php	$count++;  
				}
			} else {?>
				
				<tr><td colspan="12" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				
		<?php } ?></tbody>
			</table></td></tr>
        <tr><td><?=$pagingResult->pagingMenu();?></td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td><a href = "./req.php" class="btn">CREATE NEW REQUEST</a></td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>