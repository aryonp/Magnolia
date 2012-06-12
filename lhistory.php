<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title 	= "Log History Page";
$page_id_left 	= "14";
$page_id_right 	= "42";
$category_page 	= "mgmt";
chkSecurity($page_id_right);

$log_list_query ="SELECT lh.id, CONCAT(u.fname,' ',u.lname) AS fullname, lh.ip_addr, lh.time, CONCAT(lc.notes,' ',lh.notes) AS notes ".
				 "FROM log_history lh LEFT JOIN user u ON (u.id = lh.user_id_fk) LEFT JOIN log_code lc ON (lc.id = lh.code_id_fk) WHERE lh.del = '0' ORDER BY lh.time DESC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($log_list_query);
$pagingResult->paginate();

$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();
include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>LOG HISTORY</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
        <tr><td><?=$pagingResult->pagingMenu()?></td></tr>
        <tr><td>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr><th width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>NAME</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>IP ADDRESS</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>TIME</b>&nbsp;</td>
                 	<th width="*">&nbsp;<b>REMARKS</b>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($log_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
				<tr align="left" valign="top">
					<td width="25" align="left">&nbsp;<?=$count?>.</td>
					<td>&nbsp;<?=($log_list_array["fullname"])?ucwords($log_list_array["fullname"]):"-"?>&nbsp;</td>
					<td>&nbsp;<?=($log_list_array["ip_addr"])?ucwords($log_list_array["ip_addr"]):"-"?>&nbsp;</td>
					<td>&nbsp;<?=($log_list_array["time"])?cplday('d M Y H:i:s',$log_list_array["time"]):"-"?>&nbsp;</td>
					<td>&nbsp;<?=($log_list_array["notes"])?strtoupper($log_list_array["notes"]):"-"?>&nbsp;</td>
					</tr>
<?php			$count++;  
				}
			} else {?>
				<tr><td colspan="5" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 			}	?>
			</tbody>
			</table>
		</td></tr>
        <tr><td><?=$pagingResult->pagingMenu()?></td></tr>
        <tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>       				
<?php include THEME_DEFAULT.'footer.php';?>