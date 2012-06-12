<?php
/* -----------------------------------------------------
 * File name	: illegal.php								
 * Created by 	: M. Aryo N. Pratama		
 * -----------------------------------------------------				            
 * Purpose		: Warning message for illegal access											                 			
 * -----------------------------------------------------
 */

require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

/* Start Script */

$page_title 	= "Illegal Access";
$page_id_left 	= "2";
chkSecurity($page_id_left);

/* End Script */

include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" width="100%" height="100%">
	<tr><td valign="top" align="center"><br />
		<table class="yellowbox" width=50%>
		<tr><td valign="top" align="center">
		<H1><font color="#ff0000">ILLEGAL ACCESS !!!</font></H1><br />
		Your Username and IP Address has been recorded for auditing.<br />
		Please navigate only to the page that allowed for your user level.<br />
		</td></tr>
		</table>
	</td></tr>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>