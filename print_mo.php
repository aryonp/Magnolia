<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once CONT_PATH.'mo.class.php';
chkSession();

$page_title 	= "Monthly Report";
$page_id_left 	= "12";
$page_id_right 	= "59";
$category_page 	= "kpi";
chkSecurity($page_id_right);

$year 	= (isset($_GET['y']))?(int) trim($_GET['y']):date('Y');
$m 		= (isset($_GET['m']))?strtolower(trim($_GET['m'])):"print"; 
$t 		= (isset($_GET['t']))?(int) (trim($_GET['t'])):1;

$mo 	= new strxMoRep($year);

switch ($m) {
	case 'print':
		$header = 'print_header.php';
		$footer = 'print_footer.php';
		break;
	case 'xls':
		$header = 'xls_header.php';
		$footer = 'xls_footer.php';
		break;
	default :
		$header = 'print_header.php';
		$footer = 'print_footer.php';
		break;
}
$filename = "Monthly_report.xls";
include THEME_DEFAULT.$header;?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr><td>&nbsp;</td></tr>
	<tr><td>
<?php 
switch ($t) {
	case 1:
		$mo->getMoRep1();
		break;
	case 2:
		$mo->getMoRep2();
		break;
	case 3:
		$mo->getMoRep3();
		break;
	case 4:
		$mo->getMoRep4();
		break;
	case 5:
		$mo->getMoRep5();
		break;
	case 6:
		$mo->getMoRep6();
		break;
	case 7:
		$mo->getMoRep7();
		break;
	default :
		$mo->getMoRep1();
		break;
}
?>
	</td></tr>
	<tr><td>&nbsp;</td></tr>	
</table>
<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.$footer;?>