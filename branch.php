<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Branch List";
$page_id_left 	= "13";
$page_id_right 	= "29";
$category_page 	= "strx";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$branch_list_query ="SELECT id, name, city, emp FROM branch WHERE del = '0' ORDER BY name ASC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($branch_list_query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";

if (isset($_POST['add_branch'])){
	
	$bid = strtoupper(trim($_POST['bid']));
	$name = strtolower(trim($_POST['name']));
	$address = strip_tags(trim($_POST['addr']));
	$city = trim($_POST['city']);
	$phone = trim($_POST['phone']);
	$fax = trim($_POST['fax']);
	$zip = trim($_POST['zip']);
	$emp = (int) (trim($_POST['emp']));
	$pic = strtolower(trim($_POST['pic']));
	$email = strtolower(trim($_POST['email']));
	
	if (!empty($bid) && !empty($name) && !empty($city) && !empty($zip) && !empty($emp) ){
		$add_branch_query  ="INSERT INTO branch (id,name,address,city,zip,phone,fax,emp,pic,pemail) VALUES ('$bid','$name','$address','$city','$zip','$phone','$fax','$emp','$pic','$email');"; 
		@mysql_query($add_branch_query) or die(mysql_error());
		log_hist("43",$name);
		header("location:$this_page");
		exit();
	}
	
	else {
		$status ="<p class=\"alert\">Missing required information! Please check again!</p>";
	}
	
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$delete  ="UPDATE branch SET del = '1' WHERE id ='$did' ";
	@mysql_query($delete) or die(mysql_error());
	log_hist("45",$did);
	header("location:$this_page");
	exit();
}

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>
			<div class="well">
	<table border="0" cellpadding="1" cellspacing="1" >
		<tr valign=top>
			<td align="right"><b>ID</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><input type="text" size="30" name="bid" id="bid">
			<script language="JavaScript" type="text/javascript">
				if(document.getElementById) document.getElementById('bid').focus();
			</script></td>
			<td>&nbsp;</td>
			<td align="right"><b>NAME</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><input type="text" size="30" name="name"></td></tr>
		<tr valign=top>
			<td align="right"><b>ADDRESS</b></td>
			<td>:</td>
			<td colspan="5"><textarea cols="30" rows="2" name="addr" wrap="virtual"></textarea></td></tr>
		<tr valign=top>
			<td align="right"><b>CITY</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><input type="text" size="30" name="city"></td>
			<td>&nbsp;</td>
			<td align="right"><b>ZIP</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><input type="text" size="30" name="zip"></td></tr>
		<tr valign=top>
			<td align="right"><b>PHONE</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="phone"></td>
			<td>&nbsp;</td>
			<td align="right"><b>FAX</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="fax"></td></tr>
		<tr valign=top>
			<td align="right"><b>EMPLOYEE</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td colspan="5"><input type="text" size="30" name="emp"></td></tr>
		<tr valign=top>
			<td align="right"><b>PIC</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="pic"></td>
			<td>&nbsp;</td>
			<td align="right"><b>EMAIL</b></td>
			<td>:</td>
			<td><input type="text" size="30" name="email"></td></tr>
	</table></div></td></tr>
		<tr><td ><input type="submit" name="add_branch" value="  ADD BRANCH  " class="btn-info btn-small" /></td></tr>
		<tr><td >&nbsp;</td></tr>
		<tr>
		<?=$pagingResult->pagingMenu()?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>	
				<tr align="left" valign="top"> 
            		<td width="25" align="left">&nbsp;<b>NO</b></td> 
					<td width="45" align="left">&nbsp;<b>CODE</b></td>
					<td width="*" align="left">&nbsp;<b>NAME</b></td>
					<td width="*" align="left">&nbsp;<b>CITY</b></td>
					<td width="75" align="right">&nbsp;<b>EMPLOYEES</b>&nbsp;</td>
					<td width="*" colspan="2" align="center">&nbsp;<b>CMD</b></td>
				</tr>
				</thead>
				<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) { ?>
				<tr align="left">
					<td width="25" align="left">&nbsp;<?=$count?>.</td> 
					<td>&nbsp;<?=strtoupper($array["id"])?> </td>
					<td>&nbsp;<?=ucwords($array["name"])?> </td>
					<td>&nbsp;<?=ucwords($array["city"])?></td>
					<td align="right">&nbsp;<?=$array["emp"];?>&nbsp;</td>
					<td width="25" align="center"><a href="branch_det.php?id=<?=$array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a href="<?=$this_page?>" onclick="return confirmBox(this,'del','branch <?=ucwords($array["name"])?>', '<?=$array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
<?php		$count++;  
				} 
			} else {?>
				<tr><td colspan="7" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 		} ?></tbody>
			</table>
			<?=$pagingResult->pagingMenu()?>
		</td></tr>
	</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>