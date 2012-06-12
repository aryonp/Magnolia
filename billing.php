<?php
/* -----------------------------------------------------
 * File name  : billing.php	
 * Created by : aryonp@gmail.com	
 * -----------------------------------------------------					
 * Purpose	  : Generate billing email based on branch & 
 * department. Scheduled using cron every 15th day each 
 * month						                 			
 * -----------------------------------------------------
 */

require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';

//$headers = "From: \"STORIX Notification\" <no-reply-storix@dbschenker.com>\r\nX-Mailer:".PRODUCT." ".VERSION."/MIME-Version: 1.0\nContent-type:text/html;charset=iso-8859-1\nBCC:billy.yosafat@dbschenker.com\r\n";

$headers = "From: \"STORIX Notification\" <no-reply-storix@dbschenker.com>\r\nX-Mailer:".PRODUCT." ".VERSION."/MIME-Version: 1.0\nContent-type:text/html;charset=iso-8859-1\nBCC:aryonp@gmail.com\r\n";

$bdet_q 	= "SELECT a.branch_id_fk as branch, 
					  d.name as dname, 
					  rt.name as acc, 
					  a.name as aname, 
					  ab.price 
		   	   FROM acc a 
		   	   		LEFT JOIN acc_det ad ON (ad.acc_id_fk = a.id) 
		   	   		LEFT JOIN req_items rt ON (rt.id = ad.item_id_fk) 
		   	   		LEFT JOIN acc_bill ab ON (ab.item_id_fk = rt.id) 
		   	   		LEFT JOIN departments d ON (d.id = a.dept_id_fk) 
		   	   WHERE a.del = '0' %s 
		   	   ORDER BY a.branch_id_fk ASC, a.dept_id_fk ASC, rt.name ASC;";

$branch_q 	= "SELECT b.id AS bid, 
					  b.pic, 
					  b.name as bname, 
					  b.pemail, 
					  count(a.id) AS act_usr 
			   FROM branch b 
			   		LEFT JOIN acc a ON (a.branch_id_fk = b.id) 
			   WHERE a.del = '0' AND b.id != 'JKT' 
			   GROUP BY b.id ASC;";

$branch_SQL = @mysql_query($branch_q) or die(mysql_error());

$branch_msg = "-----------------------------------------------------------------<br/>".
			  "STORIX ACCOUNT BILLING <br/>".
              "-----------------------------------------------------------------<br/>".
              "\nDear Mr/Ms. %s<br/><br/>".
              "This is an automated billing system to charge every users for<br/>".
              "accounts they used, provided and registered by IT.<br/><br/>".
              "Your Details,<br/>".
              "<table border=\"0\">".
              "<tr><td>Period</td><td>: ".date('F Y')."</td></tr>".
              "<tr><td>Branch</td><td>: %s</td></tr>".
              "<tr><td>Active users</td><td>: %s</td></tr>".
              "</table><br/>".
              "%s <br/><br/>".
              "For further information please contact IT Department.<br/>".
              "This billing will be send every month on the 15th day<br/>". 
			  "Please re-check always and report any unusual things that you find to IT, help us to improve our system.<br/><br/>".              
           	  "<br/>Kindly Regards,".
              "<br/>- STORIX | KEIRA -";     

while($array = mysql_fetch_array($branch_SQL, MYSQL_ASSOC)) {	
	
	$branch_det_q 	= "SELECT b.id as branch, 
							  rt.name as acc, 
							  COUNT(rt.name) as active, 
							  SUM(ab.price) as cost 
					   FROM acc a 
					   		LEFT JOIN acc_det ad ON (ad.acc_id_fk = a.id) 
					   		LEFT JOIN branch b ON (b.id = a.branch_id_fk) 
					   		LEFT JOIN req_items rt ON (rt.id = ad.item_id_fk) 
					   		LEFT JOIN acc_bill ab ON (ab.item_id_fk = rt.id) 
					   WHERE ad.dsbl = '0' AND b.id = '".$array["bid"]."' 
					   GROUP BY b.id ASC, rt.name ASC;";
	
	$branch_det_SQL 	= @mysql_query($branch_det_q) or die(mysql_error());
	
	$bdet 				= sprintf($bdet_q,"AND a.branch_id_fk = '".$array["bid"]."' ");
	
	$bdetails_SQL 		= @mysql_query($bdet) or die(mysql_error());
	
	$bill_det_rec_SQL 	= @mysql_query($bdet) or die(mysql_error());
	
	$content 		= "<table border=\"0\">";
	
	$ttl_cost 		= 0;
	
	while($array_bdet = mysql_fetch_array($branch_det_SQL,MYSQL_ASSOC)) {
		$type 	= $array_bdet["acc"];
		$active = $array_bdet["active"];
		$cost 	= $array_bdet["cost"];
		$content .= "<tr><td>$type </td>".
					"<td align=\"right\">($active account)</td>".
					"<td>: EUR $cost</td></tr>";	
		$ttl_cost += $array_bdet["cost"];
	}
	
	$content 	.="<tr><td colspan=\"3\"><hr></td></tr>\n".
				  "<tr><td colspan=\"2\">TOTAL</td><td>: EUR $ttl_cost</td></tr>\n".
				  "</table>\n";
	
	$msg_gen 	= sprintf($branch_msg,ucwords($array["pic"]),ucwords($array["bname"]),$array["act_usr"],$content);
	
	$msg 		= $msg_gen.billDetails($bdetails_SQL);
	
	$bname1		= ucwords($array["bname"]);
	
	$bid		= $array["bid"];
	
	$branch_subject = "[STORIX] Test - Account billing for $bname1";
	
	echo $array["bid"]." => ".$array["pemail"]."\n";
	
	record_billing($bid,$bname1,"","",ucwords($array["pic"]),date('m'),date('Y'),date('F Y'),$array["act_usr"],$ttl_cost,$content,billDetails($bill_det_rec_SQL));
	
	//log_hist("102",$array["pemail"]);
	
	//mail($array["pemail"],$branch_subject,$msg,$headers);	
	
	//mail("muhammad.pratama@dbschenker.com",$branch_subject,$msg,$headers);	
}

//----------------------------------------------------------------------------

$dept_q 	= "SELECT d.id AS did, 
					  d.pic, 
					  d.name as dname, 
					  d.pemail, 
					  count(a.id) AS act_usr 
				FROM branch b 
					 LEFT JOIN acc a ON (a.branch_id_fk = b.id) 
					 LEFT JOIN departments d ON (d.id = a.dept_id_fk) 
				WHERE b.id = 'JKT' AND a.dsbl ='0' 
				GROUP BY d.id ASC;";

$dept_SQL 	= @mysql_query($dept_q) or die(mysql_error());

$dept_msg = "-----------------------------------------------------------------<br/>".
			"STORIX ACCOUNT BILLING <br/>".
            "-----------------------------------------------------------------<br/>".
            "\nDear Mr/Ms. %s<br/><br/>".
            "This is an automated billing system to charge every users for<br/>".
            "accounts they used, provided and registered by IT.<br/><br/>".
            "Your Details,<br/>".
            "<table border=\"0\">".
            "<tr><td>Period</td><td>: ".date('F Y')."</td></tr>".
            "<tr><td>Branch</td><td>: JKT</td></tr>".
            "<tr><td>Department</td><td>: %s</td></tr>".
            "<tr><td>Active users</td><td>: %s</td></tr>".
            "</table><br/>".
            "%s <br/><br/>".
            "For further information please contact IT Department.<br/>".
            "This billing will be send every month on the 15th day<br/>". 
			"Please re-check always and report any unusual things that you find to IT, help us to improve our system.<br/><br/>".            
           	"<br/>Kindly Regards,".
            "<br/>- STORIX | KEIRA -";

while($array_2 = mysql_fetch_array($dept_SQL, MYSQL_ASSOC)) {
	
	$dept_det_q 	= "SELECT rt.name AS acc, 
							  COUNT( rt.name ) AS active, 
							  SUM( ab.price ) AS cost 
					   FROM acc a 
					   		LEFT JOIN acc_det ad ON ( ad.acc_id_fk = a.id ) 
					   		LEFT JOIN branch b ON ( b.id = a.branch_id_fk ) 
					   		LEFT JOIN departments d ON ( d.id = dept_id_fk ) 
					   		LEFT JOIN req_items rt ON ( rt.id = ad.item_id_fk ) 
					   		LEFT JOIN acc_bill ab ON ( ab.item_id_fk = rt.id ) 
					   WHERE ad.del = '0' AND b.id = 'JKT' AND d.id = '".$array_2["did"]."' 
					   GROUP BY d.id ASC , rt.name ASC;";
	
	$dept_det_SQL 		= @mysql_query($dept_det_q) or die(mysql_error());
	
	$bdet_2 			= sprintf($bdet_q,"AND a.dept_id_fk = '".$array_2["did"]."' AND a.branch_id_fk = 'JKT' ");
	
	$bdetails_2_SQL 	= @mysql_query($bdet_2) or die(mysql_error());
	
	$bill_det_rec_2_SQL = @mysql_query($bdet_2) or die(mysql_error());
	
	$content_2 		= "<table border=\"0\">";
	
	$ttl_cost_2 	= 0;
	
	while($array_ddet = mysql_fetch_array($dept_det_SQL,MYSQL_ASSOC)) {
		$content_2 .= "<tr><td>".$array_ddet["acc"]."</td>\n
						   <td>(".$array_ddet["active"]." account) </td>\n
						   <td>: EUR ".$array_ddet["cost"]."</td>\n
					  </tr>\n";	
		$ttl_cost_2 += $array_ddet["cost"];
	}
	
	$content_2 .="<tr><td colspan=\"3\"><hr></td></tr>\n
				  <tr><td colspan=\"2\">TOTAL</td><td>: EUR $ttl_cost_2</td></tr>\n
				  </table>\n";
	
	$msg_gen_2 	= sprintf($dept_msg,ucwords($array_2["pic"]),ucwords($array_2["dname"]),$array_2["act_usr"],$content_2);
	
	$msg_2 		= $msg_gen_2.billDetails($bdetails_2_SQL);
	
	$did		= $array_2["did"];
	
	$dept_subject = "[STORIX] Test - Account billing for ".ucwords($array_2["dname"])." in branch JKT ";
	
	echo "JKT => ".ucwords($array_2["dname"])." => ".$array_2["pemail"]."\n";
	
	record_billing("JKT","JKT",$did,ucwords($array_2["dname"]),ucwords($array_2["pic"]),date('m'),date('Y'),date('F Y'),$array_2["act_usr"],$ttl_cost_2,$content_2,billDetails($bill_det_rec_2_SQL));
	
	//log_hist("102",$array_2["pemail"]);
	
	//mail($array_2["pemail"],$dept_subject,$msg_2,$headers);
	
	//mail("muhammad.pratama@dbschenker.com",$dept_subject,$msg_2,$headers);
}

//----------------------------------------------------------------------------

?>