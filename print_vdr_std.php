<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();		

$page_title 	= "Vendor Standard Evaluation";
$page_id_left 	= "10";
$page_id_right 	= "24";
$category_page 	= "archive";
chkSecurity($page_id_right);

$eval_id = $_GET['id']; 
$query = "SELECT ev.id, 
				 ev.date, 
				 v.name, 
				 p.po_nbr, 
				 ev.avg, 
				 ev.remarks, 
				 CONCAT(e.fname,' ',e.lname) AS evname, 
				 CONCAT(a.fname,' ',a.lname) AS ackname,
				 ev.period
		  FROM ev_std ev LEFT JOIN vdr v ON (v.id = ev.vdr_id_fk)	
				LEFT JOIN po p ON (p.id = ev.po_nbr)
				LEFT JOIN user e ON (e.id = ev.user_id_fk)	
				LEFT JOIN user a ON (a.id = ev.ack_id_fk) 
		  WHERE ev.id = '$eval_id' AND ev.del = 0;";	

$SQL 	= @mysql_query($query) or die(mysql_error());
$array 	= mysql_fetch_array($SQL,MYSQL_ASSOC);

$select_eval_pr_query = "SELECT ec.name, ev_det.score 
						 FROM ev_std_det ev_det 
						 	LEFT JOIN ev_std ev ON (ev.id = ev_det.ev_id_fk) 
						 	LEFT JOIN ev_crit ec ON (ec.id = ev_det.crit_id_fk) 
						WHERE ev.id = '$eval_id' ";	

$select_eval_pr_SQL = @mysql_query($select_eval_pr_query) or die(mysql_error());

log_hist("94",$eval_id);

include THEME_DEFAULT.'print_header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
        <tr><td>&nbsp;</td></tr>
        <tr><td>
		<table border="0">
			<tr><td align="left"><b>ID</b></td><td>:</td><td><?=($array["id"])?"#".trim($array["id"]):"&nbsp; -";?></td></tr>
			<tr><td align="left"><b>DATE</b></td><td>:</td><td><?=($array["date"])?cplday('d F Y',$array["date"]):"&nbsp; -";?></td></tr>
			<tr><td align="left"><b>COMPANY NAME</b></td><td>:</td><td><?=($array["name"])?ucwords($array["name"]):"&nbsp; -";?></td></tr>
			<tr><td align="left"><b>P.O. NO.</b></td><td>:</td><td><?=($array["po_nbr"])?trim($array["po_nbr"]):"&nbsp; -";?></td></tr>
		</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<table border="0" cellpadding="1" cellspacing="1" class="table table-striped table-bordered table-condensed">
			<tr class="listview" >
				<td align="left"><b>&nbsp;CRITERIA&nbsp;</b></td>
				<td align="left"><b>&nbsp;SCORE&nbsp;</b></td></tr>
<?php
$count = 1;
 while($select_eval_pr_array = mysql_fetch_array($select_eval_pr_SQL)) {?>
			<tr>
				<td align="left">&nbsp;<?=($select_eval_pr_array[0])?ucwords($select_eval_pr_array[0]):"&nbsp; -";?>&nbsp;</td>
				<td align="center">&nbsp;<?=($select_eval_pr_array[1])?number_format($select_eval_pr_array[1],'2','.',''):"&nbsp; -";?>&nbsp;</td></tr>
<?php 
$count++;
} ?><tr class="listview" >
				<td align="center"><b>&nbsp;AVERAGE&nbsp;</b></td>
				<td align="center"><b>&nbsp;<?=($array["avg"])?number_format($array["avg"],'2','.',''):"&nbsp; -";?>&nbsp;</b></td></tr>
		</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><b>REMARKS</b><br /><?=($array["remarks"])?$array["remarks"]:"&nbsp; -";?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<table border="0">
				<tr><td><b>EVALUATE BY:</b><br /><br />
			    		<?=($array["evname"])?ucwords($array["evname"]):"&nbsp; -";?></td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
		    		<td><b>ACKNOWLEDGE BY:</b><br /><br />
			    		<?=($array["ackname"])?ucwords($array["ackname"]):"&nbsp; -";?></td></tr>
			</table>
		</td></tr>
		<tr><td >&nbsp;</td></tr>
	</table>

<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'print_footer.php';?>