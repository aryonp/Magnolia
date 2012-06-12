<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once CONT_PATH.'deprec.class.php';
chkSession();

$page_title 	= "Inventory Depreciation";
$page_id_left 	= "12";
$page_id_right 	= "46";
$category_page 	= "kpi";
chkSecurity($page_id_right);

include THEME_DEFAULT.'print_header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
<?php
if(isset($_GET['class']) AND isset($_GET['cctr'])) { 
		$class 	= trim($_GET['class']);
		$cctr 	= trim($_GET['cctr']);
		$deprec = new deprec($class,$cctr);
		log_hist("61","For class : $class and cost ctr. : $cctr");
		echo "<b>INVENTORY DEPRECIATION FOR CLASS : <font color=\"Red\">".strtoupper($class)."</font>  AND COST CTR. : <font color=\"Red\">".strtoupper($cctr)."</font></b>";
		echo $deprec->deprecTable();
		echo "<br/><img src =\"./controller/deprecGraph.php?class=$class&cctr=$cctr\" /><br/>";
} else {
	echo "<br><br><br><center>No data available</center>";
}
?>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'print_footer.php';?>