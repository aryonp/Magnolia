<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Request for Approval Page";
$page_id_left	= "5";
$category_page 	= "main";
chkSecurity($page_id_left);

$fltr  	= ($_SESSION["level"] <= 3)?"":"r.user_id_fk  = '".$_SESSION['uid']."' AND";		
$search = ($_GET['search']!="")?mysql_real_escape_string(trim($_GET['search'])):"";
$param 	= ($_GET['param']!="")?trim($_GET['param']):"";

switch($param){
	case "id":
		$where = "WHERE $fltr r.del = '0' AND r.id = '$search%'";
		break;
	case "req":
		$where = "WHERE $fltr r.del = '0' AND CONCAT(u.fname,' ',u.lname) LIKE '%$search%'";
		break;
	case "fno":
		$where = "WHERE $fltr r.del = '0' AND r.code LIKE '%$search%'";
		break;
	case "content":
		$where = "WHERE $fltr r.del = '0' AND CONCAT(rd.item,' ',rd.purpose,' ',rd.spec_notes) LIKE '%$search%'";
		break;
	default:
		$where = "WHERE $fltr r.del = '0'";
}

$query = "SELECT DISTINCT(r.id) AS id, r.code, CONCAT(u.fname,' ',u.lname) AS rname, CONCAT(a.fname,' ',a.lname) AS aname, b.name as bname, r.file, r.date AS rdate, r.appr_date AS adate, r.status 
          FROM rfa r 
          LEFT JOIN user u ON (u.id = r.user_id_fk) 
          LEFT JOIN user a ON (a.id = r.appr_id_fk) 
          LEFT JOIN branch b ON ( b.id = r.branch_id_fk) 
          INNER JOIN rfa_det rd ON (rd.rfa_id_fk = r.id)
          $where 
          GROUP BY r.id DESC ";

$pagingResult 	= new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page 		= $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";

if (isset($_GET['did'])){
	$did 		= trim($_GET['did']);
	$chk_q 		= "SELECT r.user_id_fk as id, r.status, r.file FROM rfa r WHERE r.id ='$did';";
	$chk_SQL 	= @mysql_query($chk_q) or die(mysql_error());
	$chk_array 	= mysql_fetch_array($chk_SQL, MYSQL_ASSOC);
	
	if ($chk_array["id"] == $_SESSION['uid'] AND $chk_array["status"] == "pending") {
		$select_req_det_stat_query ="SELECT req_det_id_fk FROM rfa_det WHERE rfa_id_fk='$did';" ;
		$select_req_det_stat_SQL = @mysql_query($select_req_det_stat_query) or die(mysql_error());
		
		if(mysql_num_rows($select_req_det_stat_SQL) >= 1) {
			while($select_req_det_stat_array = mysql_fetch_array($select_req_det_stat_SQL)){
				$update_req_det_stat_query = "UPDATE req_det SET status ='accepted' WHERE id ='".$select_req_det_stat_array[0]."';";
				@mysql_query($update_req_det_stat_query) or die(mysql_error());
			}
			
		}
		$del1 = "UPDATE rfa r SET r.del = '1' WHERE r.id ='$did';";
		@mysql_query($del1) or die(mysql_error());
		$del2 = "UPDATE rfa_det r SET r.del = '1' WHERE r.rfa_id_fk ='$did';";
		@mysql_query($del2) or die(mysql_error());
		log_hist("82",$did);
		header("location:$this_page");
		exit();
	} else {
		$status ="<p class=\"redbox\">You don't have privilege to delete the RFA data or the data status doesn't allowed to be deleted</p>";
	}
}
include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>REQUEST FOR APPROVAL BOX</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td align="center"><form action="" method="GET">SEARCH RFA&nbsp;&nbsp;:&nbsp;&nbsp;
		<input type="text" name="search" size=50 value="<?=$search?>"/>&nbsp;&nbsp;
		BY:&nbsp;&nbsp;
		<select name="param" class="input-small">
			<option value="id">ID</option>
			<option value="fno">FILE NO.</option>
			<option value="req">REQUESTER</option>
			<option value="content">CONTENT</option>
		</select>
		<input type="submit" name="sbutton" value="  GO  " class="btn-small btn-info" />
		</form></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[<a href = "./rfa_auto.php">GENERATE RFA FROM REQUEST</a>]&nbsp;&nbsp;[<a href = "./rfa_manual.php">MANUAL RFA</a>]</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>	
				<tr align="left" valign="top"> 
            		<th width="25">&nbsp;<b>NO.</b>&nbsp;</td>
            		<th width="*">&nbsp;<b>ID</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>FILE NO.</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>FOR BRANCH</b>&nbsp;</td>
                 	<th width="*"><b>&nbsp;REQ. DATE</b>&nbsp;</td>
                 	<th width="*"><b>&nbsp;APPR. DATE</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>STATUS</b>&nbsp;</td>
                 	<th colspan="3" align="center" colspan="2">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
			<tr align="left" valign="top">
					<td >&nbsp;<?=$count?>.&nbsp;</td>
					<td >&nbsp;<?=($array["id"])?"#".$array["id"]:"-";?>&nbsp;</td>
					<td >&nbsp;<?=($array["code"])?$array["code"]:"-";?>&nbsp;</td>
					<td >&nbsp;<?=ucwords($array["bname"])?>&nbsp;</td>
					<td >&nbsp;<?=($array["rdate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["rdate"]):"-";?>&nbsp;</td>
					<td >&nbsp;<?=($array["adate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["adate"]):"-";?>&nbsp;</td>
					<td >&nbsp;<?=strtoupper($array["status"])?>&nbsp;</td>
					<td align="center" width="25"><a title="View" href="./rfa_det.php?id=<?=$array["id"]?>"><img src="<?=IMG_PATH?>view.png"></a></td>
					<td align="center" width="25"><a title="Print" href="javascript:openW('./print_rfa.php?id=<?=$array["id"]?>','Print_RFA',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png"></a></td>
					<td align="center" width="25">
					<?php if($array["status"] == "pending") {?><a title="Delete" href="<?=$this_page?>" onclick="return confirmBox(this,'del', 'RFA ID #<?=$array["id"]?>', '<?=$array["id"]?>')"><img src="<?=IMG_PATH?>delete.png"></a>
					<?php } else { ?><img src="<?=IMG_PATH?>d_delete.png"><?php } ?>
					</td>
					</tr>
<?php		$count++;  
			}
   } else {?>
				<tr><td colspan="10" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php } ?>
				</tbody>
			</table>
		</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[<a href = "./rfa_auto.php">GENERATE RFA FROM REQUEST</a>]&nbsp;&nbsp;[<a href = "./rfa_manual.php">MANUAL RFA</a>]</td></tr>
		<tr><td>&nbsp;</td></tr>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>