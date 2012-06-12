<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title 	= "Billing Record";
$page_id_left 	= "57";
$category_page 	= "main";
$page_id_right 	= "";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$query = "SELECT b.id,
				 b.branch,
				 b.dept,
				 b.resp,
				 b.period,
				 b.acc,
				 b.cost,
				 b.payment
		  FROM billing b 
		  WHERE b.del = 0
		  ORDER BY b.thn DESC, b.bln DESC, b.branch ASC, b.dept ASC ";

$pagingResult = new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();

$this_page 	= $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();
$status 	= "&nbsp;";

function bill_payment($id,$status,$log_code) {
	$paydate = ($status == 1)?date('Y-m-d H:i:s'):"0000-00-00 00:00:00";
	if(!empty($id)) {
		foreach ($id as $val) {
			$query = "UPDATE billing SET paydate = '$paydate',payment = '$status' WHERE id = '$val';";
			@mysql_query($query) or die(mysql_error());
			log_hist($log_code,$val);
		}
		header("location:$this_page");
	}
	else {
		$status="<p class=\"yellowbox\">Missing required information ! Please tick your selection </p><br/>";
	}
}

if(isset($_POST['payment_ok'])) {
	$id = $_POST['bill_id'];
	bill_payment($id,1,136);
}

elseif(isset($_POST['payment_not_ok'])) {
	$id = $_POST['bill_id'];
	bill_payment($id,0,137);
}

$button = array("payment_ok" => array("submit" => "  CONFIRM PAYMENT  "),
			    "payment_not_ok" => array("submit" => "  PENDING PAYMENT "));
			  
include THEME_DEFAULT.'header.php';?>          			
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="" name="myform">
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td align="right"><font size ="1"><img src="<?=IMG_PATH?>icon-full.gif" height ="13" width ="13"> = Paid &nbsp;&nbsp;&nbsp;<img src="<?=IMG_PATH?>icon-empty.gif" height ="13" width ="13"> = Not Paid</font></td></tr>
        <tr><td>
        	<?=genButton($button)?>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
            	<tr> 
            		<th width="20">&nbsp;</td>
                 	<th width="25" align="left">&nbsp;<b>NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>PERIOD</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>BRANCH</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>DEPT.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>RESP.</b>&nbsp;</td>                 	
                 	<th width="*" align="right">&nbsp;<b>ACC.</b>&nbsp;</td>
                 	<th width="*" align="right">&nbsp;<b>COST (EUR)</b>&nbsp;</td>
                 	<th width="75" colspan="3" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
                 </tr>
				 </thead>
				 <tbody>
<?php if($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>	
				<tr align="left" valign="top">
					<td align="center"><input type="checkbox" name="bill_id[]" value="<?=$array["id"]?>"></td>
					<td align="left">&nbsp;<?=$count?>.&nbsp;</td>
					<td align="left">&nbsp;#<?=$array["id"]?>&nbsp;</td>
					<td align="left">&nbsp;<?=trim($array["period"])?>&nbsp;</td>
					<td align="left">&nbsp;<?=strtoupper($array["branch"])?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["dept"])?ucwords($array["dept"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($array["resp"])?>&nbsp;</td>		
					<td align="right">&nbsp;<?=trim($array["acc"])?>&nbsp;</td>
					<td align="right">&nbsp;<?=number_format($array["cost"],2,'.',',')?>&nbsp;</td>
					<td width="25" align="center">
						<?=($array["payment"] == '1')?"<img src=\"".IMG_PATH."icon-full.gif\" height = \"13\" width = \"13\">":"<img src=\"".IMG_PATH."icon-empty.gif\"  height = \"13\" width = \"13\">";?></td>
					</td>
					<td align="center" width="25"><a title="Print" href="javascript:openW('./print_bill.php?id=<?=$array["id"]?>&m=print','Print_Bill',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png"></a></td>
					<td align="center" width="25"><a title="Excel" href="print_bill.php?id=<?=$array["id"]?>&m=xls"><img src="<?=IMG_PATH?>xls.gif" width="15" height="15"></a></td>
				</tr>
			<?php	$count++;  
				}
			} else {?>
				<tr><td colspan="12" align="center" bgcolor="#e5e5e5"><br />NO DATA TO CONFIRM<br /><br /></td></tr>
<?php 		}	?>		
				</tbody>
			</table><?=genButton($button)?>
		</td></tr>
		<tr><td  align="right"><font size ="1"><img src="<?=IMG_PATH?>icon-full.gif" height ="13" width ="13"> = Paid &nbsp;&nbsp;&nbsp;<img src="<?=IMG_PATH?>icon-empty.gif" height ="13" width ="13"> = Not Paid</font></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>