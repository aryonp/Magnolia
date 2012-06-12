<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();
$page_title="Agreement";
$page_id_left ="10";
$page_id_right ="22";
$category_page = "archive";
chkSecurity($page_id_right);
$agr_id = $_GET['id'];
$agree_data_query = "SELECT a.id, CONCAT(u.fname,' ',u.lname) AS fullname, d.name as dname, b.name as bname, a.status, ".
					"CONCAT(m.fname,' ',m.lname) AS mgrname, a.mgr_status, a.date, a.ackdate, ".
					"a.code, CONCAT(v.fname,' ',v.lname) AS valname, a.code_date, a.code_notes ".
					"FROM agreement a LEFT JOIN user u ON (u.id = a.user_id_fk) ".
					"LEFT JOIN user m ON (m.id = a.mgr_id) ".
					"LEFT JOIN user v ON (v.id = a.code_val) ".
					"LEFT JOIN departments d ON (d.id = u.dept_id_fk) ".
					"LEFT JOIN branch b ON (b.id = u.branch_id_fk) ".
					"WHERE a.id = '$agr_id' ";
$agree_data_SQL = @mysql_query($agree_data_query) or die(mysql_error());
$agree_data_array = mysql_fetch_array($agree_data_SQL);

$file_agreeement = "./files/agreement.txt";
$dispAgree = implode("",file($file_agreeement));

include THEME_DEFAULT.'print_header.php';
?>
<//-----------------CONTENT-START-------------------------------------------------//>
<//-----------------USR-START----------------------------------------------------//>
<table border="0" width="100%">
	<tr><td><div style='border: 1px solid #CECECE; padding: 2px; margin-top: 5px; margin-bottom: 5px;'>
		<table border="0" width="100%" cellpadding="0" cellspacing="0">
			<tr><td align="center"><b>
				IT PC Guidelines Policy Reference-No: 1.0.0 - <br />
				User Acceptance and Agreement Form<br />
				<i>Referensi Kebijakan Pedoman PC IT - No: 1.0.0<br />
				Formulir Penerimaan dan Persetujuan Pengguna</i><b></td></tr>
		</table></div>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		<table border="0" width="100%" cellpadding="0" cellspacing="0">
			<tr><td colspan="2">EMPLOYEE NAME/<i>Nama Pegawai</i>:</td></tr>
			<tr><td colspan="2"><b><?=($agree_data_array[1])?ucwords($agree_data_array[1]):"&nbsp; -";?></b></td></tr>
			<tr><td colspan="2" height="8">&nbsp;</td></tr>
			<tr><td>DEPARTMENT/<i>Departemen</i>:</td>
				<td>BRANCH/<i>Cabang</i>:<br /></td></tr>
			<tr><td><b><?=($agree_data_array[2])?ucwords($agree_data_array[2]):"&nbsp; -";?></b></td>
				<td><b><?=($agree_data_array[3])?ucwords($agree_data_array[3]):"&nbsp; -";?></b></td></tr>
		</table>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr>
<?php if (!file_exists($file_agreeement)) {?>
		<td align="center"><font color="red"><b>No Policies data</b></font></td>
<?php	}	else {	?>
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
<?=($agree_data_array[4] == "1")?"<b>YES, I AGREE</b>":"&nbsp;"; ?>
				<hr></td>
				<td width="100">&nbsp;</td>
				<td>
<?=($agree_data_array[6] == "1")?"<b>I ACKNOWLEDGED</b>":"&nbsp;"; ?>
				<hr></td></tr>
			<tr><td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>Manager's Name/Nama Manajer:</td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td><b><?=($agree_data_array[1])?ucwords($agree_data_array[1]):"&nbsp; -";?></b></td>
				<td>&nbsp;</td>
				<td><b><?=($agree_data_array[5])?ucwords($agree_data_array[5]):"&nbsp; -";?></b></td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td>Date/Tgl.: <b><?=($agree_data_array[7] != "0000-00-00")?cplday('d F Y',$agree_data_array[7]):"&nbsp; -";?></b></td>
				<td>&nbsp;</td>
				<td>Date/Tgl.: <b><?=($agree_data_array[8] != "0000-00-00")?cplday('d F Y',$agree_data_array[8]):"&nbsp; -";?></b></td></tr>
		</table>
<//-----------------------FOR COORDINATOR --------------------------//>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><hr>
		<label><b>ITS USE ONLY</b></label>
		<table border="0" cellpadding="1" cellspacing="1">
			<tr><td align="left" width=\"80\"><b>FILE NO</b></td>
				<td align="left" width=\"10\">:</td>
				<td width=\"200\"><?=($agree_data_array[9])?ucwords($agree_data_array[9]):"&nbsp; -";?></td>
				<td align="left" rowspan="4">&nbsp;</td>
				<td rowspan="4" align="left" valign="top"><b>NOTES</b>:<br/>
				<?=($agree_data_array[12])?nl2br($agree_data_array[12]):"&nbsp; -";?></td></tr>
			<tr><td align="left"><b>VALIDATED</b></td>
				<td align="left">:</td>
				<td><?=($agree_data_array[10])?ucwords($agree_data_array[10]):"&nbsp; -";?></td></tr>
			<tr><td align="left"><b>DATE</b></td>
				<td align="left">:</td>
				<td><b><?=($agree_data_array[11])?cplday('d F Y',$agree_data_array[11]):"&nbsp; -";?></b></td></tr>	
		</table></td></tr>
</table>
<//-----------------USR-END----------------------------------------------------//>
<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.'print_footer.php';?>