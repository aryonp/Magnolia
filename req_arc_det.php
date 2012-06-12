<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'notif.lib.php';
chkSession();

$page_title		= "Archive : Request";
$page_id_left 	= "10";
$page_id_right 	= "20";
$category_page 	= "archive";
chkSecurity($page_id_right);

if(isset($_GET['id'])) {$req_id = $_GET['id'];}

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

$req_det_q  = "SELECT rd.id, 
                      rt.name, 
                      rd.status, 
                      al.lname,
                      rd.confID, 
                      CONCAT(u.fname,' ',u.lname) AS cname, 
                      rd.confNote, 
                      rd.confDate, 
                      rd.confirm 
               FROM req_det rd 
					LEFT JOIN req_items rt ON (rd.item_id_fk = rt.id) 
					LEFT JOIN acc_level al ON (al.id = rd.acclvl_id_fk) 
					LEFT JOIN user u ON (u.id = rd.confID)
			   WHERE rd.req_id_fk = '$req_id' AND rd.del = '0';";
$req_det_SQL 	= @mysql_query($req_det_q) or die(mysql_error());

$status 	= "&nbsp;";
$this_page 	= $_SERVER['PHP_SELF']."?id=".$req_id;

if (isset($_POST['update_req_det'])){
	$rd 			= trim($_POST['rd']);
	$confID 		= trim($_SESSION['uid']);
	$confNote 		= mysql_real_escape_string(trim($_POST['confNote']));
	$confDate 		= date('Y-m-d H:i:s');
	$upd_rd_query  	= "UPDATE req_det SET confID = '$confID', confNote = '$confNote', confDate = '$confDate', confirm = 1 WHERE id = '$rd';";
	@mysql_query($upd_rd_query) or die(mysql_error());
	log_hist(138,$rd);
	notif_rd($rd);
	header("location:$this_page");
	exit();
}

if(isset($_POST['update_file'])){
	$file_id 		= $req_id;
	$file_note 		= trim($_POST['its-notes']); 
	$file_validated = trim($_POST['validated_name']);
	$file_date 		= date('Y-m-d H:i:s');
	
	if ($file_validated == "-") {
		$status ="<p class=\"alert\">Please complete every information that needed !</p>";
	} 
	
	else {
		$update_file_query = "UPDATE req 
							  SET code_date = '$file_date', code_val = '$file_validated', code_notes = '$file_note' 
							  WHERE id = '$file_id';";
		mysql_query($update_file_query) or die(mysql_error());
		log_hist("69",$req_id);
		header("location:$this_page"); 
	}
	
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

$button = array("update_file" => array("submit" => "  UPDATE FILE  "));

$req_list_po_q = "SELECT DISTINCT(p.id) AS 'pid', p.po_nbr as 'pnbr',r.id as 'rid' 
                  FROM req r 
					LEFT JOIN po_req pd ON (pd.req = r.id) 
					LEFT JOIN po p ON (p.id = pd.po)
                  WHERE p.del = '0' AND r.id = '$req_id';";
$req_list_po_SQL = @mysql_query($req_list_po_q) or die(mysql_error());

include THEME_DEFAULT.'header.php'; ?>               
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">	
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2>REQUEST FORM DETAILS</h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><?=$status?></td></tr>
	<tr><td>[&nbsp;<a href="./req_arc.php">BACK TO THE ARCHIVE REQUEST PAGE</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		<div class="span8 well">
		<table border="0" cellpadding="1" cellspacing="0">
		<tr><td<b>ID : </b>#<?=($array["id"])?strtoupper($array["id"]):"-"?></td></tr>
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
			<?=($array["req_type"])?strtoupper($array["req_type"]):"-"?>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
 			<label><b>ACCOUNT INFORMATION&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				DATE: </b><?=cplday('d F Y',$array["req_date"])?></label>
			<table border="0" width="100%" cellpadding="1" cellspacing="1">
				<tr><td colspan="2"><b>NAME:</b></td></tr>
				<tr><td colspan="2"><?=($array["emp_name"])?ucwords($array["emp_name"]):"-"?></td></tr>
				<tr><td><b>DEPARTMENT:</b></td>
					<td><b>STATUS:</b></td></tr>
				<tr><td><?=ucwords($array["dname"])?></td>
					<td><?=($array["emp_status"])?ucwords($array["emp_status"]):"-"?></td></tr>
				<tr><td><b>TITLE:</b></td>
					<td>&nbsp;</td></tr>
				<tr><td><?=($array["emp_title"])?ucwords($array["emp_title"]):"-"?></td>
					<td>&nbsp;</td></tr>
				<tr><td><b>BRANCH:</b></td>
					<td>&nbsp;</td></tr>
				<tr><td><?=ucwords($array["bname"])?></b></td>
					<td>&nbsp;</td></tr>
				<tr><td colspan="3" height="1"></td></tr>	
			</table></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<label><b>REQ. LIST</b></label>
				<table border="0" cellpadding="1" cellspacing="0">
				<tr><td colspan="4">&nbsp;</td></tr>
<?php while($req_det_array = mysql_fetch_array($req_det_SQL,MYSQL_ASSOC)){ 
		if(isset($_GET['rd']) AND $_GET['rd'] == $req_det_array["id"]) { ?>
			<tr align ="left" valign="top">
					<td>&nbsp;- #<?=ucwords($req_det_array["id"]);?> -> <?=ucwords($req_det_array["name"]);?></td>
					<td>&nbsp;->&nbsp;(Grup/Level : <?=($req_det_array["lname"])?strtoupper($req_det_array["lname"]):"-";?>&nbsp;)*</td>
					<td colspan="2">&nbsp;->&nbsp;<?=strtoupper($req_det_array["status"]);?>&nbsp;<input type="hidden" name="rd" value="<?=$req_det_array["id"]?>"/></td>
			</tr>
			<tr align ="left" valign="top">
					<td colspan="2">&nbsp;<textarea cols="60" rows="2" name="confNote" wrap="virtual"><?=(!empty($req_det_array["confNote"]))?nl2br(trim($req_det_array["confNote"])):"";?></textarea>&nbsp;</td>
					<td>&nbsp;
						<div class="btn-group">
						  <input type="submit" class="btn-info btn-small" name="update_req_det" value=" UPDATE ">
						  <input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL ">
						</div>
					</td>
			</tr>
			<tr><td colspan="4">&nbsp;</td></tr>
<?php 	} 
		else { ?>
			<tr align ="left" valign="top">
					<td>&nbsp;- #<?=ucwords($req_det_array["id"]);?> -> <?=ucwords($req_det_array["name"]);?>&nbsp;</td>
					<td>&nbsp;->&nbsp;(Grup/Level : <?=($req_det_array["lname"])?strtoupper($req_det_array["lname"]):"-";?>&nbsp;)*&nbsp;</td>
					<td>&nbsp;->&nbsp;<?=strtoupper($req_det_array["status"]);?>&nbsp;</td>
					<td rowspan="2">&nbsp;<?php if($req_det_array["confirm"] == 1 OR $req_det_array["status"] == "adm-authorized" OR $req_det_array["status"] == "adm-authorized (STOCK)") { echo "<a title=\"Edit RD\" href=\"$this_page&rd=".$req_det_array["id"]."\"><img src=\"".IMG_PATH."edit.png\"></a>"; } else { echo ""; } ?>&nbsp;</td>
			</tr>
			<tr align ="left" valign="top">	
					<td>&nbsp;<?=($req_det_array["confID"])?"Confirmed by : ".ucwords($req_det_array["cname"]):"";?>&nbsp;</td>
					<td colspan="2">&nbsp;<?=($req_det_array["confNote"])?"Note : ".nl2br(trim($req_det_array["confNote"])):"";?>&nbsp;</td>
			</tr>
			<tr><td colspan="4">&nbsp;</td></tr>
<?php 	}
	} ?>	 </table>
		</td></tr>		
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<label><b>DETAILS/OTHERS</b></label>
				<?=($array["details"])?nl2br(trim($array["details"])):"-"?>
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
			<tr valign="top"><td align="left"><b>FILE NO</b></td>
				<td align="left">:</td>
				<td><?=($array["code"])?ucwords($array["code"]):"-";?></td>
				<td align="left" rowspan="4">&nbsp;</td>
				<td rowspan="4" align="left"><b>NOTES</b>:<br/>
					<textarea cols="35" rows="3" name="its-notes" wrap="virtual"><?=($array["code_notes"])?strip_tags(nl2br($array["code_notes"])):"-";?></textarea></td></tr>
			<tr><td align="left"><b>VALIDATED</b></td>
				<td align="left">:</td>
				<td><select name="validated_name">
					<option value="-">-----------------</option>
<?php	$val_name_query = "SELECT u.id, CONCAT(u.fname,' ',u.lname) AS fullname FROM user u LEFT JOIN user_level ul ON (ul.id = u.level_id_fk) WHERE ul.id <= '5' AND u.del = '0' AND u.hidden = '0' AND u.active = '1' ;";
		$val_name_SQL = mysql_query($val_name_query); 
		while($val_name_array = mysql_fetch_array($val_name_SQL, MYSQL_ASSOC)) { 
		$compare_validated = ($val_name_array["id"] == $array["code_val"])?"SELECTED":"";?>
		<option value="<?=$val_name_array["id"]?>" <?=$compare_validated?>><?=ucwords($val_name_array["fullname"])?></option>
<?php } ?>
	</select></td></tr>
			<tr><td align="left"><b>DATE</b></td>
				<td align="left">:</td>
				<td><?=($array["code_date"]!="0000-00-00 00:00:00")?cplday('d F Y',$array["code_date"]):"-";?></td></tr>	
		</table>
		</td></tr>

		</table></div>
		</td></tr>
		<tr><td><?=genButton($button)?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[&nbsp;<a href="./req_arc.php">BACK TO THE ARCHIVE REQUEST PAGE</a>&nbsp;]</td></tr>
		<tr><td>&nbsp;</td></tr>
</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>