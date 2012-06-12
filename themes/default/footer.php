      			</td>
              	<td width="3">&nbsp;</td>
               			</tr>
              			</tbody>
           			</table>
       			</td>
       			<td align="left" bgcolor="#ccccff" valign="top" 
<?php 	if(!$page_id_right) { 
			echo "&nbsp;"; 
		} 
		else { 
			echo "width=\"150\">";
			echo sub_menu($page_id_right, $category_page);
		} 
?>				
				</td>
      		</tr>
			<tr height="20" bgcolor="#ccccff">
      			<td width="5">&nbsp;</td>
				<td><a href="javascript:openW('./credits.php','About',350,500,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1')">STORIX 2</a>&nbsp;&copy  <?=date('Y') == '2007'?'2007':'2007 - '.date('Y');?>. All rights reserved.</td>
				<td width="10"></td>	
			</tr>
    		</tbody>
		</table>
	</td></tr>
	</table>
<script language="javascript" type="text/javascript" src="<?=JS_PATH?>functions.js"></script>
</body>
</html>