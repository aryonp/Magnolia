<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title="Vendor Criteria Page";
$page_id_left ="13";
$page_id_right ="31";
$category_page = "strx";
chkSecurity($page_id_right);

$crit_list_query ="SELECT id, name FROM ev_crit WHERE del = '0' ORDER BY id ASC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($crit_list_query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "";

if (isset($_POST['add_crit'])){
	$name = trim($_POST['name']);
	$add_crit_query  ="INSERT INTO ev_crit (name) VALUES ('$name');"; 
	
	if (!empty($name)){
		@mysql_query($add_crit_query) or die(mysql_error());
		$crit_id = mysql_insert_id();
		log_hist("49",$crit_id);
		header("location:$this_page");
		exit();
	}
	
	else {
		$status ="<p class=\"alert\">You can't insert an empty criteria !</p>";
	}
}

if (isset($_POST['update_crit'])){
	$nid = trim($_POST['nid']);
	$name = trim($_POST['name']);
	$update_crit_query  ="UPDATE ev_crit SET name='$name' WHERE id ='$nid';";
	@mysql_query($update_crit_query) or die(mysql_error());
	log_hist("50",$nid);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$delete_crit_query  ="UPDATE ev_crit SET del='1' WHERE id ='$did';";
	@mysql_query($delete_crit_query) or die(mysql_error());
	log_hist("51",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>CRITERIA LIST</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<form method="POST" action="" class="well form-inline">
				<label><b>MESSAGE</b>&nbsp;<font color="Red">*</font>:</label>
				<input type="text" name="name" id="name" size="80">
					<script language="JavaScript" type="text/javascript">
						if(document.getElementById) document.getElementById('name').focus();
					</script>
				<input type="submit" name="add_crit" class="btn-info btn-small" value="  ADD  "></td>
			</form>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<?=$pagingResult->pagingMenu()?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>	
				<tr align="left" valign="top"> 
            		<td width="25" align="left">&nbsp;<b>NO</b></td>
                 	<td width="45" align="left">&nbsp;<b>ID</b></td>
                 	<td width="*" align="left">&nbsp;<b>MESSAGE</b></td>
                 	<td width="50" colspan="2" align="center">&nbsp;<b>CMD</b></td>
				</tr>
				</thead>
				<tbody>
<?php 	if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($crit_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $crit_list_array["id"]) {?>
					<form method="POST" action="">
					<input type="hidden" name="nid" value="<?=$crit_list_array["id"]?>">
					<tr bgcolor="#ffcc99" align="left" valign="top">
						<td width="25">&nbsp;<?=$count?>.</td>
						<td>&nbsp;#<?=$crit_list_array["id"]?></td>
						<td>&nbsp;<input type="text" name="name" size="80" value="<?=ucwords($crit_list_array["name"])?>"></td>
						<td width="50" align="center" colspan="2">
							<input type="submit" class="btn-info btn-small" name="update_crit" value="UPDATE">&nbsp;
							<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL "></td>
					</tr>
					</form>
<?php 			} else { ?>
					<tr align="left" valign="top">
						<td>&nbsp;<?=$count?>.</td>
						<td>&nbsp;#<?=$crit_list_array["id"]?> </td>
						<td>&nbsp;<?=$crit_list_array["name"]?> </td>
						<td width="25">&nbsp;<a title="Edit Criteria" href="<?=$this_page?>&nid=<?=$crit_list_array["id"]?>">
							<img src="<?=IMG_PATH?>edit.png"></a>&nbsp;</td>
						<td width="25">&nbsp;<a title="Delete Criteria" href="<?=$this_page?>" onclick="return confirmBox(this,'del', '\nEvaluation criteria ID #<?=$crit_list_array["id"]?>', '<?=$crit_list_array["id"]?>')">
							<img src="<?=IMG_PATH?>delete.png"></a>&nbsp;</td>
					</tr>
<?php	 		} $count++; 
			}
		} else {?>
				<tr><td colspan="9" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php } ?></tbody>
		</table>
				<?=$pagingResult->pagingMenu()?>
		</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>