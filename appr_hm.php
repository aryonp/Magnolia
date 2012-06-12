<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		="Approval Page";
$page_id_left 	= "6";
$page_id_right 	= "";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

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

$query = "SELECT r.id, r.code, CONCAT(u.fname,' ',u.lname) AS rname, CONCAT(a.fname,' ',a.lname) AS aname, b.name as bname, r.file, r.date AS rdate, r.appr_date AS adate, r.status, v.name AS vname 
          FROM rfa r 
          LEFT JOIN user u ON (u.id = r.user_id_fk) 
          LEFT JOIN user a ON (a.id = r.appr_id_fk) 
          LEFT JOIN branch b ON ( b.id = r.branch_id_fk) 
          LEFT JOIN rfa_det rd ON (rd.rfa_id_fk = r.id)
          LEFT JOIN vdr v ON (v.id = rd.vdr_id_fk)
          $where 
          ORDER BY r.id DESC ";

$pagingResult 	= new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page 		= $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();


include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>APPROVAL HOME</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
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
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            <tr align="left" valign="middle">
            	<th width="25"><b>&nbsp;NO.</b></td>
                <th width="*"><b>&nbsp;FILE NO.</b></td>
                <th width="*"><b>&nbsp;REQ BY</b></td>
                <th width="*"><b>&nbsp;FOR BRANCH</b></td>
                <th width="*"><b>&nbsp;REQ. DATE</b>&nbsp;</td>
                <th width="*"><b>&nbsp;APPR. DATE</b>&nbsp;</td>
                <th width="*"><b>&nbsp;STATUS</b></td>
                <th width="*" align="center" colspan="2"><b>&nbsp;CMD</b></td>
			</tr>
			</thead>
			<tbody>
<?php 
   if($pagingResult->getPageRows()>= 1) {	
		$count = $pagingResult->getPageOffset() + 1;
		$result = $pagingResult->getPageArray();
		while ($rfa_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
			<tr align="left" valign="top">
				<td >&nbsp;<?=$count?>.</td>
				<td>&nbsp;<?=($rfa_list_array["code"])?$rfa_list_array["code"]:"&nbsp; - ";?></td>
				<td>&nbsp;<?=ucwords($rfa_list_array["rname"]);?></td>
				<td>&nbsp;<?=ucwords($rfa_list_array["bname"])?></td>
				<td >&nbsp;<?=($rfa_list_array["rdate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$rfa_list_array["rdate"]):"-";?>&nbsp;</td>
				<td >&nbsp;<?=($rfa_list_array["adate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$rfa_list_array["adate"]):"-";?>&nbsp;</td>
				<td>&nbsp;<?=strtoupper($rfa_list_array["status"])?></td>
				<td align="center" width="25"><a title="Approve" href="./appr_det.php?id=<?=$rfa_list_array["id"]?>"><img src="<?=IMG_PATH?>view.png"></a></td>
				<td align="center" width="25"><a title="Print" href="javascript:openW('./print_rfa.php?id=<?=$rfa_list_array["id"]?>','Print_RFA',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png"></a></td>				
			</tr>
<?php		$count++;  
			}
           } else {?>
			<tr><td colspan="10" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php	}?></tbody>
		</table>
	</td></tr>
	<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
	<tr><td>&nbsp;</td></tr>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>