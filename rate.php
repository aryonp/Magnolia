<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
require_once CONT_PATH.'po.php';
chkSession();

$page_title		= "Euro Currency Rate";
$page_id_left 	= "13";
$page_id_right 	= "53";
$category_page 	= "strx";
chkSecurity($page_id_right);

$query 			= "SELECT r.id, r.date1, r.date2, r.curr, r.rate FROM rate r WHERE r.del = '0' ORDER BY r.id DESC ";
$pagingResult 	= new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();

$this_page 		= $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";
$curr 	= array("IDR","EUR","USD","SGD");

if (isset($_POST['add_rate'])){
	$curr 	= trim($_POST['kurs']);
	$rate 	= trim($_POST['rate']);
	$date1 	= trim($_POST['date1']);
	$date2 	= trim($_POST['date2']);
	
	if ($curr != "-" AND !empty($rate) AND $date1 != "YYYY-MM-DD" AND $date2 != "YYYY-MM-DD"){
		$add_rate  ="INSERT INTO rate (curr,rate,date1,date2) VALUES ('$curr','$rate','$date1','$date2');"; 
		@mysql_query($add_rate) or die(mysql_error());
		
		$rid = mysql_insert_id();
		log_hist("120",$rid);
		header("location:$this_page");
		exit();
	}
	
	else {
		$status ="<p class=\"yellowbox\">Missing information ! Please check your input again.</p>";
	}	
}

if (isset($_POST['update_rate'])){
	$nid 	= trim($_POST['nid']);
	$curr 	= trim($_POST['curr']);
	$rate 	= trim($_POST['rate']);
	$date1 	= trim($_POST['odate1']);
	$date2 	= trim($_POST['odate2']);
	
	$update_rate  = "UPDATE rate SET curr ='$curr', rate = '$rate', date1 = '$date1', date2 = '$date2' WHERE id ='$nid';";
	@mysql_query($update_rate) or die(mysql_error());
	
	log_hist("121",$nid);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did 			= trim($_GET['did']);
	$delete_rate  	= "UPDATE rate SET del = '1' WHERE id ='$did';";
	@mysql_query($delete_rate) or die(mysql_error());
	
	log_hist("122",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td><form method="POST" action="" class ="well">
			<table border="0" cellpadding="1" cellspacing="1">	
            	<tr> 
            	 <td><b>CURR.</b>&nbsp;<font color="Red">*</font>:<br /><?=kurs();?></td>
                 <td><b>RATE</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" name="rate" id="rate" size="40">
                 <script language="JavaScript" type="text/javascript">if(document.getElementById) document.getElementById('rate').focus();</script></td>
				 <td><b>START</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" class="input-small" id="date1" name="date1" value="<?=date('Y-m-d');?>" maxlength="10">
				 &nbsp;<a href="javascript:NewCal('date1','yyyymmdd')"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a></td>
				 <td><b>END</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" class="input-small" id="date2" name="date2" value="<?=date('Y-m-d',strtotime("+6 days"));?>" maxlength="10">
				 &nbsp;<a href="javascript:NewCal('date2','yyyymmdd')"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a></td>
				 <td width="*"><br />&nbsp;<input type="submit" name="add_rate" class="btn-info btn-small" value="  ADD RATE  "></td>
				</tr>
			</table>
			</form>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<?=$pagingResult->pagingMenu();?>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
            	<thead>
            	<tr valign="middle">
            		<td width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td> 
                 	<td width="*" align="left">&nbsp;<b>CURR</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>PERIOD</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>RATE</b>&nbsp;</td>
                 	<td width="*" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php    if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $array["id"]) {?>
				<form method="POST" action="">
				<input type="hidden" name="nid" value="<?=$array["id"]?>">
				<tr bgcolor="#ffcc99" align="left">
					<td width="25" align="left">&nbsp;<?=$count?>.</td>
					<td>&nbsp;<select name="curr">
				<option value="-">--------</option>
<?php foreach($curr as $name) {
		$compare_curr = ($name == $array["curr"])?"SELECTED":"";?>
				<option value ="<?=$name?>" <?=$compare_curr?>><?=$name?></option>
<?php	} ?>
 			    </select>&nbsp;</td>
					<td>&nbsp;
					<input type="text" id="odate1" name="odate1" value="<?=($array["date1"])?$array["date1"]:"YYYY-MM-DD";?>" maxlength="4">
				 	&nbsp;<a href="javascript:NewCal('odate1','yyyymmdd')"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a> - 
				 	<input type="text" id="odate2" name="odate2" value="<?=($array["date2"])?$array["date2"]:"YYYY-MM-DD";?>" maxlength="4">
					 &nbsp;<a href="javascript:NewCal('odate2','yyyymmdd')"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a>
					&nbsp;</td>
					<td>&nbsp;<input type="text" name="rate" value="<?=($array["rate"])?number_format($array["rate"],10):"0";?>" size="30">&nbsp;</td>
					<td width="*" align="center" colspan="2">
						<input type="submit" class="btn-info btn-small" name="update_budget" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL "></td>
				</tr>
				</form>
			<?php } else { ?>
				<tr align="left" valign="top">
					<td width="25" align="left">&nbsp;<?=$count?>.</td>
					<td>&nbsp;<?=($array["curr"])?$array["curr"]:"-";?>&nbsp</td>
					<td>&nbsp;<?=(($array["date1"]) AND ($array["date2"]))?cplday('d M y',$array["date1"])." - ".cplday('d M y',$array["date2"]):"-";?>&nbsp</td>
					<td>&nbsp;<?=($array["rate"])?number_format($array["rate"],10):"0";?>&nbsp</td>
					<td width="25" align="center"><a title="Edit Rate" href="<?=$this_page?>&nid=<?=$array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a title="Delete Rate" href="<?=$this_page?>" onclick="return confirmBox(this,'del','<?=($array["curr"])?$array["curr"]:"-";?> rate for period <?=($array["date1"])?cplday('d M y',$array["date1"]):"YYYY-MM-DD";?> - <?=($array["date2"])?cplday('d M y',$array["date2"]):"YYYY-MM-DD";?> ', '<?=$array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
<?php			} $count++;  
			}
		} else {?>
				<tr><td colspan="6" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php } ?></tbody>
			</table>
				<?=$pagingResult->pagingMenu();?>
		</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>