<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "IT Budget Planning";
$page_id_left 	= "13";
$page_id_right 	= "52";
$category_page 	= "strx";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$query 			= "SELECT b.id, b.cost, b.byear FROM budget b WHERE b.del = '0' ORDER BY b.byear DESC ";
$pagingResult 	= new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();

$this_page 		= $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";

if (isset($_POST['add_budget'])){
	$cost 		= trim($_POST['cost']);
	$year 		= trim($_POST['year']);
	$cbudget_q 	= "SELECT byear FROM budget b WHERE byear = '$year' AND b.del = '0';";
	$cbudget_sql = @mysql_query($cbudget_q) or die(mysql_error());
	
	if(mysql_num_rows($cbudget_sql) >= 1){
		$status ="<p class=\"yellowbox\">Double data input, please check again !</p>";
	}
	
	else {
		
		if (!empty($cost) AND !empty($year)){
			$add_budget  ="INSERT INTO budget (cost,byear) VALUES ('$cost','$year');"; 
			@mysql_query($add_budget) or die(mysql_error());
			$bid = mysql_insert_id();
			log_hist("117",$bid);
			header("location:$this_page");
			exit();
		}
		
		else {
			$status ="<p class=\"yellowbox\">Missing information ! Please check your input again.</p>";
		}
	}
}

if (isset($_POST['update_budget'])){
	$nid 	= trim($_POST['nid']);
	$cost 	= trim($_POST['cost']);
	$year 	= trim($_POST['year']);
	$update_budget  = "UPDATE budget SET cost ='$cost', byear = '$year' WHERE id ='$nid';";
	@mysql_query($update_budget) or die(mysql_error());
	log_hist("118",$nid);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$delete_budget  = "UPDATE budget SET del = '1' WHERE id ='$did';";
	@mysql_query($delete_budget) or die(mysql_error());
	log_hist("119",$did);
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
            	<tr valign="middle"> 
                 <td><b>COST</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" name="cost" id="cost" size="40">
                 <script language="JavaScript" type="text/javascript">
					if(document.getElementById) document.getElementById('cost').focus();
					</script></td>
				 <td>&nbsp;</td>
				 <td><b>YEAR</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" class="input-small" name="year" value="YYYY"></td>
				 <td width="*"><br />&nbsp;<input type="submit" name="add_budget" class="btn-info btn-small" value="  ADD BUDGET  "></td>
				</tr>
			</table>
			</fieldset></form>
		</td></tr>
		<tr><td >&nbsp;</td></tr>
		<tr><td ><fieldset><legend>BUDGET LIST</legend><br />
		<?=$pagingResult->pagingMenu();?>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
            	<tr valign="middle"> 
					<td width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td> 
                 	<td width="85" align="left">&nbsp;<b>YEAR</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>BUDGET (EUR)</b>&nbsp;</td>
                 	<td width="*" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php    if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				$row_color = ($count % 2)?"odd":"even"; 
			if (isset($_GET['nid']) && $_GET['nid'] == $array["id"]) {?>
				<form method="POST" action="">
				<input type="hidden" name="nid" value="<?=$array["id"]?>">
				<tr bgcolor="#ffcc99" align="left">
					<td width="25" align="left">&nbsp;<?=$count?>.&nbsp;</td>
					<td><input type="text" name="year" value="<?=($array["byear"])?$array["byear"]:"0000";?>" size="30"></td>
					<td><input type="text" name="cost" value="<?=($array["cost"])?$array["cost"]:"0";?>" size="30"></td>		
					<td width="*" align="center" colspan="2">
						<input type="submit" class="btn-info btn-small" name="update_budget" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL "></td>
				</tr>
				</form>
			<?php } else { ?>
				<tr class="<?=$row_color?>" align="left" valign="top">
					<td width="25" align="left">&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($array["byear"])?$array["byear"]:"0000";?>&nbsp</td>
					<td>&nbsp;<?=($array["cost"])?number_format($array["cost"]):"0";?>&nbsp</td>
					<td width="25" align="center"><a title="Edit Budget" href="<?=$this_page?>&nid=<?=$array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a title="Delete Budget" href="<?=$this_page?>" onclick="return confirmBox(this,'del','budget ID #<?=$array["id"]?> for year <?=$array["year"]?> ', '<?=$array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
<?php				} 
				$count++;  
			}
		} else {?>
				<tr><td colspan="6" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php } ?>
				</tbody>
			</table>
				<?=$pagingResult->pagingMenu();?>
		</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>