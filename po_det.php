<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'notif.lib.php';
require_once CONT_PATH.'po.php';
chkSession();

$page_title		= "PO Details";
$page_id_left 	= "8";
$category_page 	= "main";
chkSecurity($page_id_left);

$status	 	= "&nbsp;";
$po_id 		= ((isset ($_GET['id']) && $_GET['id'] != '')?trim($_GET['id']):'');
$this_page 	= $_SERVER['PHP_SELF']."?id=".$po_id;

$po_info_q 	= "SELECT p.id as po_id, 
					  p.po_nbr as po_nbr, 
					  p.date as po_date, 
					  p.consumable as po_cons, 
					  p.user_id_fk as po_creator, 
			 		  CONCAT(u.fname,' ',u.lname) AS fullname, 
			 		  v.name as v_name, 
			 		  v.address as v_addr, 
			 		  v.pic as v_ctc, 
			 		  v.phone as v_phn, 
			 		  v.fax as fax, 
			 		  p.kurs as v_kurs,
			 		  p.inc as po_inc, 
			 		  p.inc_val as po_val, 
			 		  p.authdate as po_auth_date, 
			 		  p.auth_id_fk as po_auth_user, 
			 		  p.rate, 
			 		  p.status as po_status,
			 		  p.project as po_proj 
			 	FROM po p 
			 		LEFT JOIN user u ON (u.id = p.user_id_fk) 
			 		LEFT JOIN vdr v ON (v.id = p.vdr_id_fk) 
			 	WHERE p.id = '$po_id' AND p.del = '0';";

$po_info_SQL 	= @mysql_query($po_info_q) or die(mysql_error());
$po_info_array 	= mysql_fetch_array($po_info_SQL, MYSQL_ASSOC);

$po_det_info_q	= "SELECT pd.id, 
						  pd.description, 
						  pd.price, 
						  pd.qty, 
						  ic.id AS ccid, 
						  CONCAT(ic.code,' : ',ic.ba,' > ',ic.spc) AS cctr,
						  pd.itcat_id_fk AS itcat,
						  pd.rcv
				   FROM po_det pd 
				   		LEFT JOIN inv_cctr ic ON (ic.id = pd.cctrID)
				   WHERE pd.po_id_fk = '$po_id' AND pd.del = '0' ";
$po_det_info_SQL= @mysql_query($po_det_info_q) or die(mysql_error());

$po_cctr_q 		= "SELECT id, CONCAT(ic.code,' : ',ic.ba,' > ',ic.spc) AS cctr 
				   FROM inv_cctr ic 
				   WHERE ic.del = 0 
				   ORDER BY ic.ba ASC;";
$po_cctr_SQL 	= @mysql_query($po_cctr_q) or die(mysql_error());

if(isset($_POST['update_po'])) {
	$po_id_fk 	= trim($_POST['po_id']);
	$po_det_id 	= $_POST['po_det_id'];
	$po_inc		= trim($_POST['po_inc']);
	$po_inc_val	= (int) trim($_POST['po_inc_val']);
	$po_type	= (int) trim($_POST['po_type']);
	$po_proj	= trim($_POST['po_proj']);
	$upd_po_q	= "UPDATE po 
				   SET consumable = '$po_type', inc = '$po_inc', inc_val = '$po_inc_val', project = '$po_proj'
				   WHERE id = '$po_id';";
	@mysql_query($upd_po_q) or die(mysql_error());
	
	
	$d_req_q = "DELETE FROM po_req WHERE po = '$po_id_fk';";
	@mysql_query($d_req_q) or die(mysql_error());
	
	$d_rfa_q = "DELETE FROM po_rfa WHERE po = '$po_id_fk';";
	@mysql_query($d_rfa_q) or die(mysql_error());
	
	
	foreach($po_det_id as $key_id => $value_id) {
		$po_desc 	= $_POST['desc'][$key_id];
		$po_cctr	= $_POST['po_cctr'][$key_id];
		$po_price 	= $_POST['price'][$key_id];
		$po_qty 	= $_POST['qty'][$key_id];
		$po_itcat	= $_POST['po_itcat'][$key_id];
		$po_req_id	= $_POST['po_req'][$key_id];
		$po_rfa_id	= $_POST['po_rfa'][$key_id];
		$po_rcv		= $_POST['po_rcv'][$key_id];
		$po_rdate	= date('Y-m-d H:i:s');
		$upd_po_det_q = "UPDATE po_det 
						 SET description = '$po_desc', itcat_id_fk = '$po_itcat', cctrID = '$po_cctr' ,price = '$po_price', qty = '$po_qty', rcv = '$po_rcv', rcvdate = '$po_rcvdate'
						 WHERE id = '$value_id';";
		@mysql_query($upd_po_det_q) or die(mysql_error());
		
		po_upd_req($po_id_fk,$value_id,$po_req_id);
		po_upd_rfa($po_id_fk,$value_id,$po_rfa_id);
	}
	log_hist(88,$po_id_fk);
	header("location:".$_SERVER['PHP_SELF']."?id=".$po_id_fk);
	exit();	
} 

elseif(isset($_POST['auth_po'])) {
	$po_id_fk = trim($_POST['po_id']);
	notify_po($po_id_fk,"authorized");
	log_hist(91,$po_id_fk);
	po_update("authorize");
} 
elseif(isset($_POST['cancel_po'])) {
	$po_id_fk = trim($_POST['po_id']);
	notify_po($po_id_fk,"cancelled");
	log_hist(92,$po_id_fk);
	po_update("cancel");
} 

$inc_det = array("VAT"=>"VAT","DISC"=>"DISC","-"=>"-");

$button1 = array("auth_po"=>array("submit"=>"  AUTH PO  "),
				 "cancel_po"=>array("submit"=>"  CANCEL PO  "));

$button2 = array("update_po" =>array("submit"=>"  UPDATE PO  "));

$button3 = array("auth_po"=>array("submit"=>"  AUTH PO  "),
                 "update_po"=>array("submit"=>"  UPDATE PO  "),
				 "cancel_po"=>array("submit"=>"  CANCEL PO  "));

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" enctype="multipart/form-data" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><?=$status?></td></tr>
	<tr><td>[&nbsp;<a href="./po_hm.php">BACK TO THE PO HOME</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
<?php 
	if($po_info_array['po_status'] == "pending" AND $_SESSION['uid'] == 10) { 
		echo "<tr><td>".genButton($button3)."</td></tr>";
	}
	elseif($po_info_array['po_status'] == "pending" AND $_SESSION['level'] <= 4) { 
		echo "<tr><td>".genButton($button1)."</td></tr>";
	}
	elseif($po_info_array['po_status'] == "pending" AND $_SESSION['level'] > 4) {
		echo "<tr><td>".genButton($button2)."</td></tr>";
	}
	elseif($po_info_array['po_status'] == "pending" AND $_SESSION['level'] <= 4 AND $po_info_array['po_creator'] == $_SESSION['uid']) {
		echo "<tr><td>".genButton($button3)."</td></tr>";
	}
	else { 
	  	echo "<tr><td>".genButton($button2)."</td></tr>";
	}
?>  
	<tr><td>	
	<table border="0" cellpadding="1" cellspacing="1">
		<tr><td>
			<div class="well">
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table-condensed">
				<tr valign="top">
					<td width="50">&nbsp;<b>ID</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td>#<?=($po_info_array['po_id'])?$po_info_array['po_id']:"-"?></td>
					<td align="right">&nbsp;<b>STATUS</b>&nbsp;</td>
					<td><b>:</b></td>
					<td><?=($po_info_array['po_status'])?strtoupper($po_info_array['po_status']):"-"?></td>
				</tr>
				<tr valign="top">
					<td width="50">&nbsp;<b>NO</b>&nbsp;</td>
					<td width="10"><b>:</b><input type="hidden" name="po_id" value="<?=$po_info_array['po_id']?>"/></td>
					<td><?=($po_info_array['po_nbr'])?$po_info_array['po_nbr']:"-"?></td>
					<td align="right" rowspan="3">&nbsp;<b>PROJECT</b>&nbsp;</td>
					<td rowspan="3"><b>:</b></td>
					<td rowspan="3"><textarea name="po_proj" class="input-large"><?=($po_info_array['po_proj'])?ucwords($po_info_array['po_proj']):"-"?></textarea></td>
				</tr>
				<tr><td width="50">&nbsp;<b>DATE</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td><?=($po_info_array['po_date'])?cplday('d F Y',$po_info_array['po_date']):"-"?></td>
				</tr>
				<tr><td width="50" valign="top" align="left">&nbsp;<b>TO</b>&nbsp;</td>
					<td width="10" valign="top"><b>:</b></td>
					<td valign="top">
						<?=($po_info_array['v_name'])?ucwords($po_info_array['v_name']):"-"?><br>
						<?=($po_info_array['v_addr'])?$po_info_array['v_addr']:"-"?></td>
				</tr>
				<tr><td width="50" valign="top" rowspan="2" align="left">&nbsp;<b>ATTN</b>&nbsp;</td>
					<td width="10" valign="top" rowspan="2"><b>:</b></td>
					<td valign="top" rowspan="2" width="200"><?=($po_info_array['v_ctc'])?$po_info_array['v_ctc']:"-"?></td>
					<td valign="top" align="right">&nbsp;<b>PHONE</b>&nbsp;</td>
					<td valign="top"><b>:</b></td>
					<td><?=($po_info_array['v_phn'])?$po_info_array['v_phn']:"-"?></td>
				</tr>
				<tr><td width="50" valign="top" align="right">&nbsp;<b>FAX</b>&nbsp;</td>
					<td width="10" valign="top"><b>:</b></td>
					<td><?=($po_info_array['fax'])?$po_info_array['fax']:"-"?></td>
				</tr>
				<tr><td align="left">&nbsp;<b>CURR</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td colspan="4"><?=po_scurr($po_info_array['v_kurs']);?></td>
				</tr>
				<tr><td width="50" valign="top" align="left">&nbsp;<b>RATE</b>&nbsp;</td>
					<td width="10" valign="top"><b>:</b></td>
					<td><?=($po_info_array['rate'])?number_format($po_info_array['rate'],10):"0"?></td>
				</tr>
				<tr><td width="50" valign="top" align="left">&nbsp;<b>TYPE</b>&nbsp;</td>
					<td width="10" valign="top"><b>:</b></td>
					<td><?=po_type($po_info_array['po_cons']);?></td>
				</tr>
				<tr><td colspan="6">&nbsp;</td></tr>
				<tr><td colspan="6">
					<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
						<thead>
						<tr valign="middle"><td align="left">&nbsp;<b>NO</b>&nbsp;</td>
							<td align="left">&nbsp;<b>DESCRIPTION</b>&nbsp;</td>
							<td align="left">&nbsp;<b>COST CTR.</b>&nbsp;</td>
							<td align="left">&nbsp;<b>UNIT PRICE</b>&nbsp;</td>
							<td align="left">&nbsp;<b>QTY</b>&nbsp;</td>
							<td align="right">&nbsp;<b>AMOUNT</b>&nbsp;</td>
						</tr>
						</thead>
						<tbody>
<?php 
$count 		= 1;
$i 			= 0;
$po_stotal 	= 0;
while($po_det_info_array = mysql_fetch_array($po_det_info_SQL,MYSQL_ASSOC)) {
		$po_price 	= $po_det_info_array["price"] * $po_det_info_array["qty"]; 
			if ($_SESSION['uid'] == $po_info_array['po_creator'] OR $_SESSION['level'] <= 5 OR $_SESSION['level'] == 10) {?>
						<tr valign="top" align="left">
						<td>&nbsp;<?=$count?>.&nbsp;
								<input type="hidden" name="po_det_id[<?=$i?>]" value="<?=$po_det_info_array["id"]?>"></td>
							<td><?=po_itcat_select($i,$po_det_info_array["itcat"])?>&nbsp;<br/><br/>
								<textarea cols="60" rows="10" name="desc[<?=$i?>]" wrap="virtual"><?=strip_tags(nl2br($po_det_info_array["description"]))?></textarea><br/><br/>
									  <label>RECEIVED</label><?=po_rcv($po_det_info_array["id"],$i)?>
								&nbsp;<label name="req">REQ ID</label>
										<input type="text" name="po_req[<?=$i?>]" value="<?=po_list_req($po_det_info_array["id"]);?>" size=20/><br/>
										<?=po_req($po_det_info_array["id"]);?>
								&nbsp;<label name="rfa">RFA ID</label>
										<input type="text" name="po_rfa[<?=$i?>]" value="<?=po_list_rfa($po_det_info_array["id"]);?>" size=20/><br/>
										<?=po_rfa($po_det_info_array["id"]);?><br/>
							</td>
							<td><?=po_cctr_select($i,$po_det_info_array["ccid"]);?></td>
							<td><input type="text" name="price[<?=$i?>]" size="20" value="<?=$po_det_info_array["price"]?>"></td>
							<td><input type="text" name="qty[<?=$i?>]" size="3" maxlength="3" value="<?=$po_det_info_array["qty"]?>" class="input-small"></td>
							<td align="right"><?=number_format($po_price,2);?></td>
						</tr>
<?php  		}
			else { ?>
						<tr valign="top" align="left">
							<td>&nbsp;<?=$count?>.&nbsp;<input type="hidden" name="po_det_id[<?=$i?>]" value="<?=$po_det_info_array["id"]?>">&nbsp;</td>
							<td>&nbsp;<?=strip_tags(nl2br($po_det_info_array["description"]),'<br>')?>&nbsp;<br/><br/>
								&nbsp;<label name="req">REQ ID</label><?=po_req($po_det_info_array["id"]);?><br/>
								&nbsp;<label name="rfa">RFA ID</label><?=po_rfa($po_det_info_array["id"]);?></td>
							<td>&nbsp;<?=!empty($po_det_info_array["cctr"])?$po_det_info_array["cctr"]:"-";?>&nbsp;</td>
							<td align="right">&nbsp;<?=number_format($po_det_info_array["price"],2)?>&nbsp;</td>
							<td align="right">&nbsp;<?=$po_det_info_array["qty"]?>&nbsp;</td>
							<td align="right">&nbsp;<?=number_format($po_price,2);?>&nbsp;</td>
						</tr>
<?php		}
		$po_stotal += $po_price;
		$po_incl 	= $po_info_array['po_val'] * 0.01 * $po_stotal;
		$po_gtotal 	= po_calc($po_info_array['po_inc'],$po_stotal,$po_incl);
		$count++;
		$i++;
} 
?>
						<tr class="listview">
							<td colspan="5" align="right">&nbsp;<b>SUBTOTAL</b>&nbsp;</td>
							<td align="right">&nbsp;<b><?=number_format($po_stotal,2);?></b>&nbsp;</td></tr>					
						<tr class="listview">
							<td colspan="5" align="right"><b>
								<select name="po_inc" class="input-small">
										<option value="-">--------</option>
										<?php foreach($inc_det as $inc_key => $inc_name) {
												$compare_inc = ($inc_key == $po_info_array['po_inc'])?"SELECTED":"";
												echo "<option value =\"$inc_key\" $compare_inc>$inc_name</option>\n";
											  } 
										?>
 			   						</select>&nbsp;
								<input type="text" name="po_inc_val" size="3" maxlength="3" value="<?=($po_info_array['po_val'])?$po_info_array['po_val']:"0"?>" class="input-small" />&nbsp;%</b></td>
							<td align="right">&nbsp;<b><?=number_format($po_incl,2);?></b>&nbsp;</td></tr>
						<tr class="listview">
							<td colspan="5" align="right">&nbsp;<b>GRANDTOTAL</b>&nbsp;</td>
							<td align="right">&nbsp;<b><?=number_format($po_gtotal,2);?></b>&nbsp;</td></tr>
					</tbody></table>
				</td></tr>
			</table>
		</td></tr>
	</table></div>
	</td></tr>
<?php 
	if($po_info_array['po_status'] == "pending" AND $_SESSION['uid'] == 10) { 
		echo "<tr><td>".genButton($button3)."</td></tr>";
	}
	elseif($po_info_array['po_status'] == "pending" AND $_SESSION['level'] <= 4) { 
		echo "<tr><td>".genButton($button1)."</td></tr>";
	}
	elseif($po_info_array['po_status'] == "pending" AND $_SESSION['level'] > 4) {
		echo "<tr><td>".genButton($button2)."</td></tr>";
	}
	elseif($po_info_array['po_status'] == "pending" AND $_SESSION['level'] <= 4 AND $po_info_array['po_creator'] == $_SESSION['uid']) {
		echo "<tr><td>".genButton($button3)."</td></tr>";
	}
	else { 
	  	echo "<tr><td>".genButton($button2)."</td></tr>";
	}
?> 
	<tr><td>[&nbsp;<a href="./po_hm.php">BACK TO THE PO HOME</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
</table>
</form>
<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>