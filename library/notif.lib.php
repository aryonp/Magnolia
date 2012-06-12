<?php
/* -----------------------------------------------------
 * File name  : notif.class.php	
 * Created by : aryonp@gmail.com
 * -----------------------------------------------------
 * Purpose	  : Configure notification according to its 
 * function and generate emails for it				   						                 			
 * -----------------------------------------------------
 */

function notify_po($po_id, $msg) {
	$headers = "From: \"STORIX Notification\" <no-reply-storix@dbschenker.com>\r\nX-Mailer:".PRODUCT." ".VERSION."/MIME-Version: 1.0\nBcc: aryonp@gmail.com\r\n";
	if ($_SESSION['level'] <= 4) {   			
		$notif_po_q  = "SELECT u.salut, CONCAT(u.fname,' ',u.lname) AS name, u.email FROM user u LEFT JOIN po p ON (p.user_id_fk = u.id) WHERE p.id = '$po_id' AND u.active = 1 AND u.del = 0;";
	}
	
	elseif($_SESSION['level'] == 5) {   			
		$notif_po_q  = "SELECT u.salut, CONCAT(u.fname,' ',u.lname) AS name, u.email FROM user u WHERE u.level_id_fk = 4 AND u.active = 1 AND u.del = 0;";
	}
	$subject 		= "PO $msg ID #$po_id";
	$notif_po_SQL 	= @mysql_query($notif_po_q) or die(mysql_error());
	$notif_po_msg 	= "Dear %s %s\n\n".
					  "You have a %s\n".
					  "Please check it on %s\n\n\n\n".
					  "Kindly Regards\n".
					  "- STORIX | KEIRA -";
	$url			= URL_INTRA."/po_det.php?id=".$po_id;
	if (mysql_num_rows($notif_po_SQL) >= 1) {
		while($notif_po_array = mysql_fetch_array($notif_po_SQL,MYSQL_ASSOC)){
			$salut 	= ucwords(trim($notif_po_array["salut"]));
			$name 	= ucwords(trim($notif_po_array["name"]));
			$email	= trim($notif_po_array["email"]);
			$message = sprintf($notif_po_msg,$salut,$name,$subject,$url);
			mail($email,$subject,$message,$headers);
			//log_hist(110,$email);
		}
	}
}

//------------------------------------------------------------------------------

function notify_pass_reset($userid, $new_pass) {
	$headers 		= "From: \"STORIX Notification\" <no-reply-storix@dbschenker.com>\r\nX-Mailer:".PRODUCT." ".VERSION."/MIME-Version: 1.0\nBcc: aryonp@gmail.com\r\n";
	$notif_user_q 	= "SELECT u.salut, CONCAT(u.fname,' ',u.lname) AS name, u.email FROM user u WHERE u.id = '$userid' AND u.active = 1 AND u.del = 0;";
	$notif_user_SQL = @mysql_query($notif_user_q) or die(mysql_error());
	$notif_pass_msg = "Dear %s %s\n\n".
					  "Your password has been resetted\n".
					  "Email    : %s\n".
					  "Password : %s\n\n".
					  "Please check your STORIX account at ".URL_INTRA."\n\n".
					  "Please change your password immediately after login in \"My Settings\"\n\n".
					  "Kindly Regards\n".
					  "- STORIX | KEIRA -";
	while($notif_user_array = mysql_fetch_array($notif_user_SQL, MYSQL_ASSOC)) {
		$salut 	= ucwords($notif_user_array["salut"]);
		$name 		= ucwords($notif_user_array["name"]);
		$to 		= $notif_user_array["email"];
		$subject 	= "\"Reset Password\" confirmation";	
		$message	= sprintf($notif_pass_msg,$salut,$name,$to,$new_pass,URL_INTRA);
		mail($to, $subject, $message, $headers);
		log_hist(109,$to);
	}
}

//------------------------------------------------------------------------------

function notify_itrf($id) {
	if($_SESSION["level"] > 7) {
		notify_itrf_1($id);
	}
	else {
		notify_adm_appr($id,"Authorized");
	}
}

//------------------------------------------------------------------------------ 

function notify_itrf_1($id){
	$headers	= "From: \"STORIX Notification\" <no-reply-storix@dbschenker.com>\r\n
			   X-Mailer:".PRODUCT." ".VERSION."/MIME-Version: 1.0\n
                        Bcc: aryonp@gmail.com\r\n";
	$url 		= URL_INTRA."/auth_det.php?id=$id";
	$subject 	= "Pending request ID #$id";
	$message 	= "Dear %s %s\n\n".
				  "You have %s \n".
				  "Please check it on %s \n".
				  "Please contact IT Manager for reconfirmation\n\n\n".
				  "Kindly Regards\n\n".
				  "- STORIX | KEIRA -";

	$query 	= "SELECT m.salut, CONCAT(m.fname,' ',m.lname) as name, m.email FROM user u LEFT JOIN user m ON (m.id = u.mgr_id_fk) WHERE u.id = '".$_SESSION['uid']."' AND u.active = 1 ";
	$SQL 	= @mysql_query($query) or die(mysql_error());
	while($array = mysql_fetch_array($SQL, MYSQL_ASSOC)) {
		$to		= trim($array["email"]);
		$salut	= ucwords(trim($array["salut"]));
		$name	= ucwords(trim($array["name"]));
		$msg 	= sprintf($message,$salut,$name,$subject,$url);
	 	mail($to,$subject,$msg,$headers);
	}
}

//------------------------------------------------------------------------------
//Additional Message OK

function notify_itrf_2($id_input,$pesan){
	$headers 	= "From: \"STORIX Notification\" <no-reply-storix@dbschenker.com>\r\nX-Mailer:".PRODUCT." ".VERSION."/MIME-Version: 1.0\nBcc: aryonp@gmail.com\r\n";

	$req_chk_q	= "SELECT it.name AS item, rd.status 
				   FROM req_det rd 
						LEFT JOIN req r ON (r.id = rd.req_id_fk)
						LEFT JOIN req_items it ON (it.id = rd.item_id_fk) 
				   WHERE r.id = '$id_input' AND r.del = '0';";
	$req_chk_SQL= @mysql_query($req_chk_q) or die(mysql_error());
	if(mysql_num_rows($req_chk_SQL) >= 1) {
		$req_d = "Request detail(s) : \n\n";
		while($rd_arr = mysql_fetch_array($req_chk_SQL,MYSQL_ASSOC)){
			$req_d		.= "- ".ucwords($rd_arr["item"])." -> ".strtoupper($rd_arr["status"])."\n\n";
		}
	}
	else {
		$req_d 	= "";
	}
	//
	
	if ($_SESSION["level"] == 7) {
	//manager -- process 2 oK
		$chk_query = "SELECT u.salut, CONCAT(u.fname,' ',u.lname) AS name, u.email FROM user u LEFT JOIN req r ON (r.user_id_fk = u.id) WHERE r.id = '$id_input' AND u.active = 1;";
	}
	$chk_SQL 			= @mysql_query($chk_query) or die(mysql_error());
	$notif_itrf_2_msg	= "Dear %s %s\n\n".
						  "You have a/an %s\n".
						  "Please check it on %s\n\n".
						  "%s\n\n".
						  "Kindly Regards\n".
						  "- STORIX | KEIRA -";
	while($chk_array = mysql_fetch_array($chk_SQL, MYSQL_ASSOC)) {
		$salut 		= ucwords(trim($chk_array["salut"]));
		$name 		= ucwords(trim($chk_array["name"]));
		$to 		= trim($chk_array["email"]);
		$url 		= URL_INTRA."/req_det.php?id=$id_input";
		$subject 	= "$pesan request ID #$id_input";
		$message	= sprintf($notif_itrf_2_msg,$salut,$name,$subject,$url,$req_d);
		mail($to, $subject, $message, $headers);
	}
}

//------------------------------------------------------------------------------
//Additional Message OK

function notify_irfa($id_input,$pesan){
	$headers 	= "From: \"STORIX Notification\" <no-reply-storix@dbschenker.com>\r\nX-Mailer:".PRODUCT." ".VERSION."/MIME-Version: 1.0\nBcc: aryonp@gmail.com\r\n";
	$rfa_msg 	= "Dear %s %s \n\n".
				  "You have a/an %s\n".
	              "Please check it on %s\n\n".
	              "%s\n\n".
	              "%s\n\n".
	              "Kindly Regards\n".
	              "- STORIX | KEIRA -";
	$rfa_chk_q	= "SELECT rd.item, rd.status 
				   FROM rfa_det rd 
						LEFT JOIN rfa r ON (r.id = rd.rfa_id_fk) 
				   WHERE r.id = '$id_input' AND r.del = '0';";
	$rfa_chk_SQL= @mysql_query($rfa_chk_q) or die(mysql_error());
	if(mysql_num_rows($rfa_chk_SQL) >= 1) {
		$rfa_d			= "RFA detail(s) : \n\n";
		while($rd_arr = mysql_fetch_array($rfa_chk_SQL,MYSQL_ASSOC)){
			$rfa_d		.= "- ".ucwords($rd_arr["item"])." -> ".strtoupper($rd_arr["status"])."\n\n";
		}
	}
	else {
		$rfa_d 	= "";
	}
	if ($_SESSION["level"] <= 5) {
	//super-admin & administrator -- process 6 ok
		$chk_query = "SELECT u.salut, CONCAT(u.fname,' ',u.lname) AS name, u.email, r.appr_note as anote FROM user u LEFT JOIN rfa r ON (r.user_id_fk = u.id) WHERE u.level_id_fk = 6 AND u.active = 1;";
		$chk_SQL = @mysql_query($chk_query) or die(mysql_error());
		if(mysql_num_rows($chk_SQL)>=1) {
			while($chk_array = mysql_fetch_array($chk_SQL, MYSQL_ASSOC)){
				$salut1 	= ucwords($chk_array["salut"]);
				$name1 		= ucwords($chk_array["name"]);
				$to1 		= $chk_array["email"];
				$subject1 	= "$pesan RFA ID #$id_input";
				$note1		= ($chk_adm_array["anote"])?"Note:\n".$chk_adm_array["anote"]:"";
				$url1 		= URL_INTRA."/appr_det.php?id=$id_input";
				$rfa_msg1	= sprintf($rfa_msg,$salut1,$name1,$subject1,$url1,$note1,$rfa_d);
				mail($to1,$subject1,$rfa_msg1,$headers);
			}
		}
	}
	elseif ($_SESSION["level"] == 6) {
	//approver -- process 7 ok 
		$chk_adm_query 	= "SELECT u.salut, CONCAT(u.fname,' ',u.lname) AS name, u.email, r.appr_note as anote FROM user u LEFT JOIN rfa r ON (r.user_id_fk = u.id) WHERE r.id = '$id_input' AND u.active = 1;";
		$chk_adm_SQL 	= @mysql_query($chk_adm_query) or die(mysql_error());
		while($chk_adm_array = mysql_fetch_array($chk_adm_SQL, MYSQL_ASSOC)){
			$salut2 	= ucwords($chk_adm_array["salut"]);
			$name2 		= ucwords($chk_adm_array["name"]);
			$to2 		= $chk_adm_array["email"];
			$subject2 	= "$pesan RFA ID #$id_input";
			$note2		= ($chk_adm_array["anote"])?"Note:\n".$chk_adm_array["anote"]:"";
			$url2 		= URL_INTRA."/rfa_det.php?id=$id_input";
			$rfa_msg2	= sprintf($rfa_msg,$salut2,$name2,$subject2,$url2,$note2,$rfa_d);
			mail($to2,$subject2,$rfa_msg2,$headers);
		}
	}
}

//------------------------------------------------------------------------------
//Additional Message OK

function notify_adm_appr($id,$pesan){
	$headers 		= "From: \"STORIX Notification\" <no-reply-storix@dbschenker.com>\r\nX-Mailer:".PRODUCT." ".VERSION."/MIME-Version: 1.0\nBcc: aryonp@gmail.com\r\n";
	$headers_html 	= "From: \"STORIX Notification\" <no-reply-storix@dbschenker.com>\r\nX-Mailer:".PRODUCT." ".VERSION."/MIME-Version: 1.0\nContent-type:text/html;charset=iso-8859-1\nBCC:aryonp@gmail.com\r\n";
	//Message details
	$req_det_q 	= "SELECT rd.id, rt.name, rd.status, al.lname 
			       FROM req_det rd 
			    		LEFT JOIN req_items rt ON (rd.item_id_fk = rt.id) 
			    		LEFT JOIN acc_level al ON (al.id = rd.acclvl_id_fk) 
				   WHERE rd.req_id_fk = '$id' AND rd.del = 0;";	
	$req_det_SQL = @mysql_query($req_det_q) or die(mysql_error());
	if(mysql_num_rows($req_det_SQL) >= 1) {
		$req_det_ct  = "Request detail(s) :\n\n";
		while($req_det_array = mysql_fetch_array($req_det_SQL, MYSQL_ASSOC)){
			$req_det_ct .= "- ".ucwords($req_det_array["name"])." -> (Grup/Level : ".strtoupper($req_det_array["lname"])." )* -> ".strtoupper($req_det_array["status"])."\n\n";
		}
		$req_det_ct  .= "(*)Only for requesting account\n\n\n";
	}
	else {
		$req_det_ct  = "";	
	}
	//End message details
	if ($_SESSION["level"] == 7) {
	//manager -- process 3 ok
		$chk_query 	= "SELECT u.salut, CONCAT(u.fname,' ',u.lname) AS name, u.email FROM user u WHERE u.level_id_fk = 4 AND u.active = 1;";
		$chk_SQL	= @mysql_query($chk_query) or die(mysql_error());
		$notif_adm_appr_7_msg = "Dear %s %s\n\n".
								"You have a/an %s\n".
								"Please check your STORIX account on\n".
								"%s\n\n".
								"%s\n\n\n".
								"Kindly Regards\n".
								"- STORIX | KEIRA -";
		if (mysql_num_rows($chk_SQL) >= 1) {
			$subject1 	= "$pesan request ID #$id";
			while($chk_array = mysql_fetch_array($chk_SQL, MYSQL_ASSOC)){
				$salut1 	= ucwords($chk_array["salut"]);
				$name1 		= ucwords($chk_array["name"]);
				$url1		= URL_INTRA."/it_appr_det.php?id=".$id;
				$message1	= sprintf($notif_adm_appr_7_msg,$salut1,$name1,$subject1,$url1,$req_det_ct);
				mail($chk_array["email"],$subject1,$message1,$headers);
			}
		}
	}
	elseif ($_SESSION["level"] <= 4) {
	//super-admin -- process 5 ok
		$chk_query 	= "SELECT u.id, u.email AS email FROM user u WHERE (u.level_id_fk = '4' OR u.level_id_fk = '5') AND u.active = '1';";
		$chk_SQL 	= @mysql_query($chk_query) or die(mysql_error());
		$url2 		= URL_INTRA."/req_arc_det.php?id=".$id;
		$notif_adm_appr_4_msg = "Dear IT Administrator(s),\n".
		                        "You have a/an %s.\n".
		                        "Please respond immediately using your STORIX account on \n".
		                        "%s\n\n".
		                        "%s\n\n\n".
		                        "Kindly Regards\n".
		                        "- STORIX | KEIRA -";
		if (mysql_num_rows($chk_SQL) >= 1) {
			$subject2 = $pesan." request ID #".$id ;
			while($chk_array = mysql_fetch_array($chk_SQL, MYSQL_ASSOC)){
				$message2 = sprintf($notif_adm_appr_4_msg,$subject2,$url2,$req_det_ct);
				mail($chk_array["email"],$subject2,$message2,$headers);
			}
		}
	}
}

//------------------------------------------------------------------------------

function notify_it_appr($id,$pesan){
	$headers = "From: \"STORIX Notification\" <no-reply-storix@dbschenker.com>\r\nX-Mailer:".PRODUCT." ".VERSION."/MIME-Version: 1.0\nBcc: aryonp@gmail.com\r\n";
	if ($_SESSION["level"] <= 4) {
	//super-admin -- process 4
		$chk_adm_query 		= "SELECT u.salut, CONCAT(u.fname,' ',u.lname) AS name, u.email FROM user u LEFT JOIN req r ON (r.user_id_fk = u.id) WHERE r.id = '$id' AND u.active = '1' ";
		$chk_adm_SQL 		= @mysql_query($chk_adm_query) or die(mysql_error());
		$msg			 	= "Dear %s %s\n\n".
							  "You have a/an %s\n".
							  "Please check your STORIX account at %s\n\n\n\n".
							  "Kindly Regards\n".
							  "- STORIX | KEIRA -";
		$url				= URL_INTRA."/req_det.php?id=".$id;
		while($array = mysql_fetch_array($chk_adm_SQL, MYSQL_ASSOC)){
			$salut 		= ucwords(trim($array["salut"]));
			$name 		= ucwords(trim($array["name"]));
			$to 		= trim($array["email"]);
			$subject 	= "$pesan request ID #$id";
			$message	= sprintf($msg,$salut,$name,$subject,$url);
			mail($to,$subject,$message,$headers);
		}
	}
}

//------------------------------------------------------------------------------

function notif_rd($rdid) {
	$query = "SELECT CONCAT(u.fname,' ',u.lname) AS requester,
       		         u.email,
                     r.id AS rid, 
                     r.emp_name AS ename, 
                     r.req_type AS rtype,
                     rt.name AS rtname,
                     rd.status,
                     CONCAT(c.fname,' ',c.lname) AS confirmer,
                     rd.confNote AS cnote
             FROM req_det rd 
                  LEFT JOIN req r ON (r.id = rd.req_id_fk)
                  LEFT JOIN user u ON (u.id = r.user_id_fk)
                  LEFT JOIN req_items rt ON (rt.id = rd.item_id_fk)
                  LEFT JOIN user c ON (c.id = rd.confID)
             WHERE r.del = '0' AND rd.confirm = '1' AND rd.id = '$rdid';"; 
	$SQL     = @mysql_query($query) or die(mysql_error());
	$headers = "From: \"STORIX Notification\" <no-reply-storix@dbschenker.com>\r\nX-Mailer:".PRODUCT." ".VERSION."/MIME-Version: 1.0\nBcc: aryonp@gmail.com\r\n";
	$msg     = "Dear Mr./Ms. %s,\n".
			   "You have an update in your request ID #%s for '%s' regarding '%s'\n".
			   "You can check it by log on using your STORIX account on\n".
               "%s\n\n".
               "Update details,\n".
			   "- %s -> %s\n".
               "  Confirmed by %s\n".
               "  Note : %s\n\n\n".
	           "Kindly Regards,\n".
			   "- STORIX | KEIRA -";
	if(mysql_num_rows($SQL) >= 1) {
  		while($array = mysql_fetch_array($SQL,MYSQL_ASSOC)) {
  			$rid	 = $array["rid"];
  			$subject = "Update on Request ID #$rid";
			$link	 = URL_INTRA."/req_det.php?id=".$rid;
  			$to		 = $array["email"];
       		$message = sprintf($msg,ucwords($array["requester"]),$array["rid"],ucwords($array["ename"]),ucwords($array["rtype"]),$link,ucwords($array["rtname"]),strtoupper($array["status"]),ucwords($array["confirmer"]),nl2br($array["cnote"]));
      		mail($to,$subject,$message,$headers);
  		}
	}
}

//------------------------------------------------------------------------------

?>