<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title ="Vendor Periodic Evaluation Details";
$page_id_left ="9";
$page_id_right = "19";
$category_page = "eval";
chkSecurity($page_id_right);

if(isset($_GET['id'])) { $eval_pr_id = $_GET['id']; }
$select_ev_pr_q = "SELECT ep.id, ep.start, ep.end, v.name as vname, ep.total, ep.avg, ep.remarks, ep.suggestion, ep.eval, CONCAT(u.fname,' ',u.lname) AS fullname ".
					"FROM ev_pr ep LEFT JOIN vdr v ON (v.id = ep.vdr_id_fk) LEFT JOIN user u ON (u.id = ep.user_id_fk) WHERE ep.id = '$eval_pr_id' ";
$select_ev_pr_SQL = @mysql_query($select_ev_pr_q) or die(mysql_error());
$array = mysql_fetch_array($select_ev_pr_SQL);

$select_ev_pr_dt_q = "SELECT ev.id, p.po_nbr, ev.avg FROM ev_pr_det epd LEFT JOIN ev_std ev ON (ev.id = epd.ev_id_fk) LEFT JOIN po p ON (p.id = ev.po_nbr) WHERE epd.ev_pr_id_fk = '$eval_pr_id' ";
$select_ev_pr_dt_SQL = @mysql_query($select_ev_pr_dt_q) or die(mysql_error());

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>VENDOR PERIODIC EVALUATION DETAILS</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><?=back_button()?></td></tr>
		<tr><td>&nbsp;</td></tr>
        <tr><td>
        <table border="0">
        	<tr><td align="left"><b>ID</b></td>
        		<td>:</td>
        		<td colspan="3"><?=($array["id"])?"#".$array["id"]:"&nbsp; -"?></td></tr>
			<tr><td align="left"><b>EVALUATION PERIOD</b></td>
				<td>:</td>
				<td><?=($array["start"])?cplday('d F Y',$array["start"]):"&nbsp; -"?></td>
				<td>&nbsp;-&nbsp;</td>
				<td><?=($array["end"])?cplday('d F Y',$array["end"]):"&nbsp; -"?></td>
				</tr>
			<tr><td align="left"><b>COMPANY NAME</b></td>
				<td>:</td>
				<td colspan="3"><?=($array["vname"])?ucwords($array["vname"]):"&nbsp; -"?></td>
				</tr>
		</table>
        </td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<table border="0" cellpadding="1" cellspacing="1" class="table table-striped table-bordered table-condensed">	
				<thead>
            	<tr valign="middle"> 
					<td align="left" width=150>&nbsp;<b>EVAL. ID</b>&nbsp;</td>
					<td align="left" width=150>&nbsp;<b>P.O.</b>&nbsp;</td>
					<td align="center" width=150>&nbsp;<b>GRADE</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php 
	$count = 1;
	while($dt_array = mysql_fetch_array($select_ev_pr_dt_SQL)) {?>
				<tr>
					<td align="left">&nbsp;#<?=$dt_array["id"]?>&nbsp;</td>
					<td align="left">&nbsp;<?=$dt_array["po_nbr"]?>&nbsp;</td>
					<td align="center">&nbsp;<?=number_format($dt_array["avg"],'2','.','')?>&nbsp;</td>
<?php $count++;
		} ?>
				<tr class="listview">
					<td align="center" colspan="2">&nbsp;<b>TOTAL</b>&nbsp;</td>
					<td align="center">&nbsp;<b><?=($array["total"])?number_format($array["total"],'2','.',''):"&nbsp; -"?></b>&nbsp;</td></tr>
				<tr class="listview">
					<td align="center" colspan="2">&nbsp;<b>AVERAGE</b>&nbsp;</td>
					<td align="center">&nbsp;<b><?=($array["avg"])?number_format($array["avg"],'2','.',''):"&nbsp; -"?></b>&nbsp;</td></tr>
			</tbody></table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><b>REMARKS</b></td></tr>
		<tr><td><?=($array["remarks"])?ucwords($array["remarks"]):"&nbsp; -"?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<table border="0" cellspacing="1" cellpadding="1">
				<tr><td><b>STATUS</b></td>
					<td><b>&nbsp;:&nbsp;</td>
					<td><?=($array["suggestion"])?strtoupper($array["suggestion"]):"&nbsp; -"?></td>
				</tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td><b>DATE</b></td>
					<td><b>&nbsp;:&nbsp;</td>
					<td><?=($array["eval"] != "0000-00-00")?cplday('d F Y',$array["eval"]):"&nbsp; -"?></td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>

				<tr><td><b>EVALUATED BY</b></td>
					<td><b>&nbsp;:&nbsp;</td>
					<td><?=($array["fullname"])?ucwords($array["fullname"]):"&nbsp; -"?></td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
			</table>
		</td></tr>
		<tr><td><?=back_button()?></td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>