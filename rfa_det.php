<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title		= "Request for Approval Page";
$page_id_left	= "5";
$category_page 	= "main";
chkSecurity($page_id_left);

$rfa_id 	= $_GET['id'];
$status 	= "&nbsp;";
$this_page 	= $_SERVER['PHP_SELF']."?id=".$rfa_id;

$rfa_info_query ="SELECT r.id, 
						 r.code, 
						 CONCAT(u.fname,' ',u.lname) AS fullname, 
						 r.user_id_fk, 
						 b.id, 
						 r.designation, 
						 r.date, 
						 r.file, 
						 r.status, 
						 CONCAT(a.fname,' ',a.lname) AS apprname, 
						 a.sign, 
						 r.appr_note, 
						 CONCAT(v.fname,' ',v.lname) AS valname, 
						 r.code_date, 
						 r.code_notes 
					FROM rfa r 
						LEFT JOIN user u ON (u.id = r.user_id_fk) 
						LEFT JOIN user v ON (v.id = r.code_val) 
						LEFT JOIN user a ON (a.id = r.appr_id_fk)
						LEFT JOIN branch b ON (b.id = r.branch_id_fk) 
					WHERE r.id = '$rfa_id' AND r.del = 0;";
$rfa_info_SQL 	= @mysql_query($rfa_info_query) or die(mysql_error());
$rfa_info_array = mysql_fetch_array($rfa_info_SQL);

$rfa_det_info_query = "SELECT rd.id, rd.item, rd.purpose, rd.spec_notes, v.name, rd.status
					   FROM rfa_det rd LEFT JOIN vdr v ON (v.id = rd.vdr_id_fk)
					   WHERE rd.rfa_id_fk = '$rfa_id' AND rd.del = 0;";
$rfa_det_info_SQL 	= @mysql_query($rfa_det_info_query) or die(mysql_error());

if(isset($_POST['update_rfa'])) {
	$rfa_id_fk 	= $_POST['rfa_id_fk'];
	$rfa_det_id = is_array($_POST['rfa_det_id'])?$_POST['rfa_det_id']:"";
	
	if(!empty($rfa_id_fk) AND !empty($rfa_det_id)){
		foreach($rfa_det_id as $key => $value) {
			$specific_note 	= mysql_real_escape_string($_POST['specific_note'][$key]);
			$purpose 		= mysql_real_escape_string($_POST['purpose'][$key]);
			$update_rfa_det_query = "UPDATE rfa_det SET purpose = '$purpose', spec_notes = '$specific_note' WHERE rfa_det.id = '$value';";
			@mysql_query($update_rfa_det_query) or die(mysql_error());
		}
		log_hist("80",$rfa_id_fk);
		header("location:".$_SERVER['PHP_SELF']."?id=".$rfa_id_fk);
	}
	
	else {
		$status = "<p class=\"alert\">Missing required information</p>";
	}
}

if(isset($_POST['upd_attach'])) {
	$rfa_id_fk 	= $_POST['rfa_id_fk'];
	$rfa_target = file_target("irfa",$_FILES['rfa-file']['name']);
	
	if(move_uploaded_file($_FILES['rfa-file']['tmp_name'], $rfa_target)) {
		$update_rfa_query = "UPDATE rfa SET file = '$rfa_target' WHERE rfa.id = '$rfa_id_fk';";
		mysql_query($update_rfa_query) or die(mysql_error());
		chmod($rfa_target, 0777);
		log_hist("80",$rfa_id_fk);
		header("location:".$_SERVER['PHP_SELF']."?id=".$rfa_id_fk);
	}
	
	else { 
		$status = "<p class=\"alert\">Sorry, there was a problem uploading your file.</p>";
	}
}

if (isset($_POST['del_attach'])){
	$location 	= $_POST['location'];
	$did 		= trim($_POST['rfa_id_fk']);
	
	if(file_exists($location)) {
		if(unlink($location)) {
			$empty_file_query  ="UPDATE rfa SET rfa.file = '' WHERE rfa.id ='$did';";
			mysql_query($empty_file_query) or die(mysql_error());
			log_hist("80",$did);
			header("location:".$_SERVER['PHP_SELF']."?id=".$did);
		} 
		 else { 
			header("location:".$_SERVER['PHP_SELF']."?id=".$did);
		}
	} 
	else { 
		header("location:".$_SERVER['PHP_SELF']."?id=".$did);
	}
}	

$button = array("update_rfa"=>array("submit"=>"  UPDATE RFA  "));

$rfa_list_po_q = "SELECT DISTINCT(p.id) AS 'pid', p.po_nbr as 'pnbr',r.id as 'rid' 
                  FROM rfa r 
					LEFT JOIN po_rfa pd ON (pd.rfa=r.id) 
					LEFT JOIN po p ON (p.id = pd.po)
                  WHERE p.del = '0' AND r.id = '$rfa_id';";
$rfa_list_po_SQL = @mysql_query($rfa_list_po_q) or die(mysql_error());

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="" enctype="multipart/form-data">					
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>RFA DETAILS</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status;?></td></tr>
		<?=back_button()?>
		<tr><td>&nbsp;</td></tr>
		<?=($rfa_info_array[3] == $_SESSION['uid'])?"<tr><td height=\"24\" valign=\"middle\" >".genButton($button)."</td></tr>\n":"";?>
		<tr><td>
			<div class="span8 well">
			<table border="0">
				<tr><td>
					<input type="hidden" name="rfa_id_fk" value="<?=$rfa_info_array[0]?>">
					<input type="hidden" name="location" value="<?=$rfa_info_array[7]?>">
				</td></tr>
				<?php if(mysql_num_rows($rfa_list_po_SQL)>= 1) { ?>
				<tr><td>
					<table>
						<tr><td><b>THIS RFA IS LISTED IN PO : </b></td></tr>
					<?php while($rlpo_arr = mysql_fetch_array($rfa_list_po_SQL,MYSQL_ASSOC)) { ?>
						<tr><td>- <a href="po_det.php?id=<?=$rlpo_arr["pid"]?>"><?=$rlpo_arr["pnbr"]?></a></td></tr> 
					<?php } ?>
					</table> 
				</td></tr>
				<tr><td>&nbsp;</td></tr>	
				<?php } ?>
				<tr><td>
					<table border="0">
						<tr><td><b>ID</b></td>
							<td>:</td>
							<td>#<?=$rfa_info_array[0]?></td>
						</tr>
						<tr><td><b>NO.</b></td>
							<td>:</td>
							<td><?=$rfa_info_array[1]?></td>
						</tr>
						<tr><td><b>REQUESTOR</b></td>
							<td>:</td>
							<td><?=ucwords($rfa_info_array[2])?></td>
						</tr>
						<tr><td><b>DATE</b></td>
							<td>:</td>
							<td valign="middle"><?=($rfa_info_array[6])?cplday('d F Y',$rfa_info_array[6]):"&nbsp; -";?></td>
						</tr>
						<tr valign="top">
							<td><b>ATTACHMENT</b></td>
							<td>:</td>
							<td>
						<?php if(($rfa_info_array[3] == $_SESSION['uid'] OR $_SESSION['uid'] <= 5 ) AND empty($rfa_info_array[7])) {?>
							<input type="file" size="30" name="rfa-file">
						<?php } 
						      elseif (!empty($rfa_info_array[7])) { ?>
						<a href="<?=($rfa_info_array[7])?$rfa_info_array[7]:"";?>" target="_blank" border= "0"><img src="<?=IMG_PATH?>attch.gif" />&nbsp;&nbsp;<?=($rfa_info_array[7])?basename($rfa_info_array[7]):"";?></a>
						<?php } else {?>
								-
						<?php } ?>
						
						<?php if(($rfa_info_array[3] == $_SESSION['uid'] OR $_SESSION['uid'] <= 5 ) AND !empty($rfa_info_array[7])) {?>
						<input type="submit" class="btn-info btn-small" name="del_attach" value=" DELETE ATTACHMENT" />
						<?php } 
							  elseif(($rfa_info_array[3] == $_SESSION['uid'] OR $_SESSION['uid'] <= 5 ) AND empty($rfa_info_array[7])) {?>
						<input type="submit" class="btn-info btn-small" name="upd_attach" value=" UPLOAD ATTACHMENT" />
						<?php } ?>
						</td></tr>
					</table>
				</td></tr>
				
<?php 
$count = 1;
while($rfa_det_info_array = mysql_fetch_array($rfa_det_info_SQL)) { ?>
				<tr><td>
					<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-bordered">	
						<tr valign="middle">
							<td><b>NO.</b></td>
							<td><b>DESCRIPTION</b></td>
							<td><b>STATUS</b></td>
						</tr>
						<tr><td rowspan="5">&nbsp;<?=$count?>.</td>
							<td>-&nbsp;<?=strtoupper($rfa_det_info_array[1]);?>&nbsp;-<br/><br/>
							<b>BRANCHES:</b><br/><?=ucwords($rfa_info_array[4]);?><br/><br/>
								<b>PURPOSE:</b><br/><textarea cols="50" rows="2" name="purpose[]" wrap="virtual"><?=($rfa_det_info_array[2])?strip_tags($rfa_det_info_array[2]):"-";?></textarea><br/><br/>
								<b>REQ. SPECIFICS:</b><br/><textarea cols="50" rows="5" name="specific_note[]" wrap="virtual"><?=($rfa_det_info_array[3])?strip_tags($rfa_det_info_array[3]):"-";?></textarea><br/><br/>
								<b>VENDOR QUOTATION:</b><br/><?=($rfa_det_info_array[4])?$rfa_det_info_array[4]:"-";?><input type="hidden" name="rfa_det_id[]" value="<?=$rfa_det_info_array[0]?>">
							</td>
							<td rowspan="5"><?=strtoupper($rfa_det_info_array[5]);?></td></tr>
					</table>
				</td></tr>
<?php 
$count++;
} ?>		
		<tr><td>
		<label><b>APPROVER'S NOTE</b></label>
		<?=($rfa_info_array[11])?nl2br($rfa_info_array[11]):"-";?>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr><td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="center" width="20%">
					Authorized by:<br><br><br>
					<?=($rfa_info_array[10])?"<img src=".$rfa_info_array[10]." width=\"150\" height=\"100\" border=\"0\">":"<br><br><br>"?>
					<?=($rfa_info_array[9])?strtoupper($rfa_info_array[9]):"-";?>
				</td></tr>
			</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<hr>
  		<label><b>ITS USE ONLY</b></label>
		<table border="0" cellpadding="1" cellspacing="1">
			<tr><td align="left"><b>FILE NO</b></td>
				<td align="left">:</td>
				<td><?=($rfa_info_array[1])?ucwords($rfa_info_array[1]):"-";?></td>
				<td align="left" rowspan="4">&nbsp;</td>
				<td rowspan="4" align="left" valign="top"><b>NOTES</b>:<br/>
					<?=($rfa_info_array[14])?nl2br($rfa_info_array[14]):"-";?></td></tr>
			<tr><td align="left"><b>VALIDATED</b></td>
				<td align="left">:</td>
				<td><?=($rfa_info_array[12])?ucwords($rfa_info_array[12]):"-";?></td></tr>
			<tr><td align="left"><b>DATE</b></td>
				<td align="left">:</td>
				<td><?=($rfa_info_array[13] != "0000-00-00 00:00:00")?cplday('d F Y',$rfa_info_array[13]):"-";?></td></tr>	
		</table>
		</td></tr>	
		</table></div>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<?=($rfa_info_array[3] == $_SESSION['uid'])?"<tr><td height=\"24\" valign=\"middle\" >".genButton($button)."</td></tr>\n":"";?>
		<tr><td>&nbsp;</td></tr>
		<?=back_button()?>
  		<tr><td>&nbsp;</td></tr>
	</table>
</form>
<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>