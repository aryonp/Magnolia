<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title="Printer";
$page_id_left ="13";
$page_id_right ="54";
$category_page = "strx";
chkSecurity($page_id_right);

$status = "&nbsp;";
$printer_q = "SELECT p.id, p.pName FROM printer p WHERE p.del = '0' ORDER BY p.pName ASC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($printer_q);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

if (isset($_POST['add_printer'])){
	$pName = strtoupper(trim($_POST['pName']));
	$inpBy = $_SESSION['uid'];
	$inpDate = date('Y-m-d H:i:s');
	$addPrinter = "INSERT INTO printer (pName,inputBy,inputDate,updBy,updDate) VALUES ('$pName','$inpBy','$inpDate','$inpBy','$inpDate');"; 
	$chkPrint_q = "SELECT p.id FROM printer p WHERE p.del = '0' AND p.pName = '$pName';";
	$chkPrint_SQL = @mysql_query($chkPrint_q) or die(mysql_error());
	
	if (!empty($pName)){
		
		if (mysql_num_rows($chkPrint_SQL) >= 1) {
			$status ="<p class=\"yellowbox\">Double input, please check again!</p>";
		}
		
		else {
			@mysql_query($addPrinter) or die(mysql_error());
			log_hist("126",$pName);
			header("location:$this_page");
			exit();
		}
		
	}
	
	else { $status ="<p class=\"yellowbox\">You can't insert an empty message !</p>"; }
}

if (isset($_POST['updPrint'])){
	$nid = trim($_POST['nid']);
	$pName = trim($_POST['pName2']);
	$updBy = $_SESSION['uid'];
	$updDate = date('Y-m-d H:i:s');
	$updPrint = "UPDATE printer SET pName ='$pName', updBy = '$updBy', updDate = '$updDate' WHERE id ='$nid';";
	@mysql_query($updPrint) or die(mysql_error());
	log_hist("127",$pName);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$updBy = $_SESSION['uid'];
	$updDate = date('Y-m-d H:i:s');
	$delPrint = "UPDATE printer SET del = '1', updBy = '$updBy', updDate = '$updDate' WHERE id ='$did';";
	@mysql_query($delPrint) or die(mysql_error());
	log_hist("128",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php'; 
?>             			
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>
			<form method="POST" action="" class="well">
			<label><b>PRINTER</b>&nbsp;<font color="Red">*</font>:</label>
			<input type="text" name="pName" size="30" id="printer">
			<script language="JavaScript" type="text/javascript">if(document.getElementById) document.getElementById('printer').focus();</script>
			<input type="submit" name="add_printer" class="btn-info btn-small" value=" ADD PRINTER ">
			</form>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<?=$pagingResult->pagingMenu()?>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
            	<tr valign="middle">
					<td width="25" align="left">&nbsp;<b>NO</b></td>
					<td width="*" align="left">&nbsp;<b>PRINTER</b></td>
					<td width="50" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $array["id"]) { ?>
		<form method="POST" action="">
		<input type="hidden" name="nid" value="<?=$array["id"]?>">
		<tr bgcolor="#ffcc99" align="left" valign="top">
			<td width="25">&nbsp;<?=$count?>.&nbsp;</td>
			<td><input type="text" name="pName2" value="<?=($array["pName"])?$array["pName"]:"-";?>"/></td>
			<td width="50" align="center" colspan="2">
				<input type="submit" class="btn-info btn-small" name="updPrint" value="UPDATE">&nbsp;
				<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL ">&nbsp;</td>
		</tr>
		</form>
<?php 		} else { ?>
		<tr align="left" valign="top">
			<td>&nbsp;<?=$count?>.</td>
			<td>&nbsp;<?=($array["pName"])?$array["pName"]:"-";?> </td>
			<td width="25" align="center"><a title="Edit Printer" href="<?=$this_page?>&nid=<?=$array["id"]?>">
				<img src="<?=IMG_PATH?>edit.png"></a></td>
			<td width="25" align="center"><a title="Delete Printer" href="<?=$this_page?>" onclick="return confirmBox(this, 'del','\nPrinter <?=$array["pName"]?>', '<?=$array["id"]?>')">
				<img src="<?=IMG_PATH?>delete.png"></a></td>
		</tr>
<?php			} $count++; 
			}
		} else {?>
		<tr><td colspan="5" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php } ?></tbody>
		</table>
		<?=$pagingResult->pagingMenu()?>
		</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>