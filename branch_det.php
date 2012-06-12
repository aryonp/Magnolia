<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Branch Details";
$page_id_left 	= "13";
$page_id_right 	= "29";
$category_page 	= "strx";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$branch_id 	= $_GET['id'];
$query 		= "SELECT id, name, address, city, zip, phone, fax, emp, pic, pemail FROM branch WHERE del = '0' AND id = '$branch_id' ORDER BY id ASC ";
$SQL 		= @mysql_query($query) or die(mysql_error());
$array 		= mysql_fetch_array($SQL,MYSQL_ASSOC);

$this_page 	= $_SERVER['PHP_SELF']."?id=".$branch_id;
$status 	= "&nbsp;";

if (isset($_POST['upd_branch'])){
	$name 		= strtolower(trim($_POST['name']));
	$address 	= trim($_POST['addr']);
	$city 		= strtolower(trim($_POST['city']));
	$zip 		= trim($_POST['zip']);
	$phone 		= trim($_POST['phone']);
	$fax 		= trim($_POST['fax']);
	$emp 		= (int) (trim($_POST['emp_num']));
	$pic 		= strtolower(trim($_POST['pic']));
	$email 		= strtolower(trim($_POST['email']));
	
	$update_branch_query  = "UPDATE branch 
							 SET name = '$name', address = '$address', city = '$city', zip = '$zip', phone = '$phone', fax = '$fax', emp ='$emp', pic = '$pic', pemail = '$email' 
							 WHERE id ='$branch_id';";
	@mysql_query($update_branch_query) or die(mysql_error());
	log_hist("44",$name);
	header("location:$this_page");
	exit();
	
}

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title);?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td ><?=$status?></td></tr>
		<tr><td>[&nbsp;<a href="./branch.php">Back to the Branch Page</a>&nbsp;]</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<div class="well">
			<table border="0" cellpadding="1" cellspacing="1" >
		<tr valign=top>
			<td align="right"><b>ID</b></td>
			<td>:</td>
			<td><?=($array["id"])?strtoupper($array["id"]):"-";?></td>
			<td>&nbsp;</td>
			<td align="right"><b>NAME</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="name" value="<?=($array["name"])?ucwords($array["name"]):"-";?>"></td></tr>
		<tr valign=top>
			<td align="right"><b>ADDRESS</b></td>
			<td>:</td>
			<td colspan="5"><textarea cols="30" rows="4" name="addr" wrap="virtual"><?=($array["address"])?strip_tags(nl2br($array["address"])):"-";?></textarea></td></tr>
		<tr valign=top>
			<td align="right"><b>CITY</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="city" value="<?=($array["city"])?ucwords($array["city"]):"-";?>"></td>
			<td>&nbsp;</td>
			<td align="right"><b>ZIP</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="zip" value="<?=($array["zip"])?$array["zip"]:"-";?>"></td></tr>
		<tr valign=top>
			<td align="right"><b>PHONE</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="phone" value="<?=($array["phone"])?$array["phone"]:"-";?>"></td>
			<td>&nbsp;</td>
			<td align="right"><b>FAX</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="fax" value="<?=($array["fax"])?$array["fax"]:"-";?>"></td></tr>
		<tr valign=top>
			<td align="right"><b>EMPLOYEE</b></td>
			<td>:</td>
			<td colspan="5"><input type="text" size="30" name="emp" value="<?=($array["emp"])?$array["emp"]:"-";?>"></td></tr>
	    <tr valign=top>
			<td align="right"><b>PIC</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="pic" value="<?=($array["pic"])?$array["pic"]:"-";?>"></td>
			<td>&nbsp;</td>
			<td align="right"><b>EMAIL</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="email" value="<?=($array["pemail"])?$array["pemail"]:"-";?>"></td></tr>
	</table></div>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><input type="submit" name="upd_branch" value="  UPDATE BRANCH  " class="btn-info btn-small"/></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[&nbsp;<a href="./branch.php">Back to the Branch Page</a>&nbsp;]</td></tr>
</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>