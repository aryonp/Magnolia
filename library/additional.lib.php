<?php
/* -----------------------------------------------------
 * File name : additional.lib.php							
 * Created by: aryonp@gmail.com	
 * Date		 : 17.02.2010	
 * -----------------------------------------------------				            
 * Purpose	 : Contain a lot of additional functions 				
 * needed by STORIX, it called by functions.lib.php																                 			
 * ----------------------------------------------------- 
 */


function vendor_list_selection(){
	$vendor_list_query ="SELECT v.id, v.name FROM vdr v WHERE del = '0' ORDER BY v.name ASC;";
	$vendor_list_SQL = @mysql_query($vendor_list_query) or die(mysql_error()); ?>
		<select name="vendor[]">
    		<option value="-">---------------------</option>
<?php	while($vendor_list_array = mysql_fetch_array($vendor_list_SQL)){?>
    		<option value="<?=$vendor_list_array[0]?>"><?=ucwords($vendor_list_array[1])?></option>
<?php 	} 
  		echo "</select>";
} 		

//------------------------------------------------------------------------------

function item_list_selection() {
	$query = "SELECT id, name FROM req_items WHERE del = '0' ORDER BY name ASC";
	$sql = @mysql_query($query) or die(mysql_error()); ?>
	<select name="item[]">
				<option value="-">-----------</option>
<?php	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){?>
			<option value ="<?=$array['name']?>"><?=ucwords($array['name'])?></option>
<?php	} ?>
	</select>
<?php	}

//------------------------------------------------------------------------------

function acc_list_selection() {
	$query = "SELECT id, name FROM req_items WHERE type_id_fk = '1' AND del = '0' ORDER BY name ASC";
	$sql = @mysql_query($query) or die(mysql_error()); ?>
	<select name="item[]">
		<option value="-">-----------</option>
<?php		while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){?>
			<option value ="<?=$array['id']?>"><?=ucwords($array['name'])?></option>
<?php		} ?>
	</select>
<?php	}

//------------------------------------------------------------------------------

function acc_lvl_selection() {
	$query = "SELECT id, lname FROM acc_level WHERE del = '0' ORDER BY lname ASC";
	$sql = @mysql_query($query) or die(mysql_error()); ?>
	<select name="level[]">
				<option value="-">-----------</option>
<?php	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){?>
		<option value ="<?=$array['id']?>"><?=ucwords($array['lname'])?></option>
<?php	} ?>
		</select>
<?php	}

//------------------------------------------------------------------------------

function item_list() {
	$query = "SELECT id, name FROM req_items WHERE type_id_fk = '2' AND del = '0' ORDER BY name ASC";
	$sql = @mysql_query($query) or die(mysql_error()); ?>
	<select name="item">
			<option value="-">-----------</option>
			<option value="all">ALL</option>
<?php	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){?>
			<option value ="<?=$array['id']?>"><?=ucwords($array['name'])?></option>
<?php	} ?>
		</select>
<?php	}

//------------------------------------------------------------------------------

function acc_list_bill() {
	//$query = "SELECT id, name FROM req_items WHERE type_id_fk = '1' AND del = '0' ORDER BY name ASC";
	$query = "SELECT * FROM req_items rt WHERE rt.del = 0  AND type_id_fk = 1 AND consumable = 0 AND rt. id NOT IN (SELECT ab.item_id_fk FROM acc_bill ab) ORDER BY rt.name ASC;";
	$sql = @mysql_query($query) or die(mysql_error()); ?>
	<select name="item">
				<option value="-">-----------</option>
<?php	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){?>
		<option value ="<?=$array['id']?>"><?=ucwords($array['name'])?></option>
<?php	} ?>
		</select>
<?php	}

//------------------------------------------------------------------------------

function acc_list() {
	$query = "SELECT id, name FROM req_items WHERE type_id_fk = '1' AND del = '0' ORDER BY name ASC";
	$sql = @mysql_query($query) or die(mysql_error()); ?>
	<select name="item">
				<option value="-">-----------</option>
<?php	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){?>
		<option value ="<?=$array['id']?>"><?=ucwords($array['name'])?></option>
<?php	} ?>
		</select>
<?php	}

//------------------------------------------------------------------------------

function acc_lvl() {
	$query 	= "SELECT id, lName FROM acc_level WHERE del = '0' ORDER BY lName DESC";
	$sql 	= @mysql_query($query) or die(mysql_error()); ?>
	<select name="level">
			<option value="-">-----------</option>
<?php	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){?>
			<option value ="<?=$array['id']?>"><?=ucwords($array['lName'])?></option>
<?php	} ?>
		</select>
<?php	}

//------------------------------------------------------------------------------

function dept_list() {
	$query 	= "SELECT id, name FROM departments WHERE del = '0' ORDER BY name ASC";
	$sql 	= @mysql_query($query) or die(mysql_error()); ?>
	<select name="dept">
			<option value="-">-----------</option>
<?php	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){?>
			<option value ="<?=$array['id']?>"><?=ucwords($array['name'])?></option>
<?php	} ?>
		</select>
<?php	}

//------------------------------------------------------------------------------

function branch_list() {
	$query 	= "SELECT id, name FROM branch WHERE del = '0' ORDER BY name ASC";
	$sql 	= @mysql_query($query) or die(mysql_error()); ?>
	<select name="branch">
		<option value="-">-----------</option>
		<option value="all">ALL</option>
<?php	while($array = mysql_fetch_array($sql,MYSQL_ASSOC)){?>
			<option value ="<?=$array['id']?>"><?=ucwords($array['name'])?></option>
<?php	} ?>
		</select>
<?php	
}

//------------------------------------------------------------------------------
function billDetails($bdet_SQL) {
	$msg_bdet = "<br/><br/>".
				"<table border=\"0\" cellpadding=\"1\" cellspacing=\"1\" width=\"100%\" class=\"table table-striped table-bordered table-condensed\">".	
				"<thead>".
					"<tr> ".
				   		"<td width=\"25\" align=\"left\">&nbsp;<b>NO.</b>&nbsp;</td>".
				   		"<td width=\"*\" align=\"left\">&nbsp;<b>BRANCH</b>&nbsp;</td>".
				   		"<td width=\"*\" align=\"left\">&nbsp;<b>DEPT</b>&nbsp;</td>".
				   		"<td width=\"*\" align=\"left\">&nbsp;<b>TYPE</b>&nbsp;</td>".
				   		"<td width=\"*\" align=\"left\">&nbsp;<b>USER</b>&nbsp;</td>".
				   		"<td width=\"*\" align=\"right\">&nbsp;<b>COST(EUR)</b>&nbsp;</td>".
				   	"</tr>".
					"</thead>".
					"<tbody>";
	$count 	= 1;
	if (mysql_num_rows($bdet_SQL)>= 1) {	
		while($data = mysql_fetch_array($bdet_SQL, MYSQL_ASSOC)){
			$msg_bdet .= "<tr align=\"left\" valign=\"top\">\n".
							"<td align=\"left\">&nbsp;$count.&nbsp;</td>\n".
							"<td align=\"left\">&nbsp;".($data["branch"])."&nbsp;</td>\n".
							"<td align=\"left\">&nbsp;".ucwords($data["dname"])."&nbsp;</td>\n".
							"<td align=\"left\">&nbsp;".ucwords($data["acc"])."&nbsp;</td>\n".
							"<td align=\"left\">&nbsp;".strtoupper($data["aname"])."&nbsp;</td>\n".
							"<td align=\"right\">&nbsp;".number_format($data["price"],2,'.',',')."&nbsp;</td>\n".
						  "</tr>\n";
			$count++;  
			$ttl_price += $data["price"];
		}
			$msg_bdet .= "<tr>\n".
							"<td width=\"*\" align=\"left\" colspan=\"5\">&nbsp;<b>TOTAL</b>&nbsp;</td>\n".
							"<td align=\"right\">&nbsp;<b>".number_format($ttl_price,2,'.',',')."</b>&nbsp;</td>\n".
						  "</tr>\n";
	} 
	else {
		$msg_bdet .= "<tr><td colspan=\"6\" align=\"center\" bgcolor=\"#e5e5e5\"><br />No data available<br /><br /></td></tr>";
	}	
	$msg_bdet .= "</tbody></table>"; 
	return $msg_bdet;
}
//------------------------------------------------------------------------------

function record_billing($bid,$branch,$did,$dept,$resp,$bln,$thn,$period,$acc,$cost,$content,$details=false){
	$query	= "INSERT INTO billing (branchID,branch,deptID,dept,resp,bln,thn,period,acc,cost,content,details) VALUES ('$bid','$branch','$did','$dept','$resp','$bln','$thn','$period','$acc','$cost','$content','$details');";
	@mysql_query($query) or die(mysql_error());
}

?>