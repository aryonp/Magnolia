<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title="Access Details";
$page_id_left 	= "11";
$page_id_right 	= "27";
$category_page 	= "inventory";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$status ="&nbsp;";
$acc_id = ($_GET['id']?$_GET['id']:"");
$branch_list_query ="SELECT b.id, b.name ".
                    "FROM branch b ".
                    "WHERE b.del = '0' ORDER BY b.name ASC;";
$branch_list_SQL = mysql_query($branch_list_query) or die(mysql_error());

$dept_list_query ="SELECT d.id, d.name ".
				  "FROM departments d ".
				  "WHERE d.del= '0' ORDER BY d.name ASC;";
$dept_list_SQL = mysql_query($dept_list_query) or die(mysql_error());

$oldQuery = "SELECT a.id, a.name, a.email, a.branch_id_fk as bid, b.name as bname, a.dept_id_fk as did, d.name as dname ".
			"FROM acc a ".
			"LEFT JOIN departments d ON (d.id = a.dept_id_fk) ".
			"LEFT JOIN branch b ON (b.id = a.branch_id_fk) ".
			"WHERE a.id = '$acc_id' ";	
$oldSQL = mysql_query($oldQuery) or die (mysql_error());
$oldArray = mysql_fetch_array($oldSQL,MYSQL_ASSOC);

$detQuery = "SELECT a.id, a.username, a.password, i.name as iname, al.lname as alname, a.al_id_fk as aid, i.id as iid, a.notes, a.regdate, a.dsbl ".
			"FROM acc_det a LEFT JOIN req_items i ON (i.id = a.item_id_fk) LEFT JOIN acc_level al ON (al.id = a.al_id_fk) ".
			"WHERE a.acc_id_fk = '$acc_id' ";
$detSQL = mysql_query($detQuery) or die (mysql_error());

$this_page = $_SERVER['PHP_SELF']."?id=".$acc_id;

function acc_edit($i,$param) {
	$query = "SELECT id, name ".
			 "FROM req_items ".
			 "WHERE type_id_fk = '1' AND del = '0' ORDER BY name ASC";
	$sql = mysql_query($query) or die(mysql_error());
	$acc_edit = "<select name=\"upd_item[$i]\">\n";
	$acc_edit .="<option value=\"-\">-----------</option>\n";
	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){
		$compare_item = ($array["id"] == $param)?"SELECTED":"";
		$acc_edit .= "<option value =\"".$array['id']."\" $compare_item>".ucwords($array['name'])."</option>\n";
	} 
	$acc_edit .= "</select>\n";return $acc_edit;
}

function lvl_edit($i,$param) {
	$query = "SELECT id, lName FROM acc_level WHERE del = '0' ORDER BY lName ASC";$sql = mysql_query($query);
	$lvl_edit = "<select name=\"upd_lvl[$i]\">\n";
	$lvl_edit .="<option value=\"-\">-----------</option>\n";
	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){
		$compare_lvl = ($array["id"] == $param)?"SELECTED":"";
		$lvl_edit .= "<option value =\"".$array['id']."\" $compare_lvl>".ucwords($array['lName'])."</option>\n";
	} 
	$lvl_edit .= "</select>\n";return $lvl_edit;
}

$button = array("submit_acc"=>array("submit"=>"  CREATE ACCESS  "),
				"reset_acc"=>array("reset"=>"  RESET ACCESS  "));
			
if(isset($_POST["add_det"])) {
	$uname = $_POST['uname'];
	$pwd = trim($_POST['passwd']);
	$item = trim($_POST['item']);
	$level = trim($_POST['level']);
	$notes = strip_tags(trim($_POST['notes']));
	$regdate = $lastupd = date('Y-m-d H:i:s');
	if ($item != "-" AND $level != "-") {
		$acc_det_q = "INSERT INTO acc_det (acc_id_fk,username,password,item_id_fk,al_id_fk,notes,regdate) VALUES ('$acc_id','".strtolower(trim($uname))."','$pwd','$item','$level','$notes','$regdate');";
		@mysql_query($acc_det_q) or die(mysql_error());
		$upd = "UPDATE acc SET lastupd = '$lastupd' WHERE id = '$acc_id' ";
		@mysql_query($upd) or die(mysql_error());
		log_hist("66",$acc_id);
		header("location:$this_page");
		exit();
	} else {
		$status = "<p class=\"redbox\">Missing Information! could not create entries.</p>";
	}
}

if(isset($_POST["upd_acc"])) {
	$email = trim($_POST['email']);
	$dept = trim($_POST['dept']);
	$branch = trim($_POST['branch']);
	$lastupd = date('Y-m-d H:i:s');
	$dets = $_POST['det_id'];
	if (!empty($email) AND $branch != "-") {
		$upd_acc_q = "UPDATE acc SET email = '$email', dept_id_fk = '$dept', branch_id_fk = '$branch', lastupd = '$lastupd' WHERE id = '$acc_id';";
		@mysql_query($upd_acc_q) or die(mysql_error());
		foreach ($dets as $key => $acc_det_id) {
			$username 	= strtolower(trim($_POST['upd_uname'][$key]));
			$password 	= trim($_POST['upd_passwd'][$key]);
			$item_type 	= trim($_POST['upd_item'][$key]);
			$lvlID 		= trim($_POST['upd_lvl'][$key]);
			$notes 		= strip_tags(trim($_POST['upd_notes'][$key]));
			$upd_acc_det = "UPDATE acc_det SET username = '$username', password = '$password', al_id_fk = '$lvlID', item_id_fk = '$item_type', notes = '$notes' WHERE id = '$acc_det_id';";
			@mysql_query($upd_acc_det) or die(mysql_error());
		}
		log_hist("66",$acc_id);
		header("location:$this_page");
		exit();
	} else { $status = "<p class=\"redbox\">Missing Information! could not create entries.</p>"; }
}

if(isset($_GET["did"])) {
	$did = $_GET["did"];
	$lastupd = date('Y-m-d H:i:s');
	$del = "UPDATE acc_det SET del = '1' WHERE id = '$did';";
	@mysql_query($del) or die(mysql_error());
	$upd = "UPDATE acc SET lastupd = '$lastupd' WHERE id = '$acc_id';";
	@mysql_query($upd) or die(mysql_error());
	log_hist("66",$did);
	header("location:$this_page");
	exit();
}

function chkDsblAcc($aid){
	$sql = "SELECT a.del FROM acc a WHERE a.dsbl = 1 AND a.id = $aid;";
	$query = @mysql_query($sql) or die(mysql_error());
	$chkArray = mysql_fetch_array($query,MYSQL_ASSOC);
	if($chkArray["del"] == 1)	
	return true;
	else 
	return false;
}

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><?=$status?></td></tr>
	<tr><td>[&nbsp;<a href="./acc_hm.php">Back to the Access Page</a>&nbsp;]</td></tr>
	<tr><td>&nbsp;</td></tr>
<?php if(chkDsblAcc($acc_id)) { ?>
	<tr><td>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td>
			<table border="0" cellpadding="1" cellspacing="1">
				<tr><td width="35">&nbsp;<b>NAME</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td><?=($oldArray['name'])?ucwords($oldArray['name']):"-";?></td></tr>
				<tr><td width="35">&nbsp;<b>EMAIL</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td><?=($oldArray['email'])?strtolower($oldArray['email']):"-";?></td></tr>
				<tr><td width="45">&nbsp;<b>DEPARTMENT</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td><?=($oldArray['dname'])?ucwords($oldArray['dname']):"-";?></td></tr>
				<tr><td width="45">&nbsp;<b>BRANCH</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td><?=($oldArray['bname'])?ucwords($oldArray['bname']):"-";?></td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td colspan="3">
					<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
						<thead>
						<tr valign="middle">
							<td>&nbsp;<b>NO</b>&nbsp;</td>
							<td>&nbsp;<b>USERNAME</b>&nbsp;</td>
							<td>&nbsp;<b>PASSWORD</b>&nbsp;</td>
							<td>&nbsp;<b>TYPE</b>&nbsp;</td>
                            <td>&nbsp;<b>LEVEL/GROUP</b>&nbsp;</td>
							<td>&nbsp;<b>NOTES</b>&nbsp;</td>
						</tr>
						</thead>
						<tbody>
<?php 	$i = 0;$count = 1;
		if(mysql_num_rows($detSQL) != false) {
			while($detArray = mysql_fetch_array($detSQL,MYSQL_ASSOC)) { ?>			
						<tr valign="top" align="left">
							<td>&nbsp;<?=$count?>.&nbsp;</td>
							<td>&nbsp;<?=($detArray['username'])?$detArray['username']:"-";?>&nbsp;</td>
							<td>&nbsp;<?=($detArray['password'])?$detArray['password']:"-";?>&nbsp;</td>
							<td>&nbsp;<?=($detArray['iname'])?ucwords($detArray['iname']):"-";?>&nbsp;</td>
                            <td>&nbsp;<?=($detArray['alname'])?$detArray['alname']:"-";?></td>
							<td>&nbsp;<?=($detArray['notes'])?strip_tags(nl2br($detArray['notes'])):"-";?>&nbsp;</td>
						</tr>
<?php 			$i++;$count++;
			} 
		} else { ?>
					<tr><td colspan="5" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 	} 		?>		</tbody>
				</table>
			</td></tr>
			</table>
		</td></tr>
	</table>
	</td></tr>
<?php }	else {?>
	<tr><td><label><b>ADD ACCESS DETAILS</b></label>			
		<div class="well form">	
            <label><b>USERNAME</b>
			<input type="text" name="uname" size="20" id="uname">
			<script language="JavaScript" type="text/javascript">if(document.getElementById) document.getElementById('uname').focus();</script>
			<label><b>PASSWORD</b>
			<input type="text" name="passwd" size="20">
			<label><b>TYPE</b>&nbsp;<font color="Red">*</font>
			<?=acc_list();?>
             <label><b>LEVEL/GROUP</b>&nbsp;<font color="Red">*</font>
			 <?=acc_lvl();?>
			<label><b>NOTES</b>
			<textarea cols="30" rows="3" name="notes" wrap="virtual"></textarea>
		</div>
	</td></tr>
	<tr><td><input type="submit" name="add_det" class="btn-info btn-small" value="  ADD ACCESS DETAILS  "></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td><label><b>EDIT ACCESS</b></label>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td>
			<table border="0" cellpadding="1" cellspacing="1">
				<tr><td width="35">&nbsp;<b>NAME</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td><?=($oldArray['name'])?ucwords($oldArray['name']):"-";?></td></tr>
				<tr><td width="35">&nbsp;<b>EMAIL</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td><input type="text" name="email" size="30" value="<?=($oldArray['email'])?strtolower($oldArray['email']):"-";?>"/></td></tr>
				<tr><td width="45">&nbsp;<b>DEPARTMENT</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td>
					<select name="dept">
    				<option value="-">---------------------</option>
<?php 
  	while($dept_list_array = mysql_fetch_array($dept_list_SQL,MYSQL_ASSOC)){
 		$compare_dept = ($dept_list_array["id"] == $oldArray["did"])?"SELECTED":"";?>
    <option value="<?=$dept_list_array["id"]?>" <?=$compare_dept?>><?=ucwords($dept_list_array["name"])?></option>
<?php } ?>
 				 	</select>
					</td></tr>
				<tr><td width="45">&nbsp;<b>BRANCH</b>&nbsp;</td>
					<td width="10"><b>:</b></td>
					<td>
					<select name="branch">
    				<option value="-">---------------------</option>
<?php 
  	while($branch_list_array = mysql_fetch_array($branch_list_SQL,MYSQL_ASSOC)){
 		$compare_branch = ($branch_list_array["id"] == $oldArray["bid"])?"SELECTED":"";?>
    <option value="<?=$branch_list_array["id"]?>" <?=$compare_branch?>><?=ucwords($branch_list_array["name"])?></option>
<?php } ?>
 					 </select>
					</td></tr>
				<tr><td colspan="3">&nbsp;</td></tr>
				<tr><td colspan="3">
					<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
						<thead>
						<tr valign="middle"><td>&nbsp;<b>NO</b>&nbsp;</td>
							<td>&nbsp;<b>USERNAME</b>&nbsp;</td>
							<td>&nbsp;<b>PASSWORD</b>&nbsp;</td>
							<td>&nbsp;<b>TYPE</b>&nbsp;</td>
                            <td>&nbsp;<b>LEVEL/GROUP</b>&nbsp;</td>
							<td>&nbsp;<b>NOTES</b>&nbsp;</td>
							<td>&nbsp;<b>CMD</b>&nbsp;</td>
						</tr>
					</thead>
					<tbody>
<?php 	$i = 0;$count = 1;
		if(mysql_num_rows($detSQL) != false) {
			while($detArray = mysql_fetch_array($detSQL,MYSQL_ASSOC)) {?>			
						<tr valign="top" align="left">
							<td>&nbsp;<?=$count?><input type="hidden" name="det_id[<?=$i?>]" size="20" value="<?=($detArray['id'])?$detArray['id']:"-";?>">.&nbsp;</td>
							<td>&nbsp;<input type="text" name="upd_uname[<?=$i?>]" size="20" value="<?=($detArray['username'])?$detArray['username']:"-";?>">&nbsp;</td>
							<td>&nbsp;<input type="text" name="upd_passwd[<?=$i?>]" size="20" value="<?=($detArray['password'])?$detArray['password']:"-";?>">&nbsp;</td>
							<td>&nbsp;<?=acc_edit($i,$detArray["iid"]);?>&nbsp;</td>
                            <td>&nbsp;<?=lvl_edit($i,$detArray["aid"]);?>&nbsp;</td>
							<td>&nbsp;<textarea cols="30" rows="3" name="upd_notes[<?=$i?>]" wrap="virtual"><?=($detArray['notes'])?strip_tags(nl2br($detArray['notes'])):"-";?></textarea>&nbsp;</td>
							<td width="25" align="center"><a title="Delete Access" href="<?=$this_page?>" onclick="return confirmBox(this,'del','access details ID #<?=ucwords($detArray["id"])?>', '<?=$detArray["id"]?>')">
							<img src="<?=IMG_PATH?>delete.png"></a></td>
						</tr>
<?php 			$i++;$count++;
			} 
		} else { ?>
					<tr><td colspan="7" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 	} 		?>		</tbody>
				</table>
			</td></tr>
			<tr><td><input type="submit" name="upd_acc" class="btn-info btn-small" value="  UPDATE ALL ACC. DETAILS  "></td></tr>
			</table>
		</td></tr>
	</table>
	</td></tr>
<?php } ?>
	<tr><td>&nbsp;</td></tr>
	<tr><td>[&nbsp;<a href="./acc_hm.php">Back to the Access Page</a>&nbsp;]</td></tr>
  	<tr><td>&nbsp;</td></tr>
</table>
</form>
<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>