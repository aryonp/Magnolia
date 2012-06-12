<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "RFA Archive";
$page_id_left 	= "10";
$page_id_right 	= "21";
$category_page 	= "archive";
chkSecurity($page_id_right);

$search = ($_GET['search']!="")?mysql_real_escape_string(trim($_GET['search'])):"";
$param 	= ($_GET['param']!="")?trim($_GET['param']):"";

switch($param){
	case "id":
		$where = "WHERE r.del = '0' AND r.id = '$search%'";
		break;
	case "req":
		$where = "WHERE r.del = '0' AND CONCAT(u.fname,' ',u.lname) LIKE '%$search%'";
		break;
	case "fno":
		$where = "WHERE r.del = '0' AND r.code LIKE '%$search%'";
		break;
	case "content":
		$where = "WHERE r.del = '0' AND CONCAT(rd.item,' ',rd.purpose,' ',rd.spec_notes) LIKE '%$search%'";
		break;
	default:
		$where = "WHERE r.del = '0'";
}

$query = "SELECT r.id, r.code, CONCAT(u.fname,' ',u.lname) AS rname, CONCAT(a.fname,' ',a.lname) AS aname, b.name as bname, r.file, r.date AS rdate, r.appr_date AS adate, r.status 
          FROM rfa r 
          LEFT JOIN user u ON (u.id = r.user_id_fk) 
          LEFT JOIN user a ON (a.id = r.appr_id_fk) 
          LEFT JOIN branch b ON (b.id = r.branch_id_fk)
          LEFT JOIN rfa_det rd ON (rd.rfa_id_fk = r.id) 
          $where 
          ORDER BY r.id DESC ";

$pagingResult 	= new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page 		= $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td align="center"><form action="" method="GET">SEARCH RFA ARCHIVE&nbsp;&nbsp;:&nbsp;&nbsp;
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
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
				<tr valign="middle"> 
            		<th width="25"><b>&nbsp;NO.</b>&nbsp;</td>
            		<th width="*"><b>&nbsp;ID</b>&nbsp;</td>
                 	<th width="*"><b>&nbsp;FILE NO.</b>&nbsp;</td>
                 	<th width="*"><b>&nbsp;REQ BY</b>&nbsp;</td>
                 	<th width="*"><b>&nbsp;APPR BY</b>&nbsp;</td>
                 	<th width="*"><b>&nbsp;FOR BRANCH</b>&nbsp;</td>
                 	<th width="*"><b>&nbsp;REQ. DATE</b>&nbsp;</td>
                 	<th width="*"><b>&nbsp;APPR. DATE</b>&nbsp;</td>
                 	<th width="*"><b>&nbsp;STATUS</b>&nbsp;</td>
                 	<th align="center" colspan="3">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($rfa_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
				<tr valign="top">
					<td >&nbsp;<?=$count?>.&nbsp;</td>
					<td >&nbsp;<?=($rfa_list_array["id"])?"#".$rfa_list_array["id"]:"-";?>&nbsp;</td>
					<td >&nbsp;<?=($rfa_list_array["code"])?$rfa_list_array["code"]:"-";?>&nbsp;</td>
					<td >&nbsp;<?=($rfa_list_array["rname"])?ucwords($rfa_list_array["rname"]):"-";?>&nbsp;</td>
					<td >&nbsp;<?=($rfa_list_array["aname"])?ucwords($rfa_list_array["aname"]):"-";?>&nbsp;</td>
					<td >&nbsp;<?=($rfa_list_array["bname"])?ucwords($rfa_list_array["bname"]):"-";?>&nbsp;</td>
					<td >&nbsp;<?=($rfa_list_array["rdate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$rfa_list_array["rdate"]):"-";?>&nbsp;</td>
					<td >&nbsp;<?=($rfa_list_array["adate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$rfa_list_array["adate"]):"-";?>&nbsp;</td>
					<td >&nbsp;<?=strtoupper($rfa_list_array["status"])?>&nbsp;</td>
					<td align="center" width="25"><a title="Update File" href="./rfa_arc_det.php?id=<?=$rfa_list_array["id"]?>"><img src="<?=IMG_PATH?>edit.png"></a></td>
					<td align="center" width="25">
			<?php if($rfa_list_array["file"]) { ?><a title="Attachment" href="<?=$rfa_list_array["file"]?>" target="_blank"><img src="<?=IMG_PATH?>attch.gif"></a><?php } else {?>-<?php } ?></td>
					<td align="center" width="25"><a title="Print" href="javascript:openW('./print_rfa.php?id=<?=$rfa_list_array["id"]?>','Print_RFA',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png"></a></td>
				</tr>
			<?php	$count++;  
			}
           } else { ?>
				<tr><td colspan="12" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php } ?></tbody>
			</table>
		</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>&nbsp;</td></tr>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>