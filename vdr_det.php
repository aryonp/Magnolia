<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title		= "Vendors List";
$page_id_left	= "13";
$page_id_right 	= "33";
$category_page 	= "strx";
chkSecurity($page_id_right);

$vendor_id 			= ((isset ($_GET['id']) && $_GET['id'] != '')?trim ($_GET['id']):'');
$this_page 			= $_SERVER['PHP_SELF']."?id=".$vendor_id;
$branch_list_query 	= "SELECT b.id, b.name FROM branch b WHERE b.del = '0' ORDER BY b.name ASC;";
$branch_list_SQL 	= @mysql_query($branch_list_query) or die(mysql_error());

if (isset($_POST['upd_vdr'])) {
	$name 		= strtoupper((isset ($_POST['name']) && $_POST['name'] != '')?trim($_POST['name']):'');
	$addr 		= ((isset ($_POST['addr']) && $_POST['addr'] != '')?trim($_POST['addr']):'');
	$phone 		= ((isset ($_POST['phone']) && $_POST['phone'] != '')?trim($_POST['phone']):'(000)-(0000000)');
	$fax 		= ((isset ($_POST['fax']) && $_POST['fax'] != '')?trim($_POST['fax']):'(000)-(0000000)');
	$pic 		= ((isset ($_POST['pic']) && $_POST['pic'] != '')?trim($_POST['pic']):'');
	$pctc 		= ((isset ($_POST['pctc']) && $_POST['pctc'] != '')?trim ($_POST['pctc']):'-');
	$pemail 	= ((isset ($_POST['pemail']) && $_POST['pemail'] != '')?trim($_POST['pemail']):'');
	$vsap 		= ((isset ($_POST['vsap']) && $_POST['vsap'] != '')?trim($_POST['vsap']):'');
	$branch 	= ((isset ($_POST['branch']) && $_POST['branch'] != '')?trim($_POST['branch']):'-');

	$update_vendor_query = "UPDATE vdr 
				   			SET name = '$name', address = '$addr', phone = '$phone', fax = '$fax', pic = '$pic', serves = '$branch', pemail = '$pemail', vsap = '$vsap', pcontact = '$pctc' 
				   			WHERE id ='$vendor_id';";
	$update_vendor_SQL 	= mysql_query($update_vendor_query) or die(mysql_error());
	
	if($update_vendor_SQL){ 
		$status ="<p class=\"yellowbox\">Vendor data has been updated !</p>";
	} 
	
	else { 
		$status ="<p class=\"yellowbox\">Update failed !</p>";
	}
	
	log_hist("56",$name);
	header("location:$this_page");
	exit();
}

$status	= "&nbsp;";

$query	= "SELECT id, name, address, phone, fax, pic, serves, pemail, vsap, pcontact FROM vdr WHERE del = '0' AND id = '$vendor_id';";
$SQL 	= @mysql_query($query) or die(mysql_error());
$array 	= mysql_fetch_array($SQL, MYSQL_ASSOC);

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="<?=$_SERVER['REQUEST_URI'];?>">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>VENDOR DETAILS</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
        <tr><td><?=$status?></td></tr>
      	<tr><td>[&nbsp;<a href="./vdr.php">Back to the Vendor Page</a>&nbsp;]</td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>
			<div class="well">
			<table border="0">
				<tr align="left">
					<td align="right"><b>ID</b></td>
					<td>:</td>
					<td><?=($array["id"])?"#".$array["id"]:"&nbsp; -";?>
					</td></tr>
				<tr align="left">
					<td align="right"><b>COMPANY NAME</b></td>
					<td>:</td>
					<td><input type="text" size="30" name="name" value="<?=($array["name"])?ucwords($array["name"]):"-";?>"></td></tr>
				<tr align="left">
					<td align="right" valign="top"><b>ADDRESS</b></td>
					<td valign="top">:</td>
					<td><textarea cols="50" rows="2" name="addr" wrap="virtual"><?=($array["address"])?strip_tags(nl2br($array["address"])):"-";?></textarea></td></tr>
				<tr align="left">
					<td align="right"><b>PHONE</b></td>
					<td>:</td>
					<td><input type="text" size="30" name="phone" value="<?=($array["phone"])?ucwords($array["phone"]):"-";?>"></td></tr>
				<tr align="left">
					<td align="right"><b>FAX</b></td>
					<td>:</td>
					<td><input type="text" size="30" name="fax" value="<?=($array["fax"])?ucwords($array["fax"]):"-";?>"></td></tr>
				<tr align="left">
					<td align="right"><b>PIC</b></td>
					<td>:</td>
					<td><input type="text" size="30" name="pic" value="<?=($array["pic"])?ucwords($array["pic"]):"-";?>"></td></tr>
				<tr align="left">
					<td align="right"><b>PIC CTC</b></td>
					<td>:</td>
					<td><input type="text" size="30" name="pctc" value="<?=($array["pcontact"])?$array["pcontact"]:"-";?>"></td></tr>
				<tr align="left">
					<td align="right"><b>EMAIL</b></td>
					<td>:</td>
					<td><input type="text" size="30" name="pemail" value="<?=($array["pemail"])?ucwords($array["pemail"]):"-";?>"></td></tr>
				<tr align="left">
					<td align="right"><b>SAP</b></td>
					<td>:</td>
					<td><input type="text" size="30" name="vsap" value="<?=($array["vsap"])?ucwords($array["vsap"]):"-";?>"></td></tr>
				<tr align="left">
					<td align="right"><b>BRANCH SERVES</b></td>
					<td>:</td>
					<td>
					<select name="branch">
    				<option value="-">---------------------</option>
<?php while($branch_list_array = mysql_fetch_array($branch_list_SQL,MYSQL_ASSOC)){
 		$compare_branch = ($branch_list_array["id"] == $array["serves"])?"SELECTED":"";?>
    		<option value="<?=$branch_list_array["id"]?>" <?=$compare_branch?>><?=ucwords($branch_list_array["name"])?></option>
<?php } ?>			</select>
				</td></tr>
			</table>
			</div>
		</td></tr>
		<tr><td><input type="submit" class="btn-info btn-small" name="upd_vdr" value="  UPDATE VENDOR  "></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[&nbsp;<a href="./vdr.php">Back to the Vendor Page</a>&nbsp;]</td></tr>
</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>