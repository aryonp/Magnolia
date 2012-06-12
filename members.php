<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
require_once CONT_PATH.'auth.class.php';
chkSession();	

$page_title		= "Members List";
$page_id_left 	= "14";
$page_id_right 	= "34";
$category_page 	= "mgmt";
chkSecurity($page_id_right);

$branch_list_query 		= "SELECT b.id, b.name FROM branch b ORDER BY b.name ASC;";
$branch_list_SQL 		= @mysql_query($branch_list_query) or die(mysql_error());

$dept_list_query 		= "SELECT d.id, d.name FROM departments d ORDER BY d.name ASC;";
$dept_list_SQL 			= @mysql_query($dept_list_query) or die(mysql_error());

$level_list_query 		= "SELECT ul.id, ul.name FROM user_level ul WHERE ul.id >= '".$_SESSION['level']."' AND ul.del = 0;";
$level_list_SQL 		= @mysql_query($level_list_query) or die(mysql_error());

$manager_list_query 	= "SELECT u.id, CONCAT(u.fname,' ',u.lname) AS fullname FROM user u WHERE u.level_id_fk ='7' AND u.del = '0' ORDER BY u.fname ASC;";
$manager_list_SQL 		= @mysql_query($manager_list_query) or die(mysql_error());

$members_list_query ="SELECT u.id, CONCAT(u.fname,' ',u.lname) AS fullname, u.email, u.status, b.id as branch, d.name as dept, ul.name as level, u.joindate, u.active ".
					 "FROM user u 
					 		LEFT JOIN branch b ON (b.id = u.branch_id_fk) 
					 		LEFT JOIN departments d ON (d.id = u.dept_id_fk ) 
					 		LEFT JOIN user_level ul ON (ul.id = u.level_id_fk) ".
					 "WHERE u.del = '0' AND u.hidden = '0' AND u.active= '1' ".
					 "ORDER BY u.fname ASC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($members_list_query);
$pagingResult->paginate();

$this_page 	= $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();
$status 	= "&nbsp;";
$lastupd 	= date('Y-m-d H:i:s');

if(isset($_POST['add_user'])){
   	$password 	= trim(md5(DEFAULT_PASS));
	$salut 		= strtolower(trim($_POST['salut']));
	$fname 		= strtolower(trim($_POST['fname']));
	$lname 		= strtolower(trim($_POST['lname']));
	$email 		= strtolower(trim($_POST['email']));
	$status 	= strtolower(trim($_POST['status']));
	$branch 	= trim($_POST['branch']);
	$department = strtolower(trim($_POST['department']));
	$manager 	= strtolower(trim($_POST['manager']));
	$level 		= strtolower(trim($_POST['level']));
	$joindate 	= date('Y-m-d');
	
   	if(!empty($salut) AND !empty($email) AND $branch != "-" AND $department != "-"){
   		if(!$email) {
			$status ="<br/><p class=\"alert span10\">You did not enter a valid e-mail address.</p>";
		} 
		else {
			$chk_email_q	= "SELECT u.email FROM user u WHERE u.email = '$email';";
			$chk_email_SQL 	= @mysql_query($chk_email_q) or die(mysql_error());
			if (mysql_num_rows($chk_email_SQL) >= 1) {
				$status ="<br/><p class=\"alert alert-error span10\">Another account with this email (<b>".$email."</b>) already created. Please choose another email!</p>";
			}
			else {
				$add_user_query  = "INSERT INTO user (salut,fname,lname,password,email,status,branch_id_fk,dept_id_fk,mgr_id_fk,level_id_fk,joindate,active,hidden,del,lastupd) ".
							   	   "VALUES ('$salut','$fname','$lname','$password','$email','$status','$branch','$department','$manager','$level','$joindate','1','0','0','$lastupd');";
				@mysql_query($add_user_query) or die(mysql_error());
				
				$userid = mysql_insert_id();
				auto_agree($userid, $branch);
				
				$status ="<br/><p class=\"alert alert-success span10\">Account for email <b>".$email."</b> has been created.</p>";
				log_hist(12,$username);
			} 
		}
	}	
	else {	
		$status ="<br/><p class=\"alert span10\">Missing required information.</p>"; }
	}
	
if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	
	$del_user_q  = "UPDATE user SET del = '1' , active = '0', hidden = '1', lastupd = '$lastupd' WHERE id ='$did';";
	@mysql_query($del_user_q) or die(mysql_error());
	
	log_hist(14,$did);
	header("location:$this_page");
	exit();
}

if (isset($_POST['confirm_code'])){
	if(trim($_POST['seccode']) == $_SESSION['seccode']){
		unset($_SESSION['seccode']);
		$rst_all_password 		= md5(trim(DEFAULT_PASS));
		$rst_all_pass_query  	= "UPDATE user 
								   SET password = '$rst_all_password', lastupd = '$lastupd' 
								   WHERE active = '1' AND hidden = '0' AND del = '0';";
		@mysql_query($rst_all_pass_query) or die(mysql_error());
		log_hist(16);
		$status ="<br/><p class=\"alert alert-success span10\">All users password has been resetted!</p>"; 
	}
	else {
		$status ="<br/><p class=\"alert alert-error span10\">Wrong confirmation code!</p>";
	}
}

if (isset($_POST['cancel_reset'])){
	unset($_SESSION['seccode']);
	header("location:$this_page");
	exit();
}

include THEME_DEFAULT.'header.php'; ?>             			
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>MEMBERS LIST</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>
		<form method="POST" action="" class="well span10">
	<table border="0" cellpadding="1" cellspacing="1" >
		<tr valign=top>
			<td align="right"><b>SALUTATION</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td>
				<select name="salut">
				<option>----</option>
    				<option value="mr.">Mr.</option>
				<option value="mrs.">Mrs.</option>
				<option value="ms.">Ms.</option>
 				</select>
 			</td>
			<td>&nbsp;</td>
			<td align="right">&nbsp;</td>
			<td>&nbsp;</td>
			<td>&nbsp;</td></tr>
		<tr valign=top>
			<td align="right"><b>FIRST NAME</b></td>
			<td>:</td>
			<td><input type=text size=30 name='fname' id="fname">
			<script language="JavaScript" type="text/javascript">
				if(document.getElementById) document.getElementById('fname').focus();
			</script></td>
			<td>&nbsp;</td>
			<td align="right"><b>LAST NAME</b></td>
			<td>:</td>
			<td><input type=text size=30 name='lname'></td></tr>
		<tr valign=top>
			<td align="right"><b>EMAIL</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><input type="text" size="30" name="email" value="<?=DEFAULT_MAIL_DOMAIN?>"></td>
			<td>&nbsp;</td>
			<td align="right"><b>PASSWORD</b></td>
			<td>:</td>
			<td>' <b><i><?=strtolower(DEFAULT_PASS)?></i></b> '&nbsp;</td></tr>
		<tr valign=top>
			<td align="right"></td>
			<td></td>
			<td></td>
			<td>&nbsp;</td>
			<td align="right"></td>
			<td></td>
			<td><font size="1">(Default password for every user, they must change it after login)</font></td></tr>
		<tr valign=top>
			<td align="right"><b>STATUS</b></td>
			<td>:</td>
			<td colspan="5"><input type="text" size="30" name="status"></td>
			</tr>
		<tr valign=top>
			<td align="right"><b>BRANCH</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td> 	<select name="branch">
    				<option value="-" SELECTED>---------------------</option>
<?php 
  	while($branch_list_array = mysql_fetch_array($branch_list_SQL)){?>
    <option value="<?=$branch_list_array[0]?>"><?=ucwords($branch_list_array[1])?></option>
<? } ?>
 				 </select>
  			</td>
			<td>&nbsp;</td>
			<td align="right"><b>DEPARTMENT</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td>
				<select name="department">
    				<option SELECTED>---------------------</option>
<?php 
  	while($dept_list_array = mysql_fetch_array($dept_list_SQL)){?>
    <option value="<?=$dept_list_array[0]?>"><?=ucwords($dept_list_array[1])?></option>
<? } ?>
 				 </select>
			</td></tr>
		<tr valign=top>
			<td align="right"><b>LEVEL</b></td>
			<td>:</td>
			<td>
			<select name="level">
    				<option SELECTED>---------------------</option>
<?php 
  	while($level_list_array = mysql_fetch_array($level_list_SQL)){?>
    <option value="<?=$level_list_array[0]?>"><?=ucwords($level_list_array[1])?></option>
<? } ?>
			</td>			
			<td>&nbsp;</td>
			<td align="right"><b>L2</b></td>
			<td>:</td>
			<td>
			<select name="manager">
    				<option SELECTED>---------------------</option>
<?php 
  	while($manager_list_array = mysql_fetch_array($manager_list_SQL)){?>
    <option value="<?=$manager_list_array[0]?>"><?=ucwords($manager_list_array[1])?></option>
<? } ?>
 				 </select>

			</td></tr>
		<tr valign=top>
			<td align="right"><b>JOIN DATE</b></td>
			<td>:</td>
			<td colspan="5"><?=date('d M Y')?></td></tr>
			</table>
		</td></tr>
		<tr><td><input type="submit" name="add_user" class="btn-info btn-small" value="  ADD ACCOUNT  "></td></tr></form>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
<?php 	
echo "<form method=\"POST\" class=\"well span10\" action=\"\">\n";
if (isset($_POST['rst_all_pass'])){
	echo "<table border=\"0\">
	  			<tr valign=\"top\"><td>CONFIRMATION : </td><td><input type=\"text\" name =\"seccode\" />&nbsp;</td></tr>
		  		<tr valign=\"middle\"><td>&nbsp;</td><td><img src=\"./plugins/captcha.php\" alt=\"captcha\"><br/><br/></td></tr>
		  		<tr valign=\"middle\"><td>&nbsp;</td>
				                      <td><input type=\"submit\" class=\"btn-small btn-primary\" name=\"confirm_code\" value=\"    CONFIRM RESET ALL PASSWORD    \">&nbsp;&nbsp;
									      <input type=\"submit\" class=\"btn-small btn-primary\" name=\"cancel_reset\" value=\"    CANCEL RESET ALL PASSWORD    \"></td></tr>
			</table>\n";
}
else {
	echo "<input type=\"submit\" name=\"rst_all_pass\" class=\"btn-large btn-danger\" value=\"RESET ALL PASSWORD TO DEFAULT\"><b> <span class=\"label label-important\">Never use the panic button, dude! I mean it!</span>";
}
echo "</form>\n";
?>		
		</td></tr>
		<tr><td>&nbsp</td></tr>
        <tr><td>
        	<?=$pagingResult->pagingMenu();?>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
            	<tr valign="middle"> 
                 	<th width="25">&nbsp;<b>NO.</b></td>
					<th width="*" align="left">&nbsp;<b>NAME</b></td>
                 	<th width="*" align="left">&nbsp;<b>EMAIL</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>BRANCH</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>DEPARTMENT</b>&nbsp;</td>
            		<th width="*" align="left">&nbsp;<b>LEVEL</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>REG. DATE</b>&nbsp;</td>
                 	<th width="*" align="center" colspan="3">&nbsp;<b>CMD</b></td>
				</tr>
				</thead>
				<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) { ?>
		<tr valign="top">
			<td align="left">&nbsp;<?=$count?>.&nbsp;</td>
			<td align="left">&nbsp;<?=($array["fullname"])?ucwords($array["fullname"]):"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["email"])?$array["email"]:"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["branch"])?ucwords($array["branch"]):"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["dept"])?ucwords($array["dept"]):"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=ucwords($array["level"])?>&nbsp;</td>
			<td align="left">&nbsp;<?=cplday('d M Y',$array["joindate"])?>&nbsp;</td>
			<td width="25" align="center" valign="middle"><a title="View Details" href="./member_det.php?id=<?=$array["id"]?>">
				<img src="<?=IMG_PATH?>edit.png"></a></td>
			<td width="25" align="center" valign="middle"><a title="Delete Member" href="<?=$this_page?>" onclick="return confirmBox(this,'del','username <?=$array["email"]?>?','<?=$array["id"]?>')">
				<img src="<?=IMG_PATH?>delete.png"></a></td>
		</tr>

<?php	$count++;  
		}
	} else {?>
		<tr><td colspan="9" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php } ?></tbody>
			</table>
				<?=$pagingResult->pagingMenu();?>
				</td></tr>	
			<tr><td>&nbsp</td></tr>	
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>