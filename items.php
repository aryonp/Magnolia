<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Item Categories";
$page_id_left 	= "13";
$page_id_right 	= "32";
$category_page 	= "strx";
chkSecurity($page_id_right);

$type_list_query 	= "SELECT rt.id, rt.name, type.id as tid, type.name as tname, rt.consumable as consum FROM req_items rt LEFT JOIN req_type type ON (type.id = rt.type_id_fk) WHERE rt.del = '0' ORDER BY rt.type_id_fk ASC, rt.name ASC ";
$pagingResult 		= new Pagination();
$pagingResult->setPageQuery($type_list_query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";

function select_consume() { ?>
	<select name="consume">
		<option value="-">-----</option>
		<option value="1">YES</option>
		<option value="0">NO</option>
	</select>	
<?php }

function select_cat_type() { 
	$disp_cat_type_q = "SELECT id, name FROM req_type WHERE del ='0';";
	$disp_cat_type_SQL = @mysql_query($disp_cat_type_q) or die(mysql_error());?>
	<select name="category">
		<option value="-">---------------------</option>
<?php while($disp_cat_type_array = mysql_fetch_array($disp_cat_type_SQL)) { ?>
		<option value="<?=$disp_cat_type_array[0]?>"><?=strtoupper($disp_cat_type_array[1])?></option>
<?php } ?>
	</select>	
<?php }

if (isset($_POST['add_type'])){
	$name = trim($_POST['name']);
	$category = trim($_POST['category']);
	$consume = trim($_POST['consume']);
	if (!empty($name) AND $consume != "-" AND $category != "-"){
		$add_type_query  ="INSERT INTO req_items (name,type_id_fk,consumable) VALUES ('$name','$category','$consume');"; 
		@mysql_query($add_type_query) or die(mysql_error());
		log_hist("52",$name);
		header("location:$this_page");
		exit();
	}
	else {
		$status ="<p class=\"yellowbox\">Missing information ! Cannot create new request item.</p>";
	}
}

$display_cat_item_q = "SELECT id, name FROM req_type WHERE del ='0';";
$display_cat_item_SQL = @mysql_query($display_cat_item_q) or die(mysql_error());
if (isset($_POST['update_type'])){
	$nid = trim($_POST['nid']);
	$name = trim($_POST['name']);
	$category = trim($_POST['category']);
	$consume = trim($_POST['consume']);
	$update_type_query  ="UPDATE req_items SET name='$name', type_id_fk='$category', consumable='$consume' WHERE id ='$nid';";
	@mysql_query($update_type_query) or die(mysql_error());
	log_hist("53",$name);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$delete_type_query  ="UPDATE req_items SET del = '1' WHERE id ='$did';";
	@mysql_query($delete_type_query) or die(mysql_error());
	log_hist("54",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>
			<form method="POST" action="" class="well">
			<table border="0" cellpadding="1" cellspacing="1">	
            	<tr> 
                 <td><b>NAME</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" name="name" id="name" size="40">
                 <script language="JavaScript" type="text/javascript">
					if(document.getElementById) document.getElementById('name').focus();
					</script></td>
				 <td><b>CATEGORY</b>&nbsp;<font color="Red">*</font>:<br /><?=select_cat_type()?></td>
				 <td><b>CONSUMABLE</b>&nbsp;<font color="Red">*</font>:<br /><?=select_consume()?></td>
				 <td width="*"><br /><input type="submit" name="add_type" class="btn-info btn-small" value="  ADD NEW  "></td>
				</tr>
			</table>
			</fieldset>
		</form>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<?=$pagingResult->pagingMenu();?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>	
			<tr align="left" valign="top"> <td width="25" align="left">&nbsp;<b>NO</b></td> 
                 	<td width="*" align="left">&nbsp;<b>NAME</b></td>
                 	<td width="*" align="left">&nbsp;<b>CATEGORY</b></td>
                 	<td width="75" align="left">&nbsp;<b>CONSUMABLE</b>&nbsp;</td>
                 	<td width="*" colspan="2" align="center">&nbsp;<b>CMD</b></td>
			</tr>
			</thead>
			<tbody>
<?php    if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($type_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $type_list_array["id"]) {?>
				<form method="POST" action="">
				<input type="hidden" name="nid" value="<?=$type_list_array["id"]?>">
				<tr bgcolor="#ffcc99" align="left" valign="top">
					<td width="25" align="left">&nbsp;<?=$count?>.</td>
					<td>&nbsp;<input type="text" name="name" value="<?=ucwords($type_list_array["name"])?>" size="30"></td>
					<td width="*" align="left">
						<select name="category">
						<?php while($display_cat_item_array = mysql_fetch_array($display_cat_item_SQL, MYSQL_ASSOC)){
  								$compare_cat = ($display_cat_item_array["id"] == $type_list_array["tid"])?"SELECTED":"";?>
    							<option value="<?=$display_cat_item_array["id"]?>" <?=$compare_cat?>><?=strtoupper($display_cat_item_array["name"])?></option>
						<? } ?>
 				 		</select>
					</td>
					<td width="75" align="center">
<?php					$cons_status = array("1","0");
						$cons_name = array("YES","NO");
						echo "<select name = \"consume\">\n";
						foreach($cons_status as $key => $status) {
							$name = $cons_name[$key];
							$compare_cons = ($status == $type_list_array["consum"])?"SELECTED":"";
							echo "\t<option value =\"$status\" $compare_cons>$name</option>\n";
						}
						echo "</select>\n";
					?>
					</td>
					<td width="*" align="center" colspan="2">
						<input type="submit" class="btn-info btn-small" name="update_type" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL "></td>
				</tr>
				</form>
			<?php } else { ?>
				<tr align="left" valign="top">
					<td width="25" align="left">&nbsp;<?=$count?>.</td>
					<td>&nbsp;<?=ucwords($type_list_array["name"])?> </td>
					<td>&nbsp;<?=strtoupper($type_list_array["tname"])?></td>
					<td align="center">&nbsp;<?=($type_list_array["consum"] == "1")?"YES":"NO"?>&nbsp;</td>
					<td width="25" align="center"><a title="Edit Item" href="<?=$this_page?>&nid=<?=$type_list_array["id"]?>"><img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a title="Delete Item" href="<?=$this_page?>" onclick="return confirmBox(this,'del','item <?=ucwords($type_list_array["name"])?> ', '<?=$type_list_array["id"]?>')"><img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
			<?php	
				} $count++;  
			}
		} else {?>
				<tr><td colspan="6" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php } ?>
				</tbody>
				</table>
				<?=$pagingResult->pagingMenu();?>
			</fieldset>
		</td></tr>
	</table>

<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>