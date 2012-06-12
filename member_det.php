<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title 	= "Member Details";
$page_id_left 	= "14";
$page_id_right 	= "34";
$category_page 	= "mgmt";
chkSecurity($page_id_right);

$member_id 		= ((isset ($_GET['id']) && $_GET['id'] != '')?trim($_GET['id']):'');
$old_user_query ="SELECT u.id, 
						 u.salut, 
						 u.fname, 
						 u.lname, 
						 u.username, 
						 u.email, 
						 u.sign, 
						 u.status, 
						 u.branch_id_fk AS branch, 
						 u.dept_id_fk AS dept, 
						 u.mgr_id_fk AS mgr, 
						 u.level_id_fk AS level, 
						 u.joindate, 
						 u.active 
				  FROM user u 
				  WHERE u.id ='$member_id';";
$old_user_SQL 	= @mysql_query($old_user_query) or die(mysql_error());
$old_user_array = mysql_fetch_array($old_user_SQL,MYSQL_ASSOC);

$branch_list_query 	= "SELECT b.id, b.name 
					   FROM branch b 
					   WHERE b.del = '0' 
					   ORDER BY b.name ASC;";
$branch_list_SQL 	= @mysql_query($branch_list_query) or die(mysql_error());

$department_list_query 	="SELECT d.id, d.name 
						  FROM departments d 
						  WHERE d.del= '0' 
						  ORDER BY d.name ASC;";
$department_list_SQL 	= @mysql_query($department_list_query) or die(mysql_error());

$level_list_query 	= "SELECT ul.id, ul.name 
					   FROM user_level ul 
					   WHERE ul.del = '0' AND ul.hidden = '0' ;";
$level_list_SQL 	= @mysql_query($level_list_query) or die(mysql_error());

$manager_list_query ="SELECT u.id, CONCAT(u.fname,' ',u.lname) AS fullname 
					  FROM user u 
					  WHERE u.level_id_fk ='7' AND u.del = '0' 
					  ORDER BY u.fname ASC;";
$manager_list_SQL = @mysql_query($manager_list_query) or die(mysql_error());

$this_page 	= $_SERVER['PHP_SELF']."?id=".$member_id;
$salut 		= array("mr."=>"Mr.","mrs."=>"Mrs.","ms."=>"Ms.");
$act_det 	= array("0"=>"No","1"=>"Yes");

$status="&nbsp;";

$lastupd = date('Y-m-d H:i:s');

if (isset($_POST['update_user'])){
	$update_id 	= $_POST['update_id'];
	$salut 		= strtolower(trim($_POST['salut']));
	$fname 		= strtolower(trim($_POST['fname']));
	$lname 		= strtolower(trim($_POST['lname']));
	$status 	= strtolower(trim($_POST['status']));
	$branch 	= trim($_POST['branch']);
	$department = strtolower(trim($_POST['department']));
	$manager 	= strtolower(trim($_POST['manager']));
	$level 		= strtolower(trim($_POST['level']));
	$active 	= trim($_POST['active']);
	$lastupd 	= date('Y-m-d H:i:s');
	$update_user_query  ="UPDATE user SET salut ='$salut', fname ='$fname', lname='$lname', status='$status', branch_id_fk='$branch', dept_id_fk='$department', mgr_id_fk='$manager', level_id_fk='$level', active='$active', lastupd = '$lastupd' WHERE id ='$update_id';";
	@mysql_query($update_user_query) or die(mysql_error());
	log_hist(13,$uname);
	header("location:$this_page");

}

elseif(isset($_POST['reset_pass'])) {
		$uname 		= $old_user_array[3];
		$update_id 	= $_POST['update_id'];
		$pass_reset = trim(md5(DEFAULT_PASS));
		$reset_pass_query = "UPDATE user SET password = '$pass_reset', lastupd = '$lastupd' WHERE id = '$update_id';";
		log_hist("15",$uname);
		@mysql_query($reset_pass_query) or die(mysql_error());
		$status ="<p class=\"yellowbox\">This account's password has been resetted !</p>";
}

else {
	$status="&nbsp;";
}
	
include THEME_DEFAULT.'header.php'; ?>

<//-----------------CONTENT-START-------------------------------------------------//>
<?php 
if($_SESSION['level'] <= $old_user_array["level"]) {?>

<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>MEMBER DETAILS</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
        <tr><td><?=$status?></td></tr>
        <tr><td>[&nbsp;<a href="./members.php">BACK TO THE MEMBERS PAGE</a>&nbsp;]</td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>
		<form method="POST" action="<?=$_SERVER['REQUEST_URI'];?>" class="well form span10">
    <input type="hidden" name="update_id" value="<?=$member_id?>">
	<table border="0">
		<tr><td colspan="7">&nbsp;</td></tr>
		<tr valign="top">
			<td align="right"><b>SALUTATION</b></td>
			<td>:</td>
			<td><select name="salut">
				<option value="-">--------</option>
<?php foreach($salut as $key => $name) {
		$compare_salut = ($key == $old_user_array["salut"])?"SELECTED":"";?>
				<option value ="<?=$key?>" <?=$compare_salut?>><?=$name?></option>
<?php	} ?>
 			    </select>
 			</td>
			<td>&nbsp;</td>
			<td align="right">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td></tr>
		<tr valign="top">
			<td align="right"><b>FIRST NAME</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="fname" value="<?=ucwords($old_user_array["fname"])?>"></td>
			<td>&nbsp;</td>
			<td align="right"><b>LAST NAME</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="lname" value="<?=ucwords($old_user_array["lname"])?>"></td></tr>
		<tr valign="top">
			<td align="right"><b>EMAIL</b></td>
			<td>:</td>
			<td><b><?=($old_user_array["email"])?$old_user_array["email"]:"-"?></b></td>
			<td>&nbsp;</td>
			<td align="right"><b>PASSWORD</b></td>
			<td>:</td>
			<td><?=($_SESSION['level'] > $old_user_array["level"])?"":"<input type=\"submit\" class=\"btn-info btn-small\" name=\"reset_pass\" value=\"  DEFAULT PASSWORD  \">&nbsp;&nbsp;";?>(Default password : <b><i>'<?=DEFAULT_PASS?>'</i></b>)</td></tr>
		<tr valign="top">
			<td align="right"><b>STATUS</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="status" value="<?=($old_user_array["status"])?ucwords($old_user_array["status"]):"-"?>"></td>
			<td colspan="4">&nbsp;</td></tr>
		<tr valign="top">
			<td align="right"><b>BRANCH</b></td>
			<td>:</td>
			<td><select name="branch">
    				<option value="-">---------------------</option>
<?php 
  	while($branch_list_array = mysql_fetch_array($branch_list_SQL)){
 		$compare_branch = ($branch_list_array[0] == $old_user_array["branch"])?"SELECTED":"";?>
    <option value="<?=$branch_list_array[0]?>" <?=$compare_branch?>><?=ucwords($branch_list_array[1])?></option>
<? } ?>
 				 </select>
  			</td>
  			<td>&nbsp;</td>
			<td align="right"><b>DEPARTMENT</b></td>
			<td>:</td>
			<td>
			<select name="department">
    			<option value="-">---------------------</option>
<?php 
  	while($department_list_array = mysql_fetch_array($department_list_SQL)){
  		$compare_dept = ($department_list_array[0] == $old_user_array["dept"])?"SELECTED":"";?>
    <option value="<?=$department_list_array[0]?>" <?=$compare_dept?>><?=ucwords($department_list_array[1])?></option>
<? } ?>
 				 </select>
			</td></tr>
		<tr valign="top">
			<td align="right"><b>L2</b></td>
			<td>:</td>
			<td colspan="5">
			<select name="manager">
    				<option value="-">---------------------</option>
<?php 
  	while($manager_list_array = mysql_fetch_array($manager_list_SQL)){
  		$compare_manager = ($manager_list_array[0] == $old_user_array["mgr"])?"SELECTED":"";?>	
    <option value="<?=$manager_list_array[0]?>" <?=$compare_manager?>><?=ucwords($manager_list_array[1])?></option>
<? } ?>
 				 </select>
			
			</td>
			
			</tr>
		<tr valign="top">
			<td align="right"><b>LEVEL</b></td>
			<td>:</td>
			<td>
<?php if ($_SESSION['level'] > $old_user_array["level"]){?><?=$old_user_array["level"]?><input type="hidden" name="level" value="<?=$old_user_array[11]?>" /><?php } else {?>
			<select name="level">
    				<option value="-">---------------------</option>
<?php 
  	while($level_list_array = mysql_fetch_array($level_list_SQL)){
  	$compare_level = ($level_list_array[0] == $old_user_array["level"])?"SELECTED":"";?>
    <option value="<?=$level_list_array[0]?>" <?=$compare_level?>><?=ucwords($level_list_array[1])?></option>
<? } }?>
			</td>
			<td>&nbsp;</td>
			<td align="right"><b>ACTIVE</b></td>
			<td>:</td>
			<td>
<?php if ($_SESSION['level'] > $old_user_array["level"]){?><?=($old_user_array["active"] == '1')?"YES":"NO"?><input type="hidden" name="active" value="<?=$old_user_array["actives"]?>" /><?php } else {?>
			<select name="active">
<?php foreach($act_det as $act_key => $act_status) {
		$compare_act = ($act_key == $old_user_array["active"])?"SELECTED":"";?>
				<option value ="<?=$act_key?>" <?=$compare_act?>><?=$act_status?></option>
<?php	} ?>
 			    </select>
<?php } ?>
 			    </td>
		</tr>	
		<tr valign="top">
			<td align="right"><b>JOIN DATE</b></td>
			<td>:</td>
			<td ><?=cplday('d F Y',$old_user_array["joindate"])?></td>
			<td>&nbsp;</td>
			<td align="right"><b>SIGNATURE</b></td>
			<td>:</td>
			<td valign="top" align="left"><?=($old_user_array["sign"])?"<img src=".$old_user_array["sign"]." width=\"150\" height=\"100\" border=\"0\">":"-"?></td>
		</tr>
		<tr valign="top"><td colspan="7">&nbsp;</td></tr>
	</table>
	</td></tr>
	<tr><td><input type="submit" class="btn-info btn-small" name="update_user" value="  UPDATE ACCOUNT  "/></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>[&nbsp;<a href="./members.php">BACK TO THE MEMBERS PAGE</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
	</form>
</table>


<?php 
} else {
	deny_perm();
	log_hist("4", " TO USER ".$old_user_array[4]);
}

?>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>