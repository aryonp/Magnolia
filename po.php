<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'notif.lib.php';
require_once CONT_PATH.'po.php';
chkSession();

$page_title 	= "Create PO";
$page_id_left 	= "8";
$category_page 	= "main";
chkSecurity($page_id_left);

$vendor_list_query 	= "SELECT v.id, v.name FROM vdr v ORDER BY v.name ASC;";
$vendor_list_SQL 	= @mysql_query($vendor_list_query) or die(mysql_error());
$status 			="&nbsp;";
$po_det 			= array();
$po_curr 			= trim($_POST['kurs']);

$rate_query = "SELECT r.rate FROM rate r WHERE r.del = '0' AND r.curr = '$po_curr' AND DATE(NOW()) BETWEEN DATE(r.date1) AND DATE(r.date2);";
$rate_sql 	= @mysql_query($rate_query) or die(mysql_error());
$rate_array = mysql_fetch_array($rate_sql,MYSQL_ASSOC);

if($po_curr == "EUR") { 
	$rate_val = 1;
	$rate_notes = ""; 
} 

else {
	if(mysql_num_rows($rate_sql) >= 1) {
		$rate_val 	= $rate_array["rate"];
		$rate_notes = "(Please check actual rate again, default rate comes from FA dept.)";
	}
	else {
		$rate_val 	= "0";
		$rate_notes = "(No data from FA dept. related to EUR rate for this currency and period)";
	}
}

if(isset($_POST['submit_po'])) {
	$po_nbr 	= genPO();
	$date 		= trim($_POST['date']);
	$po_vdr 	= trim($_POST['po_vdr']);
	$userid 	= trim($_POST['userid']);
	$po_incl 	= trim($_POST['po_incl']);
	$po_val 	= trim($_POST['po_val']);
	$po_rate 	= trim($_POST['po_rate']);
	$kurs 		= trim($_POST['po_curr']);
	$po_cons 	= (int) ($_POST['po_cons']);
	$po_proj	= mysql_real_escape_string(trim($_POST['project']));
	$po_desc 	= (is_array($_POST['desc']) OR !in_array("",$_POST['desc']))?$_POST['desc']:"";
	
	if (!empty($po_desc) AND !empty($po_vdr) AND $kurs != "-") {	
		$insert_po_q 	= "INSERT INTO po (po_nbr,date,vdr_id_fk,user_id_fk,kurs,inc,inc_val,rate,consumable,status,project) VALUES ('$po_nbr','$date','$po_vdr','$userid','$kurs','$po_incl','$po_val','$po_rate','$po_cons','pending','$po_proj');";
		@mysql_query($insert_po_q) or die(mysql_error());
		
		$po_id_fk 		= mysql_insert_id();
		$ins_po_det_q 	= "INSERT INTO po_det (po_id_fk,description,cctrID,price,qty,itcat_id_fk) VALUES ";
		
		foreach($po_desc as $key_desc => $value_desc) {
			$po_cctr 	= $_POST['po_cctr'][$key_desc];
			$po_price 	= $_POST['price'][$key_desc];
			$po_qty 	= $_POST['qty'][$key_desc];
			$po_itcat 	= $_POST['po_itcat'][$key_desc];
			$po_req		= $_POST['po_req'][$key_desc];
			$po_rfa		= $_POST['po_rfa'][$key_desc];
			array_push($po_det," ('$po_id_fk','".mysql_real_escape_string($value_desc)."','$po_cctr','$po_price','$po_qty','$po_itcat')");
			
			/*
			po_upd_req($po_id_fk,$value_id,$po_req);
			po_upd_rfa($po_id_fk,$value_id,$po_rfa);
			*/
		}
		
		$ins_po_det_q .= implode(",",$po_det);
		@mysql_query($ins_po_det_q) or die(mysql_error());
		
		log_hist(87,$po_id_fk);
		notify_po($po_id_fk,"pending");
		header("location:./po_det.php?id=".$po_id_fk);
	} 
	
	else { $status =  "<p class=\"redbox\">Missing Information! could not create PO, Press back button to repeat input</p>"; }
}

$button = array("submit_po"=>array("submit"=>"  SUBMIT PO  "),
		        "reset_po"=>array("reset"=>"  RESET PO  "));	
	
include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" enctype="multipart/form-data" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><?=$status?></td></tr>
	<tr><td><div class="well form-inline">CURRENCY:<?=kurs()?>&nbsp;&nbsp;
		VENDOR : 
  		<select name="vendor">
    		<option value="-">---------------------</option>
<?php 
$po_vdr_id = (isset($_POST['vendor']))?$_POST['vendor']:"";
while($vdr_list_array = mysql_fetch_array($vendor_list_SQL)){ 
		$selected = ($po_vdr_id == $vdr_list_array["id"])?"SELECTED":"";
	?>
    <option value="<?=$vdr_list_array["id"]?>" <?=$selected?>><?=ucwords($vdr_list_array["name"])?></option>
<?php } ?>
  		</select>
  		&nbsp;&nbsp;& &nbsp;<input type="text" class="input-small" name="items_count" size="3" maxlength="2" value="<?=(isset($_POST['items_count']))?$_POST['items_count']:"";?>">&nbsp;ITEMS&nbsp;&nbsp;<input type="submit" class="btn-info btn-small" name="gen_man_po" value=" GENERATE >>" />
	</div></td></tr>	
	<tr><td>&nbsp;</td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td>&nbsp;</td></tr>
<?php if (isset($_POST['gen_man_po']) AND $_POST['vendor'] != "-" AND (!empty($_POST['items_count'])) AND $_POST['kurs'] != "-" AND (is_numeric($_POST['items_count']))) { 
	$items_count 	= $_POST['items_count']; 
	$vdr_id 		= $_POST['vendor'];
	$vdr_det_q 		= "SELECT id, name, address, phone, fax, pic FROM vdr WHERE id = '$vdr_id';";
	$vdr_det_SQL 	= mysql_query($vdr_det_q);
	$vdr_det_array 	= mysql_fetch_array($vdr_det_SQL);
?>
	<tr><td>[&nbsp;<a href="./po_hm.php">Back to the PO Home</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>	
	<table border="0" cellpadding="1" cellspacing="1">
		<tr><td><?=genButton($button)?></td></tr>
		<tr><td><input type="hidden" name="po_vdr" value="<?=$po_vdr_id?>">
			<input type="hidden" name="userid" value="<?=$_SESSION['uid']?>"></td>
		</tr>
		<tr><td>
			<div class="well">
			<table border="0" cellpadding="1" cellspacing="1">
				<tr><td width="150">&nbsp;<b>NO</b>&nbsp;</td>
					<td><b>:</b></td>
					<td colspan="4"><b>(Auto Generate)</b></td></tr>
				<tr><td width="150">&nbsp;<b>DATE</b>&nbsp;</td>
					<td><b>:</b></td>
					<td colspan="4"><input type="text" size="20" maxlength="10" name="date" id="cal" value="<?=date('Y-m-d H:i:s')?>">&nbsp;
					<a href="javascript:NewCal('cal','yyyymmdd',true,24)"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a></td></tr>
				<tr><td width="150" valign="top" align="left"><b>&nbsp;TO</b></td>
					<td valign="top"><b>:</b></td>
					<td valign="top" colspan="4"><?=($vdr_det_array["name"])?$vdr_det_array["name"]:"-"?><br><?=($vdr_det_array["address"])?$vdr_det_array["address"]:"-"?></td></tr>
				<tr><td width="200" valign="top" rowspan="2" align="left"><b>&nbsp;ATTN</b></td>
					<td valign="top" rowspan="2"><b>:</b></td>
					<td valign="top" rowspan="2" width="200"><?=($vdr_det_array["pic"])?$vdr_det_array["pic"]:"-"?></td>
					<td valign="top" align="right"><b>PHONE</b></td>
					<td valign="top"><b>:</b></td>
					<td><?=($vdr_det_array["phone"])?$vdr_det_array["phone"]:"-"?></td></tr>
				<tr><td valign="top" align="right"><b>FAX</b></td>
					<td valign="top"><b>:</b></td>
					<td><?=($vdr_det_array["fax"])?$vdr_det_array["fax"]:"-"?></td></tr>
				<tr><td width="150">&nbsp;<b>TYPE</b>&nbsp;<font color="Red">*</font>&nbsp;</td>
					<td><b>:</b></td>
					<td colspan="4">
						<select name="po_consum">
							<option value=0>NON-CONSUMABLE</option>
							<option value=1>CONSUMABLE</option>
						</select>
					</td>
				</tr>
				<tr><td>&nbsp;<b>CURR. RATE (EUR)&nbsp;<font color="Red">*</font></b>&nbsp;</td>
					<td><b>:</b></td>
					<td colspan="4"><input type="text" name="po_rate" value="<?=number_format($rate_val,10)?>" />&nbsp;<?=$rate_notes?></td>
				</tr>
				<tr><td>&nbsp;<b>PROJECT</b>&nbsp;</td>
					<td><b>:</b></td>
					<td colspan="4"><input type="text" name="project" /></td>
				</tr>
				<tr><td colspan="6">
					<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
						<thead>
						<tr valign="middle"> 
							<td>&nbsp;<b>NO</b>&nbsp;</td>
							<td>&nbsp;<b>DESCRIPTION</b>&nbsp;<font color="Red">*</font>&nbsp;</td>
							<td>&nbsp;<b>COST CTR.</b>&nbsp;</td>
							<td>&nbsp;<b>UNIT PRICE&nbsp;<br>(<?=$po_curr?>)</b>&nbsp;<input type="hidden" name="po_curr" value="<?=$po_curr?>"></td>
							<td>&nbsp;<b>QTY</b>&nbsp;</td>
						</tr>
						</thead>
						<tbody>
<?php for($ctm = 1;$ctm <= $items_count;$ctm++) { ?>			
						<tr valign="top" align="left">
							<td>&nbsp;<?=$ctm?>.&nbsp;</td>
							<td><?=po_itcat_list()?><br/><br/><textarea cols="60" rows="10" name="desc[]" wrap="virtual"></textarea>
							<td><?=po_cctr_list()?></td>
							<td><input type="text" name="price[]" size="20"></td>
							<td><input type="text" name="qty[]" size="7" maxlength="3" class="input-small"></td></tr>
<?php } ?>
						<tr class="listview">
							<td colspan="4" align="right">INCL.&nbsp;<?=po_incl()?></td>
							<td><input type="text" name="po_val" size="3" maxlength="3" class="input-small">&nbsp;%</td>
						</tr>
					</tbody>
					</table></div>
				</td></tr>
			</table>
		</td></tr>
		<tr><td><?=genButton($button)?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[&nbsp;<a href="./po_hm.php">Back to the PO Home</a>&nbsp;]</td></tr>
	</table>
	</td></tr>
<?php } ?>
  	<tr><td>&nbsp;</td></tr>
</table>
</form>
<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>