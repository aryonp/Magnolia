<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Consumable (Printer)";
$page_id_left 	= "11";
$page_id_right	= "47";
$category_page 	= "inventory";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$filter = (isset($_GET["f"]) AND !empty($_GET["f"]))?trim($_GET["f"]):"a";

switch($filter) {
	case 'a':
		$where = "WHERE pt.del = '0' ORDER BY pt.id DESC";
		break;
	case 's':
		$where = "WHERE pt.del = '0' AND pt.pType = 'stock' ORDER BY pt.id DESC";
		break;
	case 'u':
		$where = "WHERE pt.del = '0' AND pt.pType = 'usage' ORDER BY pt.id DESC";
		break;
	default:
		$where = "WHERE pt.del = '0' ORDER BY pt.id DESC";
		break;	
}

$trs_q = "SELECT pt.id, pt.tgl, t.tName, pt.qty, pt.pType, pt.location, CONCAT(u.fname,' ',u.lname) as updater 
		  FROM printer_trs pt 
			LEFT JOIN toner t ON (t.id = pt.tonerID) 
			LEFT JOIN user u ON (u.id = pt.updBy) 
		  $where ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($trs_q);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status 	= "&nbsp;";
$toner_q 	= "SELECT t.id, t.tName FROM toner t WHERE t.del = '0' ORDER BY t.tName ASC;";
$toner_SQL 	= @mysql_query($toner_q) or die(mysql_error());

$branch_list_q 		= "SELECT b.id, b.name FROM branch b WHERE b.del='0' ORDER BY b.name ASC ";
$branch_list_SQL 	= @mysql_query($branch_list_q) or die(mysql_error());

$pTypes = array("usage","stock");

$pLocations = array("5th flr. Fax room","IT division","HR division","MG division","GA division","SPT division","OEX division","OIM division","Credit Control division","FA division","SLS division","Other division");




if(isset($_POST['add_trans'])){
 	$tgl = trim($_POST['pr_trs']);
	$tID = (int)trim($_POST['toner']);
	$bID = trim($_POST['branch']);
	$qty = (int)trim($_POST['qty']);
	$tType = trim($_POST['pType']);
	$pLoc = trim($_POST['pLocations']);
	$inpID = $_SESSION['uid'];
	$inpDate = date('Y-m-d H:i:s');
	
   	if($bID == "-" AND $pLoc == "-" AND $tType == "-"){
   		$status ="<p class=\"yellowbox\">Missing required information.</p>";
   	}
   	
	else {
		
	if($tType == "usage") {
			$add_trs_q  = "INSERT INTO printer_trs (tgl,tonerID,branchID,qty,pType,location,usg,inputBy,inputDate,updBy,updDate) VALUES ('$tgl','$tID','$bID','$qty','usage','$pLoc','$qty','$inpID','$inpDate','$inpID','$inpDate');";
		
		} 
	elseif ($tType == "stock") {
			$add_trs_q  = "INSERT INTO printer_trs (tgl,tonerID,branchID,qty,pType,location,stc,inputBy,inputDate,updBy,updDate) VALUES ('$tgl','$tID','$bID','$qty','stock','$pLoc','$qty','$inpID','$inpDate','$inpID','$inpDate');";
		
		}
		@mysql_query($add_trs_q) or die(mysql_error());
		$trsID = mysql_insert_id();
		log_hist("123",$trsID);
		header("location:$this_page");
		exit();
	}
}	

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$updBy = $_SESSION['uid'];
	$updDate = date('Y-m-d H:i:s');
	$del_prtr_q  ="UPDATE printer_trs SET del = '1', updBy = '$updBy', updDate = '$updDate' WHERE id ='$did';";
	@mysql_query($del_prtr_q) or die(mysql_error());
	log_hist("125",$did);
	header("location:$this_page");
	exit();
}

include THEME_DEFAULT.'header.php'; ?>             			
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><?=$status?></td></tr>
	<tr><td>
		<div class="well">
	<table border="0" cellpadding="1" cellspacing="1">
		<tr valign=top>
			<td align="right"><b>DATE</b></td>
			<td>:</td>
			<td><input type="text" name="pr_trs" id="pr_trs" size="10" maxlength="10" value="<?=date('Y-m-d')?>">&nbsp;
						<a href="javascript:NewCal('pr_trs','yyyymmdd')">
						<img src="<?=IMG_PATH?>cal.gif" border="0" /></a></td>
			<td>&nbsp;</td>
			<td align="right"><b>TYPE</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><select name="pType">
    				<option value="-" SELECTED>---------------------</option>
<?php foreach ($pTypes as $pType){
				echo "<option value=\"$pType\">".strtoupper($pType)."</option>\n";
} ?>
 				</select></td></tr>
		<tr valign=top>
			<td align="right"><b>TONER</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><select name="toner">
    				<option value="-" SELECTED>---------------------</option>
<?php while($toner_array = mysql_fetch_array($toner_SQL,MYSQL_ASSOC)){?>
    <option value="<?=$toner_array["id"]?>"><?=ucwords($toner_array["tName"])?></option>
<?php } ?></select></td>
			<td>&nbsp;</td>
			<td align="right"><b>QTY.</b></td>
			<td>:</td>
			<td><input type="text" maxlength="3" size="5" name="qty" value="1"></td></tr>
		<tr valign=top>
			<td align="right"><b>BRANCH</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><select name="branch">
    				<option value="-" SELECTED>---------------------</option>
<?php while($branch_list_array = mysql_fetch_array($branch_list_SQL,MYSQL_ASSOC)){?>
    <option value="<?=$branch_list_array["id"]?>"><?=ucwords($branch_list_array["name"])?></option>
<?php } ?>	</select>
			</td>
			<td>&nbsp;</td>
			<td align="right"><b>LOCATION</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><select name="pLocations">
    				<option value="-" SELECTED>---------------------</option>
<?php foreach ($pLocations as $pLocation){
				echo "<option value=\"$pLocation\">".strtoupper($pLocation)."</option>\n";
} ?>
 				</select></td></tr>
			</table>
	</div></td></tr>
	<tr><td><input type="submit" name="add_trans" class="btn-info btn-small" value="  ADD TRANSACTION  "></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>FILTER BY : <a href="<?=$this_page?>&f=a">ALL</a> | <a href="<?=$this_page?>&f=s">STOCK</a> | <a href="<?=$this_page?>&f=u">USAGE</a></td></tr>
	<tr><td>
        	<?=$pagingResult->pagingMenu();?>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr><th width="25">&nbsp;<b>NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
					<th width="*" align="left">&nbsp;<b>DATE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>TONER</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>QTY</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>TYPE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>LOCATION</b>&nbsp;</td>
                 	<th width="*" align="center" colspan="2">&nbsp;<b>CMD</b></td>
				</tr>
			</thead>
			<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$row_color = ($count % 2)?"odd":"even";   ?>
		<tr class="<?=$row_color?>" valign="top">
			<td align="left">&nbsp;<?=$count?>.&nbsp;</td>
			<td align="left">&nbsp;#<?=($array["id"])?strtoupper($array["id"]):"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=cplday('d M y',$array["tgl"]);?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["tName"])?strtoupper($array["tName"]):"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["qty"])?$array["qty"]:"0";?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["pType"])?strtoupper($array["pType"]):"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["location"])?ucwords($array["location"]):"-";?>&nbsp;</td>
			<td width="25" align="center" valign="middle"><a title="View Details" href="./csm_pr_det.php?id=<?=$array["id"]?>">
				<img src="<?=IMG_PATH?>d_edit.png"></a></td>
			<td width="25" align="center" valign="middle"><a title="Delete Transaction" href="<?=$this_page?>" onclick="return confirmBox(this, 'del','printer transaction ID # <?=$array["id"]?>','<?=$array["id"]?>')">
				<img src="<?=IMG_PATH?>delete.png"></a></td>
		</tr>

<?php	$count++;  
		}
	} else {?>
		<tr><td colspan="10" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php	}
			?>
			</tbody>
			</table>
				<?=$pagingResult->pagingMenu();?>
			</td></tr>
			<tr><td>FILTER BY : <a href="<?=$this_page?>&f=a">ALL</a> | <a href="<?=$this_page?>&f=s">STOCK</a> | <a href="<?=$this_page?>&f=u">USAGE</a></td></tr>
</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>