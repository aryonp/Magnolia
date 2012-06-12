<?php
/* -----------------------------------------------------
 * File name  : deprec.class.php	
 * Created by : aryonp@gmail.com	
 * -----------------------------------------------------
 * Purpose	  : Generate table contains of depreciation 
 * data and graphic
 * -----------------------------------------------------			   						                 			
 */

require_once PHPLOT_PATH.'phplot.php';

class deprec extends PHPlot {
	
	var $deprecQuery;
	var $class;
	var $cctr;
	
	function __construct($class,$cctr) {
		$this->class 	= trim($class);
		$this->cctr 	= trim($cctr);
		$query 			= "SELECT description AS name, buydate AS buy, date(NOW()) AS current, life * 12 AS lspan, TIMESTAMPDIFF(MONTH, buydate, NOW()) AS pmonth, price, price * (TIMESTAMPDIFF(MONTH, buydate, NOW()) / (life * 12)) AS deprec, price - (price * (TIMESTAMPDIFF(MONTH, buydate, NOW()) / (life * 12))) AS nbv FROM inv WHERE del = '0' AND cctr LIKE '$this->cctr' AND class = '$this->class' ";	 
		$this->deprecQuery = @mysql_query($query) or die(mysql_error());
	}
	
	function deprecTable() {?>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>	
				<tr align="left" valign="top"> 
                 	<td width="30">&nbsp;<b>NO</b>&nbsp;</td>
                 	<td >&nbsp;<b>NAME</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>BUY</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>CURRENT</b>&nbsp;</td>
                 	<td align="right">&nbsp;<b>LIFE(MONTHS)</b>&nbsp;</td>
                 	<td align="right">&nbsp;<b>MONTHS PASS</b>&nbsp;</td>
                 	<td align="right" width="*">&nbsp;<b>PRICE(USD)</b>&nbsp;</td>
                 	<td align="right" width="*">&nbsp;<b>DEPREC.(USD)</b>&nbsp;</td>
                 	<td align="right" width="*">&nbsp;<b>NBV(USD)</b>&nbsp;</td>
               	</tr>
				</thead>
				<tbody>
<?php 
	if (mysql_num_rows($this->deprecQuery) >= 1) {	
			$count = 1;$ttlPrice = 0; $ttlDeprec = 0; $ttlCurr= 0;
			while ($array = mysql_fetch_array($this->deprecQuery,MYSQL_ASSOC)) { 
				$deprec_val = ($array["deprec"] >= $array["price"])?$array["price"]:$array["deprec"]?>
				<tr valign="top">
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($array["name"])?ucwords($array["name"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["buy"])?cplday('d M y',$array["buy"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["current"])?cplday('d M y',$array["current"]):"-";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array["lspan"])?$array["lspan"]:"-";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array["pmonth"])?$array["pmonth"]:"-";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array["price"])?number_format($array["price"]):0;?>&nbsp;</td>
					<td align="right">&nbsp;<?=number_format($deprec_val);?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array["nbv"] < 0)?0:number_format($array["nbv"]);?>&nbsp;</td></tr>	
<?php			$count++;
				$ttlPrice += $array["price"];
				$ttlDeprec += $deprec_val;
			}?>
			<tr valign="middle" >
            		<td align="center" colspan="6">&nbsp;<b>GRAND TOTAL</b></td> 
                 	<td align="right">&nbsp;<b><?=number_format($ttlPrice)?></td></b>&nbsp;</td>
                 	<td align="right">&nbsp;<b><?=number_format($ttlDeprec)?></b>&nbsp;</td>
                 	<td align="right">&nbsp;<b><?=number_format($ttlPrice - $ttlDeprec);?></b>&nbsp;</td>
            	</tr>	
<?php	}

		else {	?>
				<tr><td colspan="9" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 	} ?>	</tbody></table>
<?php	
	}
	
	function deprecGraph() {
		$data = array();
		
		while(list($desc,$bdate,$cdate,$lspan,$pmonth,$price,$deprec,$nbv)= mysql_fetch_array($this->deprecQuery)) {
			$data[] = array($desc,$price,$nbv);
		}
		
		$this->SetXTitle('Item');
		$this->SetYTitle('Price');
		$this->PHPlot(900,600);
		$this->SetImageBorderType('plain');
		$this->SetXTickLabelPos('none');
		$this->SetXTickPos('none');
		$this->SetXLabelAngle(90);
		$this->SetLegend(array('Actual Price', 'NBV'));
		$this->SetDataValues($data);
		$this->SetLegendPixels(50,25);
		$graphTitle = "Depreciation Graph";
		$this->SetTitle($graphTitle);
		$this->DrawGraph();
	}
	
}
?>