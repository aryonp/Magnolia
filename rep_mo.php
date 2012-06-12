<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once CONT_PATH.'mo.class.php';
chkSession();

$page_title 	= "Monthly Report";
$page_id_left 	= "12";
$page_id_right 	= "59";
$category_page 	= "kpi";
chkSecurity($page_id_right);

include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>  
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tbody>
	<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
	<tr><td bgcolor="#ccccff" height="1"></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		<form action="" method="POST" class="well form-inline">
			<label><b>YEAR</b>&nbsp;<font color="Red">*</font></label>:
			<input type="text" class="input-small" size="4" maxlength="4" name="year" value="<?=($_POST['year'])?trim($_POST['year']):date('Y');?>" />
			<input type="submit" class="btn-info btn-small" name="gen_rep_mo" value="  GENERATE  ">
		</form>
	</td></tr>
	<tr><td align="left">
<?php if(isset($_POST['gen_rep_mo'])) { 
		$year 		= (isset($_POST['year']))?trim($_POST['year']):date('Y');
		$mo_result 	= new strxMoRep($year);
		log_hist(146,"For Year : $year");
		echo $mo_result->strxMoRepAll();
	  } 
?>	
	</td></tr>
	</tbody>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>