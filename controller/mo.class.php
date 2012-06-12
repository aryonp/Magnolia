<?php
/* -----------------------------------------------------
 * File name	: mo.class.php	
 * Created by 	: aryonp@gmail.com				   
 * Created date	: 07.05.2010				
 * Last Update	: 21.05.2010
 * -----------------------------------------------------
 * Purpose		: Generate Monthly Report								                 			
 * -----------------------------------------------------			   						                 			
 */

class strxMoRep {
	
	var $year;
	
	function strxMoRep($year) {
		$this->year = (int) $year;
	}

	function getMoRep1(){ 
		/*
		 * Item Category Based Expenses
		 * 
		 * */
		
		$data1_q 		= "SELECT v1.name, 
								  v1.jan, 
								  v1.feb, 
								  v1.mar, 
								  v1.apr, 
								  v1.may, 
								  v1.jun, 
								  v1.jul, 
								  v1.aug, 
								  v1.sep, 
								  v1.oct, 
								  v1.nov, 
								  v1.dec, 
								  v1.total 
						   FROM vrmo_01 v1 
						   WHERE v1.tahun = '".$this->year."';";
		$this->data1SQL = @mysql_query($data1_q) or die(mysql_error());?>
		<b>EXPENSES(EUR) CALCULATE FROM PURCHASE ORDER DATA GROUP BY ITEM CATEGORY DURING YEAR <font color="red"><?=$this->year?></font></b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
			<thead>
               <tr valign="middle">  
               		<td width="25">&nbsp;<b>NO.</b>&nbsp;</td>
                 	<td width="*">&nbsp;<b>CATEGORY</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JAN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>FEB</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>APR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAY</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUL</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>AUG</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>SEP</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>OCT</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>NOV</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>DEC</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>TOTAL</b>&nbsp;</td> 
               </tr>
			</thead>
			<tbody>
<?php 	if(mysql_num_rows($this->data1SQL) >= 1) {
			$count = 1;
			while($array1 = mysql_fetch_array($this->data1SQL,MYSQL_ASSOC)){?>
				<tr align="left" valign="top" >
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($array1["name"])?strtoupper($array1["name"]):"-";?></td>
					<td align="right">&nbsp;<?=($array1["jan"])?number_format($array1["jan"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array1["feb"])?number_format($array1["feb"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array1["mar"])?number_format($array1["mar"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array1["apr"])?number_format($array1["apr"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array1["may"])?number_format($array1["may"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array1["jun"])?number_format($array1["jun"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array1["jul"])?number_format($array1["jul"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array1["aug"])?number_format($array1["aug"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array1["sep"])?number_format($array1["sep"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array1["oct"])?number_format($array1["oct"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array1["nov"])?number_format($array1["nov"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array1["dec"])?number_format($array1["dec"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array1["total"])?number_format($array1["total"],2,'.',','):"0";?>&nbsp;</td>
				</tr>	
<?php 		$count++;
			$ttl_jan += $array1["jan"];
			$ttl_feb += $array1["feb"];
			$ttl_mar += $array1["mar"];
			$ttl_apr += $array1["apr"];
			$ttl_may += $array1["may"];
			$ttl_jun += $array1["jun"];
			$ttl_jul += $array1["jul"];
			$ttl_aug += $array1["aug"];
			$ttl_sep += $array1["sep"];
			$ttl_oct += $array1["oct"];
			$ttl_nov += $array1["nov"];
			$ttl_dec += $array1["dec"];
			$ttl_all += $array1["total"];
			} ?>
			<tr class="listview">
				<td colspan="2">&nbsp;<b>TOTAL</b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_jan,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_feb,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_mar,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_apr,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_may,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_jun,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_jul,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_aug,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_sep,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_oct,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_nov,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_dec,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_all,2,'.',',')?></b>&nbsp;</td>
			</tr>
<?php 	}
		else { ?>
			<tr><td colspan="15" bgcolor="#e5e5e5" align="center">&nbsp;No Data Available&nbsp;</td></tr>
<?php 	} ?></tbody>
		</table>
<?php 
	}
	function getMoRep2(){
		/*
		 * Consumable Based Expenses
		 * 
		 * */
		
		$data2_q 	= "SELECT rcm.jan, 
							  rcm.feb, 
							  rcm.mar, 
							  rcm.apr, 
							  rcm.may, 
							  rcm.jun, 
							  rcm.jul, 
							  rcm.aug, 
							  rcm.sep, 
							  rcm.oct, 
							  rcm.nov, 
							  rcm.dec, 
							  rcm.total 
					   FROM rep_consum_mo rcm 
					   WHERE rcm.tahun = '".$this->year."';";
		$data2SQL 	= @mysql_query($data2_q) or die(mysql_error());?>
		<b>EXPENSES(EUR) CALCULATED SPECIFIC FROM CONSUMABLE (TONER/CARTRIDGE) PURCHASE ORDER DATA THROUGH YEAR <font color="red"><?=$this->year?></font></b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
               <tr valign="middle">  
					<td width="*" align="right">&nbsp;<b>JAN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>FEB</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>APR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAY</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUL</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>AUG</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>SEP</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>OCT</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>NOV</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>DEC</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>TOTAL</b>&nbsp;</td> 
               </tr>
			</thead>
			<tbody>
<?php 	if(mysql_num_rows($data2SQL) >= 1) {
			$count = 1;
			while($array2 = mysql_fetch_array($data2SQL,MYSQL_ASSOC)){?>
				<tr align="left" valign="top">
					<td align="right">&nbsp;<?=($array2["jan"])?number_format($array2["jan"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array2["feb"])?number_format($array2["feb"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array2["mar"])?number_format($array2["mar"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array2["apr"])?number_format($array2["apr"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array2["may"])?number_format($array2["may"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array2["jun"])?number_format($array2["jun"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array2["jul"])?number_format($array2["jul"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array2["aug"])?number_format($array2["aug"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array2["sep"])?number_format($array2["sep"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array2["oct"])?number_format($array2["oct"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array2["nov"])?number_format($array2["nov"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array2["dec"])?number_format($array2["dec"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array2["total"])?number_format($array2["total"],2,'.',','):"0";?>&nbsp;</td>
				</tr>	
<?php 		$count++;
			}
		}
		else { ?>
			<tr><td colspan="13" bgcolor="#e5e5e5" align="center">&nbsp;No Data Available&nbsp;</td></tr>
<?php 	} ?>
		</tbody>
		</table>
<?php 
	}
	function getMoRep3(){
		/*
		 * PO Based Expenses
		 * 
		 * */
		
		$data3_q 	= "SELECT rp.jan, 
							  rp.feb, 
							  rp.mar, 
							  rp.apr, 
							  rp.may, 
							  rp.jun, 
							  rp.jul, 
							  rp.aug, 
							  rp.sep, 
							  rp.oct, 
							  rp.nov, 
							  rp.dec, 
							  rp.total 
					   FROM rep_po_mo rp 
					   WHERE rp.tahun = '".$this->year."';";
		$data3SQL 	= @mysql_query($data3_q) or die(mysql_error());?>
		<b>EXPENSES(EUR) CALCULATED FROM ALL PURCHASE ORDER DATA THROUGH YEAR <font color="red"><?=$this->year?></font></b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
               <tr class="listview" valign="middle">  
					<td width="*" align="right">&nbsp;<b>JAN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>FEB</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>APR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAY</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUL</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>AUG</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>SEP</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>OCT</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>NOV</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>DEC</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>TOTAL</b>&nbsp;</td>
               </tr>
			</thead>
			<tbody>
<?php 	if(mysql_num_rows($data3SQL) >= 1) {
			$count = 1;
			while($array3 = mysql_fetch_array($data3SQL,MYSQL_ASSOC)){?>
				<tr align="left" valign="top">
					<td align="right">&nbsp;<?=($array3["jan"])?number_format($array3["jan"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array3["feb"])?number_format($array3["feb"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array3["mar"])?number_format($array3["mar"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array3["apr"])?number_format($array3["apr"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array3["may"])?number_format($array3["may"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array3["jun"])?number_format($array3["jun"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array3["jul"])?number_format($array3["jul"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array3["aug"])?number_format($array3["aug"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array3["sep"])?number_format($array3["sep"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array3["oct"])?number_format($array3["oct"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array3["nov"])?number_format($array3["nov"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array3["dec"])?number_format($array3["dec"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array3["total"])?number_format($array3["total"],2,'.',','):"0";?>&nbsp;</td>
				</tr>	
<?php 		$count++;}
		}
		else { ?>
			<tr><td colspan="13" bgcolor="#e5e5e5" align="center">&nbsp;No Data Available&nbsp;</td></tr>
<?php 	} ?>
		</tbody>
		</table>
<?php 
	}
	function getMoRep4(){
		/*
		 * Cost Centre Based Expenses
		 * 
		 * */
		
		$data4_q 			= "SELECT v4.name, 
									  v4.jan,
									  v4.feb, 
									  v4.mar, 
									  v4.apr, 
									  v4.may, 
									  v4.jun, 
									  v4.jul, 
									  v4.aug, 
									  v4.sep, 
									  v4.oct, 
									  v4.nov, 
									  v4.dec, 
									  v4.total 
							   FROM vrmo_04 v4 
							   WHERE v4.tahun = '".$this->year."';";
		$this->data4SQL 	= @mysql_query($data4_q) or die(mysql_error());?>
		<b>EXPENSES(EUR) CALCULATE FROM PURCHASE ORDER DATA GROUP BY COST CENTRE DURING YEAR <font color="red"><?=$this->year?></font></b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
               <tr valign="middle">  
               		<td width="25">&nbsp;<b>NO.</b>&nbsp;</td> 
                 	<td width="*">&nbsp;<b>COST CTR.</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JAN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>FEB</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>APR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAY</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUL</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>AUG</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>SEP</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>OCT</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>NOV</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>DEC</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>TOTAL</b>&nbsp;</td> 
               </tr>
			</thead>
			<tbody>
<?php 	if(mysql_num_rows($this->data4SQL) >= 1) {
			$count = 1;
			while($array4 = mysql_fetch_array($this->data4SQL,MYSQL_ASSOC)){?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($array4["name"])?strtoupper($array4["name"]):"-";?></td>
					<td align="right">&nbsp;<?=($array4["jan"])?number_format($array4["jan"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array4["feb"])?number_format($array4["feb"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array4["mar"])?number_format($array4["mar"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array4["apr"])?number_format($array4["apr"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array4["may"])?number_format($array4["may"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array4["jun"])?number_format($array4["jun"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array4["jul"])?number_format($array4["jul"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array4["aug"])?number_format($array4["aug"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array4["sep"])?number_format($array4["sep"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array4["oct"])?number_format($array4["oct"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array4["nov"])?number_format($array4["nov"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array4["dec"])?number_format($array4["dec"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array4["total"])?number_format($array4["total"],2,'.',','):"0";?>&nbsp;</td>
				</tr>	
<?php 		$count++;
			$ttl_jan += $array4["jan"];
			$ttl_feb += $array4["feb"];
			$ttl_mar += $array4["mar"];
			$ttl_apr += $array4["apr"];
			$ttl_may += $array4["may"];
			$ttl_jun += $array4["jun"];
			$ttl_jul += $array4["jul"];
			$ttl_aug += $array4["aug"];
			$ttl_sep += $array4["sep"];
			$ttl_oct += $array4["oct"];
			$ttl_nov += $array4["nov"];
			$ttl_dec += $array4["dec"];
			$ttl_all += $array4["total"];
			} ?>
			<tr class="listview">
				<td colspan="2">&nbsp;<b>TOTAL</b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_jan,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_feb,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_mar,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_apr,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_may,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_jun,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_jul,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_aug,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_sep,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_oct,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_nov,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_dec,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_all,2,'.',',')?></b>&nbsp;</td>
			</tr>
<?php 	}
		else { ?>
			<tr><td colspan="15" bgcolor="#e5e5e5" align="center">&nbsp;No Data Available&nbsp;</td></tr>
<?php 	} ?>
		</tbody>
		</table>
<?php 
	}
	function getMoRep5(){
		/*
		 * Cartridge Stock
		 * 
		 * */
		$data5_q 	= "SELECT ts.toner AS name, 
							  ts.jan, 
							  ts.feb, 
							  ts.mar, 
							  ts.apr, 
							  ts.may, 
							  ts.jun, 
							  ts.jul, 
							  ts.aug, 
							  ts.sep, 
							  ts.oct, 
							  ts.nov, 
							  ts.dec, 
							  ts.total 
						FROM vrmo_05 ts
						WHERE ts.tahun = '".$this->year."';";
		$data5SQL 	= @mysql_query($data5_q) or die(mysql_error());?>
		<b>TONER/CARTRIDGE STOCK(UNITS) DURING YEAR <font color="red"><?=$this->year?></font></b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
               <tr valign="middle">  
               		<td width="25">&nbsp;<b>NO.</b>&nbsp;</td> 
                 	<td width="*">&nbsp;<b>TONER</b>&nbsp;</td>
                 	<td width="*" align="right">&nbsp;<b>JAN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>FEB</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>APR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAY</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUL</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>AUG</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>SEP</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>OCT</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>NOV</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>DEC</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>TOTAL</b>&nbsp;</td> 
               </tr>
			</thead>
			<tbody>
<?php 	if(mysql_num_rows($data5SQL) >= 1) {
			$count = 1;
			while($array5 = mysql_fetch_array($data5SQL,MYSQL_ASSOC)){?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($array5["name"])?strtoupper($array5["name"]):"-";?></td>
					<td align="right">&nbsp;<?=($array5["jan"])?$array5["jan"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array5["feb"])?$array5["feb"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array5["mar"])?$array5["mar"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array5["apr"])?$array5["apr"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array5["may"])?$array5["may"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array5["jun"])?$array5["jun"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array5["jul"])?$array5["jul"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array5["aug"])?$array5["aug"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array5["sep"])?$array5["sep"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array5["oct"])?$array5["oct"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array5["nov"])?$array5["nov"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array5["dec"])?$array5["dec"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array5["total"])?$array5["total"]:"0";?>&nbsp;</td>
				</tr>	
<?php 		$count++;
			$ttl_jan += $array5["jan"];
			$ttl_feb += $array5["feb"];
			$ttl_mar += $array5["mar"];
			$ttl_apr += $array5["apr"];
			$ttl_may += $array5["may"];
			$ttl_jun += $array5["jun"];
			$ttl_jul += $array5["jul"];
			$ttl_aug += $array5["aug"];
			$ttl_sep += $array5["sep"];
			$ttl_oct += $array5["oct"];
			$ttl_nov += $array5["nov"];
			$ttl_dec += $array5["dec"];
			$ttl_all += $array5["total"];
			}?>
			<tr class="listview">
				<td colspan="2">&nbsp;<b>TOTAL</b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_jan?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_feb?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_mar?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_apr?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_may?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_jun?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_jul?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_aug?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_sep?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_oct?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_nov?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_dec?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_all?></b>&nbsp;</td>
			</tr>
<?php 	}
		else { ?>
			<tr><td colspan="15" bgcolor="#e5e5e5" align="center">&nbsp;No Data Available&nbsp;</td></tr>
<?php 	} ?>
		</tbody>
		</table>
<?php 
	}
	function getMoRep6(){
		/*
		 * Cartridge Usage
		 *
		 * */
		$data6_q 	= "SELECT tu.toner AS name, 
							  tu.jan, 
							  tu.feb, 
							  tu.mar, 
							  tu.apr, 
							  tu.may, 
							  tu.jun, 
							  tu.jul, 
							  tu.aug, 
							  tu.sep, 
							  tu.oct, 
							  tu.nov, 
							  tu.dec, 
							  tu.total 
					   FROM vrmo_06 tu 
					   WHERE tu.tahun = '".$this->year."';";
		$data6SQL 	= @mysql_query($data6_q) or die(mysql_error());?>
		<b>TONER/CARTRIDGE USAGE(UNITS) DURING YEAR <font color="red"><?=$this->year?></font></b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
               <tr class="listview" valign="middle">  
               		<td width="25">&nbsp;<b>NO.</b>&nbsp;</td> 
                 	<td width="*">&nbsp;<b>TONER</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JAN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>FEB</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>APR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAY</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUL</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>AUG</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>SEP</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>OCT</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>NOV</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>DEC</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>TOTAL</b>&nbsp;</td> 
               </tr>
			</thead>
			<tbody>
<?php 	if(mysql_num_rows($data6SQL) >= 1) {
			$count = 1;
			while($array6 = mysql_fetch_array($data6SQL,MYSQL_ASSOC)){?>
				<tr align="left" valign="top">
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($array6["name"])?strtoupper($array6["name"]):"-";?></td>
					<td align="right">&nbsp;<?=($array6["jan"])?$array6["jan"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array6["feb"])?$array6["feb"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array6["mar"])?$array6["mar"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array6["apr"])?$array6["apr"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array6["may"])?$array6["may"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array6["jun"])?$array6["jun"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array6["jul"])?$array6["jul"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array6["aug"])?$array6["aug"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array6["sep"])?$array6["sep"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array6["oct"])?$array6["oct"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array6["nov"])?$array6["nov"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array6["dec"])?$array6["dec"]:"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array6["total"])?$array6["total"]:"0";?>&nbsp;</td>
				</tr>	
<?php 		$count++;
			$ttl_jan += $array6["jan"];
			$ttl_feb += $array6["feb"];
			$ttl_mar += $array6["mar"];
			$ttl_apr += $array6["apr"];
			$ttl_may += $array6["may"];
			$ttl_jun += $array6["jun"];
			$ttl_jul += $array6["jul"];
			$ttl_aug += $array6["aug"];
			$ttl_sep += $array6["sep"];
			$ttl_oct += $array6["oct"];
			$ttl_nov += $array6["nov"];
			$ttl_dec += $array6["dec"];
			$ttl_all += $array6["total"];
			}?>
			<tr class="listview">
				<td colspan="2">&nbsp;<b>TOTAL</b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_jan?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_feb?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_mar?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_apr?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_may?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_jun?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_jul?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_aug?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_sep?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_oct?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_nov?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_dec?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=$ttl_all?></b>&nbsp;</td>
			</tr>
<?php 	}
		else { ?>
			<tr><td colspan="15" bgcolor="#e5e5e5" align="center">&nbsp;No Data Available&nbsp;</td></tr>
<?php 	} ?>
		</tbody>
		</table>
<?php 
	}
	
	function getMoRep7() {
		/*
		 * Access Billing 
		 *
		 * */
		$data7_q 	= "SELECT v7.tahun,
							  v7.bname, 
							  v7.dname,
							  v7.jan, 
							  v7.feb, 
							  v7.mar, 
							  v7.apr, 
							  v7.may, 
							  v7.jun, 
							  v7.jul, 
							  v7.aug, 
							  v7.sep, 
							  v7.oct, 
							  v7.nov, 
							  v7.dec, 
							  v7.total 
					   FROM vrmo_08 v7 
					   WHERE v7.tahun = '".$this->year."';";
		$data7SQL 	= @mysql_query($data7_q) or die(mysql_error());
		?>
		<b>IT ACCESS BILLING(EUR) DURING YEAR <font color="red"><?=$this->year?></font></b>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" border="0" class="table table-striped table-bordered table-condensed">
               <tr class="listview" valign="middle">  
               		<td width="25">&nbsp;<b>NO.</b>&nbsp;</td> 
                 	<td width="*">&nbsp;<b>BRANCH</b>&nbsp;</td> 
                 	<td width="*">&nbsp;<b>DEPT.</b>&nbsp;</td> 
                 	<td width="*" align="right">&nbsp;<b>JAN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>FEB</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>APR</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>MAY</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUN</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>JUL</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>AUG</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>SEP</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>OCT</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>NOV</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>DEC</b>&nbsp;</td> 
					<td width="*" align="right">&nbsp;<b>TOTAL</b>&nbsp;</td> 
               </tr>
			</thead>
			<tbody>
 <?php 	if(mysql_num_rows($data7SQL) >= 1) {
			$count = 1;
			while($array7 = mysql_fetch_array($data7SQL,MYSQL_ASSOC)){?>  
				<tr align="left" valign="top">   
					<td>&nbsp;<?=$count?>.&nbsp;</td>
					<td>&nbsp;<?=($array7["bname"])?strtoupper($array7["bname"]):"-";?></td>  
					<td>&nbsp;<?=($array7["dname"])?strtoupper($array7["dname"]):"-";?></td>       
					<td align="right">&nbsp;<?=($array7["jan"])?number_format($array7["jan"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["feb"])?number_format($array7["feb"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["mar"])?number_format($array7["mar"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["apr"])?number_format($array7["apr"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["may"])?number_format($array7["may"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["jun"])?number_format($array7["jun"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["jul"])?number_format($array7["jul"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["aug"])?number_format($array7["aug"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["sep"])?number_format($array7["sep"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["oct"])?number_format($array7["oct"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["nov"])?number_format($array7["nov"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["dec"])?number_format($array7["dec"],2,'.',','):"0";?>&nbsp;</td>
					<td align="right">&nbsp;<?=($array7["total"])?number_format($array7["total"],2,'.',','):"0";?>&nbsp;</td>
				</tr>	
<?php 		$count++;
			$ttl_jan += $array7["jan"];
			$ttl_feb += $array7["feb"];
			$ttl_mar += $array7["mar"];
			$ttl_apr += $array7["apr"];
			$ttl_may += $array7["may"];
			$ttl_jun += $array7["jun"];
			$ttl_jul += $array7["jul"];
			$ttl_aug += $array7["aug"];
			$ttl_sep += $array7["sep"];
			$ttl_oct += $array7["oct"];
			$ttl_nov += $array7["nov"];
			$ttl_dec += $array7["dec"];
			$ttl_all += $array7["total"];
			} 
			?>
			<tr class="listview">
				<td colspan="3">&nbsp;<b>TOTAL</b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_jan,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_feb,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_mar,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_apr,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_may,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_jun,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_jul,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_aug,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_sep,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_oct,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_nov,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_dec,2,'.',',')?></b>&nbsp;</td>
				<td align="right">&nbsp;<b><?=number_format($ttl_all,2,'.',',')?></b>&nbsp;</td>
			</tr>
<?php 	}
		else { ?>
			<tr><td colspan="16" bgcolor="#e5e5e5" align="center">&nbsp;No Data Available&nbsp;</td></tr>
<?php 	} ?>
		</tbody>
		</table>
<?php 
	}
	
	function strxMoRepAll() { ?>
		<br/>
		<b>MONTHLY REPORT FOR YEAR : <font color="red"><?=$this->year?></font></b>
		<br/><br/>
		<div class="tabbable">
			<ul class="nav nav-tabs">
 				<li class="active"><a href="#mritcat" data-toggle="tab">Item Category Based Expenses</a></li> 
  				<li><a href="#mrcons" data-toggle="tab">Consumable Based Expenses</a></li> 
  				<li><a href="#mrpo" data-toggle="tab">PO Based Expenses</a></li> 
  				<li><a href="#mrcctr" data-toggle="tab">Cost Centre Based Expenses</a></li> 
  				<li><a href="#mrcartstc" data-toggle="tab">Cartridge Stock</a></li> 
  				<li><a href="#mrcartusg" data-toggle="tab">Cartridge Usage</a></li> 
  				<li><a href="#mraccbill" data-toggle="tab">Access Billing</a></li> 
			</ul> 
			<div class="tab-content">
				<div class="tab-pane active"id="mritcat">
					<p align="left"><a title="Print" href="javascript:openW('./print_mo.php?y=<?=$this->year?>&m=print&t=1','Print_Monthly_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel Monthly Report" href="print_mo.php?y=<?=$this->year?>&m=xls&t=1"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p><br/>
					<?=$this->getMoRep1()?>
				</div> 
				<div class="tab-pane" id="mrcons">
					<p align="left"><a title="Print" href="javascript:openW('./print_mo.php?y=<?=$this->year?>&m=print&t=2','Print_Monthly_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel Monthly Report" href="print_mo.php?y=<?=$this->year?>&m=xls&t=2"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p><br/>
					<?=$this->getMoRep2()?>
				</div>
				<div class="tab-pane" id="mrpo">
					<p align="left"><a title="Print" href="javascript:openW('./print_mo.php?y=<?=$this->year?>&m=print&t=3','Print_Monthly_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel Monthly Report" href="print_mo.php?y=<?=$this->year?>&m=xls&t=3"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p><br/>
					<?=$this->getMoRep3()?>
				</div>
				<div class="tab-pane" id="mrcctr">
					<p align="left"><a title="Print" href="javascript:openW('./print_mo.php?y=<?=$this->year?>&m=print&t=4','Print_Monthly_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel Monthly Report" href="print_mo.php?y=<?=$this->year?>&m=xls&t=4"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p><br/>
					<?=$this->getMoRep4()?>
				</div>
				<div class="tab-pane" id="mrcartstc">
					<p align="left"><a title="Print" href="javascript:openW('./print_mo.php?y=<?=$this->year?>&m=print&t=5','Print_Monthly_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel Monthly Report" href="print_mo.php?y=<?=$this->year?>&m=xls&t=5"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p><br/>
					<?=$this->getMoRep5()?>
				</div>
				<div class="tab-pane" id="mrcartusg">
					<p align="left"><a title="Print" href="javascript:openW('./print_mo.php?y=<?=$this->year?>&m=print&t=6','Print_Monthly_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel Monthly Report" href="print_mo.php?y=<?=$this->year?>&m=xls&t=6"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p><br/>
					<?=$this->getMoRep6()?>
				</div>
				<div class="tab-pane" id="mraccbill">
					<p align="left"><a title="Print" href="javascript:openW('./print_mo.php?y=<?=$this->year?>&m=print&t=7','Print_Monthly_Report',1050,630,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png" width ="15" height="15"/></a>&nbsp;&nbsp;<a title="Excel Monthly Report" href="print_mo.php?y=<?=$this->year?>&m=xls&t=7"><img src="<?=IMG_PATH?>xls.gif" width ="15" height="15"/></a></p><br/>
					<?=$this->getMoRep7()?>
				</div>
			</div>
		</div>	
<?php }

}
?>