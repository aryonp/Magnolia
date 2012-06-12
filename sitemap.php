<?php
/* -----------------------------------------------------
 * File name	: sitemap.php								
 * Created by 	: M. Aryo N. Pratama		
 * -----------------------------------------------------				            
 * Purpose		: Generate sitemap according to group access
 * in navigation table and sort it in alphabetic order.											                 			
 * -----------------------------------------------------
 */

require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
chkSession();

$page_title="Sitemap";
$page_id_left="16";
chkSecurity($page_id_left);

include THEME_DEFAULT.'header.php';?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
	<tr><td><h2>SITEMAP</h2></td></tr>
	<tr><td height="1" bgcolor="#ccccff"></td></tr>
	<tr><td>&nbsp;</td></tr>
	<tr><td>
		<table border="0">
		<tr><td>&nbsp;</td>
		    <td>
<?php
  $smap_q ="SELECT n.id, n.sort, n.name, n.link, n.permit FROM navigation n WHERE del = '0' ORDER BY n.name ASC;";
  $nav_list_SQL = @mysql_query($smap_q);
  $prev_row = ""; 
  $letterlinks = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
  echo '<a name="top"></a>';
  for ($i = 0; $i < 26; $i++){
     echo '<b><a href="#'.$letterlinks[$i].'">'.$letterlinks[$i].'</a></b>&nbsp;&nbsp;&nbsp;';
  }
  echo "<br/><br/>"; 
  if (mysql_num_rows($nav_list_SQL)>=1){
	while($sitemap_list_array = mysql_fetch_array($nav_list_SQL)){
		$permit_array = explode(",",$sitemap_list_array[4]);
		$compare_permit = in_array($_SESSION['level'],$permit_array);
		if ($compare_permit != 0) {
      		$letter = strtoupper(substr($sitemap_list_array[2],0,1)); 
 			if ($letter != $prev_row && !is_numeric($letter)) { ?>
				<strong><a name="<?=$letter?>" href="#top"><?=$letter?></a></strong><br>
			<?php } ?>
			<img src="<?=IMG_PATH?>r-arrow.gif"><a href="<?=$sitemap_list_array[3]?>"><?=ucwords($sitemap_list_array[2])?></a><br><br> 
       		<?php 
			$prev_row = $letter; 
			}
		} 
  	}	 
?>
	</td></tr></table>
	</td></tr>
	<tr><td>&nbsp;</td></tr>
</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>