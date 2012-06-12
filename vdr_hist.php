<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title ="Vendor History";
$page_id_left ="13";
$page_id_right = "33";
$category_page = "strx";
chkSecurity($page_id_right);

if(isset($_GET['id'])) { $hist_id = $_GET['id']; }

$status = "&nbsp;";

$select_hist_query = "SELECT rp.id, rp.nbr, rp.vendor, rp.total, rp. curr, rp.eur, rp. rate, rp.pdate FROM rep_po rp WHERE rp.vid = '$hist_id' ORDER BY rp.pdate DESC ";	
$pagingResult = new Pagination();
$pagingResult->setPageQuery($select_hist_query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$vname_query = "SELECT v.name as vname FROM vdr v WHERE v.id = '$hist_id';";
$vname_SQL = @mysql_query($vname_query) or die(mysql_error());
$vname_array = mysql_fetch_array($vname_SQL,MYSQL_ASSOC);

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>VENDOR HISTORY - <?=$vname_array["vname"]?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[&nbsp;<a href="./vdr.php">Back to the Vendor Page</a>&nbsp;]</td></tr>
        <tr><td>&nbsp;</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
        <tr><td>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr><td width="25" align="left"><b>&nbsp;NO.</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>PO</b>&nbsp;</td>
                 	<td width="*" align="left"><b>&nbsp;CURR</b>&nbsp;</td>
                 	<td width="*" align="right"><b>&nbsp;TOTAL</b>&nbsp;</td>
                 	<td width="*" align="right"><b>&nbsp;RATE</b>&nbsp;</td>
                 	<td width="*" align="right"><b>&nbsp;EURO</b>&nbsp;</td>
                 	<td width="*" align="left"><b>&nbsp;DATE</b>&nbsp;</td>	
				</tr>
			</thead>
			<tbody>
<?php 
   if($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
				<tr valign="top">
					<td align="left">&nbsp;<?=$count?>.&nbsp;</td>
					<td align="left">&nbsp;<a href="./po_det.php?id=<?=$array["id"]?>"><?=$array["nbr"]?></a>&nbsp;</td>
					<td align="left">&nbsp;<?=$array["curr"]?>&nbsp;</td>
					<td align="right">&nbsp;<?=number_format($array["total"],2,',','.')?>&nbsp;</td>
					<td align="right">&nbsp;<?=number_format($array["rate"],5,',','.')?>&nbsp;</td>
					<td align="right">&nbsp;<?=number_format($array["eur"],2,',','.')?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["pdate"])?cplday('d M Y',$array["pdate"]):"-";?>&nbsp;</td>
				</tr>
<?php			$count++;  
			}
	} else {?>
				<tr><td colspan="7" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php }	?></tbody>
			</table></td></tr>
        <tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>[&nbsp;<a href="./vdr.php">Back to the Vendor Page</a>&nbsp;]</td></tr>
        <tr><td>&nbsp;</td></tr>
	</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>