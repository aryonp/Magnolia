<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title 	= "Agreement";
$page_id_left 	= "15";
$page_id_right 	= "44";
$category_page 	= "settings";
$page_id_right 	= "";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$agree_data_query 	= "SELECT a.id, CONCAT(u.fname,' ',u.lname) AS fullname, d.name as dname, b.name as bname, a.status, CONCAT(m.fname,' ',m.lname) AS mgr_name, a.mgr_status, a.date, a.ackdate, a.code, CONCAT(v.fname,' ',v.lname) AS valname, a.code_date, a.code_notes FROM agreement a LEFT JOIN user u ON (u.id = a.user_id_fk) LEFT JOIN user m ON (m.id = a.mgr_id) LEFT JOIN user v ON (v.id = a.code_val) LEFT JOIN departments d ON (d.id = u.dept_id_fk) LEFT JOIN branch b ON (b.id = u.branch_id_fk) WHERE u.id = '".$_SESSION['uid']."';";
$agree_data_SQL 	= @mysql_query($agree_data_query) or die(mysql_error());
$agree_data_array 	= mysql_fetch_array($agree_data_SQL);

$q_agree 	= "SELECT a.status, a.mgr_status FROM agreement a WHERE a.user_id_fk = '".$_SESSION['uid']."' ";
$agree 		= @mysql_query($q_agree) or die(mysql_error());
$showAgree 	= mysql_fetch_array($agree);

$file_agreeement = "files/agreement.txt";
$dispAgree = implode("",file($file_agreeement));

$this_page = $_SERVER['PHP_SELF'];

if(isset($_POST['agree'])){
	$code_file_agree = genfilecode("ITAF",$_SESSION['bid']);
	if(mysql_num_rows($agree) == false){
		$agreeSQL="INSERT INTO agreement (code,user_id_fk,status,mgr_id,date) VALUES ('$code_file_agree','".$_SESSION['uid']."','1','".$_SESSION['mid']."','".date('Y-m-d')."') ";
	}
	else {
		$agreeSQL ="UPDATE agreement SET status = '1', date = '".date('Y-m-d')."', mgr_id= '".$_SESSION['mid']."', code = '$code_file_agree' WHERE user_id_fk = '".$_SESSION['uid']."' ";
	}
	@mysql_query($agreeSQL) or die(mysql_error());
	log_hist("38");
	header("location:$this_page");
}

$button = array("agree"=>array("submit"=>"  I AGREE  "));

include THEME_DEFAULT.'header.php';?>

<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2>AGREEMENT BOX</h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td>&nbsp;</td></tr>		
<//-----------------USR-START----------------------------------------------------//>
<tr><td>
<table border="0" width="500">
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
		<td align="center"><div class="alert alert-error"><b>No Policies data</b></div></td>
<?php }	else {	?>
		<td><div class="well">
			<?=trim(nl2br($dispAgree))?>
			</div>
		</td>
<?php } ?>
	</tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
<//-----------------------FOR COORDINATOR --------------------------//>
		<table border="0" width="100%" cellpadding="0" cellspacing="0">
			<tr align="center">
				<td>
<?=($showAgree[0] == 1)?"<b>YES, I AGREE</b>":"&nbsp;"; ?>
				<hr></td>
				<td width="100">&nbsp;</td>
				<td>
<?=($showAgree[1] == 1)?"<b>I ACKNOWLEDGED</b>":"&nbsp;"; ?>
				<hr></td></tr>
			<tr><td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>Manager's Name/Nama Manajer:</td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td><b><?=($agree_data_array[1])?ucwords($agree_data_array[1]):"&nbsp; -";?></b></td>
				<td>&nbsp;</td>
				<td><b><?=($agree_data_array[5])?ucwords($agree_data_array[5]):"&nbsp; -";?></b></td></tr>
			<tr><td colspan="3">&nbsp;</td></tr>
			<tr><td>Date/Tgl.: <b><?=($agree_data_array[7])?cplday('d M Y',$agree_data_array[7]):"-";?></b></td>
				<td>&nbsp;</td>
				<td>Date/Tgl.: <b><?=($agree_data_array[8] OR $agree_data_array[8] != "0000-00-00")?cplday('d M Y',$agree_data_array[8]):"-";?></b></td></tr>
		</table>
<//-----------------------FOR COORDINATOR --------------------------//>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
<?=($showAgree[0] != 1)?"<tr><td height=\"24\" valign=\"middle\">".genButton($button)."</td></tr>":"";?>
	<tr><td>&nbsp;</td></tr>
			<tr><td>	
			<hr>
		<label><b>ITS USE ONLY</b></label>
		<table border="0" cellpadding="1" cellspacing="1">
			<tr><td align="left"><b>FILE NO</b></td>
				<td align="left">:</td>
				<td><?=($agree_data_array[9])?ucwords($agree_data_array[9]):"-";?></td>
				<td align="left" rowspan="4">&nbsp;</td>
				<td rowspan="4" align="left" valign="top"><b>NOTES</b>:<br/>
				<?=($agree_data_array[12])?nl2br($agree_data_array[12]):"-";?></td></tr>
			<tr><td align="left"><b>VALIDATED</b></td>
				<td align="left">:</td>
				<td><?=($agree_data_array[10])?ucwords($agree_data_array[10]):"-";?></td></tr>
			<tr><td align="left"><b>DATE</b></td>
				<td align="left">:</td>
				<td><b><?=(!($agree_data_array[11]) OR $agree_data_array[11] != "0000-00-00 00:00:00")?cplday('d M Y',$agree_data_array[11]):"-";?></b></td></tr>	
		</table></td></tr>
</table>
</td></tr>
<//-----------------USR-END----------------------------------------------------//>
<tr><td>&nbsp;</td></tr>
</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>