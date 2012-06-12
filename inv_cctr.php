<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Cost Centre Page";
$page_id_left 	= "13";
$page_id_right 	= "51";
$category_page 	= "strx";
chkSecurity($page_id_right);

$cctr_list_query 	= "SELECT id, code, ba, spc FROM inv_cctr WHERE del = '0' ORDER BY code ASC, ba ASC, spc ASC ";
$pagingResult 		= new Pagination();
$pagingResult->setPageQuery($cctr_list_query);
$pagingResult->paginate();
$this_page 			= $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";

if (isset($_POST['add_cctr'])){
	$code1 	= trim($_POST['code1']);
	$ba1 	= ucwords(trim($_POST['ba1']));
	$spc1 	= ucwords(trim($_POST['spc1']));
	$add_cctr_query  = "INSERT INTO inv_cctr (code,ba,spc) VALUES ('$code1','$ba1','$spc1');"; 
	
	if (!empty($code1) AND !empty($ba1) AND !empty($spc1)){
		@mysql_query($add_cctr_query) or die(mysql_error());
		log_hist(114,$code);
		header("location:$this_page");
		exit();
	}
	
	else {
		$status ="<p class=\"yellowbox\">Missing information, please check all required data !</p>";
	}
	
}

if (isset($_POST['update_code'])){
	$nid 	= trim($_POST['nid']);
	$code 	= strtolower(trim($_POST['code2']));
	$ba 	= strtolower(trim($_POST['ba2']));
	$spc 	= strtolower(trim($_POST['spc2']));
	$update_ccode_query = "UPDATE inv_cctr SET code = '$ccode', ba = '$ba', spc = '$spc' WHERE id ='$nid';";
	@mysql_query($update_ccode_query) or die(mysql_error());
	log_hist(115,$code);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did']) AND !empty($_GET['did'])){
	$did 	= trim($_GET['did']);
	$delete_ccode_query  = "UPDATE inv_cctr SET del = '1' WHERE id ='$did';";
	@mysql_query($delete_ccode_query) or die(mysql_error());
	log_hist(116,$did);
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
            	<tr> 
					<td><b>CODE</b>&nbsp;<font color="Red">*</font></td>
					<td>&nbsp;:</td>
					<td><input type="text" name="code1" id="code" size="30">
					<script language="JavaScript" type="text/javascript">
						if(document.getElementById) document.getElementById('code').focus();
					</script>
					</td>
					<td>&nbsp;</td>
					<td><b>BUSINESS AREA</b>&nbsp;<font color="Red">*</font></td>
					<td>&nbsp;:</td>
					<td><input type="text" name="ba1" size="30"></td>
					<td>&nbsp;</td>
					<td><b>SPC</b>&nbsp;<font color="Red">*</font></td>
					<td>&nbsp;:</td>
					<td><input type="text" name="spc1" size="30"></td>
					<td width="*">&nbsp;<input type="submit" name="add_code" class="btn-info btn-small" value=" ADD "></td>
				</tr>
			</table>
			</form></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			
		<?=$pagingResult->pagingMenu()?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>	
				<tr align="left" valign="top">
					<td width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td>
					<td width="30" align="left">&nbsp;<b>CODE</b>&nbsp;</td>
					<td width="*" align="left">&nbsp;<b>BUSINESS AREA</b>&nbsp;</td>
					<td width="*" align="left">&nbsp;<b>SPC</b>&nbsp;</td>
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
					<td><input type="text" name="code2" size="30" value="<?=($array["code"])?$array["code"]:"-";?>"></td>
					<td><input type="text" name="ba2" size="30" value="<?=($array["ba"])?ucwords(trim($array["ba"])):"-";?>"></td>
					<td><input type="text" name="spc2" size="30" value="<?=($array["spc"])?ucwords(trim($array["spc"])):"-";?>"></td>
					<td width="50" align="center" colspan="2">
						<input type="submit" class="btn-info btn-small" name="update_code" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL "></td>
				</tr>
				</form>
<?php 			} else { ?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td width="30">&nbsp;<?=($array["code"])?$array["code"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["ba"])?trim($array["ba"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["spc"])?ucwords($array["spc"]):"-";?>&nbsp;</td>
					<td width="25" align="center"><a title="Edit Code" href="<?=$this_page?>&nid=<?=$array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a title="Delete Code" href="<?=$this_page?>" onclick="return confirmBox(this,'del','\nCost Center <?=$array["ccode"]?>', '<?=$array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
<?php	 		} $count++; 
			}
		} else {?>
				<tr><td colspan="6" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 	} ?></tbody>
		</table>
				<?=$pagingResult->pagingMenu()?>
			</fieldset>
		</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>