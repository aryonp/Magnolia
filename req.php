<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'notif.lib.php';
chkSession();

$page_title		= "ITS request form";
$page_id_left 	= "3";
$category_page 	= "main";
chkSecurity($page_id_left);

$status 	= "&nbsp;";
$array_det 	= array();

function acc_lvl_select($i) {
	$query 		= "SELECT id, lname FROM acc_level WHERE del = '0' ORDER BY lname ASC";
	$sql 		= @mysql_query($query) or die(mysql_error());
	$lvl_edit 	= "<select name=\"upd_lvl[$i]\">\n";
	$lvl_edit 	.="<option value=\"-\">-----------</option>\n";
	
	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){
		$lvl_edit .= "<option value =\"".$array['id']."\">".ucwords($array['lname'])."</option>\n";
	} 
	$lvl_edit .= "</select>\n";
	return $lvl_edit;
}

if(isset($_POST['submit_req'])){
	$req_type 	= strtolower(trim($_POST['req_type']));
	$file_id 	= genfilecode("ITRF", $_SESSION['bid']);
	$req_date 	= $_POST['date'];
	$dept_id 	= $_POST['dept'];
	$emp_name 	= (isset($_POST['employee']) != "")?strtolower(trim($_POST['employee'])):"";
	$title 		= (isset($_POST['title']) != "")?trim($_POST['title']):"";
	$status 	= (isset($_POST['emp_status']) != "")?trim($_POST['emp_status']):"";
	$details 	= mysql_real_escape_string(strip_tags(trim($_POST['req_details'])));
	$req_content_array 	= $_POST['req_content_items'];	
	if($_SESSION['level'] <= 7) {
		$mgr_id_fk  = $_SESSION['uid'];
		$auth_date	= date('Y-m-d H:i:s');
		$rrd_status	= "authorized";
	}
	else{
		$mgr_id_fk  = $_SESSION['mid'];
		$auth_date	= "0000-00-00 00:00:00";
		$rrd_status	= "pending";
	}
	if (!empty($req_content_array) && !empty($req_type)) {
		$req_input_query  = "INSERT INTO req (code,user_id_fk,req_type,req_date,emp_name,branch_id_fk,dept_id_fk,mgr_id_fk,emp_title,emp_status,details,status,auth_date) ".
							"VALUES ('$file_id','".$_SESSION['uid']."','$req_type','$req_date','$emp_name','".$_SESSION['bid']."','$dept_id','$mgr_id_fk','$title','$status','$details','$rrd_status','$auth_date');";
		@mysql_query($req_input_query) or die(mysql_error());
		
 		$req_id_fk 				= mysql_insert_id();
 		$req_det_input_query 	= "INSERT INTO req_det (req_id_fk, item_id_fk, acclvl_id_fk, status) VALUES ";
		foreach($req_content_array as $req_key => $req_content) {
			$req_content_lvl 	= $_POST["upd_lvl"][$req_key];
 	 		array_push($array_det," ('$req_id_fk','$req_content','$req_content_lvl','$rrd_status')");
 		}
 		$req_det_input_query .= implode(",",$array_det);
 		@mysql_query($req_det_input_query) or die(mysql_error());
 		
 		notify_itrf($req_id_fk);
		log_hist(68,$req_id_fk);
 		header("location:./req_hm.php");
 		exit();
 		
	} else {
		$status ="<p class=\"yellowbox\">You can't create an empty request !</p>";
 	}
}

$button = array("submit_req"=>array("submit"=>"  SUBMIT REQUEST  "),
	            "reset_req"=>array("reset"=>"  RESET REQUEST  "));		
    
include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<?php 
if(isset($_POST['submit']) && !empty($_POST['type'])){
	$type 		= $_POST['type'];
	$req_items 	= "SELECT rt.id, rt.name FROM req_items rt WHERE rt.del = '0' AND rt.type_id_fk = '$type' ORDER BY rt.name ASC";
	$req_items_SQL = mysql_query($req_items) or die(mysql_error());
	if($type == "1"){ ?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%">
			<tr><td><h2>ACCOUNT REQUEST FORM</h2></td></tr>
			<tr><td height="0" bgcolor="#ccccff"></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td><?=back_button();?></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td><?=genButton($button);?></td></tr>
			<tr><td><div class="span8 well">
				<table border="0" cellpadding="1" cellspacing="0">
					<tr><td>&nbsp;</td></tr>
					<tr><td><label><b>TYPE</b></label>
						<table border="0" width="100%" cellpadding="1" cellspacing="0">
							<tr><td><input type="radio" name="req_type" value="New Account">&nbsp;&nbsp;NEW USER ACCOUNT</td>
								<td><input type="radio" name="req_type" value="Modify Account">&nbsp;&nbsp;MODIFY USER ACCOUNT</td>
								<td><input type="radio" name="req_type" value="Delete Account">&nbsp;&nbsp;DELETE USER ACCOUNT</td></tr>
						</table></td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>
						<label><b>ACCOUNT INFORMATION</b></label>
						<table border="0" width="100%" cellpadding="1" cellspacing="1">
							<tr><td colspan="3"><b>EMPLOYEE'S NAME</b><br /><input type="text" size="80" name="employee"></td></tr>
							<tr><td colspan="3"><b>DATE: </b><br /><input type="text" name="date" value="<?=date('Y-m-d H:i:s')?>" id="cal" size="20">&nbsp;<a href="javascript:NewCal('cal','yyyymmdd',true,24)"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a></td></tr>
							
							<tr><td><b>TITLE:</b><br /><input type="text" size="50" name="title"></td>
							<tr><td><b>DEPARTMENT :</b><br /><?=dept_list()?></td>
								<td colspan="2"><b>STATUS:</b></td></tr>
							<tr><td><b>TITLE:</b><br /><input type="text" size="50" name="title"></td>
								<td><input type="radio" name="emp_status" value="permanent">&nbsp;&nbsp;Permanent</td>
								<td><input type="radio" name="emp_status" value="part-time">&nbsp;&nbsp;Part-time/Contract</td></tr>
							<tr><td><b>BRANCH :</b><br /><?=branch_list()?></td>
								<td><input type="radio" name="emp_status" value="probation">&nbsp;&nbsp;Probation</td>
								<td><input type="radio" name="emp_status" value="intern">&nbsp;&nbsp;Intern/Temporary</td></tr>
							<tr><td colspan="3" height="1"></td></tr>	
						</table></fieldset></td></tr>
					<tr><td>&nbsp;</td></tr>
					<tr><td>
						<label><b>ACCESS REQUESTED</b></label>
						<table border="0" width="100%" class="table table-striped table-bordered table-condensed">
<?php	$li = 0;
		while($data = mysql_fetch_array($req_items_SQL, MYSQL_ASSOC)){	?>
			<tr><td><input type="checkbox" name="req_content_items[<?=$li?>]" value="<?=$data["id"]?>">&nbsp;&nbsp;<?=ucwords($data["name"]);?></td><td><?=acc_lvl_select($li);?></td></tr>
<?php	$li++;
		} ?>
				</table></div></td></tr>
<?php
} 
elseif($type >= "2"){?>
<input type="hidden" name="req_type" value="Equipment/Peripherals Request">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2>EQUIPMENT/PERIPHERAL REQUEST FORM</h2></td></tr>
	<tr><td height="0" bgcolor="#ccccff"></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><?=back_button();?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><?=genButton($button);?></td></tr>
	<tr><td>
	<div class="well">
		<table border="0" cellpadding="1" cellspacing="0">
		<tr><td>&nbsp;</td></tr>
		<tr><td>
				<label><b>ACCOUNT INFORMATION</b></label>
				<table border="0" width="100%" cellpadding="1" cellspacing="1">
							<tr><td colspan="3"><b>EMPLOYEE'S NAME</b><br /><input type="text" size="80" name="employee"></td></tr>
							<tr><td colspan="3"><b>DATE: </b><br /><input type="text" name="date" value="<?=date('Y-m-d H:i:s')?>" id="cal" size="20">&nbsp;<a href="javascript:NewCal('cal','yyyymmdd',true,24)"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a></td></tr>
							
							<tr><td><b>DEPARTMENT :</b><br /><?=dept_list()?></td>
								<td colspan="2"><b>STATUS:</b></td></tr>
							<tr><td><b>TITLE:</b><br /><input type="text" size="50" name="title"></td>
								<td><input type="radio" name="emp_status" value="permanent">&nbsp;&nbsp;Permanent</td>
								<td><input type="radio" name="emp_status" value="part-time">&nbsp;&nbsp;Part-time/Contract</td></tr>
							<tr><td><b>BRANCH :</b><br /><?=branch_list()?></td>
								<td><input type="radio" name="emp_status" value="probation">&nbsp;&nbsp;Probation</td>
								<td><input type="radio" name="emp_status" value="intern">&nbsp;&nbsp;Intern/Temporary</td></tr>
							<tr><td colspan="3" height="1"></td></tr>	
						</table></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><fieldset>
			<label><b>PERIPHERALS/EQUIPMENT REQUESTED</b></label>
				<table border="0" width="100%" class="table table-striped table-bordered table-condensed">
						<tr>	
<?php	$zi = 1;
		while($data = mysql_fetch_array($req_items_SQL, MYSQL_ASSOC)){	
			?>
			<td><input type="checkbox" name="req_content_items[]" value="<?=$data["id"]?>">&nbsp;&nbsp;<?=ucwords($data["name"]);?>&nbsp;&nbsp;</td>
<?php	$pzi = $zi % 5;
		if($pzi == 0){ ?>
			</tr><tr>
<?php  	}
        $zi++;
		} ?><td></td>
				</tr>
				</table>
			</td></tr>
<?php } ?>
<tr><td>&nbsp;</td></tr>
		<tr><td><label><b>DETAILS/OTHERS</b></label>
			<textarea cols="100" rows="8" name="req_details" wrap="virtual"></textarea></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<table border="0" width="100%" cellpadding="1" cellspacing="1">
			<tr><td>REQUESTER'S NAME </td>
				<td>AUTHORIZATION</td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td><b><?=ucwords($_SESSION['fullname']);?></b></td>
				<td>&nbsp;</td></tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr><td>DATE/TGL:&nbsp;&nbsp;<b><?=date('d.m.y');?></b></td>
				<td>DATE/TGL:&nbsp;&nbsp;</td></tr>
				</table>
			</td></tr>
			<tr><td>&nbsp;</td></tr>
		</table></div></td>
	</tr>
	<tr><td><?=genButton($button);?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><?=back_button();?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
</table>
<?php	} else {  ?>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2>REQUEST FORM</h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><?=$status?></td></tr>
	<tr><td><div class="span6 alert">
		Dear All,<br/> 
		For any request related to a new computer/notebook please made it min. 1.5 week before.
		<br/><br/>
		Thanks,<br/>
		IT Dept.</div></td></tr>
	<tr><td><a href="./req_hm.php" class="btn">BACK TO THE REQUEST HOME</a></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><div class="span6 well">
		<table>
			<tr><td>
				<label><b>REQUEST TYPE</b></label>
				
				<table border="0" cellpadding="1" cellspacing="0">
					<tr><td>&nbsp;</td></tr>
<?php 
$req_type_query ="SELECT * FROM req_type rt WHERE hidden = '0' ";
$req_type_SQL = mysql_query($req_type_query);
while($reqTypeArray = mysql_fetch_array($req_type_SQL, MYSQL_ASSOC)) {?>
					<tr><td><input type="radio" name="type" value="<?=$reqTypeArray["id"]?>">&nbsp;&nbsp;<?=strtoupper($reqTypeArray["name"])?></td></tr>
					<tr><td>&nbsp;</td></tr>
<?php } ?>
				</table>
			</td></tr>
		</table></div>
	</td></tr>
	<tr><td><input type="submit" class="btn-info btn-small" name="submit" value="NEXT >>"></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><a href="./req_hm.php" class="btn">BACK TO THE REQUEST HOME</a></td></tr>
	<tr><td>&nbsp;</td></tr>
</table>
<?php  } ?>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>