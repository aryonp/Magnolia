<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once CONT_PATH.'po.php';
chkSession();

$page_title 	= "Vendor Evaluation Form Page";
$page_id_left 	= "9";
$page_id_right 	= "18";
$category_page 	= "eval";
chkSecurity($page_id_right);

$status = "&nbsp;";

function vendor_list_static(){
	$vendor_list_query ="SELECT v.id, v.name FROM vdr v WHERE v.del = '0' ORDER BY v.name ASC;";
	$vendor_list_SQL = @mysql_query($vendor_list_query) or die(mysql_error()); ?>
		<select name="vendor">
    		<option value="-" selected>---------------------</option>
<?php	while($vendor_list_array = mysql_fetch_array($vendor_list_SQL, MYSQL_ASSOC)){?>
    		<option value="<?=$vendor_list_array["id"]?>"><?=ucwords($vendor_list_array["name"])?></option>
<?php 	} 
  		echo "</select>";
	} 		
	
function criteria_marks() { ?>
	<select name="score[]">
		<option value="-">-----------</option>
		<option value="4">EXCELLENT</option>
		<option value="3">GOOD</option>
		<option value="2">MEDIOCRE</option>
		<option value="1">POOR</option>
	</select>
<?php }

$ev_det = array();

if (isset($_POST['submit'])){
	$date = ((isset ($_POST['date']) && $_POST['date'] != '')?trim ($_POST['date']):'');
	$vendor = ((isset ($_POST['vendor']) && $_POST['vendor'] != '-')?trim ($_POST['vendor']):'-');
	$po_number = ((isset ($_POST['po_list']) && $_POST['po_list'] != '')?trim ($_POST['po_list']):'');
	$crit_id = $_POST['crit_id'];
	$remarks = ((isset ($_POST['remarks']) && $_POST['remarks'] != '')?trim ($_POST['remarks']):'');
	$evaluator = $_SESSION['uid'];
	$scores = $_POST['score'];
	$avg_score = (array_sum($scores)/count($scores) <= 1)?"1":array_sum($scores)/count($scores);
	
	if ($vendor != "-" AND !in_array("-",$scores)) {
		
		$insert_vdr_eval_query = "INSERT INTO ev_std (date,vdr_id_fk,po_nbr,avg,remarks,user_id_fk) VALUES ".
								 "('$date', '$vendor','$po_number','$avg_score','$remarks', '$evaluator');";
		@mysql_query($insert_vdr_eval_query) or die(mysql_error());
		
		$ev_id_fk = mysql_insert_id();
		
		$ins_eval_det_query = "INSERT INTO ev_std_det (ev_id_fk,crit_id_fk,score) VALUES ";
		foreach($crit_id as $crit_id_key => $crit_id_value) {
			$score = $scores[$crit_id_key];
			array_push($ev_det," ('$ev_id_fk','$crit_id_value','$score')");
		}
		$ins_eval_det_query .= implode(",",$ev_det);
		@mysql_query($ins_eval_det_query) or die(mysql_error());
		
		log_hist("93",$ev_id_fk);
		header("location:./vdr_std_hm.php");
	}
	else {	$status ="<p class=\"yellowbox\">Missing Information! unable to create the vendor evaluation.</p>"; }
}

$vendor_criteria_query 	= "SELECT id, name FROM ev_crit WHERE del = '0' ;";
$vendor_criteria_SQL 	= @mysql_query($vendor_criteria_query) or die(mysql_error());

$button = array("submit"=>array("submit"=>"  SUBMIT EVAL  "),
				"reset"=>array("reset"=>"  RESET EVAL  "));

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>

<form method="POST" action="">
	
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>VENDOR EVALUATION FORM</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>[&nbsp;<a href="./vdr_std_hm.php">Back to the Standard Vendor Eval Home</a>&nbsp;]</td></tr>
		<tr><td>&nbsp;</td></tr>
        <tr><td>
		<?=genButton($button)?>
		<div class="well">
		<table border="0">
			<tr><td align="right"><b>DATE</b></td><td>:</td>
				<td><input type="text" name="date" value="<?=date('Y-m-d H:i:s')?>" id="cal" size="20" maxlength="20">&nbsp;
					<a href="javascript:NewCal('cal','yyyymmdd',true,24)">
					<img src="<?=IMG_PATH?>cal.gif" border="0" /></a></td></tr>
			<tr><td align="right"><b>COMPANY NAME</b></td><td>:</td><td><?=vendor_list_static()?></td></tr>
			<tr><td align="right"><b>P.O. NO.</b></td><td>:</td><td><?=po_list_eval();?></td></tr>
		</table>
		<br>
		<table border="0" cellpadding="1" cellspacing="1" class="table table-striped table-bordered table-condensed">
			<tr valign="top">
				<td align="left"><b>&nbsp;CRITERIA&nbsp;</b></td>
				<td align="center"><b>&nbsp;MARKS&nbsp;</b></td>
			</tr>
<?php
$count = 1;
 while($vendor_criteria_array = mysql_fetch_array($vendor_criteria_SQL)) { ?>
			<tr>
				<td>&nbsp;<?=ucwords($vendor_criteria_array[1])?>&nbsp;</td>
				<td align="center">
					<input type="hidden" name="crit_id[]" value="<?=ucwords($vendor_criteria_array[0])?>"/>
					<?=criteria_marks()?></td>
			</tr>
<?php 
$count++;
} ?>
		</table>
		
		<br/>
		
		<b>REMARKS</b><br />
		<textarea cols="60" rows="5" name="remarks" wrap="virtual"></textarea>
		
		<br/>
		<b>EVALUATED BY:</b><br /><br />
		<?=ucwords($_SESSION['fullname']);?>
		
		
		</div>
		<?=genButton($button)?>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[&nbsp;<a href="./vdr_std_hm.php">Back to the Standard Vendor Eval Home</a>&nbsp;]</td></tr>
		<tr><td>&nbsp;</td></tr>
		</table>
		</form>

<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>