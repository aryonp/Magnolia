<?php
/* -----------------------------------------------------
 * File name  : index.php	
 * Created by : aryonp@gmail.com
 * -----------------------------------------------------						
 * Purpose	  : First page after login. Display MOTD, 
 * summary of system statistics, and 7 last transaction 
 * of the user.					                 			
 * -----------------------------------------------------
 */

require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title 	= "Home Page";
$page_id_left 	= "2";
chkSecurity($page_id_left);

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" width="100%" height="100%">
	<tr><td valign="top" align="left">
		<table border="0" width="500" >
			<tr><td>&nbsp;</td></tr>
			<tr><td><h2>Welcome, <?=ucwords($_SESSION['fullname'])?> !</h2></td></tr>
			<tr><td>&nbsp;</td></tr>
			<tr><td><?=disp_motd();?></td></tr>
			<tr><td><?=sys_stat();?></td></tr>
			<tr><td><?=last7trans();?></td></tr>
			<tr><td>&nbsp;&nbsp;</td></tr>
		</table></td>
		<td width="324" align="right" valign="bottom"><img src="<?=IMG_PATH?>home.jpg" width="295" height="471" border="0"/></td>
	</tr>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>