<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();	
	
$page_title 	= "Vendor Evaluation Page";
$page_id_left 	= "9";
$page_id_right 	= "18";
$category_page 	= "eval";
chkSecurity($page_id_right);

$vdr_eval_list_query ="SELECT vs.id, 
							  CONCAT(u.fname,' ',u.lname) AS ename, 
							  CONCAT(a.fname,' ',a.lname) AS aname, 
							  v.name as vname, 
							  p.po_nbr, 
							  vs.avg as score, 
							  vs.date as edate, 
							  vs.ackdate as adate,
							  vs.period as period, 
							  vs.user_id_fk as user,
							  vs.ack AS ack 
						FROM ev_std vs 
								LEFT JOIN user u ON (u.id = vs.user_id_fk) 
								LEFT JOIN user a ON (a.id = vs.ack_id_fk) 
								LEFT JOIN po p ON (p.id = vs.po_nbr) 
								LEFT JOIN vdr v ON (v.id = vs.vdr_id_fk) 
						WHERE vs.del = '0' ORDER BY vs.date DESC ";

$pagingResult = new Pagination();
$pagingResult->setPageQuery($vdr_eval_list_query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "";

if (isset($_GET['did'])) {
	
	$did 				= trim($_GET['did']);
	$chk_del_stat_q 	= "SELECT vs.user_id_fk as user, vs.period FROM ev_std vs WHERE vs.id ='$did';";
	$chk_del_stat_SQL 	= @mysql_query($chk_del_stat_q) or die(mysql_error());
	$chk_del_stat_array = mysql_fetch_array($chk_del_stat_SQL, MYSQL_ASSOC);
	
	if ($chk_del_stat_array["user"] == $_SESSION['uid'] AND $chk_del_stat_array["period"] == "0") {
		$delete_eval_query  ="UPDATE ev_std SET del = '1' WHERE id ='$did';";
		@mysql_query($delete_eval_query) or die(mysql_error());
		log_hist("95",$did);
		header("location:$this_page");
		exit();
	} 
	
	else { $status ="<p class=\"redbox\">You don't have privilege to delete the evaluation data or the data status doesn't allowed to be deleted</p>";}
}

include THEME_DEFAULT.'header.php';?>         			
<//-----------------CONTENT-START-------------------------------------------------//>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>VENDOR EVALUATION LIST</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[<a href = "./vdr_std.php">CREATE NEW STD. EVALUATION</a>]</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
        <tr><td>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
				<tr valign="middle"> 
                 	<th width="25" align="left">&nbsp;<b>NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>EVAL. BY</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>ACK. BY</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>VENDOR</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>P.O.</b>&nbsp;</td>
                 	<th width="*" align="right">&nbsp;<b>SCORE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>EVAL. DATE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>ACK. DATE</b>&nbsp;</td>
                 	<th colspan="3" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
				<tr valign="top">
					<td align="left">&nbsp;<?=$count?>.</td>
					<td align="left">&nbsp;<?=($array["id"])?"#".ucwords($array["id"]):"&nbsp;-"?></td>
					<td align="left">&nbsp;<?=($array["ename"])?ucwords($array["ename"]):"&nbsp;-"?></td>
					<td align="left">&nbsp;<?=($array["aname"])?ucwords($array["aname"]):"&nbsp;-"?></td>
					<td align="left">&nbsp;<?=($array["vname"])?ucwords($array["vname"]):"&nbsp;-"?></td>
					<td align="left">&nbsp;<?=($array["po_nbr"])?$array["po_nbr"]:"&nbsp;-"?></td>
					<td align="right">&nbsp;<?=($array["score"])?number_format($array["score"],'2','.','')."&nbsp;":"&nbsp;-"?></td>
					<td align="left">&nbsp;<?=($array["edate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["edate"]):"-"?></td>
					<td align="left">&nbsp;<?=($array["adate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$array["adate"]):"-"?></td>
					<td align="center" width="25"><a title="View Details" href="./vdr_std_det.php?id=<?=$array["id"]?>"><img src="<?=IMG_PATH?>view.png"></a></td>
					<td align="center" width="25"><a title="Print" href="javascript:openW('./print_vdr_std.php?id=<?=$array["id"]?>','Print_STD_Evaluation',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png"></a></td>
					<td align="center" width="25">
<?php if($array["period"] == 0 AND $array["ack"] == 0) { ?>
						<a title="Delete" href="<?=$this_page?>" onclick="return confirmBox(this,'del', 'evaluation ID #<?=$array["id"]?>', '<?=$array["id"]?>')"><img src="<?=IMG_PATH?>delete.png"></a> 
<?php } else { ?><img src="<?=IMG_PATH?>d_delete.png"><?php } ?>
					</td>
				</tr>
<?php	$count++;  
				}
			} else {?>
				<tr><td colspan="10" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 		}	?></tbody>
			</table></td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[<a href = "./vdr_std.php">CREATE NEW STD. EVALUATION</a>]</td></tr>
        <tr><td>&nbsp;</td></tr>
	</table>

<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>