<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Department Page";
$page_id_left 	= "13";
$page_id_right 	= "30";
$category_page 	= "strx";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$dept_list_query 	= "SELECT id, name, pic, pemail FROM departments WHERE del = '0' ORDER BY name ASC ";
$pagingResult 		= new Pagination();
$pagingResult->setPageQuery($dept_list_query);
$pagingResult->paginate();
$this_page 			= $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "";

if (isset($_POST['add_dept'])){
	$name1 		= trim($_POST['name1']);
	$pic1 		= trim($_POST['pic1']);
	$pemail1 	= trim($_POST['pemail1']);
	$add_dept_query  = "INSERT INTO departments (name,pic,pemail) VALUES ('$name1','$pic1','$pemail1');"; 
	if (!empty($name1)){
		@mysql_query($add_dept_query) or die(mysql_error());
		log_hist(46,$name1);
		header("location:$this_page");
		exit();
	}
	else {
		$status ="<p class=\"alert\">You can't insert an empty department !</p>";
	}
}

if (isset($_POST['update_dept'])){
	$nid 	= trim($_POST['nid']);
	$name	= trim($_POST['name']);
	$pic 	= trim($_POST['pic']);
	$pemail = trim($_POST['pemail']);
	$update_dept_query  ="UPDATE departments SET name = '$name', pic = '$pic', pemail = '$pemail' WHERE id ='$nid';";
	@mysql_query($update_dept_query) or die(mysql_error());
	log_hist(47,$name);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did 				= trim($_GET['did']);
	$delete_dept_query  = "UPDATE departments SET del = '1' WHERE id ='$did';";
	@mysql_query($delete_dept_query) or die(mysql_error());
	log_hist(48, $did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>DEPARTMENTS LIST</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<form method="POST" action="" class="well">
			<table border="0" cellpadding="1" cellspacing="1" >	
            	<tr valign="middle"> 
					<td><b>DEPT.</b>&nbsp;<font color="Red">*</font></td>
					<td>&nbsp;:</td>
					<td><input type="text" name="name1" id="dept" size="25">
							<script language="JavaScript" type="text/javascript">
									if(document.getElementById) document.getElementById('dept').focus();
							</script>
					</td>
					<td>&nbsp;</td>
					<td><b>PIC</b>&nbsp;<font color="Red">*</font></td>
					<td>&nbsp;:</td>
					<td><input type="text" name="pic1" size="25"></td>
					<td>&nbsp;</td>
					<td><b>EMAIL</b>&nbsp;<font color="Red">*</font></td>
					<td>&nbsp;:</td>
					<td><input type="text" name="pemail1" size="25"></td>
					<td width="*">&nbsp;<input type="submit" name="add_dept" class="btn-info btn-small" value="ADD"></td>
				</tr></table>
			</form></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<?=$pagingResult->pagingMenu()?>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
				<tr align="left" valign="middle"> 
            		<td width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td>
                	<td width="45" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>NAME</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>PIC</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>EMAIL</b>&nbsp;</td>
                 	<td width="*" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
<?php if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($dept_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $dept_list_array["id"]) {?>
				<form method="POST" action="">
				<input type="hidden" name="nid" value="<?=$dept_list_array["id"]?>">
				<tr bgcolor="#ffcc99" align="left" >
					<td width="25">&nbsp;<?=$count?>.</td>
					<td>&nbsp;<?=$dept_list_array["id"]?></td>
					<td>&nbsp;<input type="text" name="name" size="30" value="<?=($dept_list_array["name"])?ucwords($dept_list_array["name"]):"";?>">&nbsp;</td>
					<td>&nbsp;<input type="text" name="pic" size="30" value="<?=($dept_list_array["pic"])?ucwords($dept_list_array["pic"]):"";?>">&nbsp;</td>
					<td>&nbsp;<input type="text" name="pemail" size="30" value="<?=($dept_list_array["pemail"])?$dept_list_array["pemail"]:"";?>">&nbsp;</td>
					<td width="*" align="center" colspan="2">
						<input type="submit" class="btn-info btn-small" name="update_dept" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL ">&nbsp;</td>
				</tr>
				</form>
<?php 		} else { ?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.</td>
					<td>&nbsp;<?=($dept_list_array["id"])?$dept_list_array["id"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($dept_list_array["name"])?$dept_list_array["name"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($dept_list_array["pic"])?ucwords($dept_list_array["pic"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($dept_list_array["pemail"])?$dept_list_array["pemail"]:"-";?>&nbsp;</td>
					<td width="25" align="center"><a href="<?=$this_page?>&nid=<?=$dept_list_array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a href="<?=$this_page?>" onclick="return confirmBox(this, 'del','department <?=$dept_list_array["name"]?>', '<?=$dept_list_array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
<?php	 		} $count++; 
			}
		} else {
?>			<tr><td colspan="5" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 	} ?> </tbody>
			</table>
				<?=$pagingResult->pagingMenu()?>
			</fieldset>
		</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>