<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$help_list_query = "SELECT title, location FROM help WHERE level_id_fk = '".$_SESSION['level']."' AND del = '0';";
$help_list_SQL = @mysql_query($help_list_query) or die(mysql_error());

$faq_list_query = "SELECT id, question, answer FROM faq WHERE del = '0';";
$faq_list_SQL = @mysql_query($faq_list_query) or die(mysql_error());

$page_title = "Help Page";
$page_id_left = "17";
chkSecurity($page_id_left);

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%" >
	<tr><td><h2>HELP & FAQ</h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>User's Manual for the system are available in PDF Format </td></tr>
	<tr><td>
		<table border="0">
<?php 	if(mysql_numrows($help_list_SQL)>= 1) {
			while($help_list_array = mysql_fetch_array($help_list_SQL, MYSQL_ASSOC)) {?>
			<tr><td valign="middle"><img src="<?=IMG_PATH?>r-arrow.gif">&nbsp;<?=($help_list_array["title"])?$help_list_array["title"]:"-";?>&nbsp;&nbsp;</td>
				<td><a href="<?=($help_list_array["location"])?$help_list_array["location"]:"-";?>"><img src="<?=IMG_PATH?>pdfdoc.png"></a></td></tr>
<?php 			} 
			} 
			else { ?>
			<tr><td>&nbsp;~ <font color="red">No User Manual available for this user level</font> ~</td></tr>
<?php 			} ?>
		</table>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>Frequently Asked Questions</td></tr>
	<tr><td>
		<table border="0">
<?php 	if(mysql_numrows($faq_list_SQL)>= 1) {
			$count = 1;
			while($faq_list_array = mysql_fetch_array($faq_list_SQL, MYSQL_ASSOC)) {?>
			<tr valign="top"><td rowspan="2"><?=$count?>.</td>
				<td><b><?=($faq_list_array["question"])?ucwords($faq_list_array["question"]):"-";?></b></td></tr>
			<tr valign="top"><td><?=($faq_list_array["answer"])?nl2br($faq_list_array["answer"]):"-";?></td></tr>
<?php  		$count++;
				} 
			} 
			else { ?>
			<tr><td>&nbsp;~ <font color="red">No FAQ available</font> ~</td></tr>
<?php 			} ?>
		</table>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>