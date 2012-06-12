<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once CONT_PATH.'po.php';
chkSession();

$page_title		="Purchase Order";
$page_id_left 	="10";
$page_id_right 	="23";
$category_page 	= "archive";
chkSecurity($page_id_right);

$po_id 	= $_GET['id'];
$q 		= "SELECT p.id, p.po_nbr, p.date, CONCAT(u.fname,' ',u.lname) AS fullname, v.name as vname, v.address as vaddr, v.pic, ".
				"v.phone, v.fax, p.kurs, p.inc, p.inc_val, p.authdate, CONCAT(a.fname,' ',a.lname) AS aname, p.status, a.sign, p.project ".
				"FROM po p 
					LEFT JOIN user u ON (u.id = p.user_id_fk)
				    LEFT JOIN user a ON (a.id = p.auth_id_fk) 
				    LEFT JOIN vdr v ON (v.id = p.vdr_id_fk) 
				 WHERE p.id = '$po_id' ;";
$SQL 	= @mysql_query($q) or die(mysql_error());
$array 	= mysql_fetch_array($SQL);

$det_q 	= "SELECT pd.id, pd.description, pd.price, pd.qty, ic.code AS cctr 
           FROM po_det pd 
           LEFT JOIN inv_cctr ic ON (ic.id = pd.cctrID) 
           WHERE pd.po_id_fk = '$po_id' AND pd.del = '0' ";
$det_SQL = @mysql_query($det_q) or die(mysql_error());

log_hist("90",$po_id);
include THEME_DEFAULT.'print_header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td width="50%" valign="top">
			<table border="0" cellpadding="0" cellspacing="0">
				<tr valign="top"><td colspan="3"><b>PURCHASE ORDER</b></td></tr>
				<tr valign="top"><td colspan="3">Information Technology Department</td></tr>
				<tr valign="top"><td colspan="3">&nbsp;</td></tr>
				<tr valign="top">
					<td><b>NO</b></td>
					<td><b>:</b></td>
					<td><?=($array["po_nbr"])?$array["po_nbr"]:"-"?></td>
				</tr>
				<tr valign="top">
					<td><b>DATE</b></td>
					<td><b>:</b></td>
					<td><?=($array["date"])?cplday('d F Y',$array["date"]):"-"?></td>
				</tr>
				<tr valign="top">
					<td><b>PROJECT</b></td>
					<td><b>:</b></td>
					<td><?=($array["project"])?ucwords($array["project"]):"-"?></td>
				</tr>
			</table>
		</td>
		<td width="50%" style='border:2px solid;'>
			<table border="0" cellpadding="0" cellspacing="0">
				<tr><td valign="top">&nbsp;<b>NAME</b>&nbsp;</td>
					<td valign="top">&nbsp;<b>:</b>&nbsp;</td>
					<td valign="top"><?=(COMP_NAME)?COMP_NAME:"-"?></td></tr>
				<tr><td valign="top">&nbsp;<b>NPWP</b>&nbsp;</td>
					<td valign="top">&nbsp;<b>:</b>&nbsp;</td>
					<td valign="top"><?=(COMP_NPWP)?COMP_NPWP:"-"?></td></tr>
				<tr><td valign="top">&nbsp;<b>ADDRESS</b>&nbsp;</td>
					<td valign="top">&nbsp;<b>:</b>&nbsp;</td>
					<td valign="top"><?=(COMP_ADDR)?COMP_ADDR:"-"?></td></tr>
				<tr><td valign="top">&nbsp;<b>PHONE</b>&nbsp;</td>
					<td valign="top">&nbsp;<b>:</b>&nbsp;</td>
					<td valign="top"><?=(COMP_PHONE)?COMP_PHONE:"-"?></td></tr>
				<tr><td valign="top">&nbsp;<b>FAX</b>&nbsp;</td>
					<td valign="top">&nbsp;<b>:</b>&nbsp;</td>
					<td valign="top"><?=(COMP_FAX)?COMP_FAX:"-"?></td></tr>
			</table>
		</td>
	</tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">
			<table border="0" cellpadding="1" cellspacing="1" width="100%">
				<tr><td valign="top" align="left"><b>&nbsp;TO</b></td>
					<td valign="top"><b>:</b></td>
					<td valign="top"><?=($array["vname"])?ucwords($array["vname"]):"-"?><br>
												 <?=($array["vaddr"])?$array["vaddr"]:"-"?></td></tr>
				<tr><td valign="top" rowspan="2" align="left"><b>&nbsp;ATTN</b></td>
					<td valign="top" rowspan="2"><b>:</b></td>
					<td valign="top" rowspan="2" width="200"><?=($array["pic"])?$array["pic"]:"-"?></td>
					<td valign="top" align="right"><b>PHONE</b></td>
					<td valign="top"><b>:</b></td>
					<td><?=($array["phone"])?$array["phone"]:"-"?></td></tr>
				<tr><td valign="top" align="right"><b>FAX</b></td>
					<td valign="top"><b>:</b></td>
					<td><?=($array["fax"])?$array["fax"]:"-"?></td></tr>
				<tr><td colspan="6">&nbsp;</td></tr>
				<tr><td colspan="6">
					<table border="1" cellpadding="1" cellspacing="1" width="100%" style='border: 1px solid #666666; padding: 2px; margin-top: 5px; margin-bottom: 5px; border-width:1px'>
						<tr valign="top" align="center" class="listview">
							<td align="left"><b>NO</b></td>
							<td align="left"><b>DESCRIPTION</b></td>
							<td align="left">&nbsp;<b>CCTR</b>&nbsp;</td>
							<td align="right"><b>UNIT PRICE (<?=($array["kurs"])?$array["kurs"]:"-"?>)</b></td>
							<td><b>QTY</b></td>
							<td align="right"><b>&nbsp;AMOUNT&nbsp;(<?=($array["kurs"])?$array["kurs"]:"-"?>)</b></td>
						</tr>

<?php 
$count = 1;
while($det_array = mysql_fetch_array($det_SQL,MYSQL_ASSOC)) {
		$row_color = ($count % 2)?"odd":"even"; 
		$po_price = $det_array["price"] * $det_array["qty"]; ?>			
						<tr class="<?=$row_color?>" valign="top">
							<td>&nbsp;<?=$count?>.&nbsp;</td>
							<td><?=nl2br($det_array["description"])?></td>
							<td align="left">&nbsp;<?=$det_array["cctr"]?>&nbsp;</td>
							<td align="right"><?=number_format($det_array["price"],2)?></td>
							<td align="center"><?=$det_array["qty"]?></td>
							<td align="right"><?=number_format($po_price,2);?></td>
						</tr>
<?php 
		$po_stotal += $po_price;
		$po_incl = $array["inc_val"] * 0.01 * $po_stotal;
		$po_gtotal = po_calc($array["inc"],$po_stotal,$po_incl);
		$count++;
} 
?>
						<tr class="listview">
							<td colspan="5" align="right"><b>SUBTOTAL</b></td>
							<td align="right"><b><?=number_format($po_stotal,2);?></b></td></tr>
<?php if($array["inc"] != "-"){?>						
						<tr class="listview">
							<td colspan="5" align="right"><b>
								<?=($array["inc"])?$array["inc"]:"-"?>&nbsp;
								<?=($array["inc_val"])?$array["inc_val"]:"-"?>%</b></td>
							<td align="right"><b><?=number_format($po_incl,2);?></b></td></tr>
<?php } ?>
						<tr class="listview">
							<td colspan="5" align="right"><b>GRANDTOTAL</b></td>
							<td align="right"><b><?=number_format($po_gtotal,2);?></b></td></tr>
					</table>
			</td></tr>
		</table>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">&nbsp;Note: For payment details please contact: Ibu Shanti ext.219 & Ibu Nur ext.221(Acc. Dept)</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">
		<table border="0" cellpadding="0" cellspacing="0" width="100%">
			<tr><td>&nbsp;</td>
				<td>&nbsp;</td>
				<td align="center" width="20%">
					Authorized by:<br><br>
					<?=($array["sign"])?"<img src=".$array["sign"]." width=\"150\" height=\"100\" border=\"0\">":"<br><br><br>"?>
					<?=($array["aname"])?strtoupper($array["aname"]):"-"?><br/>
					<?=($array["authdate"]) != "0000-00-00 00:00:00"?cplday('d F Y',$array["authdate"]):"-"?>
				</td></tr>
		</table>
	</td></tr>
</table>

<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.'print_footer.php';?>