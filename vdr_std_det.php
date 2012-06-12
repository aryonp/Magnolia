<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title 	= "Vendor Evaluation Details";
$page_id_left 	= "9";
$page_id_right 	= "18";
$category_page 	= "eval";
chkSecurity($page_id_right);

if(isset($_GET['id'])) { $eval_id = $_GET['id']; }

$this_page = $_SERVER['PHP_SELF']."?id=".$eval_id;

$status = "&nbsp;";

$select_eval_query 	= "SELECT ev.id, 
							  ev.date, 
							  v.name, 
							  p.po_nbr as po, 
							  ev.avg, 
							  ev.remarks, 
							  CONCAT(e.fname,' ',e.lname) AS evname, 
							  CONCAT(a.fname,' ',a.lname) AS ackname,
							  ev.ack_id_fk AS evid,
							  ev.ack,
							  ev.period 
					   FROM ev_std ev 
					 		LEFT JOIN vdr v ON (v.id = ev.vdr_id_fk) 
					 		LEFT JOIN user e ON (e.id = ev.user_id_fk) 
					 		LEFT JOIN user a ON (a.id = ev.ack_id_fk) 
					 		LEFT JOIN po p ON (p.id = ev.po_nbr) 
					   WHERE ev.id = '$eval_id' AND ev.del=0 ";	

$select_eval_SQL 	= @mysql_query($select_eval_query) or die(mysql_error());
$ev_array 			= mysql_fetch_array($select_eval_SQL,MYSQL_ASSOC);

$select_eval_pr_query = "SELECT cr.name, 
								ed.score 
						 FROM ev_std_det ed 
						 		LEFT JOIN ev_std ev ON (ev.id = ed.ev_id_fk) 
						 		LEFT JOIN ev_crit cr ON (cr.id = ed.crit_id_fk) 
						 WHERE ev.id = '$eval_id' AND ev.del = 0;";	

$select_eval_pr_SQL = @mysql_query($select_eval_pr_query) or die(mysql_error());

if (isset($_POST['ack_ev'])){
	$ev_q = "UPDATE ev_std 
	         SET ack_id_fk = '".$_SESSION['uid']."', ackdate = '".date('Y-m-d H:i:s')."', ack ='1'
	         WHERE id = '$eval_id';";
	@mysql_query($ev_q) or die(mysql_error());
	log_hist(139,$eval_id);
	header("location:$this_page");
	exit();
}

$button = array("ack_ev"=>array("submit"=>"  ACKNOWLEDGE EVAL.  "));

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>VENDOR EVALUATION DETAILS</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td >&nbsp;</td></tr>
		<?=back_button()?>
		<tr><td>&nbsp;</td></tr>
		<?=(!empty($ev_array["evid"]) OR $ev_array["ack"] != 0)?"":"<tr><td>".genButton($button)."</td></tr><tr><td>&nbsp;</td></tr>";?>
        <tr><td>
		<table border="0">
			<tr><td align="left"><b>ID</b></td><td>:</td><td><?=($ev_array["id"])?"#".trim($ev_array["id"]):"&nbsp; -";?></td></tr>
			<tr><td align="left"><b>DATE</b></td><td>:</td><td><?=($ev_array["date"])?cplday('d F Y',$ev_array["date"]):"&nbsp; -";?></td></tr>
			<tr><td align="left"><b>COMPANY NAME</b></td><td>:</td><td><?=($ev_array["name"])?ucwords($ev_array["name"]):"&nbsp; -";?></td></tr>
			<tr><td align="left"><b>P.O. NO.</b></td><td>:</td><td><?=($ev_array["po"])?trim($ev_array["po"]):"&nbsp; -";?></td></tr>
		</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            <tr valign="middle"> 
				<td align="left"><b>&nbsp;CRITERIA&nbsp;</b></td>
				<td align="center"><b>&nbsp;SCORE&nbsp;</b></td>
			</tr>
			</thead>
			<tbody>
<?php
$count = 1;
 while($select_eval_pr_array = mysql_fetch_array($select_eval_pr_SQL)) {?>
			<tr>
				<td>&nbsp;<?=($select_eval_pr_array[0])?ucwords($select_eval_pr_array[0]):"&nbsp; -";?>&nbsp;</td>
				<td align="center">&nbsp;<?=($select_eval_pr_array[1])?number_format($select_eval_pr_array[1],'2','.',''):"&nbsp; -";?>&nbsp;</td></tr>
<?php 
$count++;
} ?><tr class="listview" >
				<td align="center"><b>&nbsp;AVERAGE&nbsp;</b></td>
				<td align="center"><b>&nbsp;<?=($ev_array["avg"])?number_format($ev_array["avg"],'2','.',''):"&nbsp; -";?>&nbsp;</b></td></tr>
			</tbody>
		</table>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><b>REMARKS</b><br /><?=($ev_array["remarks"])?$ev_array["remarks"]:"&nbsp; -";?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<table border="0">
		<tr><td><b>EVALUATE BY:</b><br /><br />
			    <?=($ev_array["evname"])?ucwords($ev_array["evname"]):"&nbsp; -";?></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
		    <td><b>ACKNOWLEDGE BY:</b><br /><br />
			    <?=($ev_array["ackname"])?ucwords($ev_array["ackname"]):"&nbsp; -";?></td></tr>
		</table>
		</td></tr>
		<tr><td >&nbsp;</td></tr>
		<?=(!empty($ev_array["evid"]) OR $ev_array["ack"] != 0)?"":"<tr><td>".genButton($button)."</td></tr><tr><td>&nbsp;</td></tr>";?>
		<?=back_button()?>
		<tr><td>&nbsp;</td></tr>
	</table></form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>