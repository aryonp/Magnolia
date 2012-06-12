<?php
/* -----------------------------------------------------
 * File name	: kpi.class.php	
 * Created by 	: aryonp@gmail.com				   
 * Created date	: 21.10.2008				
 * Last Update	: 04.11.2009
 * -----------------------------------------------------
 * Purpose		: Generate and calculate KPI for STORIX								                 			
 * -----------------------------------------------------			   						                 			
 */

require_once PHPLOT_PATH.'phplot.php';

class strxKPI extends PHPlot {
	
	var $sdate;
	var $edate;	
	var $query1;
	var $query2;
	var $sqlKPI3;
	var $sqlKPI4;
	var $query5;
	var $query6;
	var $sqlKPI7;
	var $sqlKPI8;
	var $query9;
	var $sqlKPI10;
	var $sqlKPI11;
	var $query12;
	var $query13;
	var $sqlKPI15;
	var $sqlKPI16;
	var $sqlKPI17;
	var $sqlKPI18;
	var $sqlKPI19;
	
	function strxKPI($sdate,$edate) {
		$this->sdate = $sdate;
		$this->edate = $edate;
	}
	
	function setPieData() {	   			
		$query3 = "SELECT rt.name as title, COUNT(rt.name) AS ttl
					FROM req r LEFT JOIN req_det rd ON (rd.req_id_fk = r.id) LEFT JOIN req_items rt ON (rt.id = rd.item_id_fk)
					WHERE rt.type_id_fk = '1' AND DATE(r.req_date) BETWEEN '".$this->sdate."' AND '".$this->edate."' AND r.del = '0'
					GROUP BY rt.name
					ORDER BY COUNT(rt.name) DESC
					LIMIT 0,5;";					
		$query4 = "SELECT rt.name as title, COUNT(rt.name) AS ttl FROM req r LEFT JOIN req_det rd ON (rd.req_id_fk = r.id) LEFT JOIN req_items rt ON (rt.id = rd.item_id_fk) WHERE rt.type_id_fk = '2' AND DATE(r.req_date) BETWEEN '$this->sdate' AND '$this->edate' AND r.del = '0' GROUP BY rt.name ORDER BY COUNT(rt.name) DESC LIMIT 0,5;";	
		$query7 = "SELECT rd.item as title, COUNT(rd.item) AS ttl FROM rfa_det rd LEFT JOIN rfa r ON (r.id = rd.rfa_id_fk) WHERE r.status = 'approved' AND DATE(r.date) BETWEEN '$this->sdate' AND '$this->edate' AND r.del = '0' GROUP BY rd.item ORDER BY COUNT(rd.item) DESC LIMIT 0,5 ";				
		$query8 = "SELECT rd.item as title, COUNT(rd.item) AS ttl FROM rfa_det rd LEFT JOIN rfa r ON (r.id = rd.rfa_id_fk) WHERE r.status = 'rejected' AND DATE(r.date) BETWEEN '$this->sdate' AND '$this->edate' AND r.del = '0' GROUP BY rd.item ORDER BY COUNT(rd.item) DESC LIMIT 0,5 ";		
		$query10 = "SELECT v.name as title, COUNT(v.name) AS ttl FROM vdr v LEFT JOIN po p ON (p.vdr_id_fk = v.id) WHERE p.status = 'authorize' AND DATE(p.date) BETWEEN '$this->sdate' AND '$this->edate' AND v.del = '0' GROUP BY v.name ORDER BY COUNT(v.name) DESC LIMIT 0,5 ";				
		$query11 = "SELECT curr as title, COUNT(curr) AS ttl FROM rep_po WHERE DATE(pdate) BETWEEN '$this->sdate' AND '$this->edate' GROUP BY curr ORDER BY COUNT(curr) DESC ";		
		$query15 = "SELECT t.tName AS title, SUM(pt.qty) AS ttl FROM printer_trs pt INNER JOIN toner t ON (t.id = pt.tonerID) WHERE pt.del = '0' AND pt.pType = 'USAGE' AND DATE(pt.tgl) BETWEEN '".$this->sdate."' AND '".$this->edate."' GROUP BY pt.tonerID ORDER BY SUM(pt.qty) DESC LIMIT 0,7;";		
		$query16 = "SELECT ic.description, COUNT(i.id) FROM inv i INNER JOIN inv_class ic ON (ic.name = i.class) WHERE i.del = '0' GROUP BY i.class ASC;";		
		$query17 = "SELECT ic.code, COUNT(i.id) FROM inv i INNER JOIN inv_cctr ic ON (ic.code = i.cctr) WHERE i.del = '0' GROUP BY i.cctr ASC;";		
		$query18 = "SELECT a.branch_id_fk, COUNT(ad.id) FROM acc_det ad INNER JOIN acc a ON (a.id = ad.acc_id_fk) WHERE ad.del = '0' GROUP BY a.branch_id_fk ASC;";
		$query19 = "SELECT i.name, COUNT(ad.id) FROM acc_det ad INNER JOIN req_items i ON (i.id = ad.item_id_fk) WHERE ad.del = '0' GROUP BY i.name ASC;";		
		$query21 = "SELECT t.tName AS title, SUM(pt.qty) AS ttl FROM printer_trs pt INNER JOIN toner t ON (t.id = pt.tonerID) WHERE pt.del = '0' AND pt.pType = 'STOCK' AND DATE(pt.tgl) BETWEEN '".$this->sdate."' AND '".$this->edate."' GROUP BY pt.tonerID ORDER BY SUM(pt.qty) DESC LIMIT 0,7;";
		$this->sqlKPI3 = @mysql_query($query3) or die(mysql_error());
		$this->sqlKPI4 = @mysql_query($query4) or die(mysql_error());
		$this->sqlKPI7 = @mysql_query($query7) or die(mysql_error());
		$this->sqlKPI8 = @mysql_query($query8) or die(mysql_error());
		$this->sqlKPI10 = @mysql_query($query10) or die(mysql_error());	
		$this->sqlKPI11 = @mysql_query($query11) or die(mysql_error());		
		$this->sqlKPI15 = @mysql_query($query15) or die(mysql_error());		
		$this->sqlKPI16 = @mysql_query($query16) or die(mysql_error());	
		$this->sqlKPI17 = @mysql_query($query17) or die(mysql_error());	
		$this->sqlKPI18 = @mysql_query($query18) or die(mysql_error());	
		$this->sqlKPI19 = @mysql_query($query19) or die(mysql_error());		
		$this->sqlKPI21 = @mysql_query($query21) or die(mysql_error());		
	}
	
	function getKPI1(){ 
	$this->query1 	= "SELECT branch, req_type, SUM(CASE status WHEN 'pending' THEN 1 ELSE 0 END) AS pending, SUM(CASE status WHEN 'rejected' THEN 1 ELSE 0 END) AS rejected, SUM(CASE status WHEN 'authorized' THEN 1 ELSE 0 END) AS authorized, SUM(CASE status WHEN 'adm-authorized' THEN 1 ELSE 0 END) AS adm_auth, SUM(CASE status WHEN 'adm-rejected' THEN 1 ELSE 0 END) AS adm_rjc, SUM(CASE status WHEN 'adm-authorized(STOCK)' THEN 1 ELSE 0 END) AS stock, SUM(1) AS total
						FROM rep_req
						WHERE DATE(date_create) BETWEEN '".$this->sdate."' AND '".$this->edate."'
						GROUP BY branch, req_type;";			
	$sqlKPI1		= @mysql_query($this->query1) or die(mysql_error()); 
?> 	<b>REQUEST SUMMARY FROM <font color="red"><?=cplday('d F Y',$this->sdate);?></font> TO <font color="red"><?=cplday('d F Y',$this->edate);?></font></b>	
	<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
		<thead>
        <tr valign="middle">
       		<td align="left" width="40" rowspan="2">&nbsp;<b>BRANCH</b>&nbsp;</td>
       		<td width="40" align="left" rowspan="2">&nbsp;<b>TYPE</b>&nbsp;</td>
       		<td width="*" align="center" colspan="6">&nbsp;<b>STATUS</b>&nbsp;</td>
       		<td width="40" align="center" rowspan="2">&nbsp;<b>TOTAL</b>&nbsp;</td>
       	</tr>
       	<tr class="listview" align="center" valign="middle">
       		<td width="40">&nbsp;<b>PENDING</b>&nbsp;</td>
       		<td width="40">&nbsp;<b>REJECTED</b>&nbsp;</td>
       		<td width="40">&nbsp;<b>AUTHORIZED</b>&nbsp;</td>
       		<td width="40">&nbsp;<b>ADM-AUTHORIZED</b>&nbsp;</td>
       		<td width="40">&nbsp;<b>ADM-REJECTED</b>&nbsp;</td>
        	<td width="40">&nbsp;<b>STOCK</b>&nbsp;</td>
        </tr>
	</thead>
	<tbody>
<?php	if (mysql_num_rows($sqlKPI1) >= 1) {	
			$count = 1; $ttl_pending = 0; $ttl_rejected = 0; $ttl_authorized = 0; $ttl_admappr = 0; $ttl_admrjc = 0;
			$ttl_stock = 0;$ttl_total= 0;
			while ($arrayKPI1 = mysql_fetch_array($sqlKPI1,MYSQL_ASSOC)) {?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=($arrayKPI1["branch"])?strtoupper($arrayKPI1["branch"]):"-"?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI1["req_type"])?ucwords($arrayKPI1["req_type"]):"-"?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI1["pending"])?$arrayKPI1["pending"]:"0"?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI1["rejected"])?$arrayKPI1["rejected"]:"0"?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI1["authorized"])?$arrayKPI1["authorized"]:"0"?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI1["adm_auth"])?$arrayKPI1["adm_auth"]:"0"?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI1["adm_rjc"])?$arrayKPI1["adm_rjc"]:"0"?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI1["stock"])?$arrayKPI1["stock"]:"0"?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI1["total"])?$arrayKPI1["total"]:"0"?>&nbsp;</td></tr>
<?php				$count++;
					$ttl_pending 	+= $arrayKPI1["pending"];
					$ttl_rejected 	+= $arrayKPI1["rejected"];
					$ttl_authorized += $arrayKPI1["authorized"];
					$ttl_admappr 	+= $arrayKPI1["adm_auth"];
					$ttl_admrjc 	+= $arrayKPI1["adm_rjc"];
					$ttl_stock 		+= $arrayKPI1["stock"];
					$ttl_total 		+= $arrayKPI1["total"];	
				} ?>
			<tr class="listview" valign="middle" align="center">
          		<td colspan="2">&nbsp;<b>TOTAL</b>&nbsp;</td>
                <td>&nbsp;<b><?=$ttl_pending?></b>&nbsp;</td>
                <td>&nbsp;<b><?=$ttl_rejected?></b>&nbsp;</td>
               	<td>&nbsp;<b><?=$ttl_authorized?></b>&nbsp;</td>
                <td>&nbsp;<b><?=$ttl_admappr?></b>&nbsp;</td>
                <td>&nbsp;<b><?=$ttl_admrjc?></b>&nbsp;</td>
               	<td>&nbsp;<b><?=$ttl_stock?></b>&nbsp;</td>
                <td>&nbsp;<b><?=$ttl_total?></b>&nbsp;</td>
            </tr>
<?php		}
		else { ?>
			<tr><td colspan="9" align="center" bgcolor="#e5e5e5"><br />No Data Available<br /><br /></td></tr>
<?php		 } ?>
		 </table><br/>
<?php	
	$this->query22 	= "SELECT branch, item, SUM(CASE rstatus WHEN 'pending' THEN 1 ELSE 0 END) AS pending, SUM(CASE rstatus WHEN 'rejected' THEN 1 ELSE 0 END) AS rejected, SUM(CASE rstatus WHEN 'authorized' THEN 1 ELSE 0 END) AS authorized, SUM(CASE rstatus WHEN 'adm-authorized' THEN 1 ELSE 0 END) AS admauth, SUM(CASE rstatus WHEN 'adm-rejected' THEN 1 ELSE 0 END) AS admrjc, SUM(CASE rstatus WHEN 'adm-authorized(STOCK)' THEN 1 ELSE 0 END) AS stock, SUM(1) AS total FROM rep_req_det_01 WHERE DATE(rdate) BETWEEN '$this->sdate' AND '$this->edate' GROUP BY branch, item ";			
	$sqlKPI22		= @mysql_query($this->query22) or die(mysql_error()); 
?> 	
	<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
		<thead>
        <tr valign="middle">
       		<td align="left" width="40" rowspan="2">&nbsp;<b>BRANCH</b>&nbsp;</td>
       		<td width="40" align="left" rowspan="2">&nbsp;<b>ITEM</b>&nbsp;</td>
       		<td width="*" align="center" colspan="6">&nbsp;<b>STATUS</b>&nbsp;</td>
       		<td width="40" align="center" rowspan="2">&nbsp;<b>TOTAL</b>&nbsp;</td>
       	</tr>
       	<tr class="listview" align="center" valign="middle">
       		<td width="40">&nbsp;<b>PENDING</b>&nbsp;</td>
       		<td width="40">&nbsp;<b>REJECTED</b>&nbsp;</td>
       		<td width="40">&nbsp;<b>AUTHORIZED</b>&nbsp;</td>
       		<td width="40">&nbsp;<b>ADM-AUTHORIZED</b>&nbsp;</td>
       		<td width="40">&nbsp;<b>ADM-REJECTED</b>&nbsp;</td>
        	<td width="40">&nbsp;<b>STOCK</b>&nbsp;</td>
        </tr>
		</thead>
		<tbody>
<?php	if (mysql_num_rows($sqlKPI22)>= 1) {	
			$count2 		= 1; 
			$ttl_pending2 	= 0; 
			$ttl_rejected2 	= 0; 
			$ttl_authorized2 = 0; 
			$ttl_admappr2 	= 0; 
			$ttl_admrjc2 	= 0;
			$ttl_stock2 	= 0;
			$ttl_total2		= 0;
			while ($arrayKPI22 = mysql_fetch_array($sqlKPI22,MYSQL_ASSOC)) {?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=($arrayKPI22["branch"])?strtoupper($arrayKPI22["branch"]):"-"?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI22["item"])?ucwords($arrayKPI22["item"]):"-"?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI22["pending"])?$arrayKPI22["pending"]:"0";?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI22["rejected"])?$arrayKPI22["rejected"]:"0";?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI22["authorized"])?$arrayKPI22["authorized"]:"0";?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI22["admauth"])?$arrayKPI22["admauth"]:"0";?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI22["admrjc"])?$arrayKPI22["admrjc"]:"0";?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI22["stock"])?$arrayKPI22["stock"]:"0";?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI22["total"])?$arrayKPI22["total"]:"0";?>&nbsp;</td></tr>
<?php				$count2++;
					$ttl_pending2 	+= $arrayKPI22["pending"];
					$ttl_rejected2 	+= $arrayKPI22["rejected"];
					$ttl_authorized2 += $arrayKPI22["authorized"];
					$ttl_admappr2 	+= $arrayKPI22["admauth"];
					$ttl_admrjc2 	+= $arrayKPI22["admrjc"];
					$ttl_stock2 	+= $arrayKPI22["stock"];
					$ttl_total2 	+= $arrayKPI22["total"];	
				} ?>
			<tr class="listview" valign="middle" align="center">
          		<td colspan="2">&nbsp;<b>TOTAL</b>&nbsp;</td>
                <td>&nbsp;<b><?=$ttl_pending2;?></b>&nbsp;</td>
                <td>&nbsp;<b><?=$ttl_rejected2;?></b>&nbsp;</td>
               	<td>&nbsp;<b><?=$ttl_authorized2;?></b>&nbsp;</td>
                <td>&nbsp;<b><?=$ttl_admappr2;?></b>&nbsp;</td>
                <td>&nbsp;<b><?=$ttl_admrjc2;?></b>&nbsp;</td>
               	<td>&nbsp;<b><?=$ttl_stock2;?></b>&nbsp;</td>
                <td>&nbsp;<b><?=$ttl_total2;?></b>&nbsp;</td>
            </tr>
<?php		}
		else { ?>
			<tr><td colspan="9" align="center" bgcolor="#e5e5e5"><br />No Data Available<br /><br /></td></tr>
<?php		 } ?>
		 </table><br/>
<?php
		$this->query2 = "SELECT id, branch, req_type as type, itrv_1, itrv_2, status FROM rep_req WHERE DATE(date_create) BETWEEN '$this->sdate' AND '$this->edate' ";	
		$sqlKPI2 = @mysql_query($this->query2) or die(mysql_error());
?>		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
			<thead>
               <tr valign="middle">
            	<td align="left">&nbsp;<b>ID#</b>&nbsp;</td>
               	<td width="40" align="left" >&nbsp;<b>BRANCH</b>&nbsp;</td>
               	<td width="*">&nbsp;<b>TYPE</b>&nbsp;</td>
               	<td width="*">&nbsp;<b>STATUS</b>&nbsp;</td>
               	<td width="*" align="right">&nbsp;<b>ITRV 1 REQ-AUTH (HOURS)</b>&nbsp;</td>
               	<td width="*" align="right">&nbsp;<b>ITRV 2 AUTH-ADM (HOURS)</b>&nbsp;</td>
            </tr>
		</thead>
		<tbody>
<?php 	if (mysql_num_rows($sqlKPI2) >= 1) {	
			$count = 1;$ttl_itrv1 = 0; $ttl_itrv2 = 0;
			while ($arrayKPI2 = mysql_fetch_array($sqlKPI2,MYSQL_ASSOC)) {?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=($arrayKPI2["id"])?strtoupper($arrayKPI2["id"]):"-";?></td>
					<td>&nbsp;<?=($arrayKPI2["branch"])?strtoupper($arrayKPI2["branch"]):"-";?></td>
					<td>&nbsp;<?=($arrayKPI2["type"])?ucwords($arrayKPI2["type"]):"-";?></td>
					<td>&nbsp;<?=($arrayKPI2["status"])?strtoupper($arrayKPI2["status"]):"-";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($arrayKPI2["itrv_1"])?$arrayKPI2["itrv_1"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($arrayKPI2["itrv_2"])?$arrayKPI2["itrv_2"]:"0";?>&nbsp;</td>
				</tr>	
<?php			$count++;
				$ttl_itrv1 += $arrayKPI2["itrv_1"]; 
				$ttl_itrv2 += $arrayKPI2["itrv_2"];
			} ?>
				<tr valign="middle" align="center">
            		<td colspan="4">&nbsp;<b>TOTAL</b>&nbsp;</td> 
                 	<td align="right">&nbsp;<b><?=$ttl_itrv1?>&nbsp;</b></td>
                 	<td align="right">&nbsp;<b><?=$ttl_itrv2?>&nbsp;</b></td>
            	</tr>	
<?php    }
		else {?>
				<tr><td colspan="6" align="center" bgcolor="#e5e5e5"><br />No Data Available<br /><br /></td></tr>
<?php 	} ?>
		</table>
<?php	}

	function getKPI5(){ 
		$this->query5 = "SELECT id, branch, req, appr, itrv, status FROM rep_rfa WHERE DATE(date_create) BETWEEN '$this->sdate' AND '$this->edate' ";	
		$sqlKPI5 = @mysql_query($this->query5); 
?>		<b>RFA SUMMARY FROM <font color="red"><?=cplday('d F Y',$this->sdate);?></font> TO <font color="red"><?=cplday('d F Y',$this->edate);?></font></b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
			<thead>
               <tr valign="middle">
            		<td >&nbsp;<b>ID#</b></td>
                 	<td width="*">&nbsp;<b>BRANCH</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>CREATOR</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>APPROVER</b>&nbsp;</td>
                 	<td width="*" align="center">&nbsp;<b>INTERVAL</b>&nbsp;</td>
                 	<td width="*" align="center">&nbsp;<b>STATUS</b>&nbsp;</td>
				</tr>
		</thead>
		<tbody>
<?php 	if (mysql_num_rows($sqlKPI5) >= 1) {	
			$count = 1; $ttl_itrv = 0;
			while ($arrayKPI5 = mysql_fetch_array($sqlKPI5,MYSQL_ASSOC)) {?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=($arrayKPI5["id"])?strtoupper($arrayKPI5["id"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI5["branch"])?strtoupper($arrayKPI5["branch"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI5["req"])?ucwords($arrayKPI5["req"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI5["appr"])?ucwords($arrayKPI5["appr"]):"-";?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI5["itrv"])?$arrayKPI5["itrv"]:"0";?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI5["status"])?strtoupper($arrayKPI5["status"]):"-";?>&nbsp;</td></tr>	
<?php			$count++;
				$ttl_itrv += $arrayKPI5["itrv"];
			}	
        }
		else {?>
				<tr valign="middle"><td colspan="6" align="center" bgcolor="#e5e5e5"><br />No Data Available<br /><br /></td></tr>
				<?php } ?>
		</table><br/>
<?php	 
		$this->query6 = "SELECT branch, SUM(CASE STATUS WHEN 'pending' THEN 1 ELSE 0 END) AS \"pending\", SUM(CASE STATUS WHEN 'rejected' THEN 1 ELSE 0 END) AS \"rejected\", SUM(CASE STATUS WHEN 'approved' THEN 1 ELSE 0 END) AS \"approved\", SUM(1) AS total FROM rep_rfa WHERE DATE(date_create) BETWEEN '$this->sdate' AND '$this->edate' GROUP BY branch ";	
		$sqlKPI6 = @mysql_query($this->query6) or die(mysql_error());
?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
			<thead>
               <tr valign="middle">
                 	<td width="*" rowspan="2">&nbsp;<b>BRANCH</b>&nbsp;</td>
                 	<td width="*" colspan="3" align="center">&nbsp;<b>STATUS</b>&nbsp;</td>
                 	<td width="*" rowspan="2" align="center">&nbsp;<b>TOTAL</b>&nbsp;</td></tr>
               <tr class="listview" valign="middle" align="center">  
                 	<td width="*">&nbsp;<b>PENDING</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>REJECTED</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>APPROVED</b>&nbsp;</td>
               </tr>
		</thead>
		<tbody>
<?php if (mysql_num_rows($sqlKPI6) >= 1) {	
			$count = 1;$ttl_pending = 0; $ttl_rejected = 0; $ttl_approved = 0; $ttl_all = 0;
			while ($arrayKPI6 = mysql_fetch_array($sqlKPI6,MYSQL_ASSOC)) {?>
				<tr valign="top">
					<td>&nbsp;<?=($arrayKPI6["branch"])?strtoupper($arrayKPI6["branch"]):"-";?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI6["pending"])?$arrayKPI6["pending"]:"0";?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI6["rejected"])?$arrayKPI6["rejected"]:"0";?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI6["approved"])?$arrayKPI6["approved"]:"0";?>&nbsp;</td>
					<td align="center">&nbsp;<?=($arrayKPI6["total"])?$arrayKPI6["total"]:"0";?>&nbsp;</td></tr>	
<?php			$count++;
				$ttl_pending += $arrayKPI6["pending"]; 
				$ttl_rejected += $arrayKPI6["rejected"]; 
				$ttl_approved += $arrayKPI6["approved"]; 
				$ttl_all += $arrayKPI6["total"];
			}
?>
				<tr class="listview" valign="middle" align="center">
            		<td>&nbsp;<b>TOTAL</b>&nbsp;</td> 
                 	<td>&nbsp;<b><?=$ttl_pending?></b>&nbsp;</td>
                 	<td>&nbsp;<b><?=$ttl_rejected?></b>&nbsp;</td>
                 	<td>&nbsp;<b><?=$ttl_approved?></b>&nbsp;</td>
                 	<td>&nbsp;<b><?=$ttl_all?></b>&nbsp;</td>
            	</tr>	
<?php	}
		else {?>
				<tr valign="middle"><td colspan="5" align="center" bgcolor="#e5e5e5"><br />No Data Available<br /><br /></td></tr>
		<?php } ?>
		</table>
<?php	}

	function getKPI9(){ 
		$this->query9 = "SELECT vendor, vaddr, vphone, vfax, vpic, vserves, vsap, SUM(IF(curr = 'IDR',total, 0)) AS idr, SUM(IF(curr = 'EUR',total, 0)) AS eur, SUM(IF(curr = 'USD',total, 0)) AS usd, SUM(IF(curr = 'CAD',total, 0)) AS cad, SUM(IF(curr = 'SGD',total, 0)) AS sgd FROM rep_po WHERE DATE(pdate) BETWEEN '$this->sdate' AND '$this->edate' GROUP BY vendor ASC;";			
		$sqlKPI9 = @mysql_query($this->query9) or die(mysql_error());
?>		<b>PURCHASE ORDER SUMMARY FROM <font color="red"><?=cplday('d F Y',$this->sdate);?></font> TO <font color="red"><?=cplday('d F Y',$this->edate);?></font></b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
			<thead>
               <tr valign="middle">
                 	<td width="*" colspan="8" align="center">&nbsp;<b>VENDOR</b>&nbsp;</td>
                 	<td width="*" colspan="5" align="center">&nbsp;<b>CURRENCY</b>&nbsp;</td>
                 </tr>
               <tr class="listview" valign="middle">  
			<td align="left" width="*">&nbsp;<b>NO</b>&nbsp;</td>
               	<td align="left" width="*">&nbsp;<b>NAME</b>&nbsp;</td>
                 	<td align="left" width="*">&nbsp;<b>ADDRESS</b>&nbsp;</td>
                 	<td align="left" width="*">&nbsp;<b>PHONE</b>&nbsp;</td>
                 	<td align="left" width="*">&nbsp;<b>FAX</b>&nbsp;</td>
                 	<td align="left" width="*">&nbsp;<b>PIC</b>&nbsp;</td>
			<td align="left" width="*">&nbsp;<b>BRANCH</b>&nbsp;</td>
			<td align="left" width="*">&nbsp;<b>SAP</b>&nbsp;</td>
                 	<td align="right" width="*">&nbsp;<b>IDR</b>&nbsp;</td>
                 	<td align="right" width="*">&nbsp;<b>EUR</b>&nbsp;</td>
                 	<td align="right" width="*">&nbsp;<b>USD</b>&nbsp;</td>
                 	<td align="right" width="*">&nbsp;<b>CAD</b>&nbsp;</td>
                 	<td align="right" width="*">&nbsp;<b>SGD</b>&nbsp;</td>
            </tr>
		</thead>
		<tbody>
<?php if (mysql_num_rows($sqlKPI9) >= 1) {	
			$count = 1;$ttl_idr = 0; $ttl_eur = 0; $ttl_usd = 0; $ttl_sgd = 0;
			while ($arrayKPI9 = mysql_fetch_array($sqlKPI9,MYSQL_ASSOC)) {?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI9["vendor"])?strtoupper($arrayKPI9["vendor"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI9["vaddr"])?strtoupper($arrayKPI9["vaddr"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI9["vphone"])?strtoupper($arrayKPI9["vphone"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI9["vfax"])?strtoupper($arrayKPI9["vfax"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI9["vpic"])?strtoupper($arrayKPI9["vpic"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI9["vserves"])?strtoupper($arrayKPI9["vserves"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI9["vsap"])?strtoupper($arrayKPI9["vsap"]):"-";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($arrayKPI9["idr"])?number_format($arrayKPI9["idr"],2,',','.'):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($arrayKPI9["eur"])?number_format($arrayKPI9["eur"],2,',','.'):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($arrayKPI9["usd"])?number_format($arrayKPI9["usd"],2,',','.'):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($arrayKPI9["cad"])?number_format($arrayKPI9["cad"],2,',','.'):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($arrayKPI9["sgd"])?number_format($arrayKPI9["sgd"],2,',','.'):"0";?>&nbsp;</td></tr>	
<?php			$count++;
				$ttl_idr += $arrayKPI9["idr"];
				$ttl_eur += $arrayKPI9["eur"];
				$ttl_usd += $arrayKPI9["usd"];
				$ttl_cad += $arrayKPI9["cad"];
				$ttl_sgd += $arrayKPI9["sgd"];
			}
?>
				<tr class="listview" valign="middle" >
            		<td align="center" colspan="8">&nbsp;<b>TOTAL</b></td> 
                 	<td align="right">&nbsp;<b><?=number_format($ttl_idr)?></td></b>&nbsp;</td>
                 	<td align="right">&nbsp;<b><?=number_format($ttl_eur)?></b>&nbsp;</td>
                 	<td align="right">&nbsp;<b><?=number_format($ttl_usd)?></b>&nbsp;</td>
                 	<td align="right">&nbsp;<b><?=number_format($ttl_cad)?></b>&nbsp;</td>
                 	<td align="right">&nbsp;<b><?=number_format($ttl_sgd)?></b>&nbsp;</td>
            	</tr>	
<?php	}
		else {?>
				<tr><td colspan="13" align="center" bgcolor="#e5e5e5"><br />No Data Available<br /><br /></td></tr>
		<?php } ?>
		</table>
<?php	}

	function getKPI12(){ 
		$this->query12 = "SELECT ev.id, v.name, ev.date, p.po_nbr, ev.avg FROM vdr v JOIN ev_std ev ON (ev.vdr_id_fk = v.id) LEFT JOIN po p ON (p.id = ev.po_nbr) WHERE DATE(ev.date) BETWEEN '$this->sdate' AND '$this->edate' AND ev.del = '0';";
		$sqlKPI12 = @mysql_query($this->query12) or die(mysql_error());
		?>
		<b>VENDOR EVALUATION SUMMARY FROM <font color="red"><?=cplday('d F Y',$this->sdate);?></font> TO <font color="red"><?=cplday('d F Y',$this->edate);?></font></b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
			<thead>
               <tr valign="middle"> 
                 	<td width="*">&nbsp;<b>ID#</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>DATE</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>VENDOR</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>PO</b>&nbsp;</td>
                 	<td width="*" align="right">&nbsp;<b>SCORE</b>&nbsp;</td>
               </tr>
		</thead>
		<tbody>
<?php if (mysql_num_rows($sqlKPI12) >= 1) {	
			$count = 1;
			while ($arrayKPI12 = mysql_fetch_array($sqlKPI12,MYSQL_ASSOC)) {?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=($arrayKPI12["id"])?$arrayKPI12["id"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI12["date"] != "0000-00-00 00:00:00")?cplday('d M y',$arrayKPI12["date"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI12["name"])?ucwords($arrayKPI12["name"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI12["po_nbr"])?$arrayKPI12["po_nbr"]:"-";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($arrayKPI12["avg"])?number_format($arrayKPI12["avg"],2,'.',''):"0";?>&nbsp;</td></tr>	
<?php			$count++;
			}
		}
		else {?>
				<tr valign="middle"><td colspan="5" align="center" bgcolor="#e5e5e5"><br />No Data Available<br /><br /></td></tr>
		<?php } ?>
		</table>
<?php	}

	function getKPI13(){ 
		$this->query13 	= "SELECT pr.id, pr.start, pr.end, v.name, CONCAT(u.fname,' ',u.lname) AS eval, pr.total, pr.avg, pr.suggestion FROM ev_pr pr JOIN vdr v ON (v.id = pr.vdr_id_fk) JOIN user u ON (u.id = pr.user_id_fk) WHERE DATE(pr.eval) BETWEEN '$this->sdate' AND '$this->edate' AND pr.del = '0' ";				
		$sqlKPI13 		= @mysql_query($this->query13) or die(mysql_error());
?>		<b>PERIODIC VENDOR EVALUATION SUMMARY FROM <font color="red"><?=cplday('d F Y',$this->sdate);?></font> TO <font color="red"><?=cplday('d F Y',$this->edate);?></font></b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
			<thead>
               <tr valign="middle">
                 	<td width="*">&nbsp;<b>ID#</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>PERIOD</b>&nbsp;</td>
	                <td width="*">&nbsp;<b>VENDOR</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>EVALUATOR</b>&nbsp;</td>
                 	<td width="*" align="right">&nbsp;<b>AVG</b>&nbsp;</td>
                 	<td width="*" align="right">&nbsp;<b>TOTAL</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>SUGGESTION</b>&nbsp;</td>
               </tr>
		</thead>
		<tbody>
<?php if (mysql_num_rows($sqlKPI13) >= 1) {	
			$count = 1;
			while ($arrayKPI13 = mysql_fetch_array($sqlKPI13,MYSQL_ASSOC)) {?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=($arrayKPI13["id"])?$arrayKPI13["id"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=cplday('d M y',$arrayKPI13["start"])?> - <?=cplday('d M y',$arrayKPI13["end"])?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI13["name"])?ucwords($arrayKPI13["name"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI13["eval"])?ucwords($arrayKPI13["eval"]):"-";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($arrayKPI13["avg"])?number_format($arrayKPI13["avg"], 2, '.',''):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($arrayKPI13["total"])?number_format($arrayKPI13["total"], 2, '.',''):"0";?>&nbsp;</td>
					<td>&nbsp;<?=($arrayKPI13["suggestion"])?strtoupper($arrayKPI13["suggestion"]):"-";?>&nbsp;</td></tr>	
<?php			$count++;
			}
		}
		else {?>
				<tr valign="middle"><td colspan="7" align="center" bgcolor="#e5e5e5"><br />No Data Available<br /><br /></td></tr>
		<?php } ?>
		</table>
<?php	}

	function getKPI14(){
		$query 	= "SELECT budget, cost, exch FROM rep_budget WHERE period = '".$this->sdate."';";
		$sql 	= mysql_query($query) or die(mysql_error());
		if (mysql_num_rows($sql) >= 1) {	
			$array = mysql_fetch_array($sql,MYSQL_ASSOC);?>
			<br/>TOTAL BUDGET FOR YEAR <?=cplday('Y',$this->sdate)?> : &euro; <?=($array["budget"])?number_format($array["budget"], 2, '.',','):"0";?><br/>
			EXPENSES DURING <?=cplday('Y',$this->sdate)?> : &euro; <?=($array["cost"])?number_format($array["cost"], 2, '.',','):"-";?><br/>
			BUDGET LEFT : &euro; <?=($array["exch"])?number_format($array["exch"], 2, '.',','):"-";?><br/>
<?php 	} 
		else { ?>
			- NO DATA AVAILABLE -
<?php	}
		
	}
	
	function getKPI20(){
		$query20 	= "SELECT SUM(eur) AS consum FROM rep_consum WHERE DATE(pdate) BETWEEN '$this->sdate' AND '$this->edate';";
		$sql20 		= mysql_query($query20) or die(mysql_error());
		if (mysql_num_rows($sql20) >= 1) {	
			$array = mysql_fetch_array($sql20,MYSQL_ASSOC);?>
			<b>TOTAL EXPENSES FOR PRINTER CARTRIDGE DURING (<font color="red"><?=cplday('d F y',$this->sdate)?> - <?=cplday('d F y',$this->edate)?></font>)  : &euro; <?=($array["consum"])?number_format($array["consum"], 2, '.',','):"-";?></b>
			<br/><br/>
<?php 	} 
		else { ?>
			- NO DATA AVAILABLE -
<?php	}
		
	}
	
	function getMoRep7() {
		/*
		 * Critical consumables
		 *
		 * */
		$data7_q	= "SELECT rt.toner, rt.sisa FROM vrmo_07 rt;";
		$data7SQL 	= @mysql_query($data7_q) or die(mysql_error());?>
		<b>NEED TO BUY CONSUMABLES(UNITS)</b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
			<thead>
               <tr valign="middle">
                 	<td width="25">&nbsp;<b>NO.</b>&nbsp;</td> 
                 	<td width="*">&nbsp;<b>CONSUMABLES</b>&nbsp;</td>
              	<td width="35" align="right">&nbsp;<b>DIFF.</b>&nbsp;</td>
               </tr>
		</thead>
		<tbody>
<?php 	if(mysql_num_rows($data7SQL) >= 1) {
			$count = 1;
			while($array7 = mysql_fetch_array($data7SQL,MYSQL_ASSOC)){?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($array7["toner"])?strtoupper($array7["toner"]):"-";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["sisa"])?$array7["sisa"]:"0";?>&nbsp;</td>
				</tr>	
<?php 		$count++;
			}
		}
		else { ?>
			<tr><td colspan="3" bgcolor="#e5e5e5" align="center">&nbsp;No Data Available&nbsp;</td></tr>
<?php 	} ?>
		</table>
<?php 
	}
	
	function getMoRep9() {
		/*
		 * Consumables inventory
		 *
		 * */
		$data9_q	= "SELECT rt.toner, 
							  rt.sisa 
					   FROM rep_toner_1 rt;";
		$data9SQL 	= @mysql_query($data9_q) or die(mysql_error());?>
		<b>CONSUMABLES(UNITS) INVENTORY</b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
			<thead>
               <tr valign="middle">  
                 	<td width="25">&nbsp;<b>NO.</b>&nbsp;</td> 
                 	<td width="*">&nbsp;<b>CONSUMABLES</b>&nbsp;</td>
              	<td width="35" align="right">&nbsp;<b>DIFF.</b>&nbsp;</td>
               </tr>
		</thead>
		<tbody>
<?php 	if(mysql_num_rows($data9SQL) >= 1) {
			$count = 1;
			while($array9 = mysql_fetch_array($data9SQL,MYSQL_ASSOC)){?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($array9["toner"])?strtoupper($array9["toner"]):"-";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array9["sisa"])?$array9["sisa"]:"0";?>&nbsp;</td>
				</tr>	
<?php 		$count++;
			$ttl_sisa += $array9["sisa"];
			} ?>
			<tr class="listview">
				<td colspan="2">&nbsp;<b>TOTAL</b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_sisa?></b>&nbsp;</td></tr>
<?php 	}
		else { ?>
			<tr><td colspan="3" bgcolor="#e5e5e5" align="center">&nbsp;No Data Available&nbsp;</td></tr>
<?php 	} ?>
		</table>
<?php 
	}
	
	/*
	function getKPI22(){ 
	
	}
	*/
	
	function getKPIGraph($sqldata, $subtitle) {
		$data = array();
		while(list($title,$ttl)= mysql_fetch_array($sqldata)) {
			$data[] = array($title,$ttl);
		}
		$this->PHPlot(600, 300);
		$this->SetImageBorderType('plain');
		$this->SetPlotType('pie');
		$this->SetDataType('text-data-single');
		$this->SetDataValues($data);
		$graphTitle = $subtitle;
		$this->SetTitle($graphTitle);
		foreach ($data as $row) {
  			$this->SetLegend(implode(': ',$row));
		}
		$this->DrawGraph();
	}
	
	function strxKPIResAll() { ?>
		<div class="tabbable">
			<ul class="nav nav-tabs">
 				<li class="active" data-toggle="tab"><a href="#kpiReq">Request</a></li> 
  				<li><a href="#kpiRfa" data-toggle="tab">RFA</a></li> 
  				<li><a href="#kpiBudget" data-toggle="tab">Budget</a></li> 
  				<li><a href="#kpiPo" data-toggle="tab">PO</a></li> 
  				<li><a href="#kpiVev" data-toggle="tab">Vendor Eval</a></li> 
  				<li><a href="#kpiPev" data-toggle="tab">Periodic Vendor Eval</a></li> 
  				<li><a href="#inv" data-toggle="tab">Inventory</a></li>
  				<li><a href="#acc" data-toggle="tab">Access</a></li>
  				<li><a href="#csmTnr" data-toggle="tab">Consumables (Toner)</a></li>
  				<li><a href="#mrCritCons" data-toggle="tab">Critical Consumables</a></li> 
  				<li><a href="#mrConsInv" data-toggle="tab">Consumables Inventory</a></li>
			</ul> 
			<div class="tab-content">
				<div class="tab-pane active" id="kpiReq">
					<p align="left"><a title="Print" href="javascript:openW('./print_kpi.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&m=print&t=1','Print_KPI_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel" href="print_kpi.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&m=xls&t=1"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p>
					<?=$this->getKPI1()?><br/>
					<img src="./controller/KPIGraph.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&t=1"/><br/><br/>
					<img src="./controller/KPIGraph.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&t=2"/>
				</div> 
				<div class="tab-pane" id="kpiRfa">
					<p align="left"><a title="Print" href="javascript:openW('./print_kpi.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&m=print&t=2','Print_KPI_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel" href="print_kpi.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&m=xls&t=2"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p>
					<?=$this->getKPI5()?><br/>
					<img src="./controller/KPIGraph.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&t=3"/><br/><br/>
					<img src="./controller/KPIGraph.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&t=4"/>
				</div>
				<div class="tab-pane" id="kpiBudget">
					<p align="left"><a title="Print" href="javascript:openW('./print_kpi.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&m=print&t=3','Print_KPI_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel" href="print_kpi.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&m=xls&t=3"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p>
					<?=$this->getKPI14()?>
				</div>
				<div class="tab-pane" id="kpiPo">
					<p align="left"><a title="Print" href="javascript:openW('./print_kpi.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&m=print&t=4','Print_KPI_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel" href="print_kpi.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&m=xls&t=4"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p>
					<?=$this->getKPI9()?><br/>
					<img src="./controller/KPIGraph.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&t=5"/><br/><br/>
					<img src="./controller/KPIGraph.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&t=6"/>
				</div>
				<div class="tab-pane" id="kpiVev">
					<p align="left"><a title="Print" href="javascript:openW('./print_kpi.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&m=print&t=5','Print_KPI_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel" href="print_kpi.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&m=xls&t=5"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p>
					<?=$this->getKPI12()?>
				</div>
				<div class="tab-pane" id="kpiPev">
					<p align="left"><a title="Print" href="javascript:openW('./print_kpi.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&m=print&t=6','Print_KPI_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel" href="print_kpi.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&m=xls&t=6"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p>
					<?=$this->getKPI13()?>
				</div>	
				<div class="tab-pane" id="inv">
					<b>INVENTORY SUMMARY(UNITS)</b><br/><br/>
					<img src="./controller/KPIGraph.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&t=8"/><br/><br/>
					<img src="./controller/KPIGraph.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&t=9"/>
				</div>
				<div class="tab-pane" id="acc">
					<b>ACCOUNT/ACCESS SUMMARY(UNITS)</b><br/><br/>
					<img src="./controller/KPIGraph.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&t=10"/><br/><br/>
					<img src="./controller/KPIGraph.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&t=11"/>
				</div>
				<div class="tab-pane" id="csmTnr">
					<?=$this->getKPI20()?>
					<img src="./controller/KPIGraph.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&t=7"/><br/><br/>
					<img src="./controller/KPIGraph.php?s=<?=$this->sdate?>&e=<?=$this->edate?>&t=12"/>
				</div>
				<div class="tab-pane" id="mrCritCons">
					<p align="left"><a title="Print" href="javascript:openW('./print_kpi.php?y=<?=$this->year?>&m=print&t=7','Print_KPI_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel KPI Report" href="print_kpi.php?y=<?=$this->year?>&m=xls&t=7"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p>
					<?=$this->getMoRep7()?>
				</div>
				<div class="tab-pane" id="mrConsInv">
					<p align="left"><a title="Print" href="javascript:openW('./print_kpi.php?y=<?=$this->year?>&m=print&t=8','Print_KPI_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel KPI Report" href="print_kpi.php?y=<?=$this->year?>&m=xls&t=8"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p>
					<?=$this->getMoRep9()?>
				</div>
			</div>	
		</div>
<?php }
}
?>