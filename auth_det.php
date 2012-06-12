<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'notif.lib.php';
chkSession();

$page_title 	= "Authorization Details Page";
$page_id_left 	= "4";
$page_id_right 	= "";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$req_id 	= (int) trim($_GET['id']);
$this_page 	= $_SERVER['PHP_SELF']."?id=".$req_id;

if(isset($_POST['authorized'])){
	$req_id_fk 	= $_POST['req_id_fk'];
	$mgr_note 	= mysql_real_escape_string($_POST['auth_comm']);
	foreach ($_POST['req_det_id'] as $row => $upd_req_id) {
		$upd_det_stat 		= $_POST['req_det_stat'][$row];
		$upd_req_det_query 	= "UPDATE req_det SET status = '$upd_det_stat' WHERE id = '$upd_req_id';";
		@mysql_query($upd_req_det_query) or die(mysql_error());
	}	
	$upd_req_query = "UPDATE req SET status = 'authorized', auth_date = '".date('Y-m-d H:i:s')."', mgr_id_fk = '".$_SESSION['uid']."', mgr_note = '$mgr_note' WHERE id = '$req_id_fk' ";
	@mysql_query($upd_req_query) or die(mysql_error());
	notify_itrf_2($req_id_fk,"Authorized");
	notify_adm_appr($req_id_fk,"Authorized");
	log_hist("72",$req_id_fk);
	header("location:$this_page");
	exit();
}

elseif(isset($_POST['auth_all'])){
	$req_id_fk 	= $_POST['req_id_fk'];
	$mgr_note 	= mysql_real_escape_string($_POST['auth_comm']);
	foreach ($_POST['req_det_id'] as $row => $upd_req_id) {
		$upd_req_det_query 	= "UPDATE req_det SET status = 'authorized' WHERE id = '$upd_req_id';";
		@mysql_query($upd_req_det_query) or die(mysql_error());
	}	
	$upd_req_query = "UPDATE req SET status = 'authorized', auth_date = '".date('Y-m-d H:i:s')."', mgr_id_fk = '".$_SESSION['uid']."', mgr_note = '$mgr_note' WHERE id = '$req_id_fk' ";
	@mysql_query($upd_req_query) or die(mysql_error());
	notify_itrf_2($req_id_fk,"Authorized");
	notify_adm_appr($req_id_fk,"Authorized");
	log_hist("73",$req_id_fk);
	header("location:$this_page");
	exit();
}

elseif(isset($_POST['reject_all'])){
	$req_id_fk 	= $_POST['req_id_fk'];
	$mgr_note 	= mysql_real_escape_string($_POST['auth_comm']);
	foreach ($_POST['req_det_id'] as $row => $upd_req_id) {
		$upd_req_det_query 	= "UPDATE req_det SET status = 'rejected' WHERE id = '$upd_req_id';";
		@mysql_query($upd_req_det_query) or die(mysql_error());
	}	
	$upd_req_query = "UPDATE req SET status = 'rejected', auth_date = '".date('Y-m-d H:i:s')."', mgr_id_fk = '".$_SESSION['uid']."', mgr_note = '$mgr_note' WHERE id = '$req_id_fk' ";
	@mysql_query($upd_req_query) or die(mysql_error());
	notify_itrf_2($req_id_fk,"Rejected");
	log_hist("74",$req_id_fk);
	header("location:$this_page");
	exit();
}

function authorize_status() {
	$approval_status = array("authorized","rejected");
	echo "<select name = \"req_det_stat[]\">";
	foreach($approval_status as $status) {
		echo "<option value =\"$status\">".strtoupper($status)."</option>";
	}
	echo "</select>";
}

$query  = "SELECT r.id, 
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
				  r.appr_note,
				  r.code_date, 
				  r.code_val, 
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
$SQL 	= @mysql_query($query) or die(mysql_error());
$array 	= @mysql_fetch_array($SQL,MYSQL_ASSOC);

$req_det_q  ="SELECT rd.id AS rdid, 
					rt.name AS rtname, 
					rd.status as rdstat, 
					al.lname AS alname,
					rd.confID, 
                    CONCAT(u.fname,' ',u.lname) AS cname, 
                    rd.confNote, 
                    rd.confDate, 
                    rd.confirm  
			  FROM req_det rd 
					LEFT JOIN req_items rt ON (rd.item_id_fk = rt.id) 
					LEFT JOIN acc_level al ON (al.id = rd.acclvl_id_fk) 
					LEFT JOIN user u ON (u.id = rd.confID)
			  WHERE rd.req_id_fk = '$req_id' AND rd.del = 0;";
$req_det_SQL = @mysql_query($req_det_q) or die(mysql_error());

$button = array("authorized"=>array("submit"=>"  AUTHORIZE  "),
				"auth_all"=>array("submit"=>"  AUTHORIZE ALL  "),
				"reject_all"=>array("submit"=>"  REJECT ALL  "));	

$req_list_po_q = "SELECT DISTINCT(p.id) AS 'pid', p.po_nbr as 'pnbr',r.id as 'rid' 
                  FROM req r 
					LEFT JOIN po_req pd ON (pd.req = r.id) 
					LEFT JOIN po p ON (p.id = pd.po)
                  WHERE p.del = '0' AND r.id = '$req_id';";
$req_list_po_SQL = @mysql_query($req_list_po_q) or die(mysql_error());

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2>AUTHORIZATION FORM DETAILS</h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><input type="hidden" name="req_id_fk" value="<?=$array["id"]?>">&nbsp;</td></tr>
	<tr><td>[&nbsp;<a href="./auth_hm.php">Back to the Authorization Page</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		<?=($array["status"]=="pending")?genButton($button):"";?>
		<div class="span8 well">
		<table border="0" cellpadding="1" cellspacing="0">
		<tr><td><b>ID : </b>#<?=($array["id"])?ucwords($array["id"]):"&nbsp; -"?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<?php if(mysql_num_rows($req_list_po_SQL)>= 1) { ?>
		<tr><td>
			<table>
				<tr><td><b>THIS REQ IS LISTED IN PO : </b></td></tr>
				<?php while($rlpo_arr = mysql_fetch_array($req_list_po_SQL,MYSQL_ASSOC)) { ?>
				<tr><td>- <a href="javascript:openW('./print_po.php?id=<?=$rlpo_arr["pid"]?>','Print_PO',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><?=$rlpo_arr["pnbr"]?></a></td></tr> 
				<?php } ?>
			</table> 
		</td></tr>
		<tr><td>&nbsp;</td></tr>	
		<?php } ?>
		<tr><td>
			<label><b>TYPE</b></label>
			<?=strtoupper($array["req_type"])?></b>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
 			<label><b>ACCOUNT INFORMATION&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				DATE: </b><?=($array["req_date"])?cplday('d M Y', $array["req_date"]):"&nbsp; -";?></label>
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
				<tr><td colspan="3">&nbsp;</td></tr>
<?php while($req_det_array = mysql_fetch_array($req_det_SQL, MYSQL_ASSOC)){ ?>
<tr align ="left" valign="top">
	<td>&nbsp;-&nbsp;<?=ucwords($req_det_array["rtname"]);?><input type="hidden" name="req_det_id[]" value="<?=ucwords($req_det_array["rdid"]);?>"></td>
	<td>&nbsp;->&nbsp;(Grup/Level :<?=($req_det_array["alname"])?strtoupper($req_det_array["alname"]):"-";?>&nbsp;)*</td>
	<td>&nbsp;->&nbsp;<?=($req_det_array["rdstat"] != "pending")?strtoupper($req_det_array["rdstat"]):authorize_status();?></td>
</tr>
<tr align ="left" valign="top">	
	<td>&nbsp;<?=($req_det_array["confID"])?"Confirmed by : ".ucwords($req_det_array["cname"]):"";?>&nbsp;</td>
	<td colspan="2">&nbsp;<?=($req_det_array["confNote"])?"Note : ".nl2br(trim($req_det_array["confNote"])):"";?>&nbsp;</td>
</tr>
<tr><td colspan="3">&nbsp;</td></tr>
<?php } ?>		</table>
		</td></tr>		
		<tr><td>&nbsp;</td></tr>
		<tr><td>(*) Only for Requesting Account</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<label><b>DETAILS/OTHERS</b></label>
					<?=($array["details"])?nl2br(trim($array["details"])):"&nbsp; -"?>
		</td></tr>
<?php if($array["status"] == "pending"){?>
		<tr><td>&nbsp;</td></tr>
		<tr><td><label><b>AUTHORIZER COMMENTS</b></label>
		<table border="0" cellpadding="0">
			<tr valign="top">
				<td><textarea cols="50" rows="5" name="auth_comm" wrap="virtual"></textarea></td>
			</tr></table>
		</td></tr>
<?php } ?>
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
				<td><?=($array["code_date"] != "0000-00-00 00:00:00")?cplday('d M Y',$array["code_date"]):"&nbsp; -";?></td></tr>	
		</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
	</div>
	<?=($array["status"]=="pending")?genButton($button):"";?>
</td></tr>
<tr><td>[&nbsp;<a href="./auth_hm.php">Back to the Authorization Page</a>&nbsp;]</td></tr>
</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>