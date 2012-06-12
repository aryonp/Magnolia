<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'notif.lib.php';
chkSession();

$page_title 	= "IT Approval Form";
$page_id_left 	= "7";
chkSecurity($page_id_left);

$req_id 	= $_GET['id'];
$this_page 	= $_SERVER['PHP_SELF']."?id=".$req_id;

if(isset($_POST['adm_appr'])){
	$req_id_fk = $_POST['req_id_fk'];
	$appr_note = mysql_real_escape_string($_POST['appr_note']);
	foreach ($_POST['req_det_id'] as $row => $upd_req_id) {
		$upd_det_stat = $_POST['req_det_stat'][$row];
		$upd_req_det_query = "UPDATE req_det SET status = '$upd_det_stat' WHERE id = '$upd_req_id' AND status != 'rejected';";
		@mysql_query($upd_req_det_query) or die(mysql_error());
	}	
	$upd_req_query = "UPDATE req SET status = 'adm-authorized', appr_id_fk = '".$_SESSION['uid']."', appr_date = '".date('Y-m-d H:i:s')."', appr_note = '$appr_note' WHERE id = '$req_id_fk';";
	@mysql_query($upd_req_query) or die(mysql_error());
	notify_it_appr($req_id_fk,"ADM-Authorized");
	notify_adm_appr($req_id_fk,"ADM-Authorized");
	log_hist("75",$req_id_fk);
	header("location:$this_page");
}

elseif(isset($_POST['adm_appr_all'])){
	$req_id_fk = $_POST['req_id_fk'];
	$appr_note = mysql_real_escape_string($_POST['appr_note']);
	
	$upd_req_det_query = "UPDATE req_det SET status = 'adm-authorized' WHERE req_id_fk = '$req_id_fk' AND status != 'rejected';";
	@mysql_query($upd_req_det_query) or die(mysql_error());	
	
	$upd_req_query = "UPDATE req SET status = 'adm-authorized', appr_id_fk = '".$_SESSION['uid']."', appr_date = '".date('Y-m-d H:i:s')."', appr_note = '$appr_note' WHERE id = '$req_id_fk';";
	@mysql_query($upd_req_query) or die(mysql_error());
	
	notify_it_appr($req_id_fk,"ADM-Authorized");
	notify_adm_appr($req_id_fk,"ADM-Authorized");
	
	log_hist("76",$req_id_fk);
	header("location:$this_page");
}

elseif(isset($_POST['adm_reject_all'])){
	$req_id_fk = $_POST['req_id_fk'];
	$appr_note = mysql_real_escape_string($_POST['appr_note']);
	
	$upd_req_det_query = "UPDATE req_det SET status = 'adm-rejected' WHERE req_id_fk = '$req_id_fk' AND status != 'rejected';";
	@mysql_query($upd_req_det_query) or die(mysql_error());
	
	$upd_req_query = "UPDATE req SET status = 'adm-rejected', appr_id_fk = '".$_SESSION['uid']."', appr_date = '".date('Y-m-d H:i:s')."', appr_note = '$appr_note' WHERE id = '$req_id_fk'  ;";
	@mysql_query($upd_req_query) or die(mysql_error());
	
	notify_it_appr($req_id_fk,"ADM-Rejected");
	
	log_hist("77",$req_id_fk);
	header("location:$this_page");
}

elseif(isset($_POST['adm_stock_all'])){
	$req_id_fk 	= $_POST['req_id_fk'];
	$appr_note 	= mysql_real_escape_string($_POST['appr_note']);
	
	$upd_req_det_query 	= "UPDATE req_det SET status = 'adm-authorized (STOCK)' WHERE req_id_fk = '$req_id_fk' AND status != 'rejected';";
	@mysql_query($upd_req_det_query) or die(mysql_error());	
	
	$upd_req_query 	= "UPDATE req SET status = 'adm-authorized (STOCK)', appr_id_fk = '".$_SESSION['uid']."', appr_date = '".date('Y-m-d H:i:s')."', appr_note = '$appr_note' WHERE id = '$req_id_fk';";
	@mysql_query($upd_req_query) or die(mysql_error());
	
	notify_it_appr($req_id_fk,"ADM-Authorized (STOCK)");
	notify_adm_appr($req_id_fk,"ADM-Authorized (STOCK)");
	
	log_hist("78",$req_id_fk);
	header("location:$this_page");
}

function authorize_status($array_id) {
	$approval = array("adm-authorized","adm-rejected","adm-authorized (stock)");
	echo "<select name = \"req_det_stat[$array_id]\">\n";
	foreach($approval as $status) {
		echo "<option value =\"$status\">".strtoupper($status)."</option>\n";
	}
	echo "</select>\n";
}

$display_request_query  ="SELECT r.id, 
								 r.code, 
								 r.req_type, 
								 r.req_date, 
								 r.emp_name, 
								 d.name as dname, 
								 r.emp_title, 
								 b.name as bname, 
								 r.emp_status, 
								 r.details, 
								 CONCAT(u.fname,' ',u.lname) AS fullname, 
								 CONCAT(m.fname,' ',m.lname) AS mname, 
								 r.auth_date, 
								 r.mgr_note,
								 r.code_date,
								 r.appr_note, 
								 CONCAT(v.fname,' ',v.lname) AS vname, 
								 r.code_notes, 
								 CONCAT(it.fname,' ',it.lname) AS itappr,
								 r.appr_date,
								 r.status 
							FROM req r 
								LEFT JOIN user u ON (u.id = r.user_id_fk) 
								LEFT JOIN departments d ON (d.id = r.dept_id_fk) 
								LEFT JOIN branch b ON (b.id = r.branch_id_fk) 
								LEFT JOIN user m ON (m.id = r.mgr_id_fk) 
								LEFT JOIN user v ON (v.id = r.code_val) 
								LEFT JOIN user it ON (it.id = r.appr_id_fk) 
							WHERE r.id = '$req_id' AND r.del = 0;";
$display_request_SQL 	= @mysql_query($display_request_query) or die(mysql_error());
$array 					= mysql_fetch_array($display_request_SQL,MYSQL_ASSOC);

$details_query  ="SELECT rd.id, rt.name, rd.status, al.lname as alname ".
				 "FROM req_det rd 
				 		LEFT JOIN req_items rt ON (rd.item_id_fk = rt.id) 
				 		LEFT JOIN acc_level al ON (al.id = rd.acclvl_id_fk)".
				 "WHERE rd.req_id_fk = '$req_id' AND rd.del = '0';";			
$details_SQL = @mysql_query($details_query) or die(mysql_error());

$button = array("adm_appr"		=>array("submit"	=>	"  ADM APPROVE  "),
				"adm_appr_all"	=>array("submit"	=>	"  ADM APPROVE ALL  "),
				"adm_reject_all"=>array("submit"	=>	"  ADM REJECT ALL  "),
				"adm_stock_all"	=>array("submit"	=>	"  ADM STOCK ALL  "));		
		
include THEME_DEFAULT.'header.php';?>             
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2><?=strtoupper($page_title);?></h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><input type="hidden" name="req_id_fk" value="<?=$array["id"]?>"></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>[&nbsp;<a href="./it_appr_hm.php">BACK TO THE IT APPROVAL PAGE</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
	<?=($array["status"] == "adm-rejected" OR $array["status"] == "adm-authorized")?"":"<tr><td>".genButton($button)."</td></tr>";?>
	<tr><td>
		<div class="span8 well">
		<table border="0" cellpadding="1" cellspacing="0">
		<tr><td>&nbsp;</td></tr>
		<tr><td><b>ID : </b>#<?=($array["id"])?ucwords($array["id"]):"-"?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<label><b>TYPE</b></label>
			<?=strtoupper($array["req_type"])?></b>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
 			<label><b>ACCOUNT INFORMATION&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				DATE: </b><?=($array["req_date"])?cplday('d F Y',$array["req_date"]):"&nbsp; -";?></label>
			<table border="0" width="100%" cellpadding="1" cellspacing="1">
				<tr><td colspan="2"><b>NAME:</b></td></tr>
				<tr><td colspan="2"><?=($array["emp_name"])?ucwords($array["emp_name"]):"&nbsp; -"?></td></tr>
				<tr><td><b>DEPARTMENT:</b></td>
					<td><b>STATUS:</b></td></tr>
				<tr><td><?=ucwords($array["dname"])?></td>
					<td><?=($array["emp_status"])?ucwords($array["emp_status"]):"&nbsp; -"?></td></tr>
				<tr><td><b>TITLE:</b></td>
					<td>&nbsp;</td></tr>
				<tr><td><?=($array["emp_title"])?ucwords($array["emp_title"]):"&nbsp; -"?></td>
					<td>&nbsp;</td></tr>
				<tr><td><b>BRANCH:</b></td>
					<td>&nbsp;</td></tr>
				<tr><td><?=ucwords($array["bname"])?></td>
					<td>&nbsp;</td></tr>
				<tr><td colspan="3" height="1"></td></tr>	
			</table></fieldset></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<label><b>REQ. LIST</b></label>
				<table border="0">
				<tr><td colspan="2">&nbsp;</td></tr>
<?php 
$i = 0;
while($details_array = mysql_fetch_array($details_SQL,MYSQL_ASSOC)){ ?>
<tr>
	<td>&nbsp;-&nbsp;
		<?=ucwords($details_array["name"]);?>
		<input type="hidden" name="req_det_id[<?=$i?>]" value="<?=ucwords($details_array["id"]);?>"></td>
	<td>&nbsp;->&nbsp;(Grup/Level : 
		<?=($details_array["alname"])?strtoupper($details_array["alname"]):"-";?>&nbsp;)*
	</td>
	<td>&nbsp;->&nbsp;
		<?=($details_array["status"] == "authorized" OR $details_array["status"] == "adm-pending")?authorize_status($i):strtoupper($details_array["status"]);?>
	</td>
</tr>
<?php 
$i++;
} ?>			<tr><td colspan="2">&nbsp;</td></tr>
				</table>
		</td></tr>		
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<label><b>DETAILS/OTHERS</b></label>
					<?=($array["details"])?nl2br(trim($array["details"])):"&nbsp; -"?>
				<br/>
		</td></tr>
<?php if($array["status"] != "pending"){?>
		<tr><td>&nbsp;</td></tr>
		<tr><td><label><b>AUTHORIZER COMMENTS</b></label>
		<?=($array["mgr_note"])?nl2br(trim($array["mgr_note"])):"-"?>
		</td></tr>
<?php } ?>
<?php if($array["status"] == "adm-approved" AND $array["status"] == "adm-approved (STOCK)" AND $array["status"] == "adm-rejected"){?>
		<tr><td>&nbsp;</td></tr>
		<tr><td><label><b>IT ADMIN COMMENTS</b></label>
		<?=($array["appr_note"])?nl2br(trim($array["appr_note"])):"-"?>
		</td></tr>
<?php } ?>
		<tr><td>&nbsp;</td></tr>
		<tr><td><label><b>IT ADMIN COMMENTS</b></label>
		<table border="0" cellpadding="0">
			<tr valign="top">
				<td><textarea cols="50" rows="5" name="appr_note" wrap="virtual"></textarea></td>
			</tr></table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>(*) Only for Requesting Account</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<table border="0" width="100%" cellpadding="1" cellspacing="1">
			<tr><td><b>REQUESTER'S NAME:</b></td>
				<td>&nbsp;</td>
				<td><b>AUTHORIZATION:</b></td>
				<td>&nbsp;</td>
				<td><b>IT AUTH.:</b></td></tr>
			<tr><td colspan="5">&nbsp;</td></tr>
			<tr><td><?=ucwords($array["fullname"])?></td>
				<td>&nbsp;</td>
				<td><?=($array["status"] == "adm-authorized" OR ($array["status"] == "authorized"))?ucwords($array["mname"]):"&nbsp; -";?></td>
				<td>&nbsp;</td>
				<td><?=($array["status"] == "adm-authorized")?ucwords($array["itappr"]):"&nbsp; -";?></td></tr>
			<tr><td colspan="5">&nbsp;</td></tr>
			<tr><td><b>DATE/TGL:</b>&nbsp;&nbsp;<?=($array["req_date"])?cplday('d M Y',$array["req_date"]):"&nbsp; -";?></td>
				<td>&nbsp;</td>
				<td><b>DATE/TGL:</b>&nbsp;&nbsp;<?=($array["status"] == "adm-authorized" OR ($array["status"] == "authorized"))?cplday('d M Y',$array["auth_date"]):"&nbsp; -";?></td>
				<td>&nbsp;</td>
				<td><b>DATE/TGL:</b>&nbsp;&nbsp;<?=($array["status"] == "adm-authorized")?cplday('d M Y',$array["appr_date"]):"&nbsp; -";?></td></tr>
			</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><hr>
  		<label><b>ITS USE ONLY</b></label>
		<table border="0" cellpadding="1" cellspacing="1">
			<tr><td align="left"><b>FILE NO</b></td>
				<td align="left">:</td>
				<td><?=($array["code"])?ucwords($array["code"]):"&nbsp; -";?></td>
				<td align="left" rowspan="4">&nbsp;</td>
				<td rowspan="4" align="left" valign="top"><b>NOTES</b>:<br/>
					<?=($array["code_notes"])?nl2br($array["code_notes"]):"&nbsp; -";?></td></tr>
			<tr><td align="left"><b>VALIDATED</b></td>
				<td align="left">:</td>
				<td><?=($array["vname"])?ucwords($array["vname"]):"&nbsp; -";?></td></tr>
			<tr><td align="left"><b>DATE</b></td>
				<td align="left">:</td>
				<td><?=($array["code_date"])?cplday('d M Y',$array["code_date"]):"&nbsp; -";?></td></tr>	
		</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		</table></div>
		</td></tr>
		<?=($array["status"] == "adm-rejected" OR $array["status"] == "adm-authorized")?"":"<tr><td>".genButton($button)."</td></tr>";?>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[&nbsp;<a href="./it_appr_hm.php">BACK TO THE IT APPROVAL PAGE</a>&nbsp;]</td></tr>
		<tr><td>&nbsp;</td></tr>
</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>