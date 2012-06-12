<?php
/* -----------------------------------------------------
 * File name  	: po.class.php	
 * Created by 	: aryonp@gmail.com		
 * Last Update	: 04.05.2010	
 * -----------------------------------------------------
 * Purpose	  	: Specific functions for Purchase Order	   							                 			
 * -----------------------------------------------------
 */

function genPO(){
	$chk_code_query  = "SELECT po_nbr FROM po WHERE SUBSTRING(po_nbr,-4,4) = '".date('Y')."' AND del = '0';";
	$chk_code_SQL = mysql_query($chk_code_query);
	if(mysql_num_rows($chk_code_SQL) == false) {
		$file_code_value = "1000/".date('mY');
	}
	else {
		$chk_max_code_query  = "SELECT MAX(SUBSTRING(po_nbr,1,4)) as number FROM po WHERE SUBSTRING(po_nbr,-4,4) = '".date('Y')."' AND del = '0';";
		$chk_max_code_SQL = @mysql_query($chk_max_code_query) or die(mysql_error());
		$chk_max_code_array = mysql_fetch_array($chk_max_code_SQL, MYSQL_ASSOC);
		$code_value = $chk_max_code_array["number"];
		$runnbr = $code_value + 1;
		if(strlen($runnbr) > 4){
			$new_max_file_code = substr($runnbr,1,4);
		}
		else {
			$new_max_file_code = $runnbr;
		}
		$file_code_value = $new_max_file_code."/".date('mY');
	}
	return $file_code_value;
}

//------------------------------------------------------------------------------

function kurs() {
	$kurs = array("CAD","EUR","IDR","SGD","USD");?>
	<select name="kurs" class="input-small">
				<option value="-">-----------</option>
<?php foreach($kurs as $valas) { ?>
				<option value ="<?=$valas?>"><?=$valas?></option>
<?php	} ?>
 	</select>
<?php 
}

//------------------------------------------------------------------------------

function po_list() {
	$query = "SELECT id, po_nbr FROM po WHERE del = '0' AND status = 'authorize' ORDER BY po_nbr DESC";
	$sql = @mysql_query($query) or die(mysql_error()); ?>
	<select name="po_list" class="input-small">
				<option value="-">-----------</option>
<?php	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){?>
		<option value ="<?=$array['id']?>"><?=$array['po_nbr']?></option>
<?php	} ?>
		</select>
<?php	}

//------------------------------------------------------------------------------

function po_list_eval() {
	$query = "SELECT p.id, p.po_nbr FROM po p WHERE p.del = '0' AND p.status = 'authorize' AND p.id NOT IN (SELECT es.po_nbr FROM ev_std es WHERE es.del = 0) ORDER BY p.po_nbr ASC";
	$sql = @mysql_query($query) or die(mysql_error()); ?>
	<select name="po_list">
				<option value="-">-----------</option>
<?php	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){?>
		<option value ="<?=$array['id']?>"><?=$array['po_nbr']?></option>
<?php	} ?>
		</select>
<?php	}

//------------------------------------------------------------------------------

function po_incl() {
	$kurs = array("VAT","DISC");?>
	<select name="po_incl" class="input-small">
				<option value="-">-----------</option>
<?php foreach($kurs as $valas) { ?>
				<option value ="<?=$valas?>"><?=$valas?></option>
<?php	} ?>
 	</select>
<?php 
}

//------------------------------------------------------------------------------

function po_calc($method,$stotal,$incl) {
	if($method == "VAT"){
		$po_gtotal = $stotal + $incl;
	}
	elseif($method == "DISC"){
		$po_gtotal = $stotal - $incl;
	}
	elseif ($method == "-") {
		$po_gtotal = $stotal;
	}
	return $po_gtotal;
}

//------------------------------------------------------------------------------

function po_update($po_status){
	$po_id_fk 		= trim($_POST['po_id']);
	$po_auth_id_fk 	= $_SESSION['uid'];
	$po_auth_date 	= date('Y-m-d H:i:s');
	$upd_po_det_q 	= "UPDATE po SET status = '$po_status', auth_id_fk = '$po_auth_id_fk', authdate ='$po_auth_date' WHERE id = '$po_id_fk';";
	@mysql_query($upd_po_det_q) or die(mysql_error());
	header("location:".$_SERVER['PHP_SELF']."?id=".$po_id_fk);
	exit();
}

//------------------------------------------------------------------------------

function po_cctr_list(){
	$po_cctr_q = "SELECT ic.id, CONCAT(ic.code,' : ',ic.ba,' > ',ic.spc) AS cctr FROM inv_cctr ic WHERE ic.del = 0 ORDER BY ic.ba ASC;";
	$po_cctr_SQL = @mysql_query($po_cctr_q) or die(mysql_error());
	echo "<select name = \"po_cctr[]\">\n";
    echo "<option value = \"-\" SELECTED>---------------------</option>\n";
  	while($pcctr_array = mysql_fetch_array($po_cctr_SQL, MYSQL_ASSOC)){
    	echo "<option value =\"".$pcctr_array["id"]."\">".$pcctr_array["cctr"]."</option>\n";
	} 
	echo "</select>\n";
}

//------------------------------------------------------------------------------

function po_cctr_select($sort,$compare_data){
	$po_cctr_q = "SELECT ic.id, CONCAT(ic.code,' : ',ic.ba,' > ',ic.spc) AS cctr FROM inv_cctr ic WHERE ic.del = 0 ORDER BY ic.ba ASC;";
	$po_cctr_SQL = @mysql_query($po_cctr_q) or die(mysql_error());
	echo "<select name = \"po_cctr[$sort]\">\n";
    echo "<option value = \"-\" SELECTED>---------------------</option>\n";
  	while($pcctr_array = mysql_fetch_array($po_cctr_SQL, MYSQL_ASSOC)){
  		$compare = ($pcctr_array["id"] == $compare_data)?"SELECTED":"";	
    	echo "<option value = \"".$pcctr_array["id"]."\" $compare>".$pcctr_array["cctr"]."</option>\n";
	} 
  	echo "</select>\n";
}

//------------------------------------------------------------------------------

function po_itcat_list(){
	$po_itcat_q 	= "SELECT ic.id, ic.name FROM item_cat ic WHERE ic.del = 0 ORDER BY ic.name ASC;";
	$po_itcat_SQL 	= @mysql_query($po_itcat_q) or die(mysql_error());
	echo "<select name = \"po_itcat[]\">\n";
    echo "<option value = \"-\">---------------------</option>\n";
  	while($itcat_array = mysql_fetch_array($po_itcat_SQL, MYSQL_ASSOC)){
    	echo "<option value =\"".$itcat_array["id"]."\">".strtoupper($itcat_array["name"])."</option>\n";
	} 
	echo "</select>\n";
}

//------------------------------------------------------------------------------

function po_itcat_select($sort,$compare_data){
	$po_itcat_q 	= "SELECT ic.id, ic.name FROM item_cat ic WHERE ic.del = 0 ORDER BY ic.name ASC;";
	$po_itcat_SQL 	= @mysql_query($po_itcat_q) or die(mysql_error());
	echo "<select name = \"po_itcat[$sort]\">\n";
    echo "<option value = \"-\">---------------------</option>\n";
  	while($itcat_array = mysql_fetch_array($po_itcat_SQL, MYSQL_ASSOC)){
  		$compare = ($itcat_array["id"] == $compare_data)?"SELECTED":"";	
    	echo "<option value = \"".$itcat_array["id"]."\" $compare>".strtoupper($itcat_array["name"])."</option>\n";
	} 
  	echo "</select>\n";
}

//------------------------------------------------------------------------------

function po_rcv($id,$sort) {
	$po_rcv_q 	= "SELECT pd.rcv FROM po_det pd WHERE pd.id = '$id' ";
	$po_rcv_SQL = @mysql_query($po_rcv_q) or die(mysql_error());
	$rcv_array 	= mysql_fetch_array($po_rcv_SQL, MYSQL_ASSOC);
	$rcv_det 	= array("0"=>"NO","1"=>"YES");
	echo "<select name=\"po_rcv[$sort]\">\n";
		foreach($rcv_det as $rcv_key => $rcv_status) {
			$compare_rcv = ($rcv_key == $rcv_array["rcv"])?"SELECTED":"";
			echo "<option value =\"$rcv_key\" $compare_rcv>$rcv_status</option>\n";
		} 
 	echo "</select>\n";
}

//------------------------------------------------------------------------------

function po_type($id) {
	$cons_det 	= array("0"=>"NON-CONSUMABLE","1"=>"CONSUMABLE");
	echo "<select name=\"po_type\">\n";
	echo "<option value=\"-\">--------</option>\n";
	foreach($cons_det as $type_key => $type_name) {
		$compare_cons = ($type_key == $id)?"SELECTED":"";
		echo "<option value =\"$type_key\" $compare_cons>$type_name</option>\n";
	} 
 	echo "</select>\n";
}

//------------------------------------------------------------------------------

function po_scurr($curr) {
	$lcurr = array("CAD","EUR","IDR","SGD","USD");
	echo "<select name=\"po_curr\" class=\"input-small\">\n";
	echo "<option value=\"-\">--------</option>\n";
	foreach($lcurr as $scurr) {
		$compare_curr = ($scurr == $curr)?"SELECTED":"";
		echo "<option value =\"$scurr\" $compare_curr>$scurr</option>\n";
	} 
 	echo "</select>\n";
}

//------------------------------------------------------------------------------

function po_req($id){
	$q 		= "SELECT pr.req AS pr FROM po_req pr WHERE pr.po_det = '$id';";
	$SQL 	= @mysql_query($q) or die(mysql_error());
	
	if(mysql_num_rows($SQL)>=1) {
		$a_r = array();
		while($array = mysql_fetch_array($SQL, MYSQL_ASSOC)) { 
			array_push($a_r, "<a href = \"./req_det.php?id=".$array["pr"]."\">".$array["pr"]."</a>");	
		}
	}
	else {
	
	}
	echo implode(", ", $a_r);
}

function po_rfa($id){
	$q 		= "SELECT pf.rfa AS pr FROM po_rfa pf WHERE pf.po_det = '$id';";
	$SQL 	= @mysql_query($q) or die(mysql_error());
	
	if(mysql_num_rows($SQL)>=1) {
		$a_r = array();
		while($array = mysql_fetch_array($SQL, MYSQL_ASSOC)) { 
			array_push($a_r, "<a href = \"./rfa_det.php?id=".$array["pr"]."\">".$array["pr"]."</a>");
 		} 
	} 
	else {
	
	}
 		echo implode(", ", $a_r);
}

function po_list_req($id){
	$q 		= "SELECT pr.req as pr FROM po_req pr WHERE pr.po_det = '$id';";
	$SQL 	= @mysql_query($q) or die(mysql_error());
	
	if(mysql_num_rows($SQL)>=1) {
		$a_r = array();
		while($array = mysql_fetch_array($SQL, MYSQL_ASSOC)) { 
			array_push($a_r,$array["pr"]);
 		} 
 		echo implode(", ", $a_r);
	}
}

function po_list_rfa($id){
	$q 		= "SELECT pf.rfa AS pr FROM po_rfa pf WHERE pf.po_det = '$id';";
	$SQL 	= @mysql_query($q) or die(mysql_error());
	
	if(mysql_num_rows($SQL)>=1) {
		$a_r = array();
		while($array = mysql_fetch_array($SQL, MYSQL_ASSOC)) { 
			array_push($a_r,$array["pr"]);
 		} 
 		echo implode(", ", $a_r);
	}
}

function po_upd_req($po,$po_det,$req){
	$array   	= explode(",",$req);
	$inpBy		= $_SESSION['uid'];
	$inpDate	= date('Y-m-d H:i:s');
	$inpFrom	= $_SERVER["REMOTE_ADDR"];
	foreach($array as $values) {
		$i_q = "INSERT INTO po_req(po,po_det,req,inpBy,inpDate,inpFrom) VALUES('$po','$po_det','$values','$inpBy','$inpDate','$inpFrom');";
		@mysql_query($i_q) or die(mysql_error());
	}
}

function po_upd_rfa($po,$po_det,$rfa){
	$array 		= explode(",",$rfa);
	$inpBy		= $_SESSION['uid'];
	$inpDate	= date('Y-m-d H:i:s');
	$inpFrom	= $_SERVER["REMOTE_ADDR"];
	foreach($array as $values) {
		$i_q ="INSERT INTO po_rfa(po,po_det,rfa,inpBy,inpDate,inpFrom) VALUES('$po','$po_det','$values','$inpBy','$inpDate','$inpFrom');";
		@mysql_query($i_q) or die(mysql_error());
	}
}

//------------------------------------------------------------------------------

?>