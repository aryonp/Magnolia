<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title		= "STORIX Account Billing";
$page_id_left 	= "57";
$category_page 	= "archive";
chkSecurity($page_id_left);

$bill_id 	= is_numeric(trim($_GET['id']))?trim($_GET['id']):0;

$bill_q 	= "SELECT b.id, 
					  b.branch, 
					  b.dept, 
					  b.resp,
					  b.period,
					  b.acc,
					  b.cost,
					  b.content,
					  b.details,
					  b.payment
		   	   FROM billing b
		   	   WHERE b.del = '0' AND b.id = '$bill_id'
		   	   ORDER BY b.period ASC;";
$bill_SQL = @mysql_query($bill_q) or die(mysql_error());

$bill_msg = "\nDear Mr/Ms. %s<br/><br/>".
              "This is an automated billing system to charge every users for<br/>".
              "accounts they used, provided and registered by IT.<br/><br/>".
              "Your Details,<br/>".
              "<table border=\"0\">".
              "<tr><td>Period</td><td>: %s</td></tr>".
              "<tr><td>Branch</td><td>: %s</td></tr>".
			  "<tr><td>Department</td><td>: %s</td></tr>".
              "<tr><td>Active users</td><td>: %s</td></tr>".
              "</table><br/>".
              "%s <br/><br/>".
              "For further information please contact IT Department.<br/>".
              "This billing will be send every month on the 15th day<br/>". 
			  "Please re-check always and report any unusual things that you find to IT, help us to improve our system.<br/><br/>".            
           	  "<br/>Kindly Regards,".
              "<br/>- STORIX System -".
			  "%s";

$b_arr 		= mysql_fetch_array($bill_SQL,MYSQL_ASSOC);

$bill_msg 	= sprintf($bill_msg,ucwords($b_arr["resp"]),$b_arr["period"],strtoupper($b_arr["branch"]),strtoupper($b_arr["dept"]),$b_arr["acc"],$b_arr["content"],$b_arr["details"]);

$m = (isset($_GET['m']))?strtolower(trim($_GET['m'])):"print"; 

switch ($m) {
	case 'print':
		$header = 'print_header.php';
		$footer = 'print_footer.php';
		log_hist(147,$b_arr["period"]);
		break;
	case 'xls':
		$header = 'xls_header.php';
		$footer = 'xls_footer.php';
		log_hist(148,$b_arr["period"]);
		break;
	default :
		$header = 'print_header.php';
		$footer = 'print_footer.php';
		log_hist(147,$b_arr["period"]);
		break;
}

$ed_branch	= str_replace(" ","_",$b_arr["branch"]);
$ed_dept	= str_replace(" ","_",$b_arr["dept"]);

$xls_note	= ($b_arr["branch"] == "JKT")?"JKT_".$ed_dept:$ed_branch;

$filename 	= "Access_billing_for_$xls_note.xls";

include THEME_DEFAULT.$header;?>
<//-----------------CONTENT-START-------------------------------------------------//>

<table border="0" cellpadding="2" cellspacing="2" width="100%">
	<tr><td>&nbsp;</td></tr>
	<tr><td><?=$bill_msg?></td></tr>
	<tr><td>&nbsp;</td></tr>	
</table>

<//-----------------CONTENT-END---------------------------------------------------//>
<?php include THEME_DEFAULT.$footer;?>