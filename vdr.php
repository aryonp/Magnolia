<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title 	= "Vendors List";
$page_id_left 	= "13";
$page_id_right 	="33";
$category_page 	= "strx";
chkSecurity($page_id_right);

$search = ($_POST['search']!="")?trim($_POST['search']):"";
$sQ 	= (isset($_POST['sbutton']))?"name LIKE '%$search%' AND":"";

$vendors_list_query ="SELECT id, name, address, phone, fax, pic, serves FROM vdr WHERE $sQ del = '0' ORDER BY serves, name ASC ";	
$pagingResult = new Pagination();
$pagingResult->setPageQuery($vendors_list_query);
$pagingResult->paginate();

$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "";

if (isset($_POST['add_vdr'])) { 
		$name 	= strtoupper((isset ($_POST['name']) && $_POST['name'] != '')?trim($_POST['name']):'');
		$addr 	= ((isset ($_POST['addr']) && $_POST['addr'] != '')?trim($_POST['addr']):'');
		$phone 	= ((isset ($_POST['phone']) && $_POST['phone'] != '')?trim($_POST['phone']):'(000)-(0000000)');
		$fax 	= ((isset ($_POST['fax']) && $_POST['fax'] != '')?trim($_POST['fax']):'(000)-(0000000)');
		$pic 	= ((isset ($_POST['pic']) && $_POST['pic'] != '')?trim($_POST['pic']):'');
		$branch = ((isset ($_POST['branch']) && $_POST['branch'] != '')?trim ($_POST['branch']):'-');
		$pemail = ((isset ($_POST['pemail']) && $_POST['branch'] != '')?trim ($_POST['pemail']):'-');
		$vsap 	= ((isset ($_POST['vsap']) && $_POST['vsap'] != '')?trim ($_POST['vsap']):'-');
		$pctc 	= ((isset ($_POST['pctc']) && $_POST['pctc'] != '')?trim ($_POST['pctc']):'-');
		
		if (!empty ($name) && !empty($addr) && !empty($pic)  && $branch != "-"){
			$sql  ="INSERT INTO vdr (name, address, phone, fax, pic, serves, pemail, vsap, pcontact) VALUES ('$name','$addr','$phone','$fax','$pic','$branch','$pemail','$vsap', '$pctc');";
			@mysql_query($sql) or die(mysql_error());
			log_hist("55",$name);
			header("location:$this_page");
			exit();
		}  
		
		else { 
			$status="<p class=\"alert\">Missing required information ! </p>"; 
		}
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$delete_query  ="UPDATE vdr SET del = '1' WHERE vdr.id ='$did';";
   	@mysql_query($delete_query) or die(mysql_error());
    log_hist("57",$did);
    header("location:$this_page");
	exit();
} 

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0"  width="100%">
	<tr><td><h2>VENDORS LIST</h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td><?=$status?></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		<form action="" method="POST" class="well">
		<table border="0" cellpadding="0">
			<tr valign="top"><td align="right"><b>VENDOR NAME</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" name="name" id="name" size="50">
			<script language="JavaScript" type="text/javascript">if(document.getElementById) document.getElementById('name').focus();</script></td></tr>
			<tr valign="top"><td align="right"><b>ADDRESS</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><textarea cols="50" rows="2" name="addr" wrap="virtual"></textarea></td></tr>
			<tr valign="top"><td align="right"><b>PHONE</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" name="phone" size="50" value="(AREA)-PHONE NBR X(EXT)"></td></tr>
			<tr valign="top"><td align="right"><b>FAX</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" name="fax" size="50" value="(AREA)-PHONE NBR"></td></tr>
			<tr valign="top"><td align="right"><b>PIC<b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" name="pic" size="50"></td></tr>
			<tr valign="top"><td align="right"><b>PIC CTC<b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" name="pctc" size="50"></td></tr>
			<tr valign="top"><td align="right"><b>EMAIL<b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" name="pemail" size="50"></td></tr>
			<tr valign="top"><td align="right"><b>SAP<b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" name="vsap" size="50"></td></tr>
			<tr valign="top"><td align="right"><b>BRANCH SERVES</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><?=branch_list()?></td></tr>
			<tr valign="top"><td>&nbsp;</td><td>&nbsp;</td>
			<td><br/><input type="submit" name="add_vdr" class="btn-info btn-small" value="  ADD VENDOR "></td></tr>
		</table>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
		<tr><td align="center">
			<form action="" method="POST" class="well">SEARCH VENDOR&nbsp;&nbsp;:&nbsp;&nbsp;
			<input type="text" name="search" size=50 value="<?=$search?>"/>&nbsp;&nbsp;
			<input type="submit" name="sbutton" value="  GO  " class="btn-info btn-small" />
			</form></td>
		</tr>
		<tr><td>&nbsp;</td></tr>
	<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr><td width="25"><b>&nbsp;NO.&nbsp;</b></td>
            		<td width="*"><b>&nbsp;NAME&nbsp;</b></td>
                 	<td width="*"><b>&nbsp;PHONE&nbsp;</b></td>
                 	<td width="*"><b>&nbsp;FAX&nbsp;</b></td>
                 	<td width="*"><b>&nbsp;PIC&nbsp;</b></td>
                 	<td width="*"><b>&nbsp;FOR BRANCH&nbsp;</b></td>
                 	<td colspan="3" align="center" width="75" >&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
<?php 	if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
				<tr align="left" valign="top">
					<td >&nbsp;<?=$count?>.&nbsp;</td>
					<td >&nbsp;<?=($array["name"])?strtoupper($array["name"]):"-";?>&nbsp;</td>
					<td >&nbsp;<?=($array["phone"])?trim($array["phone"]):"-";?>&nbsp;</td>
					<td >&nbsp;<?=($array["fax"])?trim($array["fax"]):"-";?>&nbsp;</td>
					<td >&nbsp;<?=($array["pic"])?ucwords($array["pic"]):"-";?>&nbsp;</td>
					<td >&nbsp;<?=($array["serves"])?ucwords($array["serves"]):"-";?>&nbsp;</td>
					<td align="center" width="30">&nbsp;<a title="History" href="./vdr_hist.php?id=<?=$array["id"]?>"><img src="<?=IMG_PATH?>hist.png"></a>&nbsp;</td>
					<td align="center" width="30">&nbsp;<a title="View" href="./vdr_det.php?id=<?=$array["id"]?>"><img src="<?=IMG_PATH?>view.png"></a>&nbsp;</td>
					<td align="center" width="30">&nbsp;<a title="Delete" href="<?=$this_page?>" onclick="return confirmBox(this,'del','vendor \n<?=ucwords($array["name"])?>', '<?=$array["id"]?>')"><img src="<?=IMG_PATH?>delete.png"></a>&nbsp;</td>
				</tr>
<?php			$count++;  
			}
        } 
		else {?>
				<tr><td colspan="9" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 	} ?> </tbody>
			</table>
		</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>&nbsp;</td></tr>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>