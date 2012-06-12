<?php
/* -----------------------------------------------------
 * File name  : reminder.php	
 * Created by : aryonp@gmail.com
 * -----------------------------------------------------						
 * Purpose	  : Generate reminder to user for REQ, RFA, 
 * & PO that still on pending status. It has interval of 3 days 
 * from the date REQ,RFA, PO created					                 			
 * -----------------------------------------------------
 * 
 */

require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';

$headers = "From: \"STORIX Notification\" <no-reply-storix@dbschenker.com>\r
			\nX-Mailer:".PRODUCT." ".VERSION."/MIME-Version: 1.0
			\nBcc: aryonp@gmail.com\r\n";

$req_query 	= "SELECT r.id, (DATEDIFF(NOW(), r.req_date) % 2) AS itrvl, r.req_date AS datecreate, m.salut as salut, COALESCE(m.lname, m.fname, '-') AS name, m.email FROM req r LEFT JOIN user m ON (m.id = r.mgr_id_fk) WHERE r.status = 'pending' AND r.del = 0 ;";
$req_SQL 	= @mysql_query($req_query) OR die(mysql_error());
$req_msg 	= "Dear %s %s,\n\n".
              "You have a pending request ID #%s\n".
              "Please check it on %s\n\n".
              "This reminder will be repeat every 2 days since the request created.\n\n\n\n".
              "Kindly Regards\n".
              "- STORIX | KEIRA -";

$req_adm_query 	= "SELECT r.id, (DATEDIFF(NOW(), r.auth_date) % 2) AS itrvl, r.auth_date FROM req r WHERE r.status = 'authorized' AND r.del = 0;";
$req_adm_SQL 	= @mysql_query($req_adm_query) OR die(mysql_error());
$req_adm_2_q 	= "SELECT u.salut, COALESCE(u.lname, u.fname, '-') AS name, u.email FROM user u WHERE u.level_id_fk = 4 AND u.del = 0;";
$req_adm_2_SQL 	= @mysql_query($req_adm_2_q) or die(mysql_error());
$req_adm_msg 	= "Dear %s %s,\n\n".
                  "You have an accepted request ID #%s\n".
                  "Please check it on %s\n\n".
                  "This reminder will be repeat every 2 days since the request accepted.\n\n\n\n".
                  "Kindly Regards\n".
                  "- STORIX | KEIRA -";

$rfa_query 	= "SELECT r.id, (DATEDIFF(NOW(), r.date) % 2) AS itrvl, r.date, r.status as status FROM rfa r WHERE r.status = 'pending' AND r.del = 0;";
$rfa_SQL 	= @mysql_query($rfa_query) OR die(mysql_error());
$rfa_2_q 	= "SELECT u.salut, COALESCE(u.lname, u.fname, '-') AS name, u.email FROM user u WHERE u.level_id_fk = 6 AND u.del = 0;";
$rfa_2_SQL 	= @mysql_query($rfa_2_q) or die(mysql_error());
$rfa_msg 	= "Dear %s %s,\n\n".
              "You have a pending RFA ID #%s\n".
              "Please check it on %s\n\n".
              "This reminder will be repeat every 2 days since the RFA created.\n\n\n\n".
              "Kindly Regards\n".
              "- STORIX | KEIRA -";

$po_query 	= "SELECT p.id as po_id, po_nbr, (DATEDIFF(NOW(), p.date) % 2) as itrvl, p.date, p.status as status FROM po p WHERE p.status = 'pending' AND p.del = 0;";
$po_SQL 	= @mysql_query($po_query) OR die(mysql_error());
$po_msg 	= "Dear %s %s,\n\n".
              "You have a pending PO ID#%s\n".
              "Please check it on %s\n\n".
              "This reminder will be repeat every 2 days since the request created.\n\n\n\n".
              "Kindly Regards\n\n".
              "- STORIX | KEIRA -";

$user_po_query 	= "SELECT u.salut, COALESCE(u.lname, u.fname, '-') AS name, u.email FROM user u WHERE u.level_id_fk = 4 AND u.del = 0;";
$user_po_SQL 	= @mysql_query($user_po_query) OR die(mysql_error());

while($req_array = mysql_fetch_array($req_SQL, MYSQL_ASSOC)) {
	if ($req_array["itrvl"] == 0 AND cplday('Y-m-d',$req_array["datecreate"]) != date('Y-m-d')) {
		$req_to			= $req_array["email"];
		$req_salut 		= ucwords($req_array["salut"]);
		$req_name 		= ucwords($req_array["name"]);	
		$req_id 		= $req_array["id"];
		$req_subject 	= "[STORIX] Reminder - Pending Request ID #$req_id";	
		$req_main 		= sprintf($req_msg,$req_salut,$req_name,$req_id,URL_INTRA."/auth_det.php?id=".$req_id);	
		mail($req_to, $req_subject, $req_main, $headers);
		log_hist(103,$req_to);
		//mail("aryonp@gmail.com", $req_subject, $req_main, $headers);
		//echo "Email (REQ) sent to $req_to\n\n";
	}
}

while($req_adm_array = mysql_fetch_array($req_adm_SQL, MYSQL_ASSOC)) {
	if ($req_adm_array["itrvl"] == 0 AND cplday('Y-m-d',$req_adm_array["auth_date"]) != date('Y-m-d')) {
		$req_adm_id 		= $req_adm_array["id"];
		$req_adm_subject 	= "[STORIX] Reminder - Accepted Request ID #$req_adm_id";	
		while($req_adm_2_array = mysql_fetch_array($req_adm_2_SQL, MYSQL_ASSOC)) {
			$req_adm_to 	= $req_adm_2_array["email"];
			$req_adm_salut 	= ucwords($req_adm_2_array["salut"]);
			$req_adm_name 	= ucwords($req_adm_2_array["name"]);
			$req_adm_main 	= sprintf($req_adm_msg,$req_adm_salut,$req_adm_name,$req_adm_id,URL_INTRA."/it_appr_det.php?id=".$req_adm_id);	
			mail($req_adm_to, $req_adm_subject, $req_adm_main, $headers);
			log_hist(104,$req_adm_to);
			//mail("aryonp@gmail.com", $req_adm_subject, $req_adm_main, $headers);
			//echo "Email (REQ - ADM) sent to $req_adm_to\n\n";
		}
	}
}

while($rfa_array = mysql_fetch_array($rfa_SQL, MYSQL_ASSOC)) {
	if ($rfa_array["itrvl"] == 0 AND cplday('Y-m-d',$rfa_array["date"]) != date('Y-m-d')) {
		$rfa_id 	  	= $rfa_array["id"];
		$rfa_subject  	= "[STORIX] Reminder - Pending RFA ID #$rfa_id";
		while($rfa_2_array = mysql_fetch_array($rfa_2_SQL, MYSQL_ASSOC)){
			$rfa_to   	= $rfa_2_array["email"];
			$rfa_salut 	= ucwords($rfa_2_array["salut"]);
			$rfa_name 	= ucwords($rfa_2_array["name"]);
			$rfa_main 	= sprintf($rfa_msg,$rfa_salut,$rfa_name,$rfa_id,URL_INTRA."/appr_det.php?id=".$rfa_id);
			mail($rfa_to, $rfa_subject, $rfa_main, $headers);
			log_hist(105,$rfa_to);
			//mail("aryonp@gmail.com", $rfa_subject, $rfa_main, $headers);
			//echo "Email (RFA) sent to $rfa_to\n\n";
		}
	}
}

while($po_array = mysql_fetch_array($po_SQL, MYSQL_ASSOC)) {
	if ($po_array["itrvl"] == 0 AND cplday('Y-m-d',$po_array["authdate"]) != date('Y-m-d')) {
		$po_id	= $po_array["po_id"];
		$po_nbr	= $po_array["po_nbr"];
		while($po_sent_array = mysql_fetch_array($user_po_SQL, MYSQL_ASSOC)) {
			$po_to		= $po_sent_array["email"];
			$po_salut 	= ucwords($po_sent_array["salut"]);
			$po_name 	= ucwords($po_sent_array["name"]);
			$po_subject = "[STORIX] Reminder - Pending PO #$po_nbr";	
			$po_main 	= sprintf($po_msg,$po_salut,$po_name,$po_id,URL_INTRA."/po_det.php?id=".$po_id);
			mail($po_to, $po_subject, $po_main, $headers);
			log_hist(135,$po_to);
			//mail("aryonp@gmail.com", $po_subject, $po_main, $headers);
			//echo "Email (PO) sent to $po_to\n\n";
		}
	}
}

?>