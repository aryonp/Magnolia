<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'notif.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title 	= "IT Approval";
$page_id_left 	= "7";
$page_id_right 	= "";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$filter = (isset($_GET["f"]) AND !empty($_GET["f"]))?trim($_GET["f"]):"a";

$search = ($_GET['search']!="")?mysql_real_escape_string(trim($_GET['search'])):"";
$param 	= ($_GET['param']!="")?trim($_GET['param']):"";

switch($param){
	case "id":
		$fltr 	= "AND r.del = '0' AND r.id = '$search'";
		break;
	case "fno":
		$fltr 	= "AND r.del = '0' AND r.code LIKE '%$search%'";
		break;
	case "req":
		$fltr 	= "AND r.del = '0' AND CONCAT(u.fname,' ',u.lname) LIKE '%$search%'";
		break;
	default:
		$fltr 	= "AND r.del = '0'";
}

switch($filter) {
	case 'a':
		$where = "WHERE (r.status = 'authorized' OR r.status = 'pending') $fltr ORDER BY r.id DESC";
		break;
	case 'p':
		$where = "WHERE r.status = 'pending' $fltr ORDER BY r.id DESC";
		break;
	case 'au':
		$where = "WHERE r.status = 'authorized' $fltr ORDER BY r.id DESC";
		break;
	default:
		$where = "WHERE (r.status = 'authorized' OR r.status = 'pending') $fltr ORDER BY r.id DESC";
		break;	
}

$query = "SELECT r.id, 
                 r.code, 
                 CONCAT(u.fname,' ',u.lname) AS fullname, 
				 r.req_type, 
				 r.req_date as rdate, 
                 r.auth_date as l2date, 
                 r.appr_date as adate, 
                 r.status 
		  FROM req r 
		  LEFT JOIN user u ON (u.id = r.user_id_fk) 
          $where ";
          
$pagingResult = new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();

$req_stat_q 	= "SELECT DISTINCT(r.status) FROM req r ";
$req_stat_SQL 	= @mysql_query($req_stat_q) or die(mysql_error());
$this_page 		= $_SERVER['PHP_SELF']."?f=".$filter."&".$pagingResult->getPageQString();
$status 		= "&nbsp;";

function it_appr($id,$msg,$lcode) {
	if(!empty($id)) {
		foreach ($id as $val) {
			$upd_appr_q = "UPDATE req 
						   SET status = '".strtolower($msg)."', appr_id_fk = '".$_SESSION['uid']."', appr_date = '".date('Y-m-d H:i:s')."' 
						   WHERE req.id = '$val';";
			@mysql_query($upd_appr_q) or die(mysql_error());
	
			$upd_det_appr_q = "UPDATE req_det 
			                   SET status = '".strtolower($msg)."' 
							   WHERE req_det.req_id_fk = '$val';";
			@mysql_query($upd_det_appr_q) or die(mysql_error());
	
			notify_it_appr($val,$msg);
			notify_adm_appr($val,$msg);
			log_hist($lcode,$val);
		}
		header("location:$this_page");
	}
	else {
		$status="<p class=\"yellowbox\">Missing required information ! Please tick your selection </p>";
	}
}

if(isset($_POST['it_appr_all'])) {
	$id = $_POST['req_id'];
	it_appr($id,"ADM-Authorized",76);
}

elseif(isset($_POST['it_cancel_all'])) {
	$id = $_POST['req_id'];
	it_appr($id,"ADM-Rejected",77);
}

elseif(isset($_POST['it_stock'])) {
	$id = $_POST['req_id'];
	it_appr($id,"ADM-Authorized (STOCK)",78);
	
}

$button = array("it_appr_all"=>array("submit"=>"  ADM APPROVE  "),
			    "it_cancel_all"=>array("submit"=>"  ADM REJECT  "),
			    "it_stock"=>array("submit"=>"  ADM STOCK  "));
			  
include THEME_DEFAULT.'header.php';?>          			
<//-----------------CONTENT-START-------------------------------------------------//>

	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td align="center"><form method="GET" action="">SEARCH&nbsp;&nbsp;:&nbsp;&nbsp;
		<input type="text" name="search" size=50 value="<?=$search?>"/>&nbsp;&nbsp;
		BY:&nbsp;&nbsp;
		<select name="param" class="input-small">
			<option value="id">ID</option>
			<option value="fno">FILE NO.</option>
			<option value="req">REQUESTER</option>
		</select>
		<input type="submit" name="sbutton" value="  GO  " class="btn-small btn-info"/>
		</form></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>FILTER BY : <a href="<?=$this_page?>&f=a">ALL</a> | <a href="<?=$this_page?>&f=p">PENDING</a> | <a href="<?=$this_page?>&f=au">AUTHORIZED</a></td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
        <tr><td>
            <form method="POST" action="">
			<?=genButton($button)?>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
            	<tr> 
            		<th width="20">&nbsp;</td>
                 	<th width="25" align="left">&nbsp;<b>NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>FILE NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>REQUESTER</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>REQ. TYPE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>REQ. DATE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>L2 DATE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>IT APPR.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>STATUS</b>&nbsp;</td>
                 	<th colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
				</thead><tbody>
<?php if($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($data = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
				<tr align="left" valign="top">
					<td align="center"><input type="checkbox" name="req_id[]" value="<?=$data["id"]?>"></td>
					<td align="left">&nbsp;<?=$count?>.&nbsp;</td>
					<td align="left">&nbsp;#<?=$data["id"]?>&nbsp;</td>
					<td align="left">&nbsp;<?=$data["code"]?>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($data["fullname"])?>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($data["req_type"])?>&nbsp;</td>
					<td align="left">&nbsp;<?=($data["rdate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$data["rdate"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($data["l2date"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$data["l2date"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($data["adate"] != "0000-00-00 00:00:00")?cplday('D, d M Y',$data["adate"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=strtoupper($data["status"])?>&nbsp;</td>
					<td align="center" width="25"><a title="View" href="./it_appr_det.php?id=<?=$data["id"]?>"><img src="<?=IMG_PATH?>view.png"></a></td>
					<td align="center" width="25"><a title="Print" href="javascript:openW('./print_req.php?id=<?=$data["id"]?>','Print_Request',500,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png"></a></td>
					</td>
				</tr>
			<?php	$count++;  
				}
			} else {?>
				<tr><td colspan="12" align="center" bgcolor="#e5e5e5"><br />No Data to authorize<br /><br /></td></tr>
		
			<?php }	?>		
				</tbody>
			</table><?=genButton($button)?></form></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>FILTER BY : <a href="<?=$this_page?>&f=a">ALL</a> | <a href="<?=$this_page?>&f=p">PENDING</a> | <a href="<?=$this_page?>&f=au">AUTHORIZED</a></td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>

<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>