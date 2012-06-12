<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
require_once CONT_PATH.'po.php';
chkSession();
	
$page_title="Inventory Details";
$page_id_left ="11";
$page_id_right ="26";
$category_page = "inventory";
$inv_id = (isset($_GET['id']))?strip_tags(trim($_GET['id'])):"";

$old = "SELECT iv.id, iv.aid, iv.description as name, iv.life, iv.class, iv.price, iv.cctr, iv.buydate, iv.startdate FROM inv iv WHERE iv.id = '$inv_id' AND iv.del = '0' ";
$oldSQL = @mysql_query($old) or die(mysql_error());
$oldArray = mysql_fetch_array($oldSQL, MYSQL_ASSOC);
chkSecurity($page_id_right);

$this_page = $_SERVER['PHP_SELF']."?id=$inv_id";
$class_list_query ="SELECT name, description FROM inv_class WHERE del ='0';";
$class_list_SQL = mysql_query($class_list_query) or die(mysql_error());

if(isset($_POST["upd_inv"])) {
	$price = trim($_POST["price"]);
	$cctr = strtoupper(trim($_POST["cctr"]));
	$class = trim($_POST["class"]);
	$bdate = trim($_POST["bdate"]);
	$udate = trim($_POST["udate"]);
	$invnbr = trim($_POST["invnbr"]);
	$life = (is_numeric(trim($_POST["life"])))?trim($_POST["life"]):"";
	$details = trim($_POST["details"]);
	
	if($class != "-" OR !empty($price) OR !empty($cctr) OR !empty($life) OR !empty($details)) {
		$edit = "UPDATE inv SET aid = '$invnbr',description = '$details',class = '$class',life = '$life',cctr = '$cctr',buydate = '$bdate',price = '$price',startdate = '$udate' WHERE id = '$inv_id' ";	
		@mysql_query($edit) or die(mysql_error());
		log_hist("63",$inv_id);
		header("location:$this_page");
	}
	
	else {
		$status = "<p class=\"yellowbox\">Missing required information! Please complete all necessary infos!</p>";
	}
}

$status = "&nbsp;";

include THEME_DEFAULT.'header.php'; 
?>             			
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>[&nbsp;<a href="./inv_hm.php">Back to the Inventory home</a>&nbsp;]</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
       	<div class="well">
	<table border="0" cellpadding="1" cellspacing="1">
		<tr valign="top">
			<td align="right"><b>BUYDATE</b></td>
			<td>:</td>
			<td><input type="text" name="bdate" value="<?=($oldArray["buydate"])?$oldArray["buydate"]:"";?>" id="bdate" size="20">&nbsp;<a href="javascript:NewCal('bdate','yyyymmdd')"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><b>USEDATE</b></td>
			<td>:</td>
			<td><input type="text" name="udate" value="<?=($oldArray["startdate"])?$oldArray["startdate"]:"";?>" id="udate" size="20">&nbsp;<a href="javascript:NewCal('udate','yyyymmdd')"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a>&nbsp;</td></tr>
		<tr valign="top">
			<td align="right"><b>CLASS</b>&nbsp;<font color="Red">*</font></td><td>:</td>
			<td><select name="class">
<?php	while($array = mysql_fetch_array($class_list_SQL,MYSQL_ASSOC)){
			$compare_item = ($array["name"] == $oldArray["class"])?"SELECTED":"";?>
		<option value ="<?=$array["name"]?>" <?=$compare_item?>><?=ucwords($array["description"])?></option>
<?php	} ?>
		</select></td>
			<td>&nbsp;</td>
			<td align="right"><b>COST CENTER</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" size="30" name="cctr" value="<?=($oldArray["cctr"])?$oldArray["cctr"]:"-";?>"/></td></tr>
		<tr valign="top">
			<td align="right"><b>PRICE (USD)</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" size="30" maxlength="20" name="price" value="<?=($oldArray["price"])?$oldArray["price"]:"0";?>"/></td>
			<td>&nbsp;</td>
			<td align="right"><b>LIFE (YEARS)</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" size="30" name="life" value="<?=($oldArray["life"])?$oldArray["life"]:"";?>"/></td></tr>
		<tr valign="top">
			<td align="right"><b>DESCRIPTION</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><textarea cols="50" rows="2" name="details" wrap="virtual"><?=($oldArray["name"])?$oldArray["name"]:"-";?></textarea></td>
			<td>&nbsp;</td>
			<td align="right"><b>INV. NBR</b></td><td>:</td><td><input type="text" size="30" name="invnbr" value="<?=($oldArray["aid"])?$oldArray["aid"]:"-";?>"/></td></tr>
			</table>
		</td></tr>
		<tr><td><input type="submit" name="upd_inv" class="btn-info btn-small" value="  UPDATE INVENTORY  "></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[&nbsp;<a href="./inv_hm.php">Back to the Inventory home</a>&nbsp;]</td></tr>
	</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>