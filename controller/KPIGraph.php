<?php
/* -----------------------------------------------------
 * File name	: KPIGraph.php	
 * Created by 	: aryonp@gmail.com				   
 * Created on	: 28.08.2008		
 * -----------------------------------------------------					
 * Purpose		: Create KPI Graph								                 			
 * -----------------------------------------------------
 */

require_once '../init.php';
require_once LIB_PATH.'functions.lib.php';
require_once CONT_PATH.'kpi.class.php';

$t 		= (isset($_GET['t']))?(int) (trim($_GET['t'])):1;
$plot 	= new strxKPI($_GET['s'],$_GET['e']);
$plot->setPieData();

switch ($t) {
	case 1:
    	$plot->getKPIGraph($plot->sqlKPI3,"Most requested access (item)");
    	break;	
	case 2:
    	$plot->getKPIGraph($plot->sqlKPI4,"Most requested items (item)");
    	break;   	
	case 3:
    	$plot->getKPIGraph($plot->sqlKPI7,"Top Approved RFA items");
    	break;   	
	case 4:
    	$plot->getKPIGraph($plot->sqlKPI8,"Top rejected RFA items");
    	break;    	
	case 5:
    	$plot->getKPIGraph($plot->sqlKPI10,"Most used vendors (transactions)");
    	break;    	
	case 6:
    	$plot->getKPIGraph($plot->sqlKPI11,"Used currencies (transactions)");
   		break;   		
	case 7:
    	$plot->getKPIGraph($plot->sqlKPI15,"Most toner usage (units)");
    	break;   	
	case 8:
    	$plot->getKPIGraph($plot->sqlKPI16,"Inventory based on class");
    	break;   	
	case 9:
    	$plot->getKPIGraph($plot->sqlKPI17,"Inventory based on cost center");
    	break;   	
	case 10:
    	$plot->getKPIGraph($plot->sqlKPI18,"Access per Branch");
    	break;   	
	case 11:
    	$plot->getKPIGraph($plot->sqlKPI19,"Access per Type");
    	break;   	
	case 12:
    	$plot->getKPIGraph($plot->sqlKPI21,"Most toner stock (units)");
    	break;
	default:
    	echo "Unknown input";
}
?>
<html>
	<head>
		<title>&copy 2008 - aryonp@gmail.com</title>
	</head>
<body>
</body>
</html>