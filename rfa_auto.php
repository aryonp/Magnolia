<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'notif.lib.php';
chkSession();

$page_title		= "Create RFA Page";
$page_id_left 	= "5";
$category_page 	= "main";
chkSecurity($page_id_left);

$req_det_list_query  ="SELECT rd.id, r.id, r.code, b.id, d.name as dept, CONCAT(u.fname,' ',u.lname) AS fullname, rt.name as items, rd.status
					   FROM req r 
							LEFT JOIN req_det rd ON (rd.req_id_fk = r.id) 
							LEFT JOIN req_items rt ON (rt.id = rd.item_id_fk) 
							LEFT JOIN user u ON (u.id = r.user_id_fk) 
							LEFT JOIN departments d ON (d.id = u.dept_id_fk) 
							LEFT JOIN branch b ON (b.id = u.branch_id_fk) 
					   WHERE rt.type_id_fk = '2' AND rd.status = 'adm-authorized' AND r.del = '0' ";
if(isset($_POST['pick_branch'])){
	$branch_opt = ($_POST['branch'])?$_POST['branch']:"";
	$req_det_list_query .= ($branch_opt != "ALL")?"AND r.branch_id_fk = '$branch_opt' ":"";
} 
$req_det_list_query .="ORDER BY rd.id DESC ";
 
$status = "&nbsp;";
$rfa_det = array();
$req_det_list_SQL = @mysql_query($req_det_list_query) or die(mysql_error());

$branch_list_query ="SELECT b.id, b.name FROM branch b;";
$branch_list_SQL = @mysql_query($branch_list_query) or die(mysql_error());

$vendor_list_query ="SELECT v.id, v.name FROM vdr v ORDER BY v.name ASC;";
$vendor_list_SQL = @mysql_query($vendor_list_query) or die(mysql_error());

$this_page = $_SERVER['PHP_SELF'];

if(isset($_POST['temp_rfa'])) {
	$date 	= trim($_POST['date']);
	$file 	= file_target("irfa",$_FILES['rfa-file']['name']);
	$branch = trim($_POST['rfa_branch']);
	$file_id = genfilecode("IRFA",$branch);
	$req_to_rfa_det_id = $_POST['req_det_id'];
	
	if(!in_array("-",$_POST['vendor'])) {	
		
		if(move_uploaded_file($_FILES['rfa-file']['tmp_name'], $file)) {
			$insert_rfa_query = "INSERT INTO rfa (code,user_id_fk,branch_id_fk,date,file,status) ".
								"VALUES ('$file_id','".$_SESSION['uid']."','$branch','$date','$file','pending');";
			chmod($file, 0777);
			@mysql_query($insert_rfa_query) or die(mysql_error());
			
			$rfa_id_fk = mysql_insert_id();
			$rfa_det_query = "INSERT INTO rfa_det (rfa_id_fk,req_det_id_fk,item,purpose,spec_notes,vdr_id_fk,status) VALUES ";
			
			foreach($req_to_rfa_det_id as $key => $req_det_id) {
				$specific_note 	= mysql_real_escape_string($_POST['specific_note'][$key]);
				$purpose 		= mysql_real_escape_string($_POST['purpose'][$key]);
				$vendor 		= $_POST['vendor'][$key];
				$item_name 		= $_POST['rfa_item_name'][$key];
				array_push($rfa_det," ('$rfa_id_fk','$req_det_id','$item_name','$purpose','$specific_note','$vendor','pending')");
				$update_req_details_to_rfa_query = "UPDATE req_det SET status = 'rfa-pending' WHERE id = '$req_det_id' ;";
				@mysql_query($update_req_details_to_rfa_query) or die(mysql_error());
			}
			$rfa_det_query .= implode(",",$rfa_det);
			@mysql_query($rfa_det_query) or die(mysql_error());
			notify_irfa($rfa_id_fk,"Pending");
			log_hist("79",$rfa_id_fk);
			header("location:./rfa_hm.php");
		}
		else { 
			$status = "<p class=\"alert\">Sorry, there was a problem uploading your file.</p>";
		}
	} else {
		echo "<table border=\"0\"><tr><td>&nbsp;</td></tr>";
		echo back_button();
		echo "<tr><td><p class=\"alert\">Missing Information! could not create RFA</p></td></tr>";
		echo back_button();
		echo "<tr><td>&nbsp;</td></tr></table>";
	}
}
$button_1 = array("temp_rfa"=>array("submit"=>"  SUBMIT RFA  "),
				  "reset_rfa"=>array("reset"=>"  RESET RFA  "));	
$button_2 = array("create_rfa"=>array("submit"=>"  CREATE RFA  "));

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<?php 
if (isset($_POST['create_rfa'])) {
	$item_id = (is_array($_POST['req_det_id']))?$_POST['req_det_id']:"";
	$branch_id = $_POST['rfa_branch_id']; 
	$items_name = (is_array($_POST['req_item_name']))?$_POST['req_item_name']:""; 
	if(!empty($branch_id) AND !empty($item_id) AND !empty($items_name)) { ?>
	<form method="POST" enctype="multipart/form-data" action="">					
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>REQUEST FOR APPROVAL FORM</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<?=back_button()?>
		<tr><td>&nbsp;</td></tr>
		<tr><td height="24" valign="middle"><?=genButton($button_1)?></td></tr>
		<tr><td>
			<div class="well">
			<table border="0">
				<tr><td>
					<table border="0">
						<tr><td align="right"><b>NO</b></td>
							<td>:</td>
							<td><b>(Auto Generate)</b></td></tr>
						<tr><td align="right"><b>REQUESTOR</b></td>
							<td>:</td>
							<td><?=ucwords($_SESSION['fullname'])?></td></tr>
						<tr><td align="right"><b>DATE</b></td>
							<td>:</td>
							<td valign="middle"><input type="text" size="30" maxlength="15" name="date" id="cal" value="<?=date('Y-m-d H:i:s')?>">&nbsp;
								<a href="javascript:NewCal('cal','yyyymmdd',true,24)"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a></td></tr>
						<tr><td align="right"><b>ATTACHMENT</b>&nbsp;<font color="Red">*</font>&nbsp;</td>
							<td>:</td>
							<td valign="middle"><input type="file" size="30" name="rfa-file">&nbsp;&nbsp;(Max: <?=ini_get('post_max_size');?>)</td></tr>
					</table>
			</td></tr>
			<tr><td><input type="hidden" name="rfa_branch" value="<?=$branch_id?>"></td></tr>
			<tr><td>
<?php 
$count = 1;
foreach($item_id as $item_key => $item_value) { 
		$item_name = $items_name[$item_key];?>
					<div style='border: 1px solid #666666; padding: 2px; margin-top: 5px; margin-bottom: 5px;'>
					<table border="0" cellpadding="1" cellspacing="1" width="100%">
						<tr class="listview">
							<td><b>&nbsp;NO.</b></td>
							<td colspan="2"><b>&nbsp;DESCRIPTION</b></td>
						<tr><td rowspan="4" align="left" valign="top">&nbsp;&nbsp;<?=$count?></td>
							<td colspan="2"><?=strtoupper($item_name)?></td>
						<tr valign="top"><td><b>PURPOSE:</b><br />
								<textarea cols="40" rows="2" name="purpose[]" wrap="virtual"></textarea></td>
							<td><b>BRANCHES:</b><br /><?=$branch_id?></td></tr>
						<tr><td colspan="2"><b>REQ. SPECIFICS:</b><br />
							<textarea cols="50" rows="5" name="specific_note[]" wrap="virtual"></textarea>
							</td></tr>
						<tr><td colspan="2"><b>VENDOR QUOTATION:</b><br /><?=vendor_list_selection()?></td></tr>
						<tr><td colspan="3"><input type="hidden" name="req_det_id[]" value="<?=$item_value?>"></td></tr>
						<tr><td colspan="3"><input type="hidden" name="rfa_item_name[]" value="<?=$item_name?>"></td></tr>
						<tr><td colspan="3">&nbsp;</td></tr>
					</table>
					</div>
<?php 
$count++;
} ?>
				</td></tr>
			</table></div>
		</td></tr>
		<tr><td height="24" valign="middle"><?=genButton($button_1)?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<?=back_button()?>
  		<tr><td>&nbsp;</td></tr>
	</table>
	</form>
<?php	}
	else {
		echo "<table border=\"0\"><tr><td>&nbsp;</td></tr>";
		echo back_button();
		echo "<tr><td><p class=\"alert\">Missing Information! could not create RFA</p></td></tr>";
		echo back_button();
		echo "<tr><td>&nbsp;</td></tr></table>";
	}	
}
else { ?>

<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>GENERATE RFA</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[&nbsp;<a href="./rfa_hm.php">Back to the RFA Home</a>&nbsp;]</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<form method="POST" action="" class="well form-inline">	
		RFA FOR BRANCH : 
  <select name="branch">
  <option value="ALL" <?=$selected?>>ALL</option>
    <option >---------------------</option>
    
<?php while($branch_list_array = mysql_fetch_array($branch_list_SQL)){
  		$selected = ($branch_opt == $branch_list_array[0])?"SELECTED":""; ?>
    <option value="<?=$branch_list_array[0]?>" <?=$selected?>><?=ucwords($branch_list_array[1])?></option>
<? } ?>
  			</select>
  		&nbsp;&nbsp;<input type="submit" class="btn-info btn-small" name="pick_branch" value="   OK   " />
		</form></td></tr>		
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<form method="POST" action="" name="temp_rfa">
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
<?php if($branch_opt && $branch_opt != "ALL" && mysql_num_rows($req_det_list_SQL) >= 1) { ?>
				<tr><td height="24" valign="middle" colspan="9"><?=genButton($button_2);?></td></tr> 
<?php	} else { ?><?php } ?>
            	<tr align="left" valign="middle">
<?php if($branch_opt && $branch_opt != "ALL" && mysql_num_rows($req_det_list_SQL) >= 1) { ?>           	
					<td width="20"><input type="hidden" name="rfa_branch_id" value="<?=$branch_opt?>"></td>
<?php } else { ?><?php } ?>
                 	<td width="25"><b>&nbsp;NO.</b></td>
                 	<td width="*"><b>&nbsp;ID</b></td>
                 	<td width="*"><b>&nbsp;FILE NO.</b></td>
                 	<td width="*"><b>&nbsp;BRANCH</b></td>
                 	<td width="*"><b>&nbsp;DEPARTMENT</b></td>
                 	<td width="*"><b>&nbsp;REQUESTER</b></td>
                 	<td width="*"><b>&nbsp;ITEM</b></td>
                 	<td width="*"><b>&nbsp;STATUS</b></td>
				</tr></thead><tbody>
                 	<?php 	
            if (mysql_num_rows($req_det_list_SQL) >= 1) {	
				$count = 1;	$i = 0;
				while($req_det_list_array = mysql_fetch_array ($req_det_list_SQL)){?>
					<tr align="left" >
<?php if($branch_opt && $branch_opt != "ALL" && mysql_num_rows($req_det_list_SQL) >= 1) { ?>           	
						<td>&nbsp;<input type="checkbox" name="req_det_id[<?=$i?>]" value="<?=$req_det_list_array[0]?>"></td>
<?php } else { ?><?php } ?>
						<td>&nbsp;<?=$count?>.</td>
						<td>&nbsp;#<?=$req_det_list_array[0]?></td>
						<td><a href="./req_det.php?id=<?=$req_det_list_array[1]?>">
							&nbsp;<?=$req_det_list_array[2]?></a></td>
						<td>&nbsp;<?=$req_det_list_array[3]?></td>
						<td>&nbsp;<?=$req_det_list_array[4]?></td>
						<td>&nbsp;<?=ucwords($req_det_list_array[5])?></td>
						<td>&nbsp;<input type="hidden" name="req_item_name[<?=$i?>]" value="<?=$req_det_list_array[6]?>"><?=ucwords($req_det_list_array[6])?></td>
						<td>&nbsp;<?=strtoupper($req_det_list_array[7])?></td>
					</tr>
					
<?php		$count++; $i++;
			}
        } 	else {?>
				<tr><td colspan="8" align="center"><br />No Data Entries<br /><br /></td></tr>
<?php			}
if($branch_opt && $branch_opt != "ALL" && mysql_num_rows($req_det_list_SQL) >= 1) { ?>
				<tr><td height="24" valign="middle" colspan="9"><?=genButton($button_2);?></td></tr> 
<?php	} ?>	</tbody>
				</table></div>
			</form>
			</td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td>[&nbsp;<a href="./rfa_hm.php">Back to the RFA Home</a>&nbsp;]</td></tr>
			<tr><td>&nbsp;</td></tr>
</table>
<?php } ?>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>