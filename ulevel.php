<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();
$page_title		= "User Level Page";
$page_id_left 	= "14";
$page_id_right 	= "35";
$category_page 	= "mgmt";
chkSecurity($page_id_right);

$query ="SELECT id, name, hidden FROM user_level WHERE del = '0' ORDER BY id ASC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();

$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";

if (isset($_POST['add_level'])){
	$level = strip_tags(trim($_POST['level']));
	$hidden = (is_numeric($_POST['hidden']) OR !empty($_POST['hidden']))?$_POST['hidden']:0;
	$add_level_query  ="INSERT INTO user_level VALUES (NULL,'$level','$hidden', '0');"; 
	if (!empty($level)){
		@mysql_query($add_level_query) or die(mysql_error());
		log_hist("17",$level);
		header("location:$this_page");
		exit();
	}
	else {
		$status ="<p class=\"alert\">You can't create an empty user level !</p>";
	}
}
if (isset($_POST['update_level'])){
	$nid = trim($_POST['nid']);
	$level = strip_tags(trim($_POST['level']));
	$hidden = (is_numeric($_POST['hidden']) OR !empty($_POST['hidden']))?$_POST['hidden']:0;
	$update_level_query = "UPDATE user_level SET name = '$level', hidden = '$hidden' WHERE id ='".$nid."';";
	@mysql_query($update_level_query) or die(mysql_error());
	log_hist("18",$level);
	header("location:$this_page");
	exit();
}
if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$delete_level_query  ="UPDATE user_level SET del = '1' WHERE id ='$did';";
	@mysql_query($delete_level_query) or die(mysql_error());
	log_hist("19",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>USER LEVEL</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>
			<form method="POST" action="" class="span8 well">
			<table border="0" cellpadding="1" cellspacing="1" >	
            	<tr  valign="middle"> 
					<td><b>NAME</b>&nbsp;<font color="Red">*</font></td>
					<td>&nbsp;:</td>
					<td><input type="text" name="level" id="level" size="80">
					<script language="JavaScript" type="text/javascript">
						if(document.getElementById) document.getElementById('level').focus();
					</script></td>
					<td width="*">&nbsp;<input type="submit" name="add_level" class="btn-info btn-small" value=" ADD "></td>
				</tr></table>
			</form></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<?=$pagingResult->pagingMenu()?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr><th width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td>
					<th width="*" align="left">&nbsp;<b>LEVEL</b>&nbsp;</td>
					<th width="35" align="left">&nbsp;<b>HIDDEN</b>&nbsp;</td>
					<th width="50" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
<?php 	if($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($level_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $level_list_array["id"]) {?>
				<form method="POST" action="">
				<input type="hidden" name="nid" value="<?=$level_list_array["id"]?>">
				<tr bgcolor="#ffcc99" align="left" valign="top">
					<td width="25">&nbsp;<?=$count?>.</td>
					<td><input type="text" name="level" size="30" value="<?=($level_list_array["name"])?ucwords($level_list_array["name"]):"-";?>"></td>
					<td><input type="text" name="hidden" size="5" value="<?=$level_list_array["hidden"]?>"></td>
					<td width="*" align="center" colspan="2">
						<input type="submit" class="btn-info btn-small" name="update_level" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL "></td>
				</tr>
				</form>
<?php 		} else { ?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.</td>
					<td>&nbsp;<?=($level_list_array["name"])?ucwords($level_list_array["name"]):"-";?> </td>
					<td>&nbsp;<?=($level_list_array["hidden"] == '1')?"YES":"NO";?></td>
					<td width="25" align="center"><a title="Edit Level" href="<?=$this_page?>&nid=<?=$level_list_array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a title="Delete Level" href="<?=$this_page?>" onclick="return confirmBox(this, 'del', 'user level <?=ucwords($level_list_array["name"])?>', '<?=$level_list_array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
<?php	 		} $count++; 
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
