<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title = "Vendor Period Evaluation Page";
$page_id_left ="9";
$page_id_right = "19";
$category_page = "eval";
chkSecurity($page_id_right);

$vdr_eval_pr_list_query ="SELECT vp.id, CONCAT(u.fname,' ',u.lname) AS ename, v.name, vp.start, vp.end, vp.avg, vp.suggestion, vp.eval, vp.user_id_fk as user ".
							"FROM ev_pr vp 
							LEFT JOIN user u ON (u.id = vp.user_id_fk) 
							LEFT JOIN vdr v ON (v.id = vp.vdr_id_fk) 
							WHERE vp.del = '0' 
							ORDER BY vp.id DESC,vp.eval DESC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($vdr_eval_pr_list_query);
$pagingResult->paginate();

$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$chk_del_stat_q = "SELECT vp.user_id_fk as userid FROM ev_pr vp WHERE vp.id ='$did';";
	$chk_del_stat_SQL = @mysql_query($chk_del_stat_q) or die(mysql_error());
	$chk_del_stat_array = mysql_fetch_array($chk_del_stat_SQL, MYSQL_ASSOC);
	if ($chk_del_stat_array["userid"] == $_SESSION['uid']) {
		$select_period_query = "SELECT ev_id_fk as id FROM ev_pr_det WHERE ev_pr_id_fk = '$did' ";
		$select_period_array = @mysql_query($select_period_query) or die(mysql_error());
		while($vdr_ev_id_fk = mysql_fetch_array($select_period_array, MYSQL_ASSOC)) {
			$update_period_query = "UPDATE ev_std SET period ='0' WHERE id = '".$vdr_ev_id_fk["id"]."';";
			mysql_query($update_period_query) or die(mysql_error());
		}
		$delete_eval_query  ="UPDATE ev_pr SET del = '1' WHERE id ='$did';";
		@mysql_query($delete_eval_query) or die(mysql_error());
		log_hist("98",$did);
		header("location:$this_page");
		exit();
	} else {
		$status ="<p class=\"redbox\">You don't have privilege to delete the periodic evaluation data </p>";
	}
}
include THEME_DEFAULT.'header.php';?>               			
<//-----------------CONTENT-START-------------------------------------------------//>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>VENDOR PERIODIC EVALUATION LIST</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>[<a href = "./vdr_pr.php">CREATE NEW PERIODIC EVAL</a>]</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
        <tr><td>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
				<tr valign="middle"> 
                 	<th width="25" align="left">&nbsp;<b>NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>EVAL BY</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>VENDOR</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>PERIOD</b>&nbsp;</td>
                 	<th width="*" align="right">&nbsp;<b>SCORE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>SUGGESTION</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>EVAL DATE</b>&nbsp;</td>
                 	<th colspan="3" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
				<tr align="left" valign="top">
					<td align="left">&nbsp;<?=$count?>.&nbsp;</td>
					<td align="left">&nbsp;<?=($array["id"])?"#".ucwords($array["id"]):"-"?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["ename"])?ucwords($array["ename"]):"&nbsp;-"?></td>
					<td align="left">&nbsp;<?=($array["name"])?ucwords($array["name"]):"-"?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["start"])?cplday('d M Y',$array["start"]):"-"?>&nbsp;-
						  		     &nbsp;<?=($array["end"])?cplday('d M Y',$array["end"]):"&nbsp;-"?>&nbsp;</td>
					<td align="right"><?=($array["avg"])?number_format($array["avg"],'2','.','')."&nbsp;":"-"?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["suggestion"])?ucwords($array["suggestion"]):"-"?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["eval"])?cplday('d M Y',$array["eval"]):"-"?>&nbsp;</td>
					<td align="center" width="25"><a title="View Details" href="./vdr_pr_det.php?id=<?=$array["id"]?>"><img src="<?=IMG_PATH?>view.png"></a>&nbsp;</td>
					<td align="center" width="25"><a title="Print" href="javascript:openW('./print_vdr_pr.php?id=<?=$array["id"]?>','Print_Period_Evaluation',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png"></a></td>
					<td align="center" width="25">
<?php if ($array["user"] == $_SESSION['uid']) { ?>
					<a title="Delete" href="<?=$this_page?>" onclick="return confirmBox(this,'del', 'periodic evaluation ID #<?=$array["id"]?>', '<?=$array["id"]?>')"><img src="<?=IMG_PATH?>delete.png"></a>
<?php } else { ?><img src="<?=IMG_PATH?>d_delete.png"><?php } ?>
					</td>
				</tr>
			<?php	$count++;  
				}
			} else {?>
				<tr><td colspan="10" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?}
			?></tbody>
			</table></td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[<a href = "./vdr_pr.php">CREATE NEW PERIODIC EVAL</a>]</td></tr>
        <tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>