<?php
/* -----------------------------------------------------
 * File name	: deprecGraph.php	
 * created by 	: aryonp@gmail.com				   
 * Created date	: 25.11.2008	
 * -----------------------------------------------------						
 * Purpose		: Create depreciation graph			
 * -----------------------------------------------------			   						                 			
 */

require_once '../init.php';
require_once LIB_PATH.'functions.lib.php';
require_once CONT_PATH.'deprec.class.php';

$plot = new deprec($_GET['class'],$_GET['cctr']);
echo $plot->deprecGraph();

?>