<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title 	= "Authorization Page";
$page_id_left 	= "4";
$page_id_right 	= "";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$search = ($_GET['search']!="")?mysql_real_escape_string(trim($_GET['search'])):"";
$param 	= ($_GET['param']!="")?trim($_GET['param']):"";
$fltr 	= ($_SESSION['level'] <= 3)?"":"u.mgr_id_fk = '".$_SESSION['uid']."' AND";

switch($param){
	case "id":
			$where  = "WHERE $fltr r.del = '0' AND r.id = '$search'";
		break;
	case "fno":
			$where  = "WHERE $fltr r.del = '0' AND r.code LIKE '%$search%'";
		break;
	case "req":
			$where  = "WHERE $fltr r.del = '0' AND CONCAT(u.fname,' ',u.lname) LIKE '%$search%'";
		break;
	default:
			$where  = "WHERE $fltr r.del = '0'";
}

$query  = "SELECT r.id, r.code, b.name as bname, d.name as dname, CONCAT(u.fname,' ',u.lname) AS fullname, 
		                      r.req_type, r.req_date as rdate, r.auth_date AS l2date, r.appr_date AS adate, r.status 
		   			   FROM req r 
					   LEFT JOIN user u ON (u.id = r.user_id_fk) 
			           LEFT JOIN departments d ON (d.id = r.dept_id_fk) 
			           LEFT JOIN branch b ON (b.id = r.branch_id_fk) 
		               $where 
		               ORDER BY r.req_date DESC ";

$pagingResult = new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h3>AUTHORIZATION BOX</h3></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
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
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
        <tr><td>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
            	<tr valign="middle">
                 	<th width="25" align="left"><b>&nbsp;NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<th width="*" align="left"><b>&nbsp;FILE NO.</b>&nbsp;</td>
                 	<th width="*" align="left"><b>&nbsp;BRANCH</b>&nbsp;</td>
                 	<th width="*" align="left"><b>&nbsp;DEPARTMENT</b>&nbsp;</td>
                 	<th width="*" align="left"><b>&nbsp;REQUESTER</b>&nbsp;</td>
                 	<th width="*" align="left"><b>&nbsp;REQ. TYPE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>REQ. DATE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>L2 DATE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>IT APPR.</b>&nbsp;</td>
                 	<th width="*" align="left"><b>&nbsp;STATUS</b>&nbsp;</td>
                 	<th width="*" align="center" colspan="2"><b>&nbsp;CMD</b>&nbsp;</td>   	
				</tr>
				</thead>
				<tbody>
<?php 
   if($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$row_color = ($count % 2)?"odd":"even";   ?>
				<tr class="<?=$row_color?>" valign="top">
					<td align="left">&nbsp;<?=$count?>.&nbsp;</td>
					<td align="left">&nbsp;#<?=$array["id"]?>&nbsp;</td>
					<td align="left">&nbsp;<?=$array["code"]?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["bname"])?ucwords($array["bname"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["dname"])?ucwords($array["dname"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["fullname"])?ucwords($array["fullname"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["req_type"])?ucwords($array["req_type"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["rdate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["rdate"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["l2date"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["l2date"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["adate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["adate"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=strtoupper($array["status"])?>&nbsp;</td>
					<td align="center" width="25"><a title="Authorize" href="./auth_det.php?id=<?=$array["id"]?>"><img src="<?=IMG_PATH?>view.png"></a></td>
					<td align="center" width="25"><a title="Print" href="javascript:openW('./print_req.php?id=<?=$array["id"]?>','Print_Request',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png"></a></td>
					</tr>
<?php	$count++;  
			}
           } else {?>
				<tr><td colspan="13" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php }?></tbody>
			</table></td></tr>
        <tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>