<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title="Request For Approval";
$page_id_left ="10";
$page_id_right ="21";
$category_page = "archive";
chkSecurity($page_id_right);

$rfa_id = $_GET['id'];
$rfa_info_query ="SELECT r.id, r.code, CONCAT(u.fname,' ',u.lname) AS fullname, b.id, r.designation, r.date, CONCAT(a.fname,' ',a.lname) AS apprname, a.sign, r.appr_note, CONCAT(v.fname,' ',v.lname) AS valname, r.code_date, r.code_notes FROM rfa r LEFT JOIN user u ON (u.id = r.user_id_fk) LEFT JOIN user a ON (a.id = r.appr_id_fk) LEFT JOIN user v ON (v.id = r.code_val) LEFT JOIN branch b ON (b.id = r.branch_id_fk) WHERE r.id = '$rfa_id' ";
$rfa_info_SQL = @mysql_query($rfa_info_query) or die(mysql_error());
$rfa_info_array = mysql_fetch_array($rfa_info_SQL);

$rfa_det_info_query = "SELECT rd.item, rd.purpose, rd.spec_notes, v.name, rd.status FROM rfa_det rd LEFT JOIN vdr v ON (v.id = rd.vdr_id_fk) WHERE rd.rfa_id_fk = '$rfa_id' AND rd.del = '0';";
$rfa_det_info_SQL = @mysql_query($rfa_det_info_query) or die(mysql_error());

log_hist("83",$rfa_id);
include THEME_DEFAULT.'print_header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">					
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		<table border="0" width="100%">
			<tr><td>
				<table border="0" class="table-condensed">
					<tr><td><b>ID</b></td>
						<td>:</td>
						<td>#<?=$rfa_info_array[0]?></td></tr>
					<tr><td><b>NO.</b></td>
						<td>:</td>
						<td><?=$rfa_info_array[1]?></td></tr>
					<tr><td><b>REQUESTOR</b></td>
						<td>:</td>
						<td><?=ucwords($rfa_info_array[2])?></td></tr>
					<tr><td><b>DATE</b></td>
						<td>:</td>
						<td valign="middle"><?=cplday('d F Y',$rfa_info_array[5])?></td></tr>
				</table>
			</td></tr>
			<tr><td>&nbsp;</td></tr>			
<?php 
$count = 1;
while($rfa_det_info_array = mysql_fetch_array($rfa_det_info_SQL)) { ?>
				<tr><td>
					<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-bordered table-condensed">
						<tr class="listview">
							<td width="25"><b>&nbsp;NO.</b></td>
							<td colspan="2"><b>&nbsp;DESCRIPTION</b></td>
							<td><b>&nbsp;APPROVAL&nbsp;</b></td>
						</tr>
						<tr><td rowspan="7" align="left" valign="top">&nbsp;&nbsp;<?=$count?>.</td>
							<td colspan="2">
								-&nbsp;<?=strtoupper($rfa_det_info_array[0])?>&nbsp;-<br /><br />
								<b>PURPOSE:</b><br />
								<?=($rfa_det_info_array[1])?nl2br($rfa_det_info_array[1]):"&nbsp; - "?><br /><br />
								<b>BRANCHES:</b><br />
								<?=ucwords($rfa_info_array[3])?><br /><br />
								<b>REQ. SPECIFICS:</b><br />
								<?=($rfa_det_info_array[2])?nl2br($rfa_det_info_array[2]):"&nbsp; -"?><br /><br />
								<b>VENDOR QUOTATION:</b><br />
								<?=($rfa_det_info_array[3])?$rfa_det_info_array[3]:"&nbsp; -"?><br /><br />
							</td>
							<td rowspan="7" align="left" valign="top">&nbsp;<?=strtoupper($rfa_det_info_array[4])?></td>
						</tr>
					</table>
				</td></tr>
<?php 
$count++;
} ?>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<label><b>APPROVER'S NOTE</b></label>
		<?=($rfa_info_array[8])?nl2br($rfa_info_array[8]):"&nbsp; -";?>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr><td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="center" width="20%">
					Authorized by:<br><br>
					<?=($rfa_info_array[7])?"<img src=".$rfa_info_array[7]." width=\"150\" height=\"100\" border=\"0\">":"<br><br><br>"?>
					<?=($rfa_info_array[6])?strtoupper($rfa_info_array[6]):"-";?>
				</td></tr>
			</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
  		<hr>
  		<label><b>ITS USE ONLY</b></label>
		<table border="0" cellpadding="1" cellspacing="1">
			<tr><td align="left" width=\"80\"><b>FILE NO</b></td>
				<td align="left" width=\"10\">:</td>
				<td width=\"200\"><?=($rfa_info_array[1])?ucwords($rfa_info_array[1]):"-";?></td>
				<td align="left" rowspan="4">&nbsp;</td>
				<td rowspan="4" align="left" valign="top"><b>NOTES</b>:<br/>
					<?=($rfa_info_array[11])?strip_tags(nl2br($rfa_info_array[11])):"-";?></td></tr>
			<tr><td align="left"><b>VALIDATED</b></td>
				<td align="left">:</td>
				<td><?=($rfa_info_array[9])?ucwords($rfa_info_array[9]):"-";?></td></tr>
			<tr><td align="left"><b>DATE</b></td>
				<td align="left">:</td>
				<td><?=($rfa_info_array[10] != "0000-00-00 00:00:00")?cplday('d F Y',$rfa_info_array[10]):"-";?></td></tr>	
		</table>
		</td></tr>	
			</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
  		<tr><td>&nbsp;</td></tr>
	</table>
	</form>
<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.'print_footer.php';?>