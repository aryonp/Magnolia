<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'notif.lib.php';
chkSession();

$page_title 	= "Request for Approval Page";
$page_id_left 	= "6";
$page_id_right 	= "";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$rfa_id 	= $_GET['id'];
$this_page 	= $_SERVER['PHP_SELF']."?id=".$rfa_id;

if(isset($_POST['approve'])){
	$rfa_id_fk = $_POST['rfa_id_fk'];
	$appr_note = mysql_real_escape_string($_POST['appr_note']);
	$rfa_det_id_fk = (is_array($_POST['rfa_det_id_fk']))?$_POST['rfa_det_id_fk']:"";
	if(!empty($rfa_det_id_fk) AND !empty($rfa_id_fk)) {
		foreach ($rfa_det_id_fk as $rfa_det_id_key => $rfa_det_id_value) {
			$upd_rfa_det_stat 	= $_POST['rfa_det_status'][$rfa_det_id_key];
			$upd_rfa_det_query 	= "UPDATE rfa_det SET status = '$upd_rfa_det_stat' WHERE id = '$rfa_det_id_value';";
			@mysql_query($upd_rfa_det_query) or die(mysql_error());
			$select_req_det_id_q 	= "SELECT req_det_id_fk FROM rfa_det WHERE id = '$rfa_det_id_value';";
			$select_req_det_id_SQL 	= mysql_query($select_req_det_id_q);
			if (mysql_num_rows($select_req_det_id_SQL) >= 1) {	
				while($select_req_det_id_array = mysql_fetch_array($select_req_det_id_SQL)) {
					$upd_req_det_rfa_q = "UPDATE req_det SET status ='$upd_rfa_det_stat' WHERE id = '".$select_req_det_id_array[0]."';";
					@mysql_query($upd_req_det_rfa_q) or die(mysql_error());
				}
			}
		}
		$upd_rfa_query = "UPDATE rfa SET status = 'approved', appr_id_fk = '".$_SESSION['uid']."', appr_note = '$appr_note', appr_date = '".date('Y-m-d H:i:s')."' WHERE id = '$rfa_id_fk';";
		@mysql_query($upd_rfa_query) or die(mysql_error());
		notify_irfa($rfa_id_fk, "Approved");
		log_hist("84",$rfa_id_fk);
		header("location:$this_page");
	}
	else { 
		$status = "<p class=\"redbox\">Missing required information</p>"; 
	}
}

elseif(isset($_POST['appr_all'])){
	$rfa_id_fk = $_POST['rfa_id_fk'];
	$appr_note = mysql_real_escape_string($_POST['appr_note']);
	foreach ($_POST['rfa_det_id_fk'] as $rfa_det_id_key => $rfa_det_id_value) {
		$upd_rfa_det_stat 	= $_POST['rfa_det_status'][$rfa_det_id_key];
		$upd_rfa_det_query 	= "UPDATE rfa_det SET status = 'approved' WHERE id = '$rfa_det_id_value';";
		@mysql_query($upd_rfa_det_query) or die(mysql_error());
		$select_req_det_id_q 	= "SELECT req_det_id_fk FROM rfa_det WHERE id = '$rfa_det_id_value';";
		$select_req_det_id_SQL 	= mysql_query($select_req_det_id_q) or die(mysql_error());
		if (mysql_num_rows($select_req_det_id_SQL) >= 1) {	
			while($select_req_det_id_array = mysql_fetch_array($select_req_det_id_SQL)) {
				$upd_req_det_rfa_q = "UPDATE req_det SET status ='rfa-approved' WHERE id = '".$select_req_det_id_array[0]."';";
				@mysql_query($upd_req_det_rfa_q) or die(mysql_error());
			}
		}
	}	
	$upd_rfa_query = "UPDATE rfa SET status ='approved', appr_id_fk = '".$_SESSION['uid']."', appr_note = '$appr_note', appr_date = '".date('Y-m-d H:i:s')."' WHERE id = '$rfa_id_fk';";
	@mysql_query($upd_rfa_query) or die(mysql_error());
	notify_irfa($rfa_id_fk, "Approved");
	log_hist("85",$rfa_id_fk);
	header("location:$this_page");
}
elseif(isset($_POST['reject_all'])){
	$rfa_id_fk = $_POST['rfa_id_fk'];
	$appr_note = mysql_real_escape_string($_POST['appr_note']);
	foreach ($_POST['rfa_det_id_fk'] as $rfa_det_id_key => $rfa_det_id_value) {
		$upd_rfa_det_stat 	= $_POST['rfa_det_status'][$rfa_det_id_key];
		$upd_rfa_det_query 	= "UPDATE rfa_det SET status = 'rejected' WHERE id = '$rfa_det_id_value';";
		@mysql_query($upd_rfa_det_query) or die(mysql_error());
		$select_req_det_id_q 	= "SELECT req_det_id_fk FROM rfa_det WHERE id = '$rfa_det_id_value';";
		$select_req_det_id_SQL 	= @mysql_query($select_req_det_id_q) or die(mysql_error());
		if (mysql_num_rows($select_req_det_id_SQL) >= 1) {	
			while($select_req_det_id_array = mysql_fetch_array($select_req_det_id_SQL)) {
				$upd_req_det_rfa_q = "UPDATE req_det SET status ='rfa-rejected' WHERE id = '".$select_req_det_id_array[0]."';";
				@mysql_query($upd_req_det_rfa_q) or die(mysql_error());
			}
		}
	}	
	$upd_rfa_query = "UPDATE rfa SET status = 'rejected', appr_id_fk = '".$_SESSION['uid']."', appr_note = '$appr_note', appr_date = '".date('Y-m-d H:i:s')."' WHERE id = '$rfa_id_fk';";
	@mysql_query($upd_rfa_query) or die(mysql_error());
	notify_irfa($rfa_id_fk, "Rejected");
	log_hist("86",$rfa_id_fk);
	header("location:$this_page");
}
$rfa_info_query = "SELECT r.id, 
						 r.code, 
						 CONCAT(u.fname,' ',u.lname) AS fullname, 
						 r.user_id_fk as uid, 
						 b.id as bid, r.date, 
						 r.file, 
						 r.status, 
						 CONCAT(a.fname,' ',a.lname) AS aname, 
						 a.sign, 
						 r.appr_note, 
						 CONCAT(v.fname,' ',v.lname) AS vname, 
						 r.code_date, 
						 r.code_notes,
						 r.del as rdel 
					FROM rfa r 
						LEFT JOIN user u ON (u.id = r.user_id_fk) 
						LEFT JOIN user v ON (v.id = r.code_val) 
						LEFT JOIN user a ON (a.id = r.appr_id_fk)
						LEFT JOIN branch b ON (b.id = r.branch_id_fk) 
					WHERE r.id = '$rfa_id' AND r.del = 0";

$rfa_info_query2 	= "SELECT r.id, r.del as rdel FROM rfa r WHERE r.id = '$rfa_id';";

$rfa_info_SQL 		= @mysql_query($rfa_info_query) or die(mysql_error());
$rfa_info_array 	= mysql_fetch_array($rfa_info_SQL, MYSQL_ASSOC);

$rfa_info_SQL2 		= @mysql_query($rfa_info_query2) or die(mysql_error());
$ria2 				= mysql_fetch_array($rfa_info_SQL2, MYSQL_ASSOC);

$rfa_det_info_query = "SELECT rd.id, rd.item, rd.purpose, rd.spec_notes, v.name, rd.status 
					   FROM rfa_det rd LEFT JOIN vdr v ON (v.id = rd.vdr_id_fk) 
					   WHERE rd.rfa_id_fk = '$rfa_id' AND rd.del = 0;";
$rfa_det_info_SQL 	= @mysql_query($rfa_det_info_query) or die(mysql_error());

function approval_status() {
	$approval_status 	= array("rfa-approved","rfa-rejected");
	$approval_name 		= array("APPROVE","REJECT");
	echo "<select name = \"rfa_det_status[]\">\n";
	foreach($approval_status as $key => $status) {
		$name = $approval_name[$key];
		echo "\t<option value =\"$status\">$name</option>\n";
	}
	echo "</select>\n";	
}

$button = array ("approve"=>array("submit"=>"  APPROVE  "),
				 "appr_all"=>array("submit"=>"  APPROVE ALL  "),
				 "reject_all"=>array("submit"=>"  REJECT ALL "));	

$rfa_list_po_q = "SELECT DISTINCT(p.id) AS 'pid', p.po_nbr as 'pnbr',r.id as 'rid' 
                  FROM rfa r 
					LEFT JOIN po_rfa pd ON (pd.rfa=r.id) 
					LEFT JOIN po p ON (p.id = pd.po)
                  WHERE p.del = '0' AND r.id = '$rfa_id';";
$rfa_list_po_SQL = @mysql_query($rfa_list_po_q) or die(mysql_error());

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<?php 
if($ria2["rdel"] == '0'){ 
?>
<form method="POST" action="">					
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>APPROVAL DETAILS</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<?=back_button();?>
		<tr><td>&nbsp;</td></tr>
		<?=($rfa_info_array["status"] == "pending")?"<tr><td height=\"24\" valign=\"middle\" >".genButton($button)."</td></tr>\n":"";?>
		<tr><td>
		<div class="span8 well">
		<table border="0">
				<tr><td><input type="hidden" name="rfa_id_fk" value="<?=$rfa_info_array["id"]?>"></td></tr>
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
							<td>#<?=$rfa_info_array["id"]?></td></tr>
						<tr><td><b>NO</b></td>
							<td>:</td>
							<td><?=$rfa_info_array["code"]?></td></tr>
						<tr><td><b>REQUESTOR</b></td>
							<td>:</td>
							<td><?=ucwords($rfa_info_array["fullname"])?></td></tr>
						<tr><td><b>DATE</b></td>
							<td>:</td>
							<td valign="middle"><?=($rfa_info_array["date"])?cplday('d M Y',$rfa_info_array["date"]):"-";?></td></tr>
						<tr><td><b>ATTACHMENT</b></td>
				    		    <td>:</td>
				                  <td>
						    <?php if(!empty($rfa_info_array["file"])) { ?>
						    <a href="<?=($rfa_info_array["file"])?$rfa_info_array["file"]:"";?>" target="_blank" border= "0">
						    <img src="<?=IMG_PATH?>attch.gif" />&nbsp;&nbsp;<?=($rfa_info_array["file"])?"Click to open/download the attachment":"";?></a>
						    <?php } else { ?>&nbsp;-<?php } ?>
						 </td></tr>
					</table>
				</td></tr>
				<tr><td>&nbsp;</td></tr>
<?php 
$count = 1;
while($rfa_det_info_array = mysql_fetch_array($rfa_det_info_SQL)) { ?>
				<tr><td>
					<table border="0" cellpadding="1" cellspacing="1" width="500" class="table table-bordered">
						<tr class="listview">
							<td width="25"><b>NO.</b></td>
							<td><b>DESCRIPTION</b></td>
							<td align="left" valign="top"><b>STATUS</b></td>
						</tr>
						<tr><td rowspan="5" align="left" valign="top">&nbsp;&nbsp;<?=$count?>.</td>
							<td>-&nbsp;<?=strtoupper($rfa_det_info_array[1])?>&nbsp;-<br/><br/>
							
								<b>BRANCHES:</b><br />
								<?=ucwords($rfa_info_array["bid"])?>
								<input type="hidden" name="rfa_det_id_fk[]" value="<?=$rfa_det_info_array[0]?>"><br/><br/>
								
								<b>PURPOSE:</b><br/><?=($rfa_det_info_array[2])?nl2br($rfa_det_info_array[2]):"-"?><br/><br/>
								
								<b>REQ. SPECIFICS:</b><br/><?=($rfa_det_info_array[3])?nl2br($rfa_det_info_array[3]):"-"?><br/><br/>
								
								<b>VENDOR QUOTATION:</b><br/><?=($rfa_det_info_array[4])?$rfa_det_info_array[4]:"-"?></td>
							<td rowspan="5" align="left" valign="top"><?=($rfa_det_info_array[5] != "pending")?strtoupper($rfa_det_info_array[5]):approval_status();?></td>
						</tr>
					</table>
				</td></tr>
<?php 	$count++;
		} ?>
		<tr><td>&nbsp;</td></tr>
		<tr><td><label><b>APPROVER'S NOTE</b></label>
<?php if($rfa_info_array["status"] == "pending"){ ?>
		<textarea cols="60" rows="8" name="appr_note" wrap="virtual"></textarea>
<?php } 
	  else { ?>
	  	<?=($rfa_info_array["appr_note"])?nl2br($rfa_info_array["appr_note"]):"&nbsp; -";?>
<?php } ?>
		</td></tr>	
		<tr><td>&nbsp;</td></tr>
<?php if($rfa_info_array["status"] != "pending"){ ?>
		<tr><td>
			<table border="0" cellpadding="0" cellspacing="0" width="100%">
				<tr><td>&nbsp;</td>
					<td>&nbsp;</td>
					<td align="center" width="20%">
					Authorized by:<br><br>
					<?=($rfa_info_array["sign"])?"<img src=".$rfa_info_array["sign"]." width=\"150\" height=\"100\" border=\"0\">":"<br><br><br>"?>
					<?=($rfa_info_array["aname"])?strtoupper($rfa_info_array["aname"]):"-";?>
				</td></tr>
			</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
<?php } ?>
		<tr><td><hr>
  		<label><b>ITS USE ONLY</b></label>
		<table border="0" cellpadding="1" cellspacing="1">
			<tr><td align="left"><b>FILE NO</b></td>
				<td align="left">:</td>
				<td><?=($rfa_info_array["code"])?ucwords($rfa_info_array["code"]):"&nbsp; -";?></td>
				<td align="left" rowspan="4">&nbsp;</td>
				<td rowspan="4" align="left" valign="top"><b>NOTES</b>:<br/>
					<?=($rfa_info_array["code_notes"])?nl2br($rfa_info_array["code_notes"]):"&nbsp; -";?></td></tr>
			<tr><td align="left"><b>VALIDATED</b></td>
				<td align="left">:</td>
				<td><?=($rfa_info_array["vname"])?ucwords($rfa_info_array["vname"]):"&nbsp; -";?></td></tr>
			<tr><td align="left"><b>DATE</b></td>
				<td align="left">:</td>
				<td><?=($rfa_info_array["code_date"] != "0000-00-00 00:00:00")?cplday('d M Y',$rfa_info_array["code_date"]):"&nbsp; -";?></td></tr>
		</table>
		</td></tr>	
			</table></div>
		</td></tr>
		<?=($rfa_info_array["status"] == "pending")?"<tr><td height=\"24\" valign=\"middle\" >".genButton($button)."</td></tr>\n":"";?>
		<tr><td>&nbsp;</td></tr>
		<?=back_button();?>
		<tr><td>&nbsp;</td></tr>
	</table>
</form>
<?php }
else {
    echo "<table>\n";
    echo "<tr><td>&nbsp;</td></tr>\n";
    echo "<tr><td>[&nbsp;<a href=\"./appr_hm.php\">Back to the Approver Home Page</a>&nbsp;]</td></tr>\n";
	echo "<tr><td><p class=\"alert\">RFA has been deleted by requester</p></td></tr>\n";
	echo "<tr><td>[&nbsp;<a href=\"./appr_hm.php\">Back to the Approver Home Page</a>&nbsp;]</td></tr>\n";
	echo "<table>\n"; 
} 
?>
<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>