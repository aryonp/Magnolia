<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title		= "ITS Request Form";
$page_id_left 	= "10";
$page_id_right 	= "20";
$category_page 	= "archive";
chkSecurity($page_id_right);

$req_id = ((isset ($_GET['id']) && $_GET['id'] != '')?trim ($_GET['id']):'');
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

$det_query  = "SELECT rd.id, rt.name, 
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
			   WHERE rd.req_id_fk = '$req_id' AND rd.del = 0;";
$det_SQL 	= @mysql_query($det_query) or die(mysql_error());

log_hist(71,$req_id);
include THEME_DEFAULT.'print_header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		<table border="0" cellpadding="1" cellspacing="0" width="100%">
		<tr><td><b>ID :</b> #<?=($array["id"])?strtoupper($array["id"]):"&nbsp; -"?> </td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<label><b>TYPE</b></label>
			<?=($array["req_type"])?strtoupper($array["req_type"]):"&nbsp; -"?>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
 			<label><b>ACCOUNT INFORMATION&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; 
				DATE: </b><?=($array["req_date"] != "0000-00-00")?cplday('d F Y',$array["req_date"]):"&nbsp; -"?></label>
			<table border="0" width="100%" cellpadding="1" cellspacing="1">
				<tr><td colspan="2"><b>NAME:</b></td></tr>
				<tr><td colspan="2">
					<?=($array["emp_name"])?ucwords($array["emp_name"]):"&nbsp; -"?>
				</td></tr>
				<tr><td><b>DEPARTMENT:</b></td>
					<td><b>STATUS:</b></td></tr>
				<tr><td><?=($array["dname"])?ucwords($array["dname"]):"&nbsp; -"?></td>
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
			</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<label><b>REQ. LIST</b></label>
				<table border="0" cellpadding="1" cellspacing="0">
				<tr><td colspan="3">&nbsp;</td></tr>
<?php
while($req_det_array = mysql_fetch_array($det_SQL,MYSQL_ASSOC)){ ?>
			<tr align ="left" valign="top">
				<td>&nbsp;- <?=ucwords($req_det_array["name"]);?>&nbsp;</td>
				<td>&nbsp;->&nbsp;(Grup/Level : <?=($req_det_array["lname"])?strtoupper($req_det_array["lname"]):"-";?>&nbsp;)*&nbsp;</td>
				<td>&nbsp;->&nbsp;<?=strtoupper($req_det_array["status"]);?>&nbsp;</td>
			</tr>
			<tr align ="left" valign="top">	
					<td>&nbsp;<?=($req_det_array["confID"])?"Confirmed by : ".ucwords($req_det_array["cname"]):"";?>&nbsp;</td>
					<td colspan="2">&nbsp;<?=($req_det_array["confNote"])?"Note : ".nl2br(trim($req_det_array["confNote"])):"";?>&nbsp;</td>
			</tr>
			<tr><td colspan="3">&nbsp;</td></tr>
<?php } ?>		</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<label><b>DETAILS/OTHERS</b></label>
				&nbsp;<?=($array["details"])?nl2br(trim($array["details"])):"&nbsp; -"?>&nbsp;
		</td></tr>
<?php if($array["status"] != "pending"){?>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<label><b>AUTHORIZER COMMENTS</b></label>
		<?=($array["mgr_note"])?nl2br(trim($array["mgr_note"])):"-"?>
		</td></tr>
<?php } ?>
<?php if($array["status"] == "adm-approved" AND $array["status"] == "adm-approved (STOCK)" AND $array["status"] == "adm-rejected"){?>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<label><b>IT ADMIN COMMENTS</b></label>
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
		<table border="0" cellpadding="1" cellspacing="1" width="100%">
			<tr><td align="left" width="80"><b>FILE NO</b></td>
				<td align="left" width="10">:</td>
				<td width="200"><?=($array["code"])?ucwords($array["code"]):"&nbsp; -"?></td>
				<td align="left" rowspan="4">&nbsp;</td>
				<td rowspan="4" align="left" valign="top"><b>NOTES</b>:<br/>
					&nbsp;<?=($array["code_notes"])?nl2br($array["code_notes"]):"&nbsp; -"?></td></tr>
			<tr><td align="left"><b>VALIDATED</b></td>
				<td align="left">:</td>
				<td><?=($array["valname"])?ucwords($array["valname"]):"&nbsp; -"?></td></tr>
			<tr><td align="left"><b>DATE</b></td>
				<td align="left">:</td>
				<td><?=($array["code_date"] != "0000-00-00 00:00:00")?cplday('d F Y',$array["code_date"]):"&nbsp; -"?></td></tr>	
		</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
</td></tr></table>
<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.'print_footer.php';?>