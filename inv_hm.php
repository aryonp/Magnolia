<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();		

$page_title		= "Inventory Management";
$page_id_left 	= "11";
$page_id_right 	= "26";
$category_page 	= "inventory";
chkSecurity($page_id_right);

$query ="SELECT iv.id as id, iv.aid, iv.description as name, iv.class, iv.life, iv.cctr, iv.buydate as bdate, iv.startdate as udate 
		 FROM inv iv  WHERE iv.del = '0' ORDER BY iv.startdate DESC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();

$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$class_list_query ="SELECT name, description FROM inv_class WHERE del ='0';";
$class_list_SQL = @mysql_query($class_list_query) or die(mysql_error());

$status = "&nbsp;";

if(isset($_POST["add_inv"])) {
	$price = trim($_POST["price"]);
	$cctr = strtoupper(trim($_POST["cctr"]));
	$class = trim($_POST["class"]);
	$bdate = trim($_POST["bdate"]);
	$udate = trim($_POST["udate"]);
	$invnbr = trim($_POST["invnbr"]);
	$life = (is_numeric(trim($_POST["life"])))?trim($_POST["life"]):"";
	$details = trim($_POST["details"]);
	//$user = $_SESSION["uid"];
	//$lastupd = date('Y-m-d H:i:s');
	if($class != "-" OR !empty($price) OR !empty($cctr) OR !empty($life) OR !empty($details)) {
		$add = "INSERT INTO inv (aid,description,class,life,cctr,buydate,price,startdate) VALUES ('$invnbr','$details','$class','$life','$cctr','$bdate','$price','$udate');";	
		$addID = mysql_insert_id();   	
		@mysql_query($add) or die(mysql_error());
		log_hist("62",$addID);
	}
	else {
		$status = "<p class=\"yellowbox\">Missing required information! Please complete all necessary infos!</p>";
	}
}	   
if(isset($_GET["did"])) {
	$did = $_GET["did"];
	$del = "UPDATE inv SET del = '1' WHERE id = '$did' ";
	@mysql_query($del) or die(mysql_error());
	log_hist("64",$did);
	header("location:$this_page");
	exit();
}
include THEME_DEFAULT.'header.php'; ?>             			
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>
		<div class="well">
		<table border="0" cellpadding="1" cellspacing="1">
		<tr valign="top">
			<td align="right"><b>BUYDATE</b></td>
			<td>:</td>
			<td><input type="text" name="bdate" value="<?=date('Y-m-d')?>" id="bdate" size="20">&nbsp;<a href="javascript:NewCal('bdate','yyyymmdd')"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="right"><b>USEDATE</b></td>
			<td>:</td>
			<td><input type="text" name="udate" value="<?=date('Y-m-d')?>" id="udate" size="20">&nbsp;<a href="javascript:NewCal('udate','yyyymmdd')"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a>&nbsp;</td></tr>
		<tr valign="top">
			<td align="right"><b>CLASS</b>&nbsp;<font color="Red">*</font></td><td>:</td>
			<td><select name="class">
    				<option value="-">---------------------</option>
<?php 
  	while($class_list_array = mysql_fetch_array($class_list_SQL)){?>
    <option value="<?=$class_list_array["name"]?>"><?=ucwords($class_list_array["description"])?></option>
<?php } ?>
 				 </select></td>
			<td>&nbsp;</td>
			<td align="right"><b>COST CENTER</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" size="30" name="cctr"/></td></tr>
		<tr valign="top">
			<td align="right"><b>PRICE (USD)</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" size="30" maxlength="20" name="price"/></td>
			<td>&nbsp;</td>
			<td align="right"><b>LIFE (YEARS)</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><input type="text" size="30" name="life"/></td></tr>
		<tr valign="top">
			<td align="right"><b>DESCRIPTION</b>&nbsp;<font color="Red">*</font></td><td>:</td><td><textarea cols="50" rows="2" name="details" wrap="virtual"></textarea></td>
			<td>&nbsp;</td>
			<td align="right"><b>INV. NBR</b></td><td>:</td><td><input type="text" size="30" name="invnbr"/></td></tr>
			</table></div>
		</td></tr>
		<tr><td><input type="submit" name="add_inv" class="btn-info btn-small" value="  ADD INVENTORY  "></td></tr>
		<tr><td>&nbsp;</td></tr>
        <tr><td>
        	<?=$pagingResult->pagingMenu();?>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr>
                 	<th width="25">&nbsp;<b>NO.</b>&nbsp;</td>
					<th width="*" align="left">&nbsp;<b>AID</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>DESCRIPTION</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>CLASS</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>LIFE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>COST CTR.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>BUYDATE</b>&nbsp;</td>
            		<th width="*" align="left">&nbsp;<b>USEDATE</b>&nbsp;</td>
                 	<th width="*" align="center" colspan="2">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
		<tr valign="top">
			<td align="left">&nbsp;<?=$count?>.&nbsp;</td>
			<td align="left">&nbsp;<?=($array["aid"])?strtoupper($array["aid"]):"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["name"])?ucwords($array["name"]):"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["class"])?ucwords($array["class"]):"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["life"])?ucwords($array["life"]):"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["cctr"])?strtoupper($array["cctr"]):"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["bdate"])?cplday('d M Y',$array["bdate"]):"-";?>&nbsp;</td>
			<td align="left">&nbsp;<?=($array["udate"])?cplday('d M Y',$array["udate"]):"-";?>&nbsp;</td>
			<td width="25" align="center" valign="middle"><a title="Edit" href="./inv_det.php?id=<?=$array["id"]?>">
				<img src="<?=IMG_PATH?>edit.png"></a></td>
			<td width="25" align="center" valign="middle"><a title="Delete" href="<?=$this_page?>" onclick="return confirmBox(this,'del','inventory ID #<?=$array["id"]?>', '<?=$array["id"]?>')">
				<img src="<?=IMG_PATH?>delete.png"></a></td>
		</tr>
<?php	$count++;  
		}
	} else { ?>
		<tr><td colspan="10" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
		<?php } ?>
		</tbody>
		</table>
		<?=$pagingResult->pagingMenu();?>
		</td></tr>
	</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>