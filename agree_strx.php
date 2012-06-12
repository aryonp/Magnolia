<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title 	= "Agreement";
$page_id_left 	= "13";
$page_id_right 	= "28";
$category_page 	= "strx";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$agree_list_query  ="SELECT a.id, 
						a.code, 
						b.id AS bname, 
						d.name AS dept, 
						CONCAT(u.fname,' ',u.lname) AS fullname, 
						a.status, 
						CONCAT(m.fname,' ',m.lname) AS mname, 
						a.mgr_status, a.date, 
						a.ackdate 
					 FROM agreement a 
					 	LEFT JOIN user u ON (u.id = a.user_id_fk) 
					 	LEFT JOIN departments d ON (d.id = u.dept_id_fk) 
					 	LEFT JOIN branch b ON (b.id = u.branch_id_fk) 
					 	LEFT JOIN user m ON (m.id = u.mgr_id_fk) 
					 WHERE a.del = '0' 
					 	ORDER BY a.id DESC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($agree_list_query);
$pagingResult->paginate();

$this_page 			= $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();
$status 			= "&nbsp;";
$file_agreeement 	= "files/agreement.txt";
$dispAgree 			= implode("",file($file_agreeement));

$q_agree 			= "SELECT a.status, a.mgr_status FROM agreement a WHERE a.user_id_fk = '".$_SESSION['uid']."' ";
$agree 				= @mysql_query($q_agree) or die(mysql_error());
$showAgree 			= mysql_fetch_array($agree, MYSQL_ASSOC);

if(isset($_POST['adm_agree'])){
	auto_agree($_SESSION['uid'], $_SESSION['bid']);
	log_hist("38");
	header("location:$this_page");
}

if(isset($_POST['svAgree'])){
	$updAgreeTxt = trim($_POST['ctAgree']);
		if (!file_exists($file_agreeement)) {
			fopen($file_agreeement, 'w+');
		}
		if (is_writable($file_agreeement)){
			if (!$handle = fopen($file_agreeement, 'w')) {
				$status = "<div class=\"alert alert-error\" align=\"center\">Can't open file <b>$file_agreeement</b></div>";
				exit;
			}
			if (fwrite($handle, $updAgreeTxt) === FALSE) {
				$status = "<div class=\"alert alert-error\" align=\"center\">Failed to write file <b>$file_agreeement</b></div>";
				exit;
			} 
			$status = "<div class=\"alert alert-success\" align=\"center\">Success to write file <b>$file_agreeement</b></div>";		
			fclose($handle);
			log_hist("40");
		} 
		else{  
				$status = "<div class=\"alert alert-error\" align=\"center\">Cannot write to file <b>$file_agreeement</b></div>";
			}
	
}

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2>AGREEMENT BOX</h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><?=$status?></td></tr>		
 	<tr><td>
 		<table border="0" cellpadding="0" cellspacing="0" height="100%">
			<tr class="listview"><td height="24">
				<?=($showAgree["status"] != 1)?"&nbsp;<input type=\"submit\" name=\"adm_agree\" value=\"  I AGREE  \" class=\"btn-info btn-small\">&nbsp;&nbsp;(Only for Admin)&nbsp;":"&nbsp;<b>Yes, I agree with the IT policies (For Admin Only)</b>&nbsp;";?>
			</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>
			<textarea cols="80" rows="10" name="ctAgree" wrap="virtual" class="input-xlarge">
				<?=strip_tags(trim($dispAgree),'<b><i>')?>
				</textarea></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td><input type="submit" name="svAgree" class="btn-info btn-small" value="UPDATE AGREEMENT TEXT"></td></tr>
		</table>
		</td></tr>
		<tr><td >&nbsp;</td></tr>	
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td >&nbsp;</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td >
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>	
				<tr align="left" valign="top"> 
                 	<td width="25">&nbsp;<b>NO.</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>ID</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>FILE NO.</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>BRANCH</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>DEPARTMENT</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>NAME</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>AGREE</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>MANAGER</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>ACKNOWLEDGE</b>&nbsp;</td>
                	<td width="*">&nbsp;<b>DATE</b>&nbsp;</td>
                	<td width="*">&nbsp;<b>ACK. DATE</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($agree_list_array = mysql_fetch_array($result)) { ?>
					<tr align="left" valign="top">
						<td>&nbsp;<?=$count?>.&nbsp;</td>
						<td>&nbsp;#<?=($agree_list_array["id"])?$agree_list_array["id"]:"&nbsp; -";?>&nbsp;</td>
						<td>&nbsp;<?=($agree_list_array["code"])?$agree_list_array["code"]:"&nbsp; -";?>&nbsp;</td>
						<td>&nbsp;<?=($agree_list_array["bname"])?ucwords($agree_list_array["bname"]):"&nbsp; -";?>&nbsp;</td>
						<td>&nbsp;<?=($agree_list_array["dept"])?ucwords($agree_list_array["dept"]):"&nbsp; -";?>&nbsp;</td>
						<td>&nbsp;<?=($agree_list_array["fullname"])?ucwords($agree_list_array["fullname"]):"&nbsp; -";?>&nbsp;</td>
						<td>
<?php				if($agree_list_array["status"] == 1) {?>&nbsp;YES<?}else{?>&nbsp;PENDING<?}?>&nbsp;</td>
					<td>&nbsp;<?=($agree_list_array["mname"])?ucwords($agree_list_array["mname"]):"&nbsp; -";?>&nbsp;</td>
					<td>
<?php				if($agree_list_array["mstatus"] == 1) {?>&nbsp;YES<?}else{?>&nbsp;PENDING<?}?>&nbsp;</td>
					<td>&nbsp;<?=($agree_list_array["date"] != "0000-00-00")?cplday('D, d M Y',$agree_list_array["date"]):"&nbsp; -";?>&nbsp;</td>
					<td>&nbsp;<?=($agree_list_array["ackdate"] != "0000-00-00")?cplday('D, d M Y',$agree_list_array["ackdate"]):"&nbsp; -";?>&nbsp;</td>
				</tr>
<?php			$count++;  
				}
			} else {?>
				<tr><td colspan="11" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 		} ?></tbody>
			</table>

		</td></tr>	
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td >&nbsp;</td></tr>
<tr><td>&nbsp;</td></tr>
</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>