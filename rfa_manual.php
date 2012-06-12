<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'notif.lib.php';
chkSession();

$page_title		= "Manual RFA";
$page_id_left 	= "5";
$category_page 	= "main";
chkSecurity($page_id_left);

$branch_list_query 	= "SELECT b.id, b.name FROM branch b WHERE b.del = '0' ORDER BY b.name ASC;";
$branch_list_SQL 	= @mysql_query($branch_list_query) or die(mysql_error());

$vendor_list_query 	= "SELECT v.id, v.name FROM vdr v WHERE v.del= '0' ORDER BY v.name ASC;";
$vendor_list_SQL 	= @mysql_query($vendor_list_query) or die(mysql_error());

$status ="&nbsp;";
$rfa_det = array();

if(isset($_POST['temp_rfa'])) {
	$branch 	= trim($_POST['rfa_branch']);
	$file_id 	= genfilecode("IRFA",$branch);
	$date 		= trim($_POST['date']);
	$items 		= $_POST['item'];	
	$file 		= file_target("irfa",$_FILES['rfa-file']['name']);
	
	if (!in_array("-",$_POST['vendor']) AND !in_array("-",$_POST['item'])) {	
		if(move_uploaded_file($_FILES['rfa-file']['tmp_name'], $file)) {
			$insert_rfa_query = "INSERT INTO rfa (code,user_id_fk,branch_id_fk,date,file,status) ".
								"VALUES ('$file_id','".$_SESSION['uid']."','$branch','$date','$file','pending');";
			chmod($file, 0777);
			@mysql_query($insert_rfa_query) or die(mysql_error());
			$rfa_id_fk 				= mysql_insert_id();
			$insert_rfa_det_query 	= "INSERT INTO rfa_det (rfa_id_fk,item,purpose,spec_notes,vdr_id_fk,status) VALUES ";
			foreach($items as $key_items => $value_items) {
				$specnote 		= mysql_real_escape_string($_POST['specific_note'][$key_items]);
				$purpose 		= mysql_real_escape_string($_POST['purpose'][$key_items]);
				$vendor_id_fk 	= $_POST['vendor'][$key_items];
				array_push($rfa_det," ('$rfa_id_fk','$value_items','$purpose','$specnote','$vendor_id_fk','pending')");
			}
			$insert_rfa_det_query .= implode(",",$rfa_det);
			@mysql_query($insert_rfa_det_query) or die(mysql_error());
			notify_irfa($rfa_id_fk,"Pending");
			log_hist("79",$rfa_id_fk);
			header("location:./rfa_hm.php");
		}
		else { 
			$status = "<p class=\"yellowbox\">Sorry, there was a problem uploading your file.</p>";
		}
	} else {
		$status = "<p class=\"yellowbox\">Missing Information! could not create RFA, Press back button to repeat input</p>";
	}
}
$button = array("temp_rfa"=>array("submit"=>"  SUBMIT RFA  "),
				"reset_rfa"=>array("reset"=>"  RESET RFA  "));	
			  
include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><div class="well form-inline">
		<label for="branch"><b>RFA FOR BRANCH :</b></label>
  		<select name="branch">
    		<option value="-">---------------------</option>
    
<?php 
$rfa_branch_id = (isset($_POST['branch']))?$_POST['branch']:"";
while($branch_list_array = mysql_fetch_array($branch_list_SQL)){ 
		$selected = ($rfa_branch_id == $branch_list_array[0])?"SELECTED":"";
?>
    <option value="<?=$branch_list_array[0]?>" <?=$selected?>><?=ucwords($branch_list_array[1])?></option>
<?php } ?>
  		</select>
  		&nbsp;&nbsp;& &nbsp;<input type="text" name="items_count" size="3" maxlength="3" class="input-small" value="<?=(isset($_POST['items_count']))?$_POST['items_count']:"";?>">&nbsp;Items&nbsp;&nbsp;<input type="submit" class="btn-info btn-small" name="gen_man_rfa" value=" GENERATE " />
		</div></td></tr>	
	<tr><td>&nbsp;</td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td>&nbsp;</td></tr>
<?php if (isset($_POST['gen_man_rfa']) AND $_POST['branch'] != "-" AND (!empty($_POST['items_count'])) AND (is_numeric($_POST['items_count']))) { 
	$items_count = $_POST['items_count']; ?>
	<tr><td>[&nbsp;<a href="./rfa_hm.php">Back to the RFA Home</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><?=genButton($button);?></td></tr>
	<tr><td><div class="well span8">
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
							<td valign="middle"><input type="text" size="30" maxlength="10" name="date" id="cal" value="<?=date('Y-m-d H:i:s')?>">&nbsp;
								<a href="javascript:NewCal('cal','yyyymmdd', true, 24)"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a></td></tr>
						<tr><td align="right"><b>ATTACHMENT</b>&nbsp;<font color="Red">*</font>&nbsp;</td>
							<td>:</td>
							<td valign="middle"><input type="file" size="30" name="rfa-file">&nbsp;&nbsp;(Max: <?=ini_get('post_max_size');?>)</td></tr>

					</table>
			</td></tr>
			<tr><td><input type="hidden" name="rfa_branch" value="<?=$rfa_branch_id?>">
					<input type="hidden" name="user_id" value="<?=$_SESSION['uid']?>"></td></tr>
			<tr><td>
<?php 
for($ctm = 1;$ctm <= $items_count;$ctm++) { 
		?>		<table border="0" cellpadding="1" cellspacing="1" class="table table-bordered">
						<tr class="listview">
							<td><b>&nbsp;NO.</b></td>
							<td><b>&nbsp;DESCRIPTION</b></td>
						</tr>
						<tr><td rowspan="5" align="left" valign="top">&nbsp;&nbsp;<?=$ctm?>.</td>
							<td><?=item_list_selection()?></td>
						</tr>
						<tr><td><b>BRANCHES</b><br/><?=$rfa_branch_id?></td></tr>
						<tr valign="top">
							<td><b>PURPOSE:</b><br />
								<textarea cols="50" rows="2" name="purpose[]" wrap="virtual"></textarea></td>
						</tr>
						<tr><td><b>REQ. SPECIFICS:</b><br />
							<textarea cols="50" rows="5" name="specific_note[]" wrap="virtual"></textarea></td>
						</tr>
						<tr><td><b>VENDOR QUOTATION:</b><br /><?=vendor_list_selection()?></td></tr>
					</table>
<?php } ?>
				</td></tr>
			</table></div>
		</td></tr>
		<tr><td><?=genButton($button);?></td></tr>
		<tr><td>[&nbsp;<a href="./rfa_hm.php">Back to the RFA Home</a>&nbsp;]</td></tr>
<?php } ?>
		<tr><td><?=$status?></td></tr>
  		<tr><td>&nbsp;</td></tr>
	</table>
</form>
<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>