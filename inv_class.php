<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title="Inventory Class Page";
$page_id_left ="13";
$page_id_right ="49";
$category_page = "strx";
chkSecurity($page_id_right);

$cctr_list_query ="SELECT id, description, name FROM inv_class WHERE del = '0' ORDER BY id ASC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($cctr_list_query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";

if (isset($_POST['add_cctr'])){
	$description = strtolower(trim($_POST['description']));
	$name = strtolower(trim($_POST['name']));
	$add_class_query  ="INSERT INTO inv_class (description,name) VALUES ('$description','$name');"; 
	if (!empty($description) AND !empty($name)){
		@mysql_query($add_class_query) or die(mysql_error());
		log_hist("111",$name);
		header("location:$this_page");
		exit();
	}
	else {
		$status ="<p class=\"alert\">Missing information, please check all required data !</p>";
	}
}

if (isset($_POST['update_class'])){
	$nid = trim($_POST['nid']);
	$description = trim($_POST['description']);
	$name = strtolower(trim($_POST['name']));
	$update_description_query = "UPDATE inv_class SET description = '$description', name = '$name' WHERE id ='$nid';";
	@mysql_query($update_description_query) or die(mysql_error());
	log_hist("112",$name);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did']) AND !empty($_GET['did'])){
	$did = trim($_GET['did']);
	$delete_class_query  ="UPDATE inv_class SET del = '1' WHERE id ='$did';";
	@mysql_query($delete_class_query) or die(mysql_error());
	log_hist("113",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title);?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>
			<form method="POST" action="" class="well">
			<table border="0" cellpadding="1" cellspacing="1" >	
            	<tr valign="middle"> 
            		<td>&nbsp;<b>INV. CLASS</b>&nbsp;<font color="Red">*</font></td>
					<td>&nbsp;:</td>
					<td><input type="text" name="description" id="code" size="30">
					<script language="JavaScript" type="text/javascript">
						if(document.getElementById) document.getElementById('code').focus();
					</script></td>
					</td>
					<td>&nbsp;</td>
					<td>&nbsp;<b>NAME</b>&nbsp;<font color="Red">*</font></td>
					<td>&nbsp;:</td>
					<td><input type="text" name="name" size="30"></td>
					<td width="*">&nbsp;<input type="submit" name="add_code" class="btn-info btn-small" value=" ADD "></td>
				</tr></table>
			</form></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<?=$pagingResult->pagingMenu()?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>	
				<tr align="left" valign="top"> 
            	<td width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td>
                <td width="30" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                <td width="30" align="left">&nbsp;<b>CLASS</b>&nbsp;</td>
                <td width="*" align="left">&nbsp;<b>DESCRIPTION</b>&nbsp;</td>
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
					<td width="30">&nbsp;<?=($array["id"])?$array["id"]."#":"-";?>&nbsp;</td>
					<td><input type="text" name="name" size="30" value="<?=($array["name"])?strtolower($array["name"]):"-";?>"></td>
					<td><input type="text" name="description" size="30" value="<?=($array["description"])?$array["description"]:"-";?>"></td>
					<td width="50" align="center" colspan="2">
						<input type="submit" class="btn-info btn-small" name="update_code" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL "></td>
				</tr>
				</form>
<?php 			} else { ?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($array["id"])?$array["id"]."#":"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["name"])?ucwords($array["name"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["description"])?$array["description"]:"-";?>&nbsp;</td>
					<td width="25" align="center"><a title="Edit Code" href="<?=$this_page?>&nid=<?=$array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a title="Delete Code" href="<?=$this_page?>" onclick="return confirmBox(this,'del','\nCost Center <?=$array["description"]?>', '<?=$array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
<?php	 		} $count++; 
			}
		} else {?>
				<tr><td colspan="6" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 	} ?></tbody>
		</table>
				<?=$pagingResult->pagingMenu()?>
		</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>