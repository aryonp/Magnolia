<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$mgr_agree_list_query  ="SELECT a.id, a.code, b.name AS bname, d.name AS dept, CONCAT(u.fname,' ',u.lname) AS fullname, a.status, CONCAT(m.fname,' ',m.lname) AS mname, a.mgr_status, a.date, a.ackdate ".
						"FROM agreement a ".
						"LEFT JOIN user u ON (u.id = a.user_id_fk) ".
						"LEFT JOIN departments d ON (d.id = u.dept_id_fk) ".
						"LEFT JOIN branch b ON (b.id = u.branch_id_fk) ".
						"LEFT JOIN user m ON (m.id = u.mgr_id_fk) ".
						"WHERE a.mgr_status = '0' AND a.mgr_id = '".$_SESSION['uid']."' ".
						"ORDER BY fullname ASC ";

$pagingResult = new Pagination();
$pagingResult->setPageQuery($mgr_agree_list_query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";

$q_agree 	= "SELECT a.status, a.mgr_status FROM agreement a LEFT JOIN user u ON (u.id = a.user_id_fk) WHERE u.id = '".$_SESSION['uid']."' ";
$agree 		= @mysql_query($q_agree) or die(mysql_error());
$showAgree 	= mysql_fetch_array($agree);

$file_agreeement = "files/agreement.txt";
$dispAgree = implode("",file($file_agreeement));

$this_page = $_SERVER['PHP_SELF'];

if(isset($_POST['ack_manager'])){ 
	$mgr_agree_ack_id = $_POST['mgr_agree_ack_id'];
	if(!empty($mgr_agree_ack_id)) {
		foreach($mgr_agree_ack_id  as $ack_id_value) {
			$sql  ="UPDATE agreement SET mgr_status = '1', ackdate = '".date('Y-m-d')."' WHERE id = '$ack_id_value'; ";
			@mysql_query($sql) or die(mysql_error());
			log_hist("39",$ack_id_value);
		}
		header("location:$this_page");
	}
	else {
		$status = "<p class=\"alert\">There's nothing to acknowledge. Make sure you already tick your selection</p>";
	}
}

if(isset($_POST['mgr_agree'])){
	auto_agree($_SESSION['uid'], $_SESSION['bid']);
	log_hist("38");
	header("location:$this_page");
}

$page_title 	= "Agreement";
$page_id_left 	= "15";
$page_id_right 	= "48";
$category_page 	= "settings";
chkSecurity($page_id_right);

$button = array("ack_manager"=>array("submit"=>"  ACKNOWLEDGE  "));

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>	
	<tr><td><?=$status?></td></tr>
	<tr class="listview"><td height="24">
		<?=($showAgree[0] != 1)?"&nbsp;<input type=\"submit\" name=\"mgr_agree\" value=\" I AGREE  \" class=\"btn-small btn-info\">&nbsp;&nbsp;(Only for Manager)&nbsp;":"&nbsp;<b>Yes, I agree with the IT policies (For Manager Only)</b>&nbsp;";?>
	</td></tr>
	<tr>
<?php if (!file_exists($file_agreeement)) {?>
		<td align="center"><font color="red"><b>No Policies data</b></font></td>
<?php	}
		else {	?>
		<td>
			<div class=" span8 well"><?=trim(nl2br($dispAgree))?></div>
		</td>
<?php } ?>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
	<tr><td >
		<?=genButton($button)?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
				<tr align="left" valign="middle"> 
        			<td width="25">&nbsp;</td>
                 	<td width="25">&nbsp;<b>NO.</b></td>
                 	<td width="*">&nbsp;<b>FILE NO.</b></td>
                 	<td width="*">&nbsp;<b>BRANCH</b></td>
                 	<td width="*">&nbsp;<b>DEPARTMENT</b></td>
                 	<td width="*">&nbsp;<b>NAME</b></td>
                 	<td width="*">&nbsp;<b>AGREE</b></td>
                 	<td width="*">&nbsp;<b>MANAGER</b></td>
                 	<td width="*">&nbsp;<b>ACKNOWLEDGE</b></td>
                	<td width="*">&nbsp;<b>DATE</b></td>
                	<td width="*">&nbsp;<b>ACK. DATE</b></td>
				</tr>
			</thead>
			<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($mgr_agree_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$row_color = ($count % 2)?"odd":"even";   ?>
				<tr class="<?=$row_color?>" align="left" valign="top">
					<td>&nbsp;<input type="checkbox" name="mgr_agree_ack_id[]" value="<?=$mgr_agree_list_array["id"]?>"></td>
					<td>&nbsp;<?=$count?>.</td>
					<td>&nbsp;<?=($mgr_agree_list_array["code"])?ucwords($mgr_agree_list_array["code"]):"&nbsp; -";?></td>
					<td>&nbsp;<?=($mgr_agree_list_array["bname"])?ucwords($mgr_agree_list_array["bname"]):"&nbsp; -";?></td>
					<td>&nbsp;<?=($mgr_agree_list_array["dname"])?ucwords($mgr_agree_list_array["dname"]):"&nbsp; -";?></td>
					<td>&nbsp;<?=($mgr_agree_list_array["fullname"])?ucwords($mgr_agree_list_array["fullname"]):"&nbsp; -";?></td>
					<td><?php
					if($mgr_agree_list_array["status"] == 1) {?>&nbsp;YES<?}else{?>&nbsp;PENDING<?}?></td>
					<td><?=ucwords($mgr_agree_list_array["mname"])?></td>
					<td><?php
					if($mgr_agree_list_array["mgr_status"] == 1) {?>&nbsp;YES<?}else{?>&nbsp;PENDING<?}?></td>
					<td>&nbsp;<?=($mgr_agree_list_array["date"]!= "0000-00-00")?cplday('d M Y',$mgr_agree_list_array["date"]):"&nbsp; -";?></td>
					<td>&nbsp;<?=($mgr_agree_list_array["ackdate"]!= "0000-00-00")?cplday('d M Y',$mgr_agree_list_array["ackdate"]):"&nbsp; -";?></td>
				</tr>
<?php		$count++;  
				}
			} else {?>
				<tr><td colspan="11" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php		} ?>
			</tbody>
			</table>
			<?=genButton($button)?>
		</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td >&nbsp;</td></tr>	
<tr><td>&nbsp;</td></tr>
</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>