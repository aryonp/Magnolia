<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Access Management";
$page_id_left	= "11";
$page_id_right 	= "27";
$category_page 	= "inventory";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$query 			= "SELECT a.id, a.name, a.email, b.name as bname, d.name as dname, a.lastupd, a.dsbl, a.del FROM acc a LEFT JOIN branch b ON (b.id = a.branch_id_fk) LEFT JOIN departments d ON (d.id = a.dept_id_fk) ORDER BY a.branch_id_fk ASC, a.name ASC ";
$pagingResult 	= new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "&nbsp;";

if(isset($_GET["did"])) {
	$did 	= $_GET["did"];
	$lupd 	= date('Y-m-d H:i:s');
	$del 	= "UPDATE acc SET dsbl = '1', del ='1', lastupd = '$lupd' WHERE id = '$did';";
	@mysql_query($del) or die(mysql_error());
	$del_det = "UPDATE acc_det SET del ='1' WHERE acc_id_fk = '$did';";
	@mysql_query($del_det) or die(mysql_error());
	log_hist("67",$did);
	header("location:$this_page");
	exit();
}
include THEME_DEFAULT.'header.php'; ?>      
<//-----------------CONTENT-START-------------------------------------------------//>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title);?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>[<a href = "./acc_create.php">CREATE NEW ACCESS PROFILE</a>]</td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
        <tr><td>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr><th width="25" align="left">&nbsp;<b>NO.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>NAME.</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>EMAIL</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>BRANCH</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>DEPT</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>LAST UPDATE</b>&nbsp;</td>
                 	<th colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
<?php if($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {?>				
				<tr align="left" valign="top">
					<td align="left">&nbsp;<?=$count?>.&nbsp;</td>
					<td align="left">&nbsp;#<?=$array['id']?>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($array['name'])?>&nbsp;</td>
					<td align="left">&nbsp;<?=strtolower($array['email'])?>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($array['bname'])?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array['dname'])?ucwords($array['dname']):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array['lastupd'] != "0000-00-00 00:00:00")?cplday('d M Y H:i:s',$array['lastupd']):"-";?>&nbsp;</td>
<?php 			if($array["dsbl"] == "0" AND $array["del"] == "0"){?>
					<td align="center" width="25"><a title="Update" href="./acc_det.php?id=<?=$array['id']?>"><img src="<?=IMG_PATH?>edit.png"></a></td>
					<td align="center" width="25"><a title="Delete" href="<?=$this_page?>" onclick="return confirmBox(this,'del','access for <?=ucwords($array["name"])?>', '<?=$array['id']?>')"><img src="<?=IMG_PATH?>delete.png"></a></td>
<?php			} else { ?>
					<td align="center" colspan="2">&nbsp;<a title="Update" href="./acc_det.php?id=<?=$array['id']?>"><img src="<?=IMG_PATH?>view.png"></a>&nbsp;</td>
<?php 			}	?> 
				</tr>
<?php 			$count++;  
				}
		} else {?>
				<tr><td colspan="9" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php	}	?>
			</tbody>
			</table>
		</td></tr>
        <tr><td><?=$pagingResult->pagingMenu();?></td></tr>
        <tr><td>&nbsp;</td></tr>
        <tr><td>[<a href = "./acc_create.php">CREATE NEW ACCESS PROFILE</a>]</td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>