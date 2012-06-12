<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Access Level";
$page_id_left 	= "13";
$page_id_right 	= "56";
$category_page 	= "strx";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$status = "&nbsp;";
$level_q = "SELECT al.id, al.lName FROM acc_level al WHERE al.del = '0' ORDER BY al.lName ASC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($level_q);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

if (isset($_POST['add_level'])){
	$lName = trim($_POST['lName']);
	$inpBy = $_SESSION['uid'];
	$inpDate = date('Y-m-d H:i:s');
	$addLvl = "INSERT INTO acc_level (lName,inpBy,inpDate,updBy,updDate) VALUES ('$lName','$inpBy','$inpDate','$inpBy','$inpDate');"; 
	$chkLvl_q = "SELECT al.id FROM acc_level al WHERE al.del = '0' AND al.lName = '$lName';";
	$chkLvl_SQL = @mysql_query($chkLvl_q) or die(mysql_error());
	if (!empty($lName)){
		if (mysql_num_rows($chkLvl_SQL) >= 1) {
			$status ="<p class=\"yellowbox\">Double input, please check again!</p>";
		}
		else {
			@mysql_query($addLvl) or die(mysql_error());
			log_hist("132",$lName);
			header("location:$this_page");
			exit();
		}
	}
	else {
		$status ="<p class=\"yellowbox\">You can't insert an empty message !</p>";
	}
}

if (isset($_POST['updLvl'])){
	$nid = trim($_POST['nid']);
	$lName = trim($_POST['lName2']);
	$updBy = $_SESSION['uid'];
	$updDate = date('Y-m-d H:i:s');
	$updLevel = "UPDATE acc_level SET lName ='$lName', updBy = '$updBy', updDate = '$updDate' WHERE id ='$nid';";
	@mysql_query($updLevel) or die(mysql_error());
	log_hist("133",$lName);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$updBy = $_SESSION['uid'];
	$updDate = date('Y-m-d H:i:s');
	$delLevel = "UPDATE acc_level SET del = '1', updBy = '$updBy', updDate = '$updDate' WHERE id ='$did';";
	@mysql_query($delLevel) or die(mysql_error());
	log_hist("134",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php'; ?>             			
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td><form method="POST" action="" class="well">
			<table border="0" cellpadding="1" cellspacing="1" >	
            	<tr  valign="middle"> 
					<td><b>LEVEL</b>&nbsp;<font color="Red">*</font></td>
					<td>&nbsp;:</td>
					<td><input type="text" name="lName" size="30" id="level" value="[LEVELGROUP] LEVELNAME">
					<script language="JavaScript" type="text/javascript">
						if(document.getElementById) document.getElementById('level').focus();
					</script></td>
					<td width="*">&nbsp;<input type="submit" name="add_level" class="btn-info btn-small" value=" ADD NEW LEVEL "></td>
				</tr></table></form></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<?=$pagingResult->pagingMenu()?>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            <tr valign="middle">
            	<td width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td>
                <td width="*" align="left">&nbsp;<b>ACCESS LEVEL</b>&nbsp;</td>
                <td width="50" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
			</tr>
			</thead>
			<tbody>
<?php if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $array["id"]) {?>
		<form method="POST" action="">
		<input type="hidden" name="nid" value="<?=$array["id"]?>">
		<tr bgcolor="#ffcc99" align="left" valign="top">
			<td width="25">&nbsp;<?=$count?>.&nbsp;</td>
			<td><input type="text" name="lName2" size="50" value="<?=($array["lName"])?$array["lName"]:"-";?>"/></td>
			<td width="50" align="center" colspan="2">&nbsp;<input type="submit" class="btn-info btn-small" name="updLvl" value=" UPDATE ">&nbsp;<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL ">&nbsp;</td>
		</tr>
		</form>
<?php 		} else { ?>
		<tr align="left" valign="top">
			<td>&nbsp;<?=$count?>.</td>
			<td>&nbsp;<?=($array["lName"])?$array["lName"]:"-";?> </td>
			<td width="25" align="center"><a title="Edit Level" href="<?=$this_page?>&nid=<?=$array["id"]?>">
				<img src="<?=IMG_PATH?>edit.png"></a></td>
			<td width="25" align="center"><a title="Delete Level" href="<?=$this_page?>" onclick="return confirmBox(this, 'del','\nLevel <?=$array["lName"]?>', '<?=$array["id"]?>')">
				<img src="<?=IMG_PATH?>delete.png"></a></td>
		</tr>
<?php			} $count++; 
			}
		} else {?>
		<tr><td colspan="5" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php } ?></tbody>
		</table>
		<?=$pagingResult->pagingMenu()?>
		</fieldset>
		</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>