<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "ITRF Archive";
$page_id_left  	= "10";
$page_id_right 	= "20";
$category_page 	= "archive";
chkSecurity($page_id_right);

$search = ($_GET['search']!="")?mysql_real_escape_string(trim($_GET['search'])):"";
$param 	= ($_GET['param']!="")?trim($_GET['param']):"";

switch($param){
	case "id":
		$fltr 	= "AND r.id = '$search'";
		break;
	case "fno":
		$fltr 	= "AND r.code LIKE '%$search%'";
		break;
	case "req":
		$fltr 	= "AND CONCAT(u.fname,' ',u.lname) LIKE '%$search%'";
		break;
	default:
		$fltr 	= "";
}

$query 	= "SELECT r.id, 
                  r.code, 
                  CONCAT(u.fname,' ',u.lname) AS fullname, 
                  r.req_type as type, 
				  r.emp_name AS emp, 
				  d.name AS dname, 
                  r.req_date as rdate, 
                  r.auth_date AS l2date, 
                  r.appr_date AS adate, 
                  r.status 
           FROM req r 
           LEFT JOIN user u ON (u.id = r.user_id_fk) 
           LEFT JOIN departments d ON (d.id = r.dept_id_fk)
           WHERE r.del = '0' $fltr
           ORDER BY r.req_date DESC ";

$pagingResult = new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[<a title="Print" href="javascript:openW('./print_req_report.php?param=acc','Print_Request_ACC',1000,500,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');">PRINT REQUEST SUMMARY ACCOUNT</a>]
		[<a title="Print" href="javascript:openW('./print_req_report.php?param=per','Print_Request_Per',1000,500,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');">PRINT REQUEST SUMMARY PERIPHERAL</a>]</td></tr>
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
            	<tr valign="middle"> 
                 	<th width="25" align="left">&nbsp;<b>NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>FILE NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>REQUESTER</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>REQ. TYPE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>FOR</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>DEPT.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>REQ. DATE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>L2 DATE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>IT APPR.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>STATUS</b>&nbsp;</td>
                 	<th align="center" colspan="2">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php 	if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
				<tr valign="top">
					<td align="left">&nbsp;<?=$count?>.&nbsp;</td>
					<td align="left">&nbsp;#<?=$array["id"]?>&nbsp;</td>
					<td align="left">&nbsp;<?=$array["code"]?>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($array["fullname"])?>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($array["type"])?>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($array["emp"])?>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($array["dname"])?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["rdate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["rdate"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["l2date"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["l2date"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["adate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["adate"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=strtoupper($array["status"])?>&nbsp;</td>
					<td align="center" width="25"><a title="Update File" href="./req_arc_det.php?id=<?=$array["id"]?>"><img src="<?=IMG_PATH?>edit.png"></a></td>
					<td align="center" width="25"><a title="Print" href="javascript:openW('./print_req.php?id=<?=$array["id"]?>','Print_Request',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png"></a></td>
				</tr>
<?php			$count++;  
				}
			} else {?>
				<tr><td colspan="13" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php }		?></tbody>
			</table></td></tr>
        <tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>