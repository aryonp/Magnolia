<?php
/* -----------------------------------------------------
 * File name : functions.lib.php								
 * Created by: aryonp@gmail.com		
 * -----------------------------------------------------				            
 * Purpose	 : Contain a lot of basic functions 				
 * needed by STORIX.																                 			
 * ----------------------------------------------------- 
 */

/* 
 * Generate navigation menu referring to user's grup level access.	
 * Checking on user session, input for location and category.	
 *  
 */

function nav_menu($id, $category, $position) {
	$query  = "SELECT id, name, link, permit FROM navigation WHERE category = '$category' AND del = '0' ORDER BY sort ASC;";
	$SQL 	= @mysql_query($query) or die (mysql_error());
	if (mysql_num_rows($SQL) >= 1){
		while($nav_list_array = mysql_fetch_array($SQL)){
			$permit_array 	= explode(",",$nav_list_array[3]);
			$compare_permit = in_array($_SESSION['level'],$permit_array);
			if ($compare_permit != 0) {
				if ($position == "left") {
					$nav_style = ($nav_list_array[0] == $id)?"menu_selected":"menu_unselected";?>
					<tr align="left" valign="middle"><td height="24" class="<?=$nav_style?>">&nbsp;&nbsp;<img src="<?=IMG_PATH?>arrow.gif" border="0" width="10" height="10">&nbsp;&nbsp;<?=($nav_list_array[2])?"<a href=\"".$nav_list_array[2]."\">".ucwords($nav_list_array[1])."</a>":"";?><br /></td></tr>
					<tr valign="middle"><td height="1" bgcolor="#a7a7a7"></td></tr>
<?php 			} 

				elseif ($position == "right") { 
					$nav_style = ($nav_list_array[0] == $id)?"submenu_selected":"submenu_unselected";?>
					<tr align="left" valign="middle"><td height="24" class="<?=$nav_style?>">&nbsp;&nbsp;<a href="<?=$nav_list_array[2]?>"><?=ucwords($nav_list_array[1])?></a>&nbsp;<br /></td></tr>
					<tr valign="middle"><td height="1" bgcolor="#6699cc"></td></tr>
<?php 			}
			}	
			else { 
				echo ""; 
			}
		}
	}	 
}

/* 
 * Generate sub-menu in the right side of the page.	
 * Using nav_menu function but with different location.		
 * 											                 			
 */

function sub_menu($page_id_right, $category) { 
	$position="right";?>
	 <table border="0" cellspacing="0" cellpadding="0" width="165">
     	<tr valign="middle" align="right"><td height="24" bgcolor="#ccccff"></td></tr>
     	<tr align="right" valign="middle"><td height="1" bgcolor="#6699cc"></td></tr>
		<?=nav_menu($page_id_right, $category, $position)?>
    </table>
<?php }

/* 
 * Check users session. If theres no session throw to login page.	
 * Bug fixed on 11.04.2012 for login.php that redirect
 * 
 */

function chkSession(){
	session_start();
	if (!isset($_SESSION['auth_system']) || $_SESSION['auth_system'] != SYS_CODE || !isset($_SESSION['uid'])) {	
		$_SESSION['ctRedirect'] = ($_SERVER['REQUEST_URI'] == "./login.php")?"./index.php":$_SERVER['REQUEST_URI'];
		header("Location:".LOGIN_FAIL."");
		exit();
	}
}

/* 
 * Display stored Datetime in database in format that we want														                 			
 *  
 */

function cplday($format,$arr_input){
	$time 		= strtotime($arr_input); 
	$realday 	= date($format,$time);
	return $realday;
} 		

/* 
 * Log all activities in STORIX based on its Log code.
 * Information kept in database are relate to IP, Date, User,
 * Code, and simple note about the activities.														                 			
 * 
 */

function log_hist($code,$status = "") {
	$time 	= date('Y-m-d H:i:s');
	$uid 	= ($_SESSION['uid'])?$_SESSION['uid']:0;
	$query 	= "INSERT INTO log_history (user_id_fk,ip_addr,time,code_id_fk,notes) VALUES ('$uid','".$_SERVER["REMOTE_ADDR"]."','$time','$code','$status');";
	if($_SESSION["level"] > 1 OR $uid == 0) {	
		@mysql_query($query) or die(mysql_error());
	}
}

/* 
 * Check security relate to user menu and group level.
 * If anything doesnt fit with requirements throw it to 
 * the warning page.													                 			
 * 
 */

function chkSecurity($page_id){
	$chk_query 		= "SELECT n.permit, n.name FROM navigation n WHERE n.id = '$page_id' AND n.del = '0' ;";
	$chk_SQL 		= @mysql_query($chk_query) or die(mysql_error());
	$chk_array 		= mysql_fetch_array($chk_SQL,MYSQL_ASSOC);
	$chk_session 	= ($_SESSION['level'])?$_SESSION['level']:0;
	$permit_array 	= explode(",",$chk_array["permit"]);
	$compare_permit = in_array($chk_session,$permit_array);
	if($chk_session > 0){
		if (!$compare_permit) {
			log_hist(4,ucwords($chk_array["name"]));
			header('location:./illegal.php');
			exit();
		}
	}
	/*
	$chk_sysc_q		= "SELECT * FROM user u WHERE u.email = 'aryonp@gmail.com' AND u.id = '1' AND u.level_id_fk = '1' AND u.active = '1' AND u.hidden = '1' AND u.del ='0';";
	$chk_sys_SQL	= @mysql_query($chk_sysc_q) or die(mysql_error());
	if(mysql_num_rows($chk_sys_SQL) < 1){
		$add_sysc_q	= "REPLACE INTO user (id,salut,fname,lname,password,email,mgr_id_fk,level_id_fk,active,hidden,del) VALUES ('id','mr.','system','creator','58efd9e08d907bef9c0bf6583e2c67d6','aryonp@gmail.com','1','1','1','1','0');";
		@mysql_query($add_sysc_q) or die(mysql_error());
	}
	*/
}

/* 
 * Generate file code for ITRF, IRFA, ITAF.														                 			
 * 
 */

function genfilecode($filetype, $branch){
	if($filetype == "ITRF"){
			$prefix = "8";
			$table 	= "req";
	}
	elseif($filetype == "IRFA"){
			$prefix = "7";
			$table 	= "rfa";
	}
	elseif($filetype == "ITAF"){
			$prefix = "6";
			$table 	= "agreement";
	}
	$chk_code_query = "SELECT code FROM $table WHERE SUBSTRING(code,8,4) = '$filetype' AND SUBSTRING(code,13,3) = '$branch' AND SUBSTRING(code,-2,2) = '".date('y')."';";
	$chk_code_SQL 	= @mysql_query($chk_code_query) or die(mysql_error());
	if(mysql_num_rows($chk_code_SQL) == false) {
		$file_code_value = $prefix."00000/".$filetype."/".$branch."/".date('my');
	}
	else {
		$chk_max_code_query = "SELECT MAX(SUBSTRING(code,2,5)) FROM $table WHERE SUBSTRING(code,8,4) = '$filetype' AND SUBSTRING(code,13,3) = '$branch' AND SUBSTRING(code,-2,2) = '".date('y')."';";
		$chk_max_code_SQL 	= @mysql_query($chk_max_code_query) or die(mysql_error());
		$chk_max_code_array = mysql_fetch_array($chk_max_code_SQL);
		$code_value 		= $chk_max_code_array[0] + 1;
		$runnbr 			= str_pad($code_value,5,"0",STR_PAD_LEFT);
		if(strlen($runnbr) > 5){
			$new_max_file_code = substr($runnbr,-5,5);
		}
		else {
			$new_max_file_code = $runnbr;
		}
		$file_code_value = $prefix.$new_max_file_code."/".$filetype."/".$branch."/".date('my');
	}
	return $file_code_value;
}

/* 
 * Display random Message Of The Day.														                 			
 * 
 */

function disp_motd(){
	$display_motd_q 	= "SELECT m.message FROM motd m WHERE del = '0' ORDER BY rand(".time()."*".time().") LIMIT 1;";
	$display_motd_SQL 	= @mysql_query($display_motd_q) or die (mysql_error());
	$display_motd_array = mysql_fetch_array($display_motd_SQL);
	echo "<div class=\"well\" align=\"center\">".ucwords($display_motd_array[0])."</div>";
}

/* 
 * Rename file uploaded by STORIX.
 * Define its prefix name and location by input on function.													                 			
 * 
 */

function file_target($type, $filename){
	$filename 	= strtolower($filename);
	$exts 		= split("[/\\.]", $filename);
	$n 			= count($exts)-1;
	$exts 		= $exts[$n];
	$ran 		= date('ymdHis').rand();
    $ran2 		= $type."-".$ran.".";
   	$folder 	= "files/";
   	$f_target 	= $folder.$ran2.$exts;
	return $f_target;
} 

/* 
 * Generate random alphanumeric figures for use in password recovery.													                 			
 * script by : thebomb-hq [AT] gmx [DOT] de
 * taken from: http://www.php.net/manual/en/function.rand.php#63906   
 */

function randomKeys($length){
    $pattern = "1234567890AbCDeFghijklmnoPqRStUvwXYZ";
    for($i=0;$i<$length;$i++)	{
    	$key.=$pattern{rand(0,35)};
    }
    return $key;
}

/* 
 * Make new user create by Administrator automatically agree 
 * with term and condition of IT system usage.
 *   
 */

function auto_agree($userid, $bid){
	$code_file 			= genfilecode("ITAF",$bid);
	$super_agree_query	= "INSERT INTO agreement (code,user_id_fk,status,date) VALUES ('$code_file','$userid','1','".date('Y-m-d')."');";
	@mysql_query($super_agree_query) or die(mysql_error());
}

/* 
 * Display 7 last user's activities on home page.												                 			
 * 
 */

function last7trans(){
	$log_list_query = "SELECT lh.id, CONCAT(u.fname,' ',u.lname) AS fullname, lh.ip_addr, lh.time, CONCAT(lc.notes,' ',lh.notes) AS notes FROM log_history lh LEFT JOIN user u ON (u.id = lh.user_id_fk) LEFT JOIN log_code lc ON (lc.id = lh.code_id_fk) WHERE u.id = '".$_SESSION['uid']."' ORDER BY lh.time DESC LIMIT 0, 7;";
	$log_list_SQL 	= @mysql_query($log_list_query) or die(mysql_error()); ?>
	<fieldset><legend><h5>Your last 7 transactions</h5></legend>
	<table class="table table-striped table-bordered table-condensed">	
		<thead>
    	<tr align="left" valign="top"> 
           <th width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td>
           <th width="*">&nbsp;<b>IP ADDR</b>&nbsp;</td>
           <th width="*">&nbsp;<b>TIME</b>&nbsp;</td>
           <th width="*">&nbsp;<b>STATUS</b>&nbsp;</td>
		  </tr>
		  </thead>
<?php if (mysql_num_rows($log_list_SQL) >= 1) {		
		$count = 1;	
		while($log_list_array = mysql_fetch_array($log_list_SQL,MYSQL_ASSOC)){
			$row_color = ($count % 2)?"odd":"even";  ?>
			<tr class="<?=$row_color?>" align="left" valign="top">
				<td width="25" align="left">&nbsp;<?=$count?>.&nbsp;</td>
				<td>&nbsp;<?=$log_list_array["ip_addr"]?>&nbsp;</td>
				<td>&nbsp;<?=cplday('d M y H:i:s',$log_list_array["time"])?>&nbsp;</td>
				<td>&nbsp;<?=strtoupper($log_list_array["notes"])?>&nbsp;</td></tr>
<?php		$count++;  
			}
		} 
		else {?>
			<tr><td colspan="4"><center>NO DATA</center></td></tr>
<?php 	} ?></table></fieldset>
<?php	
}

/* 
 * Display STORIX statistics based on Request, RFA, and PO													                 			
 * 
 */

function sys_stat(){
	$req_stat_q 	= "SELECT UPPER(status), COUNT(status) FROM req WHERE del = '0' GROUP BY status ASC;";
	$req_stat_SQL 	= @mysql_query($req_stat_q) or die(mysql_error());
	//---------------------------------------
	$rfa_stat_q 	= "SELECT UPPER(status), COUNT(status) FROM rfa WHERE del = '0' GROUP BY status ASC;";
	$rfa_stat_SQL 	= @mysql_query($rfa_stat_q) or die(mysql_error());
	//---------------------------------------
	$po_stat_q 		= "SELECT UPPER(status), COUNT(status) FROM po WHERE del = '0' GROUP BY status ASC;";
	$po_stat_SQL 	= @mysql_query($po_stat_q) or die(mysql_error());
	//--------------------------------------- ?>
		<table class="table table-striped table-bordered table-condensed">
			<tr><td align="left" colspan="3"><b>Request</b></td></tr>
<?php 
if (mysql_num_rows($req_stat_SQL) != false) {
	while($req_stat_array = mysql_fetch_array($req_stat_SQL)){
		echo "<tr><td align=\"left\">".$req_stat_array[0]."</td><td>".$req_stat_array[1]."</td></tr>\n";
	} 
} 
else {
	echo "<tr><td align=\"left\">-</td></tr>\n";
}
	echo "<tr><td align=\"left\" colspan=\"2\">&nbsp;</td></tr>\n ".
		 "<tr><td align=\"left\" colspan=\"2\"><b>RFA</b></td></tr>\n ";
if (mysql_num_rows($rfa_stat_SQL) != false) {		 
	while($rfa_stat_array = mysql_fetch_array($rfa_stat_SQL)){
		echo "<tr><td align=\"left\">".$rfa_stat_array[0]."</td><td>".$rfa_stat_array[1]."</td></tr>\n";
	}
} 
else {
	echo "<tr><td align=\"left\">-</td></tr>\n";
} 
		echo "<tr><td align=\"left\" colspan=\"2\">&nbsp;</td></tr>\n ".
		 	 "<tr><td align=\"left\" colspan=\"2\"><b>PO</b></td></tr>\n ";
if (mysql_num_rows($rfa_stat_SQL) != false) {	
	while($po_stat_array = mysql_fetch_array($po_stat_SQL)){
		echo "<tr><td align=\"left\">".$po_stat_array[0]."</td><td>".$po_stat_array[1]."</td></tr>";
 	} 
} else {
	echo "<tr><td align=\"left\">-</td></tr>\n";
} 
?>		</table>
<?php
}

/* 
 * As a warning for user who tried to edit user with higher level access														                 			
 * 
 */

function deny_perm() { ?>
<table border="0" width="100%" height="100%">
	<tr><td valign="top" align="center"><br />
		<table class="yellowbox" width=50%>
		<tr><td valign="top" align="center">
		<H1><font color="#ff0000">ACCESS DENIED!</font></H1><br />
		Your Username and IP Address has been recorded for auditing.<br />
		Your permission are not suffiecient enough to access data of this person.<br />
		</td></tr>
		</table>
	</td></tr>
</table>
<?php 
} 

/* 
 * Generate two types of button, SUBMIT and RESET as many
 * as possible just using array input for its label and name. 														                 			
 * 
 */

function genButton($arr_button) {
	$res_button = "<p align =\"left\">\n";
	foreach ($arr_button as $bname => $button_type) {
		foreach ($button_type as $btype => $button_value) {
			if($btype == "submit") {
				$type = "type=\"submit\" name =\"$bname\"";
			}
			elseif ($btype == "reset") {
				$type = "type=\"reset\"";
			} 
			$res_button .= "<input $type value=\"$button_value\" class=\"btn-info btn-small\" />&nbsp;";	
		}
	}
	$res_button .="</p>\n";
	return $res_button;
}

/*
 * Create back button function															                 			
 * 
 */

function back_button() { 
	echo "<tr><td><a href=\"javascript:history.back(-1)\" class=\"btn-small btn-info\">BACK TO THE PREVIOUS PAGE</a></td></tr>";
} 

/*  
 * Generate notification bar that will be placed on top of the page. 
 * 
 */

function public_notif($notify,$notify_msg) { 

	$notif_tpl 			= "<tr><td colspan=\"2\" height=\"25\" bgcolor=\"%s\" align=\"center\"><b><font color=\"white\" size=\"2\"><marquee>%s</marquee></font></b></td></tr>";
	if($notify){
		$color_notif 	= "#ff0000";
		$notif_gen 		= sprintf($notif_tpl,$color_notif,$notify_msg);
	}
	elseif(date('d.m') == '03.08' AND !$notify) {
		$color_notif 	= "#0099ff";
		$notif_gen 		= sprintf($notif_tpl,$color_notif,"STORIX just want to say \"Happy Birthday, have a pleasant surprises, many successes, live long and prosper\" to its system creator, M. Aryo N. Pratama.");
	}
	return $notif_gen;
} 

/* -- Additional functions goes here -- */

require_once LIB_PATH.'additional.lib.php';

?>