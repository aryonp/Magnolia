<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title 	= "MOTD Page";
$page_id_left 	= "14";
$page_id_right  = "37";
$category_page 	= "mgmt";
chkSecurity($page_id_right);

$motd_list_query 	= "SELECT id, message FROM motd WHERE del = 0 ORDER BY id ASC ";
$pagingResult 		= new Pagination();
$pagingResult->setPageQuery($motd_list_query);
$pagingResult->paginate();

$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "";

if (isset($_POST['add_motd'])){
	
	$message = strtolower(trim($_POST['msg']));
	$add_motd_query  ="INSERT INTO motd VALUES (NULL,'$message', '0');"; 
	
	if (!empty($message)){
		@mysql_query($add_motd_query) or die(mysql_error());
		log_hist(23,$message);
		header("location:$this_page");
		exit();
	}
	
	else {
		$status ="<br/><p class=\"yellowbox\">You can't insert an empty message !</p><br/>";
	}
	
}

if (isset($_POST['update_motd'])){
	$nid = trim($_POST['nid']);
	$message = trim($_POST['msg']);
	$update_motd_query  ="UPDATE motd SET message = '$message' WHERE id ='$nid';";
	@mysql_query($update_motd_query) or die(mysql_error());
	log_hist(24,$nid);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$delete_motd_query  ="UPDATE motd SET del = '1' WHERE id ='$did';";
	@mysql_query($delete_motd_query) or die(mysql_error());
	log_hist(25,$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>MOTD : MESSAGE OF THE DAY LIST</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><form method="POST" action="" class="well form-inline">
			<label><b>MESSAGE</b>&nbsp;<font color="Red">*</font>:</label>
			<input type="text" name="msg" size="80" id="msg">
				<script language="JavaScript" type="text/javascript">
					if(document.getElementById) document.getElementById('msg').focus();
				</script>
			<input type="submit" name="add_motd" class="btn-info btn-small" value=" ADD MOTD ">
			</form></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<?=$pagingResult->pagingMenu()?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            <tr align="left" valign="middle">
            	<th width="25" align="left">&nbsp;<b>NO</b></td>
                <th width="*" align="left">&nbsp;<b>MESSAGE</b></td>
                <th width="50" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
			</tr>
			</thead>
			<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($motd_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $motd_list_array["id"]) {?>
				<form method="POST" action="">
				<input type="hidden" name="nid" value="<?=$motd_list_array["id"]?>">
				<tr bgcolor="#ffcc99" align="left" valign="top">
					<td width="25">&nbsp;<?=$count?>.</td>
					<td><textarea cols="60" rows="2" name="msg" wrap="virtual"><?=ucwords($motd_list_array["message"])?></textarea></td>
					<td width="50" align="center" colspan="2">&nbsp;
						<input type="submit" class="btn-info btn-small" name="update_motd" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL ">&nbsp;</td>
				</tr>
				</form>
	<?php 		
			} else { ?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.</td>
					<td>&nbsp;<?=ucwords($motd_list_array["message"])?> </td>
					<td width="25" align="center"><a title="Edit Message" href="<?=$this_page?>&nid=<?=$motd_list_array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a title="Delete Message" href="<?=$this_page?>" onclick="return confirmBox(this, 'del','\nMessage ID #<?=$motd_list_array["id"]?>', '<?=$motd_list_array["id"]?>')">
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
		</td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>