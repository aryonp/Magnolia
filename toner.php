<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title="Toner";
$page_id_left ="13";
$page_id_right ="55";
$category_page = "strx";
chkSecurity($page_id_right);

$status = "&nbsp;";

$toner_q = "SELECT t.id, t.tName FROM toner t WHERE t.del = '0' ORDER BY tName ASC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($toner_q );
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";
if (isset($_POST['addTnr'])){
	$tName1 = trim($_POST['tName1']);
	$inpBy = $_SESSION['uid'];
	$inpDate = date('Y-m-d H:i:s');
	$addTnr = "INSERT INTO toner (tName,inputBy,inputDate,updBy,updDate) VALUES ('$tName1','$inpBy','$inpDate','$inpBy','$inpDate');"; 
	$chkTnr_q = "SELECT t.id FROM toner t WHERE t.del = '0' AND t.tName = '$tName1';";
	$chkTnr_SQL = @mysql_query($chkTnr_q) or die(mysql_error());
	if (!empty($tName1)){
		if (mysql_num_rows($chkTnr_SQL) >= 1) {
			$status ="<p class=\"yellowbox\">Double input, please check again!</p>";
		} 
		else {
			@mysql_query($addTnr) or die(mysql_error());
			log_hist(129,$pName);
			header("location:$this_page");
			exit();
		}
	}
	else {
		$status ="<p class=\"alert\">You can't insert an empty message !</p>";
	}
}
if (isset($_POST['updTnr'])){
	$nid = trim($_POST['nid']);
	$tName2 = trim($_POST['tName2']);
	$updBy = $_SESSION['uid'];
	$updDate = date('Y-m-d H:i:s');
	$updTnr = "UPDATE toner SET tName ='$tName2', updBy = '$updBy', updDate = '$updDate' WHERE id ='$nid';";
	@mysql_query($updTnr) or die(mysql_error());
	log_hist(130,$tName2);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$updBy = $_SESSION['uid'];
	$updDate = date('Y-m-d H:i:s');
	$delTnr = "UPDATE toner SET del = '1', updBy = '$updBy', updDate = '$updDate' WHERE id ='$did';";
	@mysql_query($delTnr) or die(mysql_error());
	log_hist(131,$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php'; ?>             			
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title);?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>
			<div class="well">
			<table border="0" cellpadding="1" cellspacing="1">	
            	<tr valign="top"> 
					<td><b>TONER</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" name="tName1" id="tName1" size="20">
						<script language="JavaScript" type="text/javascript">
							if(document.getElementById) document.getElementById('tName1').focus();
						</script>
					</td>
					<td>&nbsp;</td>
					<td><b>QTY.</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" class="input-small" name="qty1" maxlength="3" size="5"></td>
				</tr>
			</table></div>
		</td></tr>
		<tr><td><input type="submit" name="addTnr" class="btn-info btn-small" value="  ADD TONER  "></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<?=$pagingResult->pagingMenu()?>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
            	<tr valign="middle"> 
					<td width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>TONER</b>&nbsp;</td>
                 	<td width="50" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $array["id"]) {?>
					<form method="POST" action="">
					<input type="hidden" name="nid" value="<?=$array["id"]?>">
					<tr bgcolor="#ffcc99" align="left" valign="top">
						<td width="25">&nbsp;<?=$count?>.&nbsp;</td>
						<td width="*"><input type="text" name="tName2" size="20" value="<?=($array["tName"])?$array["tName"]:"-";?>"></td>
						<td width="50" align="center" colspan="2">
						<input type="submit" class="btn-info btn-small" name="updTnr" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL ">&nbsp;
						</td>
					</tr>
					</form>
<?php 			} else { ?>
					<tr align="left" valign="top">
						<td>&nbsp;<?=$count?>.&nbsp;</td>
						<td>&nbsp;<?=($array["tName"])?$array["tName"]:"-";?>&nbsp;</td>
						<td width="25">&nbsp;<a title="Edit Toner" href="<?=$this_page?>&nid=<?=$array["id"]?>">
							<img src="<?=IMG_PATH?>edit.png"></a>&nbsp;</td>
						<td width="25">&nbsp;<a title="Delete Toner" href="<?=$this_page?>" onclick="return confirmBox(this,'del', '\ntoner <?=$array["tName"]?>', '<?=$array["id"]?>')">
							<img src="<?=IMG_PATH?>delete.png"></a>&nbsp;</td>
					</tr>
			<?php	 
				} $count++; 
			}
		} else {?>
				<tr><td colspan="4" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php } ?> 
				</tbody>
			</table>
			<?=$pagingResult->pagingMenu()?>
		</td></tr>
	</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>