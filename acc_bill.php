<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "IT Access Billing";
$page_id_left	= "13";
$page_id_right 	= "50";
$category_page 	= "strx";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$bill_list_query 	= "SELECT ab.id, rt.id as iid, rt.name, ab.price FROM acc_bill ab LEFT JOIN req_items rt ON (rt.id = ab.item_id_fk) WHERE ab.del = '0' ORDER BY rt.name ASC ";
$pagingResult 		= new Pagination();
$pagingResult->setPageQuery($bill_list_query);
$pagingResult->paginate();
$this_page 			= $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();
$status 			= "&nbsp;";

function acc_edit($param) {
	$query 		= "SELECT id, name FROM req_items WHERE type_id_fk = '1' AND del = '0' ORDER BY name ASC";
	$sql 		= @mysql_query($query) or die(mysql_error());
	$acc_edit 	= "<select name=\"item\">\n";
	$acc_edit 	.="<option value=\"-\">-----------</option>\n";
	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){
		$compare_item = ($array["id"] == $param)?"SELECTED":"";
		$acc_edit .= "<option value =\"".$array['id']."\" $compare_item>".ucwords($array['name'])."</option>\n";
	} 
	$acc_edit .= "</select>\n";
	
	return $acc_edit;
}

if (isset($_POST['add_cost'])){
	$item 	= trim($_POST['item']);
	$price 	= trim($_POST['price']);
	$add_query  ="INSERT INTO acc_bill (item_id_fk,price) VALUES ('$item','$price');"; 
	if (!empty($price) AND $item != "-"){
		@mysql_query($add_query) or die(mysql_error());
		log_hist("106",$item);
		header("location:$this_page");
		exit();
	}
	else {
		$status ="<p class=\"yellowbox\">Missing required information!</p>";
	}
}

if (isset($_POST['upd_cost'])){
	$nid 	= trim($_POST['nid']);
	$item 	= trim($_POST['item']);
	$price 	= trim($_POST['price']);
	$upd_query  = "UPDATE acc_bill SET item_id_fk = '$item', price = '$price' WHERE id ='$nid';";
	@mysql_query($upd_query) or die(mysql_error());
	log_hist("107",$nid);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did 	= trim($_GET['did']);
	$del_q 	= "UPDATE acc_bill SET del = '1' WHERE id ='$did';";
	@mysql_query($del_q) or die(mysql_error());
	log_hist("108",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php';?>
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
						<td><b>TYPE</b>&nbsp;<font color="Red">*</font>:<br /><?=acc_list_bill();?></td>
						<td>&nbsp;</td>
						<td><b>COST (EUR)</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" name="price" size="20">&nbsp;</td>
					</tr>
				</table>
			</div>
		</td></tr>
		<tr><td><input type="submit" name="add_cost" class="btn-info btn-small" value="  ADD ACCOUNT COST  "></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><br />
		<?=$pagingResult->pagingMenu()?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr><th width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>TYPE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>COST (EUR)</b>&nbsp;</td>
                 	<th width="50" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
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
						<!--<td>&nbsp;#<?=$array["id"]?></td>-->
						<td>&nbsp;<?=acc_edit($array["iid"]);?>&nbsp;</td>
						<td width="*">&nbsp;<input type="text" name="price" size="80" value="<?=ucwords($array["price"])?>">&nbsp;</td>
						<td align="center" colspan="2">
							<input type="submit" class="btn-info btn-small" name="upd_cost" value="UPDATE">&nbsp;
							<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL "></td>
					</tr>
					</form>
<?php 			} else { ?>
					<tr align="left" valign="top">
						<td>&nbsp;<?=$count?>.&nbsp;</td>
						<!--<td>&nbsp;#<?=($array["id"])?$array["id"]:"-";?>&nbsp;</td>-->
						<td>&nbsp;<?=($array["name"])?ucwords($array["name"]):"-";?>&nbsp;</td>
						<td width="*">&nbsp;<?=($array["price"])?$array["price"]:"-";?>&nbsp;</td>
						<td width="25">&nbsp;<a title="Edit Criteria" href="<?=$this_page?>&nid=<?=$array["id"]?>"><img src="<?=IMG_PATH?>edit.png"></a>&nbsp;</td>
						<td width="25">&nbsp;<a title="Delete Criteria" href="<?=$this_page?>" onclick="return confirmBox(this,'del', '\nEvaluation criteria ID #<?=$array["id"]?>', '<?=$array["id"]?>')"><img src="<?=IMG_PATH?>delete.png"></a>&nbsp;</td>
					</tr>
<?php	 		} $count++; 
			}
		} else {?>
				<tr><td colspan="5" align="center" bgcolor="#e5e5e5">&nbsp;<br />No Data Entries<br /><br />&nbsp;</td></tr>
<?php 	} ?>
		</tbody>
			</table>
				<?=$pagingResult->pagingMenu()?>
		</td></tr>
	</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>