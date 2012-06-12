<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title ="Vendor Evaluation Form Page";
$page_id_left ="9";
$page_id_right = "19";
$category_page = "eval";
chkSecurity($page_id_right);

$vdr_pr = array();

function vendor_list_static($vendor_id_sl){
	$vendor_list_query ="SELECT v.id, v.name FROM vdr v ORDER BY v.name ASC;";
	$vendor_list_SQL = @mysql_query($vendor_list_query) or die(mysql_error()); ?>
		<select name="vendor">
    		<option value="-">---------------------</option>
<?php	while($vendor_list_array = mysql_fetch_array($vendor_list_SQL,MYSQL_ASSOC)){
			$selected = ($vendor_id_sl == $vendor_list_array["id"])?"SELECTED":"";
		?>
    		<option value="<?=$vendor_list_array["id"]?>" <?=$selected?>><?=ucwords($vendor_list_array["name"])?></option>
<?php 	} 
  		echo "</select>";
	} 	
$status = "&nbsp;";

function vendor_eval_sugg(){ ?>
		<select name="suggestion">
    		<option value="-">---------------------</option>
    		<option value="maintain">Maintain in AVL</option>
    		<option value="eliminate">Eliminate from AVL</option>
		</select>
<?php } 

if (isset($_POST['submit'])){
 	$startdate = $_POST['date1'];
	$enddate = $_POST['date2'];
	$vendor_id_fk = $_POST['vendor'];
	$total = ($_POST['total'])?$_POST['total']:"";
	$average = ($_POST['average'])?$_POST['average']:"";
	$remarks = ($_POST['eval_remarks'])?$_POST['eval_remarks']:"";
	$suggestion = ($_POST['suggestion'])?$_POST['suggestion']:"";
	$evaldate = date('Y-m-d');
	$user_id_fk = $_SESSION['uid'];
	$eval_id_fk = $_POST['eval_id_fk'];
	
	if ($suggestion != "-" AND $vendor_id_fk != "-"){
		$insert_eval_pr_query  = "INSERT INTO ev_pr (start,end,vdr_id_fk,total,avg,remarks,suggestion,eval,user_id_fk) ".
								 "VALUES ('$startdate','$enddate','$vendor_id_fk','$total','$average','$remarks','$suggestion','$evaldate','$user_id_fk');";
		@mysql_query($insert_eval_pr_query) or die (mysql_error());
		$vdr_ev_pr_id_fk = mysql_insert_id();
		$insert_eval_pr_det_query  = "INSERT INTO ev_pr_det (ev_pr_id_fk,ev_id_fk) VALUES ";
		foreach($eval_id_fk as $vdr_eval_id_fk) {
			array_push($vdr_pr," ('$vdr_ev_pr_id_fk','$vdr_eval_id_fk') ");
			$update_eval_query = "UPDATE ev_std SET period = '1' WHERE id = '$vdr_eval_id_fk';";
			@mysql_query($update_eval_query) or die(mysql_error());
		}
		$insert_eval_pr_det_query .= implode(",",$vdr_pr);
		@mysql_query($insert_eval_pr_det_query) or die (mysql_error());
		log_hist("96",$vdr_ev_pr_id_fk);
		header("location:./vdr_pr_hm.php");
	} 
	
	else {
			$status ="<p class=\"yellowbox\">Please give also your <font color=\"red\"><b>status</b></font> suggestion ! hit the back button to change your data</p>";
		
	}
}

$vendor_criteria_query = "SELECT id, name FROM ev_crit;";
$vendor_criteria_SQL = @mysql_query($vendor_criteria_query) or die(mysql_error());

$vendor_id = (isset($_POST['vendor']))?$_POST['vendor']:"-";

$button = array("submit"=>array("submit"=>"  SUBMIT EVAL  "),
				"reset"=>array("reset"=>"  RESET EVAL  "));

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>VENDOR PERIODIC EVALUATION FORM</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
        <tr><td>
		<div class="well">
        <table border="0" >
			<tr><td align="left"><b>START DATE</b></td>
				<td>:</td>
				<td><input type="text" name="date1" id="cal1" size="10" maxlength="10" value="<?=(isset($_POST['date1']))?$_POST['date1']:"";?>">&nbsp;
					<a href="javascript:NewCal('cal1','yyyymmdd')">
					<img src="<?=IMG_PATH?>cal.gif" border="0" /></a></td>
				<td>&nbsp;&nbsp;<b>END</b>:</td>
				<td><input type="text" name="date2" id="cal2" size="10" maxlength="10" value="<?=(isset($_POST['date2']))?$_POST['date2']:"";?>">&nbsp;
					<a href="javascript:NewCal('cal2','yyyymmdd')">
					<img src="<?=IMG_PATH?>cal.gif" border="0" /></a>
				</td>
				<td>&nbsp;&nbsp;<input type="submit" class="btn-info btn-small" name="gen-vdr-eval-per" value="  GENERATE  "></td>
			</tr>
			<tr><td align="left"><b>COMPANY NAME</b></td><td>:</td><td colspan="3"><?=vendor_list_static($vendor_id)?></td><td>&nbsp;</td></tr>
		</table></div>
        </td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
<?php if (isset($_POST['gen-vdr-eval-per']) AND !empty($_POST['date1']) AND !empty($_POST['date2']) AND $_POST['vendor'] != "-"  ) { 
			$datestart = $_POST['date1'];
			$dateend = $_POST['date2'];
			$get_eval_data_query ="SELECT ev.id, ev.po_nbr, ev.avg ".
									"FROM ev_std ev ".
									"WHERE ev.vdr_id_fk = '$vendor_id' AND ev.period = '0' AND ev.del = '0' ".
									"AND date(ev.date) BETWEEN '$datestart' AND '$dateend'; ";
			$get_eval_data_SQL = mysql_query($get_eval_data_query);
			$eval_total = 0;
			$eval_avg = 0;
		if (mysql_num_rows($get_eval_data_SQL) >= 1) { ?>
		<tr><td>[&nbsp;<a href="./vdr_pr_hm.php">Back to the Periodic Vendor Eval Home</a>&nbsp;]</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td bgcolor="#6699cc"><?=genButton($button);?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<table border="0" cellspacing="1" cellpadding="1" width="300" style='border: 1px solid #CECECE; padding: 2px; margin-top: 5px; margin-bottom: 5px;'>
				<tr class="listview">
					<td align="left">&nbsp;<b>EVAL. ID</b>&nbsp;</td>
					<td align="center">&nbsp;<b>P.O.</b>&nbsp;</td>
					<td align="center">&nbsp;<b>GRADE</b>&nbsp;</td></tr>
<?php		 
			$count = 1;
			$loop = 0;
			while($array = mysql_fetch_array($get_eval_data_SQL,MYSQL_ASSOC)) { 
				$row_color = ($count % 2)?"odd":"even";?>
				<tr class="<?=$row_color?>">
					<td align="left">&nbsp;<input type="hidden" name="eval_id_fk[]" value="<?=$array["id"]?>">&nbsp;#<?=$array["id"]?>&nbsp;</td>
					<td align="center">&nbsp;<?=$array["po_nbr"]?>&nbsp;</td>
					<td align="center">&nbsp;<?=number_format($array["avg"],'2','.','')?>&nbsp;</td>
				</tr>
<?php 			$eval_total = $eval_total + $array["avg"];
				$count++;
				$loop++;
			} 
				$count_avg = $eval_total / $loop;
				$eval_avg = number_format($count_avg, 2,'.','');?>
				<tr class="listview">
					<td align="center" colspan="2">&nbsp;<b>TOTAL</b>&nbsp;</td>
					<td align="center"><input type="hidden" name="total" value="<?=$eval_total?>">&nbsp;<b><?=number_format($eval_total,'2','.','')?></b>&nbsp;</td></tr>
				<tr class="listview">
					<td align="center" colspan="2">&nbsp;<b>AVERAGE</b>&nbsp;</td>
					<td align="center"><input type="hidden" name="average" value="<?=$eval_avg?>">&nbsp;<b><?=number_format($eval_avg,'2','.','')?></b>&nbsp;</td></tr>
			</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><b>REMARKS</b></td></tr>
		<tr><td><textarea cols="40" rows="5" name="eval_remarks" wrap="virtual"></textarea></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<table border="0" cellspacing="1" cellpadding="1">
				<tr><td><b>STATUS</b></td>
					<td><b>&nbsp;:&nbsp;</td>
					<td><?=vendor_eval_sugg()?></td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td><b>DATE</b></td>
					<td><b>&nbsp;:&nbsp;</td>
					<td><?=date('d.m.Y')?></td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td><b>EVALUATED BY</b></td>
					<td><b>&nbsp;:&nbsp;</td>
					<td><?=ucwords($_SESSION['fullname'])?></td></tr>
			</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td bgcolor="#6699cc"><?=genButton($button);?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[&nbsp;<a href="./vdr_pr_hm.php">Back to the Periodic Vendor Eval Home</a>&nbsp;]</td></tr>
		<tr><td>&nbsp;</td></tr>
<?php 	} else {	
				$status ="<p class=\"yellowbox\">No Data Available, Press evaluate your input.</p>";
		}
	} ?>       
		<tr><td><?=$status?></td></tr>
	</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>