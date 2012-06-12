<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title		= "Create Access";
$page_id_left 	= "11";
$page_id_right 	= "27";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
$category_page 	= "inventory";
chkSecurity($page_id);

$status 	= "&nbsp;";
$acc_det	= array();

if(isset($_POST['submit_acc'])) {
	$name 		= strtolower(trim($_POST['name']));
	$email 		= strtolower(trim($_POST['email']));
	$dept 		= (int) trim($_POST['dept']);
	$branch 	= trim($_POST['branch']);
	$creator 	= (int) trim($_SESSION['uid']);
	$unames 	= $_POST['uname'];
	$regdate 	= trim($_POST['regdate']);
	$lastupd 	= date('Y-m-d H:i:s');
	if (!empty($name)) {	
		$add_acc_q = "INSERT INTO acc (name,email,branch_id_fk,dept_id_fk,user_id_fk,lastupd) ".
					 "VALUES ('$name','$email','$branch','$dept','$creator','$lastupd');";
		@mysql_query($add_acc_q) or die(mysql_error());
		$acc_id_fk = mysql_insert_id();
		$acc_det_q = "INSERT INTO acc_det (acc_id_fk,username,password,item_id_fk,al_id_fk,notes,regdate) VALUES ";
		foreach($unames as $key => $uname) {
			$pwd 	= trim($_POST['passwd'][$key]);
			$item 	= $_POST['item'][$key];
			$level 	= $_POST['level'][$key];
			$notes 	= strip_tags(trim($_POST['notes'][$key]));
			array_push($acc_det," ('$acc_id_fk','".strtolower(trim($uname))."','$pwd','$item','$level','$notes','$regdate')");
		}
		$acc_det_q .= implode(",",$acc_det);
		@mysql_query($acc_det_q) or die(mysql_error());
		log_hist("65",$acc_id_fk);
		header("location:./acc_det.php?id=".$acc_id_fk);
	} 
	else {
		$status = "<p class=\"redbox\">Missing Information! could not create entries, Press back button to repeat input</p>";
	}
}

$button = array("submit_acc" => array("submit" => "  CREATE ACCESS  "),
				"reset_acc"  => array("reset"  => "  RESET ACCESS  "));
					
include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="<?=$_SERVER['PHP_SELF']?>">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><?=$status?></td></tr>
	<tr><td>
		<div class="well form-inline">
		<label for="items_count"><b>CREATE ACCESS WITH :</b></label>
		<input type="text" name="items_count" size="3" maxlength="1" id="icounts" value="<?=(isset($_POST['items_count']))?$_POST['items_count']:"";?>">&nbsp;&nbsp;ITEMS&nbsp;&nbsp;
		<input type="submit" class="btn-info btn-small" name="gen_acc" value=" GENERATE " />
		<script language="JavaScript" type="text/javascript">if(document.getElementById) document.getElementById('icounts').focus();</script>
		</div>
	</td></tr>	
	<tr><td>&nbsp;</td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td>&nbsp;</td></tr>
<?php if (isset($_POST['gen_acc']) AND (!empty($_POST['items_count'])) AND (is_numeric($_POST['items_count']))) { 
	$items_count = $_POST['items_count'];
	?>
	<tr><td>[&nbsp;<a href="./acc_hm.php">Back to the Access Page</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>	
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><?=genButton($button)?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<table border="0" cellpadding="1" cellspacing="1">
				<tr><td width="35">&nbsp;<b>NAME</b>&nbsp;<font color="Red">*</font></td>
					<td width="10"><b>:</b></td>
					<td><input type="text" name="name" size="30"/></td></tr>
				<tr><td width="35">&nbsp;<b>EMAIL</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td><input type="text" name="email" size="30"/></td></tr>
				<tr><td width="35">&nbsp;<b>REG DATE</b>&nbsp;<font color="Red">*</font></td>
					<td width="10"><b>:</b></td>
					<td><input type="text" name="regdate" id="rdate" size="10" maxlength="10">&nbsp;
						<a href="javascript:NewCal('rdate','yyyymmdd')">
						<img src="<?=IMG_PATH?>cal.gif" border="0" /></a></td></tr>
				<tr><td width="45">&nbsp;<b>DEPARTMENT</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td><?=dept_list();?></td></tr>
				<tr><td width="45">&nbsp;<b>BRANCH</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td><?=branch_list();?></td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td colspan="3">
					<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
						<thead>
						<tr valign="middle"> 
							<td>&nbsp;<b>NO</b>&nbsp;</td>
							<td>&nbsp;<b>USERNAME</b>&nbsp;</td>
							<td>&nbsp;<b>PASSWORD</b>&nbsp;</td>
							<td>&nbsp;<b>TYPE</b>&nbsp;</td>
                            <td>&nbsp;<b>LEVEL</b>&nbsp;</td>
							<td>&nbsp;<b>NOTES</b>&nbsp;</td>
						</tr>
						</thead>
						<tbody>
<?php 
for($ctm = 1;$ctm <= $items_count;$ctm++) { 
		$row_color = ($ctm % 2)?"odd":"even"; ?>			
						<tr class="<?=$row_color?>" valign="top" align="left">
							<td>&nbsp;<?=$ctm?>.&nbsp;</td>
							<td>&nbsp;<input type="text" name="uname[]" size="20">&nbsp;</td>
							<td>&nbsp;<input type="text" name="passwd[]" size="20">&nbsp;</td>
							<td>&nbsp;<?=acc_list_selection();?>&nbsp;</td>
                            <td>&nbsp;<?=acc_lvl_selection();?>&nbsp;</td>
							<td>&nbsp;<textarea cols="30" rows="3" name="notes[]" wrap="virtual"></textarea>&nbsp;</td>
						</tr>
<?php } ?>				</tbody>
					</table>
				</td></tr>
			</table>
		</td></tr>
		<tr><td><?=genButton($button)?></td></tr>
	</table>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>[&nbsp;<a href="./acc_hm.php">Back to the Access Page</a>&nbsp;]</td></tr>
<?php } ?>
  	<tr><td>&nbsp;</td></tr>
</table>
</form>
<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>