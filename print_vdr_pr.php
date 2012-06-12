<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title = "Vendor Period Evaluation";
$page_id_left ="10";
$page_id_right = "25";
$category_page = "archive";
chkSecurity($page_id_right);

$eval_pr_id = $_GET['id'];

$select_ev_pr_q = "SELECT ep.id, ep.start, ep.end, v.name as vname, ep.total, ep.avg, ep.remarks, ep.suggestion, ep.eval, CONCAT(u.fname,' ',u.lname) AS fullname ".
					"FROM ev_pr ep LEFT JOIN vdr v ON (v.id = ep.vdr_id_fk) ".
					"LEFT JOIN user u ON (u.id = ep.user_id_fk) ".
					"WHERE ep.id = '$eval_pr_id' ";
$select_ev_pr_SQL = @mysql_query($select_ev_pr_q) or die(mysql_error());
$array = mysql_fetch_array($select_ev_pr_SQL);

$select_ev_pr_dt_q = "SELECT ev.id, p.po_nbr, ev.avg ".
						"FROM ev_pr_det ep_det LEFT JOIN ev_std ev ON (ev.id = ep_det.ev_id_fk) ".
						"LEFT JOIN po p ON (p.id = ev.po_nbr) ".
						"WHERE ep_det.ev_pr_id_fk = '$eval_pr_id'; ";
$select_ev_pr_dt_SQL = @mysql_query($select_ev_pr_dt_q) or die(mysql_error());

log_hist("97",$eval_pr_id);
include THEME_DEFAULT.'print_header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
        <tr><td>
        <table border="0">
        	<tr><td align="left"><b>ID</b></td>
        		<td>:</td>
        		<td colspan="3"><?=($array["id"])?"#".$array["id"]:"&nbsp; -"?></td></tr>
			<tr><td align="left"><b>EVALUATION PERIOD</b></td>
				<td>:</td>
				<td><?=($array["start"] != "0000-00-00")?cplday('d F Y',$array["start"]):"&nbsp; -"?></td>
				<td>&nbsp;-&nbsp;</td>
				<td><?=($array["end"] != "0000-00-00")?cplday('d F Y',$array["end"]):"&nbsp; -"?></td>
				</tr>
			<tr><td align="left"><b>COMPANY NAME</b></td>
				<td>:</td>
				<td colspan="3"><?=($array["vname"])?ucwords($array["vname"]):"&nbsp; -"?></td>
				</tr>
		</table>
        </td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<table border="0" cellspacing="1" cellpadding="1" width="300" class="table table-striped table-bordered table-condensed">
				<tr class="listview">
					<td align="left">&nbsp;<b>EVAL. ID</b>&nbsp;</td>
					<td align="left">&nbsp;<b>P.O.</b>&nbsp;</td>
					<td align="center">&nbsp;<b>GRADE</b>&nbsp;</td></tr>
<?php 
	$count = 1;
	while($select_ev_pr_dt_array = mysql_fetch_array($select_ev_pr_dt_SQL)) {?>
				<tr>
					<td align="left">&nbsp;#<?=$select_ev_pr_dt_array[0]?>&nbsp;</td>
					<td align="left">&nbsp;<?=$select_ev_pr_dt_array[1]?>&nbsp;</td>
					<td align="center">&nbsp;<?=number_format($select_ev_pr_dt_array[2],'2','.','')?>&nbsp;</td>
<?php $count++;
		} ?>
				<tr class="listview">
					<td align="center" colspan="2">&nbsp;<b>TOTAL</b>&nbsp;</td>
					<td align="center">&nbsp;<b><?=($array["total"])?number_format($array["total"],'2','.',''):"&nbsp; -"?></b>&nbsp;</td></tr>
				<tr class="listview">
					<td align="center" colspan="2">&nbsp;<b>AVERAGE</b>&nbsp;</td>
					<td align="center">&nbsp;<b><?=($array["avg"])?number_format($array["avg"],'2','.',''):"&nbsp; -"?></b>&nbsp;</td></tr>
			</table>
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
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'print_footer.php';?>