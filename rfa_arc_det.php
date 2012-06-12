<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title		= "Archive : RFA";
$page_id_left 	= "10";
$page_id_right 	= "21";
$category_page 	= "archive";
chkSecurity($page_id_right);

$rfa_id 	= $_GET['id'];

$rfa_info_query ="SELECT r.id, 
						 r.code, 
						 CONCAT(u.fname,' ',u.lname) AS fullname, 
						 r.user_id_fk as uid, 
						 b.id as bid, 
						 r.designation, 
						 r.date, r.file, 
						 r.status, 
						 r.appr_note, 
						 r.appr_id_fk as aid, 
						 r.code_val, 
						 CONCAT(v.fname,' ',v.lname) AS vname, 
						 r.code_date, 
						 r.code_notes 
				   FROM rfa r 
				   	LEFT JOIN user u  ON (u.id = r.user_id_fk) 
				   	LEFT JOIN branch b ON (b.id = r.branch_id_fk) 
				   	LEFT JOIN user v ON (v.id = r.code_val) 
				   WHERE r.id = '$rfa_id' AND r.del = 0;";

$rfa_info_SQL	= @mysql_query($rfa_info_query) or die(mysql_error());
$array 			= mysql_fetch_array($rfa_info_SQL,MYSQL_ASSOC);

$rfa_det_info_query = "SELECT rd.item, 
                              rd.purpose, 
                              rd.spec_notes, 
                              v.name as vname, 
                              rd.status 
                       FROM rfa_det rd 
                       LEFT JOIN vdr v ON (v.id = rd.vdr_id_fk) 
                       WHERE rd.rfa_id_fk = '$rfa_id' AND rd.del = 0;";
$rfa_det_info_SQL = @mysql_query($rfa_det_info_query) or die(mysql_error());

$status		= "&nbsp;";

$this_page 	= $_SERVER['PHP_SELF']."?id=".$rfa_id;

if(isset($_POST['update_file'])){
	$file_note 		= trim($_POST['its-notes']); 
	$file_validated = trim($_POST['validated_name']);
	$file_date 		= date('Y-m-d H:i:s');
	
	if ($file_validated == "-") {
		$status ="<p class=\"yellowbox\">Please complete every information that needed !</p>";
	}
	
	else {
		$update_file_query = "UPDATE rfa 
							  SET code_date = '$file_date', code_val = '$file_validated', code_notes = '$file_note' 
							  WHERE id = '$rfa_id';";
		@mysql_query($update_file_query) or die(mysql_error());
		log_hist("81",$rfa_id);
		header("location:$this_page"); 
	}
	
}

$button = array("update_file"=>array("submit"=>"  UPDATE FILE  "));

$rfa_list_po_q = "SELECT DISTINCT(p.id) AS 'pid', p.po_nbr as 'pnbr',r.id as 'rid' 
                  FROM rfa r 
					LEFT JOIN po_rfa pd ON (pd.rfa=r.id) 
					LEFT JOIN po p ON (p.id = pd.po)
                  WHERE p.del = '0' AND r.id = '$rfa_id';";
$rfa_list_po_SQL = @mysql_query($rfa_list_po_q) or die(mysql_error());

include THEME_DEFAULT.'header.php';

?>  
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">					
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>REQUEST FOR APPROVAL FORM</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<?=back_button();?>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<div class="span8 well">
		<table>
			<?php if(mysql_num_rows($rfa_list_po_SQL)>= 1) { ?>
				<tr><td>
					<table>
					<tr><td><b>THIS RFA IS LISTED IN PO : </b></td></tr>
					<?php while($rlpo_arr = mysql_fetch_array($rfa_list_po_SQL,MYSQL_ASSOC)) { ?>
					<tr><td>- <a href="javascript:openW('./print_po.php?id=<?=$rlpo_arr["pid"]?>','Print_PO',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><?=$rlpo_arr["pnbr"]?></a></td></tr> 
				    <?php } ?>
					</table> 
				</td></tr>
				<tr><td>&nbsp;</td></tr>	
			<?php } ?>
			<tr><td>
			<table border="0">
				<tr><td><b>ID</b></td>
					<td>:</td>
					<td>#<?=$array["id"]?></td></tr>
				<tr><td><b>NO.</b></td>
					<td>:</td>
					<td><?=$array["code"]?></td></tr>
				<tr><td><b>REQUESTOR</b></td>
					<td>:</td>
					<td><?=ucwords($array["fullname"])?></td></tr>
				<tr><td><b>DATE</b></td>
					<td>:</td>
					<td valign="middle"><?=cplday('d F Y',$array["date"])?></td></tr>
				<tr><td><b>ATTACHMENT</b></td>
				    <td>:</td>
				    <td><?php if(!empty($array["file"])) { ?>
						    <a href="<?=($array["file"])?$array["file"]:"";?>" target="_blank" border= "0">
						    <img src="<?=IMG_PATH?>attch.gif" />&nbsp;&nbsp;<?=($array["file"])?basename($array["file"]):"";?></a>
						<?php } else { ?>&nbsp;-<?php } ?>
					</td></tr>
			</table>
		</td></tr>
<?php 
$count = 1;
while($det_array = mysql_fetch_array($rfa_det_info_SQL, MYSQL_ASSOC)) { ?>
		<tr><td>
		<br/>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-bordered">
				<tr>
					<td width="25"><b>NO.</b></td>
					<td colspan="2"><b>DESCRIPTION</b></td>
					<td><b>STATUS</b></td>
				</tr>
				<tr><td rowspan="7" align="left" valign="top">&nbsp;&nbsp;<?=$count?>.</td>
					<td colspan="2">
					-&nbsp;<?=strtoupper($det_array["item"])?>&nbsp;-<br /><br />
					<b>BRANCHES:</b><br />
					<?=ucwords($array["bid"])?><br /><br />
					<b>PURPOSE:</b><br />
					<?=($det_array["purpose"])?nl2br($det_array["purpose"]):"-"?><br /><br />
					<b>REQ. SPECIFICS:</b><br />
					<?=($det_array["spec_notes"])?nl2br($det_array["spec_notes"]):"-"?><br /><br />
					<b>VENDOR QUOTATION:</b><br />
					<?=($det_array["vname"])?$det_array["vname"]:"-"?><br /><br />
					</td>
					<td rowspan="7" align="left" valign="top"><?=strtoupper($det_array["status"])?></td>
				</tr>
			</table>
		</td></tr>
<?php 
$count++;
} ?>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<label><b>APPROVER'S NOTE</b></label>
		<?=($array["appr_note"])?nl2br($array["appr_note"]):"&nbsp; -";?>
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
					<textarea cols="35" rows="3" name="its-notes" wrap="virtual"><?=($array["code_notes"])?strip_tags(nl2br($array["code_notes"])):"&nbsp; -";?></textarea></td></tr>
			<tr><td align="left"><b>VALIDATED</b></td>
				<td align="left">:</td>
				<td><select name="validated_name">
					<option value="-">-----------------</option>
<?php	$val_name_query = "SELECT u.id, CONCAT(u.fname,' ',u.lname) AS fullname ".
							"FROM user u ".
							"LEFT JOIN user_level ul ON (ul.id = u.level_id_fk) ".
							"WHERE ul.id <= '5' AND u.del = '0' AND u.hidden = '0' AND u.active = '1' ;";
		$val_name_SQL = mysql_query($val_name_query); 
		
		while($val_name_array = mysql_fetch_array($val_name_SQL,MYSQL_ASSOC)) { 
		$compare_validated = ($val_name_array["id"] == $array["code_val"])?"SELECTED":"";?>
		<option value="<?=$val_name_array["id"]?>" <?=$compare_validated?>><?=ucwords($val_name_array["fullname"])?></option>
<?php } ?>
				</select></td></tr>
			<tr><td align="left"><b>DATE</b></td>
				<td align="left">:</td>
				<td><?=($array["code_date"] != "0000-00-00 00:00:00")?cplday('d F Y',$array["code_date"]):"-";?></td></tr>	
		</table>
		</td></tr>
		</table></div>
		</td></tr>
  		<tr><td>&nbsp;</td></tr>
		<tr><td><?=genButton($button)?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<?=back_button();?>
		<tr><td>&nbsp;</td></tr>
	</table>
</form>
<//-----------------CONTENT-END---------------------------------------------------//>			
<?php include THEME_DEFAULT.'footer.php'; ?>  