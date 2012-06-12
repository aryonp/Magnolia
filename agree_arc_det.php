<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title		= "Archive : Agreement";
$page_id_left 	= "10";
$page_id_right 	= "22";
$category_page 	= "archive";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

if(isset($_GET['id'])) {$agr_id = $_GET['id'];}
$q_agree ="SELECT a.status, a.mgr_status ".
		  "FROM agreement a LEFT JOIN user u ON (u.id = a.user_id_fk) ".
		  "WHERE u.id = '$agr_id' ";
$agree 		= @mysql_query($q_agree) or die(mysql_error());
$showAgree 	= mysql_fetch_array($agree);

$status				="&nbsp;";
$file_agreeement 	= "./files/agreement.txt";
$dispAgree 			= implode("",file($file_agreeement));

$agree_data_query = "SELECT a.id, CONCAT(u.fname,' ',u.lname) AS fullname, d.name as dname, b.name as bname, a.status, ".
						"CONCAT(m.fname,' ',m.lname) AS mname, a.mgr_status as mstatus, a.date, a.ackdate, ".
						"a.code, a.code_val, a.code_date, a.code_notes ".
						"FROM agreement a LEFT JOIN user u ON (u.id = a.user_id_fk) ".
						"LEFT JOIN user m ON (m.id = a.mgr_id) ".
						"LEFT JOIN user v ON (v.id = a.code_val) ".
						"LEFT JOIN departments d ON (d.id = u.dept_id_fk) ".
						"LEFT JOIN branch b ON (b.id = u.branch_id_fk) ".
						"WHERE a.id = '$agr_id' ";
$agree_data_SQL = @mysql_query($agree_data_query) or die(mysql_error());
$array 			= mysql_fetch_array($agree_data_SQL);

$this_page = $_SERVER['PHP_SELF']."?id=".$agr_id;

if(isset($_POST['update_file'])){
	$file_id 		= trim($_POST['file_id']);
	$file_note 		= trim($_POST['its-notes']); 
	$file_checked 	= trim($_POST['checked_name']); 
	$file_validated = trim($_POST['validated_name']);
	$file_date 		= date('Y-m-d');
	
	if ($file_validated == "-") {
		$status ="<p class=\"yellowbox\">Please complete every information that needed !</p>";
	} 
	
	else {
		$update_file_query = "UPDATE agreement SET code_date = '$file_date', code_val = '$file_validated', code_notes = '$file_note' WHERE id = '$agr_id';";
		@mysql_query($update_file_query) or die(mysql_error());
		log_hist("",$agr_id);
		header("location:$this_page"); 
	}
}
$button = array("update_file"=>array("submit"=>"  UPDATE FILE  "));

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2>AGREEMENT FILE DETAILS</h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><?=$status?></td></tr>
	<?=back_button();?>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
	<table border="0" width="500">
	<tr><td>
		<table border="0" width="100%" cellpadding="0" cellspacing="0">
			<tr><td align="center"><b>
				IT PC Guidelines Policy Reference-No: 1.0.0 - <br />
				User Acceptance and Agreement Form<br />
				<i>Referensi Kebijakan Pedoman PC IT - No: 1.0.0<br />
				Formulir Penerimaan dan Persetujuan Pengguna</i><b></td></tr>
		</table>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		<table border="0" width="100%" cellpadding="0" cellspacing="0">
			<tr><td colspan="2">EMPLOYEE NAME/<i>Nama Pegawai</i>:</td></tr>
			<tr><td colspan="2"><b><?=($array["fullname"])?ucwords($array["fullname"]):"&nbsp; -";?></b></td></tr>
			<tr><td colspan="2" height="8">&nbsp;</td></tr>
			<tr><td>DEPARTMENT/<i>Departemen</i>:</td>
				<td>BRANCH/<i>Cabang</i>:<br /></td></tr>
			<tr><td><b><?=($array["dname"])?ucwords($array["dname"]):"&nbsp; -";?></b></td>
				<td><b><?=($array["bname"])?ucwords($array["bname"]):"&nbsp; -";?></b></td></tr>
		</table>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
<?php if (!file_exists($file_agreeement)) {?>
		<td align="center"><font color="red"><b>No Policies data</b></font></td>
<?php	}
	else {	?>
		<td><div class="well">
			<?=trim(nl2br($dispAgree))?>
			</div>
		</td>
	<?php } ?>
			</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		<table border="0" width="100%" cellpadding="0" cellspacing="0">
			<tr align="center">
				<td>
<?=($array["status"] == "1")?"<b>YES, I AGREE</b>":"&nbsp;"; ?>
				<hr></td>
				<td width="100">&nbsp;</td>
				<td>
<?=($array["mstatus"] == "1")?"<b>I ACKNOWLEDGED</b>":"&nbsp;"; ?>
				<hr></td></tr>
			<tr><td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>Manager's Name/Nama Manajer:</td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td><b><?=($array["fullname"])?ucwords($array["fullname"]):"&nbsp; -";?></b></td>
				<td>&nbsp;</td>
				<td><b><?=($array["mname"])?ucwords($array["mname"]):"&nbsp; -";?></b></td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td>Date/Tgl.: <b><?=($array["date"] != "0000-00-00")?cplday('d F Y',$array["date"]):"&nbsp; -";?></b></td>
				<td>&nbsp;</td>
				<td>Date/Tgl.: <b><?=($array["ackdate"] != "0000-00-00")?cplday('d F Y',$array["ackdate"]):"&nbsp; -";?></b></td></tr>
		</table>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		<hr>
  		<label><b>ITS USE ONLY</b></label>
		<table border="0" cellpadding="1" cellspacing="1">
			<tr><td align="left"><b>FILE NO</b></td>
				<td align="left">:</td>
				<td><?=($array["code"])?ucwords($array["code"]):"&nbsp; -";?></td>
				<td align="left" rowspan="4">&nbsp;</td>
				<td rowspan="4" align="left" valign="top"><b>NOTES</b>:<br/>
					<textarea cols="35" rows="3" name="its-notes" wrap="virtual"><?=($array["code_notes"])?strip_tags(nl2br($array["code_notes"])):"-";?></textarea></td></tr>
			<tr><td align="left"><b>VALIDATED</b></td>
				<td align="left">:</td>
				<td><select name="validated_name">
					<option value="-">-----------------</option>
<?php	$val_name_query = "SELECT u.id, CONCAT(u.fname,' ',u.lname) AS fullname ".
						  "FROM user u LEFT JOIN user_level ul ON (ul.id = u.level_id_fk) ".
						  "WHERE ul.id <= '5' AND u.del = '0' AND u.hidden = '0' AND u.active = '1' ;";
		$val_name_SQL = mysql_query($val_name_query); 
		while($val_name_array = mysql_fetch_array($val_name_SQL)) { 
		$compare_validated = ($val_name_array[0] == $array["code_val"])?"SELECTED":"";?>
		<option value="<?=$val_name_array[0]?>" <?=$compare_validated?>><?=ucwords($val_name_array[1])?></option>
<?php } ?>
	</select></td></tr>
			<tr><td align="left"><b>DATE</b></td>
				<td align="left">:</td>
				<td><b><?=($array["code_date"] != "0000-00-00 00:00:00")?cplday('d M Y',$array["code_date"]):"-";?></b></td></tr>	
		</table>
			</td></tr>
			
		</table>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><?=genButton($button)?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<?=back_button();?>
	<tr><td>&nbsp;</td></tr>
	<input type="hidden" name="file_id" value="<?=$array[13]?>">
</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>