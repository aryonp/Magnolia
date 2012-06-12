<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Printer Transaction";
$page_id_left 	= "11";
$page_id_right 	= "47";
$category_page 	= "inventory";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);


$trs_id 	= ((isset ($_GET['id']) && $_GET['id'] != '')?trim($_GET['id']):'');
$this_page 	= $_SERVER['PHP_SELF']."?id=".$trs_id;

$trs_q = "SELECT pt.id, pt.tgl, pt.tonerID, p.pName, pt.branchID, pt.qty, pt.pType, pt.location, CONCAT(u.fname,' ',u.lname) as updater
		  FROM printer_trs pt LEFT JOIN toner t ON (t.id = pt.tonerID) 
		  					  LEFT JOIN printer p ON (p.id = t.printerID) 
		  					  LEFT JOIN user u ON (u.id = pt.updBy) 
		  					  LEFT JOIN branch b ON (b.id = pt.branchID) 
		  WHERE pt.del = '0' AND pt.id = '$trs_id' 
		  ORDER BY pt.tgl DESC ";
$trs_SQL 	= @mysql_query($trs_q) or die(mysql_error());
$array 		= mysql_fetch_array($trs_SQL,MYSQL_ASSOC);

$status 	= "&nbsp;";
$toner_q 	= "SELECT t.id, t.tName FROM toner t WHERE t.del = '0' ORDER BY t.tName ASC;";
$toner_SQL 	= @mysql_query($toner_q) or die(mysql_error());

$branch_list_q 		= "SELECT b.id, b.name FROM branch b WHERE b.del='0' ORDER BY b.name ASC ";
$branch_list_SQL 	= @mysql_query($branch_list_q) or die(mysql_error());

$pTypes 	= array("usage","stock");
$pLocations = array("5th flr. Fax room","6th flr. Fax room","7th flr. Fax room","IT division","HR division","MG division","GA division","SPT division","OEX division","OIM division","Credit Control division","FA division","SLS division","Other division");

if(isset($_POST['updTrs'])){
 	$tgl 	= trim($_POST['pr_trs']);
	$tID 	= (int)trim($_POST['toner']);
	$bID 	= trim($_POST['branch']);
	$qty 	= (int)trim($_POST['qty']);
	$pLoc 	= trim($_POST['pLoc']);
	$inpID 	= $_SESSION['uid'];
	$inpDate = date('Y-m-d H:i:s');
	
   	if($tID == "-" AND $bID == "-" AND $pLoc == "-" AND $tType == "-"){
   		$status ="<p class=\"yellowbox\">Missing required information.</p>";
   	}
   	
	else {
		$upd_trs_q  = "UPDATE printer_trs SET tgl = '$tgl',tonerID = '$tID', qty = '$qty', location = '$pLoc', branchID = '$bID', updBy = '$inpID',updDate = '$inpDate' WHERE id = '$trs_id' ";
		@mysql_query($upd_trs_q) or die(mysql_error());
		log_hist("124",$trs_id);
		header("location:$this_page");
		exit();
	}
	
}	

include THEME_DEFAULT.'header.php'; ?>             			
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><?=$status?></td></tr>
	<tr><td>[&nbsp;<a href="./csm_pr.php">BACK TO THE TRANSACTION PAGE</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
        <label><b>UPDATE TRANSACTION</b></label>
		<div class="well">
	<table border="0" cellpadding="1" cellspacing="1" >
		<tr valign=top>
			<td align="right"><b>DATE</b></td>
			<td>:</td>
			<td><input type="text" name="pr_trs" id="pr_trs" size="10" maxlength="10" value="<?=($array["tgl"]!="0000-00-00")?$array["tgl"]:"-"?>">&nbsp;
						<a href="javascript:NewCal('pr_trs','yyyymmdd')">
						<img src="<?=IMG_PATH?>cal.gif" border="0" /></a></td>
			<td>&nbsp;</td>
			<td align="right"><b>TYPE</b></td>
			<td>:</td>
			<td><?=($array["pType"])?strtoupper($array["pType"]):"-"?></td></tr>
		<tr valign=top>
			<td align="right"><b>TONER</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><select name="toner">
    				<option value="-">---------------------</option>
<?php 
  	while($toner_array = mysql_fetch_array($toner_SQL,MYSQL_ASSOC)){
 		$compare_toner = ($toner_array["id"] == $array["tonerID"])?"SELECTED":"";?>
    <option value="<?=$toner_array["id"]?>" <?=$compare_toner?>><?=ucwords($toner_array["tName"])?></option>
<?php } ?>
 				 </select>
 			</td>
			<td>&nbsp;</td>
			<td align="right"><b>QTY.</b></td>
			<td>:</td>
			<td><input type="text" maxlength="3" size="5" name="qty" value="<?=(is_numeric($array["qty"]) AND !empty($array["qty"]))?$array["qty"]:"-"?>"></td></tr>
		<tr valign=top>
			<td align="right"><b>BRANCH</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><select name="branch">
    				<option value="-">---------------------</option>
<?php 
  	while($branch_list_array = mysql_fetch_array($branch_list_SQL,MYSQL_ASSOC)){
 		$compare_branch = ($branch_list_array["id"] == $array["branchID"])?"SELECTED":"";?>
    <option value="<?=$branch_list_array["id"]?>" <?=$compare_branch?>><?=ucwords($branch_list_array["name"])?></option>
<?php } ?>
 				 </select>
			</td>
			<td>&nbsp;</td>
			<td align="right"><b>LOCATION</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><select name="pLoc">
				<option value="-">--------</option>
<?php foreach($pLocations as $Loc) {
		$compare_loc = ($Loc == $array["location"])?"SELECTED":"";?>
				<option value ="<?=$Loc?>" <?=$compare_loc?>><?=strtoupper($Loc);?></option>
<?php	} ?>
 			    </select>
 			</td></tr>
	</table></div>
	</td></tr>
	<tr><td><input type="submit" name="updTrs" class="btn-info btn-small" value="  UPDATE TRANSACTION  "></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>[&nbsp;<a href="./csm_pr.php">BACK TO THE TRANSACTION PAGE</a>&nbsp;]</td></tr>
</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>