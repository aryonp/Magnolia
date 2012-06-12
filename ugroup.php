<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title="User Group Page";
$page_id_left ="14";
$page_id_right ="36";
$category_page = "mgmt";
chkSecurity($page_id_right);

$query ="SELECT id, name FROM user_group WHERE del = '0' ORDER BY id ASC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "";

if (isset($_POST['add_group'])){
	$group = strtolower(trim($_POST['group']));
	$add_group_query  ="INSERT INTO user_group VALUES (NULL,'$group', '0');"; 
	
	if (!empty($group)){
		@mysql_query($add_group_query) or die(mysql_error());
		log_hist("20",$group);
		header("location:$this_page");
		exit();
	}
	
	else {
		$status ="<p class=\"alert\">You can't create an empty user group !</p>";
	}
	
}

if (isset($_POST['update_group'])){
	$nid = trim($_POST['nid']);
	$group = strtolower(trim($_POST['group']));
	$update_group_query = "UPDATE user_group SET name = '$group' WHERE id ='$nid';";
	@mysql_query($update_group_query) or die(mysql_error());
	log_hist("21",$group);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$delete_group_query  ="UPDATE user_group SET del = '1' WHERE id ='$did';";
	@mysql_query($delete_group_query) or die(mysql_error());
	log_hist("22",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>USER GROUP</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td ><?=$status?></td></tr>
		<tr><td >&nbsp;</td></tr>
		<tr><td >
			<form method="POST" action="" class="well form-inline">
			<label><b>NAME</b>&nbsp;<font color="Red">*</font>:</label>
			<input type="text" name="group" size="80" id="group">
			<script language="JavaScript" type="text/javascript">if(document.getElementById) document.getElementById('group').focus();</script>
			<input type="submit" name="add_group" class="btn-info btn-small" value=" ADD ">
			</form>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<fieldset><legend>GROUP LIST</legend><br />
		<?=$pagingResult->pagingMenu()?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr><td width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td>
					<td width="*" align="left">&nbsp;<b>GROUP</b>&nbsp;</td>
					<td width="50" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
			</tr>
			</thead>
			<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($group_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $group_list_array["id"]) {?>
				<form method="POST" action="">
				<input type="hidden" name="nid" value="<?=$group_list_array["id"]?>">
				<tr bgcolor="#ffcc99" align="left" valign="top">
					<td width="25">&nbsp;<?=$count?>.</td>
					<td><input type="text" name="group" size="30" value="<?=($group_list_array["name"])?ucwords($group_list_array["name"]):"-";?>"></td>
					<td width="*" align="center" colspan="2">
						<input type="submit" class="btn-info btn-small" name="update_group" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL "></td>
				</tr>
				</form>
	<?php 		
			} else { ?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.</td>
					<td>&nbsp;<?=($group_list_array["name"])?ucwords($group_list_array["name"]):"-";?> </td>
					<td width="25" align="center"><a title="Edit Group" href="<?=$this_page?>&nid=<?=$group_list_array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a title="Delete Group" href="<?=$this_page?>" onclick="return confirmBox(this, 'del','user group <?=ucwords($group_list_array["name"])?>', '<?=$group_list_array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
			<?php	 
				} $count++; 
			}
		} else {?>
				<tr><td colspan="5" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php } ?>
				</tbody>
			</table>
				<?=$pagingResult->pagingMenu()?>
			</fieldset>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>