<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once CONT_PATH.'kpi.class.php';
chkSession();

$page_title 	= "Summary Report";
$page_id_left 	= "12";
$page_id_right 	= "45";
$category_page 	= "kpi";
chkSecurity($page_id_right);

$s 	= trim($_GET['s']);
$e 	= trim($_GET['e']);
$m 	= (isset($_GET['m']))?strtolower(trim($_GET['m'])):"print"; 
$t 	= (isset($_GET['t']))?(int) (trim($_GET['t'])):1;
$kr = new strxKPI($s,$e);
			
switch ($m) {
	case 'print':
		$header = 'print_header.php';
		$footer = 'print_footer.php';
		//$lcomment	= 'print';
		break;
	case 'xls':
		$header = 'xls_header.php';
		$footer = 'xls_footer.php';
		//$lcomment	= 'generate excel';
		break;
	default :
		$header = 'print_header.php';
		$footer = 'print_footer.php';
		//$lcomment	= 'print';
		break;
}
$filename 		= "kpi_report.xls";
//$filename 	= "Monthly_report_for_$xls_note.xls";
include THEME_DEFAULT.$header;?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr><td>&nbsp;</td></tr>
	<tr><td>
<?php 
switch ($t) {
	case 1:
		$kr->getKPI1();
		break;
	case 2:
		$kr->getKPI5();
		break;
	case 3:
		$kr->getKPI14();
		break;
	case 4:
		$kr->getKPI9();
		break;
	case 5:
		$kr->getKPI12();
		break;
	case 6:
		$kr->getKPI13();
		break;
	case 7:
		$kr->getMoRep7();
		break;
	case 8:
		$kr->getMoRep9();
		break;
	default :
		$kr->getKPI1();
		break;
}
?>
	</td></tr>
	<tr><td>&nbsp;</td></tr>	
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.$footer;?>