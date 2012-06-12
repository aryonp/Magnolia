<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title 	= "Vendor Period Evaluation Archive";
$page_id_left 	= "10";
$page_id_right 	= "25";
$category_page 	= "archive";
chkSecurity($page_id_right);

$vdr_eval_pr_list_query = "SELECT vp.id, CONCAT(u.fname,' ',u.lname) AS ename, v.name, vp.start, vp.end, vp.avg, vp.suggestion, vp.eval, vp.user_id_fk as user FROM ev_pr vp LEFT JOIN user u ON (u.id = vp.user_id_fk) LEFT JOIN vdr v ON (v.id = vp.vdr_id_fk) WHERE vp.del = '0' ORDER BY vp.eval DESC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($vdr_eval_pr_list_query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

include THEME_DEFAULT.'header.php';?>               			
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" action="">
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
        <tr><td>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr>
                 	<th width="25" align="left">&nbsp;<b>NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>EVAL BY</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>VENDOR</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>PERIOD</b>&nbsp;</td>
                 	<th width="*" align="right">&nbsp;<b>SCORE</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>SUGGESTION</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>EVAL DATE</b>&nbsp;</td>
                 	<th align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
<?php 	if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>
				<tr align="left" valign="top">
					<td align="left">&nbsp;<?=$count?>.</td>
					<td align="left">&nbsp;<?=($array["id"])?"#".ucwords($array["id"]):"&nbsp;-"?></td>
					<td align="left">&nbsp;<?=($array["ename"])?ucwords($array["ename"]):"&nbsp;-"?></td>
					<td align="left">&nbsp;<?=($array["name"])?ucwords($array["name"]):"&nbsp;-"?></td>
					<td align="left">&nbsp;<?=($array["start"])?cplday('d M Y',$array["start"]):"&nbsp;-"?>&nbsp;-
						  		     &nbsp;<?=($array["end"])?cplday('d M Y',$array["end"]):"&nbsp;-"?></td>
					<td align="right"><?=($array["avg"])?number_format($array["avg"],'2','.','')."&nbsp;":"&nbsp;-"?></td>
					<td align="left">&nbsp;<?=($array["suggestion"])?ucwords($array["suggestion"]):"&nbsp;-"?></td>
					<td align="left">&nbsp;<?=($array["eval"])?cplday('d M Y',$array["eval"]):"&nbsp;-"?></td>
					<td align="center" width="25"><a title="Print" href="javascript:openW('./print_vdr_pr.php?id=<?=$array["id"]?>','Print_Period_Evaluation',650,650,'toolbar=0,status=0,fullscreen=0,menubar=0,resizable=0,scrollbars=1');"><img src="<?=IMG_PATH?>print.png"></a>
				</tr>
<?php				$count++;  
				}
			} else {?>
				<tr><td colspan="8" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 		}	?>
			</tbody>
			</table>
		</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
        <tr><td>&nbsp;</td></tr>
	</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php';?>