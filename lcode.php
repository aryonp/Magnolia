<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Log Code Page";
$page_id_left 	= "14";
$page_id_right 	= "41";
$category_page 	= "mgmt";
chkSecurity($page_id_right);

$code_list_query = "SELECT id, notes FROM log_code WHERE del = '0' ORDER BY id ASC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($code_list_query);
$pagingResult->paginate();

$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "";
if (isset($_POST['add_code'])){
	$notes = strtolower(trim($_POST['code']));
	$add_code_query  ="INSERT INTO log_code VALUES (NULL,'$notes','0');"; 
	if (!empty($notes)){
		@mysql_query($add_code_query) or die(mysql_error());
		log_hist("35",$notes);
		header("location:$this_page");
		exit();
	}
	else {
		$status ="<p class=\"yellowbox\">You can't create an empty code !</p>";
	}
}

if (isset($_POST['update_code'])){
	$nid = trim($_POST['nid']);
	$notes = strtolower(trim($_POST['notes']));
	$update_code_query = "UPDATE log_code SET notes = '$notes' WHERE id ='".$nid."';";
	@mysql_query($update_code_query) or die(mysql_error());
	log_hist("36",$nid);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$delete_code_query  ="UPDATE log_code SET del = '1' WHERE id ='".$did."';";
	@mysql_query($delete_code_query) or die(mysql_error());
	log_hist("37",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>USER LOG CODE</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<form method="POST" action="" class="well form-inline">
			<label><b>NOTES</b>&nbsp;<font color="Red">*</font>:</label>
			<input type="text" name="code" id="code" size="80">
					<script language="JavaScript" type="text/javascript">
						if(document.getElementById) document.getElementById('code').focus();
					</script>
					&nbsp;
			<input type="submit" name="add_code" class="btn-info btn-small" value=" ADD ">
			</form></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<?=$pagingResult->pagingMenu()?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr><th width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td>
                <th width="30" align="left">&nbsp;<b>CODE</b>&nbsp;</td>
                <th width="*" align="left">&nbsp;<b>NOTES</b>&nbsp;</td>
                <th width="50" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
			</tr>
			</thead>
			<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($code_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) { 
				if (isset($_GET['nid']) && $_GET['nid'] == $code_list_array["id"]) {?>
				<form method="POST" action="">
				<input type="hidden" name="nid" value="<?=$code_list_array["id"]?>">
				<tr bgcolor="#ffcc99" align="left" valign="top">
					<td width="25">&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($code_list_array["id"])?$code_list_array["id"]."#":"-";?>&nbsp;</td>
					<td><input type="text" name="notes" size="30" value="<?=($code_list_array["notes"])?strtoupper($code_list_array["notes"]):"-";?>">&nbsp;</td>
					<td width="50" align="center" colspan="2">&nbsp;
					<input type="submit" class="btn-info btn-small" name="update_code" value="UPDATE">&nbsp;
					<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL ">&nbsp;</td>
				</tr>
				</form>
<?php 			} else { ?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($code_list_array["id"])?$code_list_array["id"]."#":"-";?>&nbsp;</td>
					<td>&nbsp;<?=($code_list_array["notes"])?strtoupper($code_list_array["notes"]):"-";?>&nbsp;</td>
					<td width="25" align="center"><a title="Edit Code" href="<?=$this_page?>&nid=<?=$code_list_array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a title="Delete Code" href="<?=$this_page?>" onclick="return confirmBox(this,'del','\nLog Code ID #<?=$code_list_array["id"]?>', '<?=$code_list_array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
<?php	 		} $count++; 
			}
		} else {?>
				<tr><td colspan="5" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 	} ?>
		</tbody>
			</table>
				<?=$pagingResult->pagingMenu()?>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>