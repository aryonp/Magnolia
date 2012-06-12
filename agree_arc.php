<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Agreement Archive";
$page_id_left 	= "10";
$page_id_right 	= "22";
$category_page 	= "archive";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$agree_list_query  ="SELECT a.id, a.code, b.name AS bname, d.name AS dept, CONCAT(u.fname,' ',u.lname) AS fullname, a.date, a.status FROM agreement a LEFT JOIN user u ON (u.id = a.user_id_fk) LEFT JOIN departments d ON (d.id = u.dept_id_fk) LEFT JOIN branch b ON (b.id = u.branch_id_fk) ORDER BY a.date DESC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($agree_list_query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td>&nbsp;</td></tr>		
	<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
	<tr><td >
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr><th width="25">&nbsp;<b>NO.</b>&nbsp;</td>
					<th width="*">&nbsp;<b>ID</b>&nbsp;</td>
					<th width="*">&nbsp;<b>FILE NO.</b>&nbsp;</td>
					<th width="*">&nbsp;<b>BRANCH</b>&nbsp;</td>
					<th width="*">&nbsp;<b>DEPARTMENT</b>&nbsp;</td>
					<th width="*">&nbsp;<b>NAME</b>&nbsp;</td>
					<th width="*">&nbsp;<b>DATE</b>&nbsp;</td>
					<th width="*">&nbsp;<b>STATUS</b>&nbsp;</td>
					<th width="*" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($agree_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
				<tr valign="top" >
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;#<?=($agree_list_array["id"])?$agree_list_array["id"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($agree_list_array["code"])?$agree_list_array["code"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($agree_list_array["bname"])?ucwords($agree_list_array["bname"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($agree_list_array["dept"])?ucwords($agree_list_array["dept"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($agree_list_array["fullname"])?ucwords($agree_list_array["fullname"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($agree_list_array["date"])?cplday('d M Y',$agree_list_array["date"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($agree_list_array["status"] == "1")?"AGREE":"PENDING";?>&nbsp;</td>
					<td align="center" width="25"><a title="Update File" href="./agree_arc_det.php?id=<?=$agree_list_array["id"]?>"><Img src="<?=IMG_PATH?>edit.png"></a>&nbsp;</td>
					<td align="center" width="25"><a title="Print" href="javascript:openW('./print_agree.php?id=<?=$agree_list_array["id"]?>','Print_Agreement',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><Img src="<?=IMG_PATH?>print.png"></a>&nbsp;</td>
				</tr>
<?php  $count++;   }
	} else {?>
				<tr><td colspan="10" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php } ?> </tbody>
			</table>
		</td></tr>	
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>&nbsp;</td></tr>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>