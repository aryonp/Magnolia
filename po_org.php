<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'notif.lib.php';
require_once CONT_PATH.'po.php';
chkSession();

$page_title		= "Organize PO";
$page_id_left 	= "8";
$category_page 	= "main";
chkSecurity($page_id_left);

$status	 	= "&nbsp;";
$y			= ((isset ($_GET['y']) && $_GET['y'] != '')?(int) trim($_GET['y']):date('Y'));
$m			= ((isset ($_GET['m']) && $_GET['m'] != '')?(int) trim($_GET['m']):date('m'));

$this_page 	= $_SERVER['PHP_SELF']."?y=$y&m=$m";

$po_det_q	= "SELECT pd.id, 
						  p.id AS pid,
						  p.po_nbr AS pn,
						  pd.description, 
						  pd.price, pd.qty, 
						  ic.id AS ccid, 
						  CONCAT(ic.code,' : ',ic.ba,' > ',ic.spc) AS cctr,
						  pd.itcat_id_fk AS itcat
				   FROM po_det pd 
				   LEFT JOIN inv_cctr ic ON (ic.id = pd.cctrID) 
				   LEFT JOIN po p ON (p.id = pd.po_id_fk) 
				   WHERE p.del = '0' AND YEAR(p.date) = '$y' AND MONTH(p.date) = '$m';";

$po_det_SQL		= @mysql_query($po_det_q) or die(mysql_error());

$po_cctr_q 		= "SELECT id, CONCAT(ic.code,' : ',ic.ba,' > ',ic.spc) AS cctr FROM inv_cctr ic WHERE ic.del = 0 ORDER BY ic.ba ASC;";

$po_cctr_SQL 	= @mysql_query($po_cctr_q) or die(mysql_error());

if(isset($_POST['update_po'])) {
	$po_id_fk 	= trim($_POST['po_id']);
	$po_det_id 	= $_POST['po_det_id'];
	
	$d_req_q = "DELETE FROM po_req WHERE po = '$po_id_fk';";
	@mysql_query($d_req_q) or die(mysql_error());
	
	$d_rfa_q = "DELETE FROM po_rfa WHERE po = '$po_id_fk';";
	@mysql_query($d_rfa_q) or die(mysql_error());
	
	foreach($po_det_id as $key_id => $value_id) {
		$po_desc 	= mysql_real_escape_string($_POST['desc'][$key_id]);
		$po_cctr	= $_POST['po_cctr'][$key_id];
		$po_itcat	= $_POST['po_itcat'][$key_id];
		$po_rfa		= $_POST['po_rfa'][$key_id];
		$po_req		= $_POST['po_req'][$key_id];
		$po_rcv		= $_POST['po_rcv'][$key_id];
		$po_rdate	= date('Y-m-d H:i:s');
		$upd_po_det_q = "UPDATE po_det SET rfa_id_fk = '$po_rfa', req_id_fk = '$po_req', description = '$po_desc', itcat_id_fk = '$po_itcat', cctrID = '$po_cctr', rcv = '$po_rcv', rcvdate = '$po_rcvdate' WHERE id = '$value_id';";
		@mysql_query($upd_po_det_q) or die(mysql_error());
		
		po_upd_req($po_id_fk,$value_id,$po_req);
		po_upd_rfa($po_id_fk,$value_id,$po_rfa);
	}
	
	header("location:$this_page");
	exit();	
} 

$button2 = array("update_po"=>array("submit"=>"  UPDATE PO  "));

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><?=$status?></td></tr>
	<tr><td>[&nbsp;<a href="./po_hm.php">BACK TO THE PO HOME</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		<form method="GET" action="" class="well">YEAR&nbsp;:&nbsp;<input type="text" class="input-small" name="y" value="<?=$y?>" size = 5 maxlength = 4>&nbsp;&nbsp;&nbsp;MONTH&nbsp;:&nbsp;<input type="text" class="input-small" name="m" value="<?=$m?>" size = 3 maxlength = 2>&nbsp;<input type="submit" value="  GO  " class="btn-info btn-small"/></form></td></tr>
	<tr><td><?=genButton($button2)?></td></tr>
	<tr><td><form method="POST" action="">
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
            	<tr valign="middle"> 
					<td>&nbsp;<b>NO</b>&nbsp;</td>
					<td>&nbsp;<b>PO ID | PO NBR</b>&nbsp;</td>
					<td>&nbsp;<b>DESCRIPTION</b>&nbsp;</td>
					<td>&nbsp;<b>COST CTR.</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php $count = 1;$i = 1; 
if(mysql_num_rows($po_det_SQL)>= 1) {
	while($po_det_info_array = mysql_fetch_array($po_det_SQL,MYSQL_ASSOC)) { ?>
						<tr valign="top" align="left">
							<td>&nbsp;<?=$count?>.&nbsp;
								<input type="hidden" name="po_det_id[<?=$i?>]" value="<?=$po_det_info_array["id"]?>"></td>
							<td align="center"><a href="./po_det.php?id=<?=$po_det_info_array["pid"];?>">ID #<?=$po_det_info_array["pid"];?> | PO: <?=$po_det_info_array["pn"];?></a><br/>(Click link above to view the PO)</td>
							<td>
							<?=po_itcat_select($i,$po_det_info_array["itcat"])?>&nbsp;&nbsp;<br/>
							<textarea cols="60" rows="5" name="desc[<?=$i?>]" wrap="virtual"><?=strip_tags(nl2br($po_det_info_array["description"]))?></textarea><br/>
							RECEIVED<br/>
							<?=po_rcv($po_det_info_array["id"],$i)?><br/>
							REQ ID <br/>
							<input type="text" name="po_req[<?=$i?>]" value="<?=po_list_req($po_det_info_array["id"]);?>" size=20/><br/>
							RFA ID<br/>
							<input type="text" name="po_rfa[<?=$i?>]" value="<?=po_list_rfa($po_det_info_array["id"]);?>" size=20/></td>
							<td><?=po_cctr_select($i,$po_det_info_array["ccid"]);?></td>
						</tr>
<?php  	$count++;$i++;
	} 
}
else { ?>
	<tr><td colspan="4" align="center" bgcolor="#e5e5e5"><br />NO DATA<br /><br /></td></tr>
<?php }	?>	</tbody>
</table></form>
	</td></tr>
	<tr><td><?=genButton($button2)?></td></tr> 
	<tr><td>&nbsp;</td></tr>
	<tr><td>[&nbsp;<a href="./po_hm.php">BACK TO THE PO HOME</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
</table>
<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>