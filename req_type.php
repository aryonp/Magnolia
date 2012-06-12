<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Request Type";
$page_id_left 	= "13";
$page_id_right 	= "58";
$category_page 	= "strx";
chkSecurity($page_id_right);

$query = "SELECT rt.id, rt.name FROM req_type rt WHERE rt.del = '0' ORDER BY rt.id ASC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "";

if (isset($_POST['add_rt'])){
	$rt1 	= strtolower(trim($_POST['rt1']));
	$input1	= $_SESSION['uid'];
	$inpD1	= date('Y-m-d H:i:s');
	$add_q  = "INSERT INTO req_type (name,inputBy,inputDate,updBy,updDate) VALUES ('$itcat1','$rt1','$inpD1','$rt1','$inpD1');"; 
	
	if (!empty($rt1)){
		@mysql_query($add_q) or die(mysql_error());
		log_hist(143,$rt1);
		header("location:$this_page");
		exit();
	}
	
	else {
		$status ="<p class=\"alert\">Missing parameter! Please check your input again.</p>";
	}
	
}

if (isset($_POST['update_rt'])){
	$nid 	= trim($_POST['nid']);
	$rt2 	= strtolower(trim($_POST['rt2']));
	$input2	= $_SESSION['uid'];
	$inpD2	= date('Y-m-d H:i:s');
	$upd_q 	= "UPDATE req_type SET name = '$rt2', updBy = '$input2', updDate = '$inpD2' WHERE id ='$nid';";
	@mysql_query($upd_q) or die(mysql_error());
	log_hist(144,$nid);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did 	= trim($_GET['did']);
	$input3	= $_SESSION['uid'];
	$inpD3	= date('Y-m-d H:i:s');
	$del_q 	= "UPDATE req_type SET del = '1', updBy = '$input3', updDate = '$inpD3' WHERE id ='$did';";
	@mysql_query($del_q) or die(mysql_error());
	log_hist(145,$did);
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
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<form method="POST" action="" class="well form-inline">
			<label><b>NAME</b>&nbsp;<font color="Red">*</font>:</label>
			<input type="text" name="rt1" size="80" id="rt1">
			<script language="JavaScript" type="text/javascript">if(document.getElementById) document.getElementById('rt1').focus();</script>
			<input type="submit" name="add_rt" class="btn-info btn-small" value=" ADD ">
			</form>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<?=$pagingResult->pagingMenu()?>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>	
				<tr align="left" valign="top"> 
					<td width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td>
					<td width="*" align="left">&nbsp;<b>NAME</b>&nbsp;</td>
					<td width="50" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count 	= $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $array["id"]) {?>
				<form method="POST" action="">
				<input type="hidden" name="nid" value="<?=$array["id"]?>">
				<tr bgcolor="#ffcc99" align="left" valign="top">
					<td width="25">&nbsp;<?=$count?>.</td>
					<td><input type="text" name="rt2" size="30" value="<?=($array["name"])?ucwords($array["name"]):"-";?>"></td>
					<td width="*" align="center" colspan="2">
						<input type="submit" class="btn-info btn-small" name="update_rt" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL ">
					</td>
				</tr>
				</form>
	<?php 		
			} else { ?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.</td>
					<td>&nbsp;<?=($array["name"])?ucwords($array["name"]):"-";?> </td>
					<td width="25" align="center"><a title="Edit Group" href="<?=$this_page?>&nid=<?=$array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a title="Delete Group" href="<?=$this_page?>" onclick="return confirmBox(this, 'del','Item Category : <?=ucwords($array["name"])?>', '<?=$array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
			<?php	 
				} $count++; 
			}
		} else {?>
				<tr><td colspan="4" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php } ?></tbody>
		</table>
				<?=$pagingResult->pagingMenu()?>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>