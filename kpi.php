<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once CONT_PATH.'kpi.class.php';
chkSession();

$page_title 	= "Dashboard";
$page_id_left 	= "12";
$page_id_right 	= "45";
$category_page 	= "kpi";
chkSecurity($page_id_right);

$sdate 		= trim($_POST['date1']);
$edate 		= trim($_POST['date2']);
$kpiResult 	= new strxKPI($sdate,$edate);

include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>  
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tbody>
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td bgcolor="#ccccff" height="1"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<form action="" method="POST" class="well form-inline">
				<label><b>START DATE</b>&nbsp;<font color="Red">*</font>&nbsp;:</label>
				<input type="text" class="input-small" name="date1" id="cal1" size="10" maxlength="10" value="<?=($_POST['date1'])?$_POST['date1']:date('Y-m-')."01";?>">&nbsp;<a href="javascript:NewCal('cal1','yyyymmdd')"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a>
				&nbsp;&nbsp;
				<label><b>END</b>&nbsp;<font color="Red">*</font>&nbsp;:</label>
				<input type="text" class="input-small" name="date2" id="cal2" size="10" maxlength="10" value="<?=($_POST['date2'])?$_POST['date2']:date('Y-m-d');?>">&nbsp;<a href="javascript:NewCal('cal2','yyyymmdd')"><img src="<?=IMG_PATH?>cal.gif" border="0" /></a>
				<input type="submit" class="btn-info btn-small" name="genkpi" value="  GENERATE  ">
			</form>
		</td></tr>
		<tr><td align="left">
<?php 	if(isset($_POST['genkpi'])) { 
			log_hist(58,"From ".cplday('d.m.y',$sdate)." to ".cplday('d.m.y',$edate));
			echo $kpiResult->strxKPIResAll();
		} ?>
		</td></tr>
	</tbody>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>