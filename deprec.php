<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once CONT_PATH.'deprec.class.php';
chkSession();

$page_title 	= "Inventory Depreciation";
$page_id_left 	= "12";
$page_id_right 	= "46";
$category_page 	= "kpi";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$class_list_query 	= "SELECT cl.name, cl.description FROM inv_class cl WHERE cl.del = '0' ORDER BY cl.name ASC;";
$class_list_SQL 	= @mysql_query($class_list_query) or die(mysql_error());

$cctr_list_query 	= "SELECT cc.id, cc.code, CONCAT(cc.code,' : ',cc.ba,' > ',cc.spc) AS cctr FROM inv_cctr cc WHERE cc.del = '0' ORDER BY cc.ba ASC";
$cctr_list_SQL 		= @mysql_query($cctr_list_query) or die(mysql_error());

include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>  
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tbody><tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td bgcolor="#ccccff" height="1"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<form action="" method="POST" class="well">
			<table border="0">
				<tr><td align="left"><b>CLASS</b>&nbsp;<font color="Red">*</font>&nbsp;:</td>
					<td>
				<select name="class">
    				<option value="-">---------------------</option>
<?php 
  	while($class_list_array = mysql_fetch_array($class_list_SQL,MYSQL_ASSOC)){
 		$compare_class = ($class_list_array["name"] == $_POST['class'])?"SELECTED":"";?>
    <option value="<?=$class_list_array["name"]?>" <?=$compare_class?>><?=ucwords($class_list_array["description"])?></option>
<?php } ?>
 				 </select></td>
					<td><b>COST CENTER</b>&nbsp;<font color="Red">*</font>&nbsp;:</td>
					<td>
				<select name="cctr">
					<option value="-">-----------</option>
<?php 
  	while($cctr_list_array = mysql_fetch_array($cctr_list_SQL, MYSQL_ASSOC)){
 		$compare_cctr = ($cctr_list_array["code"] == $_POST['cctr'])?"SELECTED":"";?>
    <option value="<?=$cctr_list_array["code"]?>" <?=$compare_cctr?>><?=ucwords($cctr_list_array["cctr"])?></option>
<?php } ?>
 				 </select></td>
					<td>&nbsp;&nbsp;<input type="submit" class="btn-info btn-small" name="gendeprec" value="  SHOW  "></td>
				</tr>
			</table>
		</form></td></tr>
		<tr><td>&nbsp;</td></tr>
<?php if(isset($_POST['gendeprec']) && $_POST['class'] != "-" && $_POST['cctr'] != "-") { 
		$class = ($_POST['class'])?$_POST['class']:"-";
		$cctr = ($_POST['cctr'])?$_POST['cctr']:"-";
?>
		<tr><td><a title="Print" href="javascript:openW('./print_deprec.php?class=<?=$class?>&cctr=<?=$cctr?>','Print_Deprec',900,600,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png"></a><br/></td></tr>
		<tr><td><b>INVENTORY DEPRECIATION FOR CLASS : <font color="Red"><?=strtoupper($_POST['class'])?></font>  AND COST CENTER : <font color="Red"><?=strtoupper($_POST['cctr'])?></font></b></td></tr>
		<tr><td>
<?php	$deprec = new deprec($class,$cctr);
		log_hist("60","For class : $class and cost ctr. : $cctr");
		echo $deprec->deprecTable();
		echo "<br/><img src =\"./controller/deprecGraph.php?class=$class&cctr=$cctr\" /><br/>";
?>
		</td></tr>
<?php } ?>
	<tr><td>&nbsp;</td></tr>
	<tr><td>&nbsp;</td></tr>
	</tbody></table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>