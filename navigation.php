<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Navigation Page";
$page_id_left 	= "14";
$page_id_right 	= "40";
$category_page 	= "mgmt";
chkSecurity($page_id_right);

$query 			= "SELECT id, pid, sort, name, link, category, permit FROM navigation WHERE del = '0' ORDER BY category ASC, sort ASC ";
$pagingResult 	= new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$level_list_query 	= "SELECT id, name FROM user_level WHERE del = '0' ORDER BY id ASC ;";
$level_list_SQL 	= @mysql_query($level_list_query) or die(mysql_error());

$status ="&nbsp;";

if (isset($_POST['add_nav'])){
	$pid  = trim($_POST['pud']);
	$sort = trim($_POST['sort']);
	$name = trim($_POST['name']);
	$link = trim($_POST['link']);
	$category = strtolower(trim($_POST['category']));
	$permit = trim($_POST['permit']);
	
	if (!empty($sort) AND !empty($pid) AND !empty($name) AND !empty($link) AND !empty($category) AND !empty($permit)){
		$add_nav_query  ="INSERT INTO navigation (pid,sort,name,link,category,permit) VALUES ('$pid','$sort','$name','$link','$category','$permit');"; 
		@mysql_query($add_nav_query) or die(mysql_error());
		log_hist("32",$name);
		header("location:$this_page");
	}
	
	else {
		$status ="<p class=\"yellowbox\">You can't insert an empty menu !</p>";
	}
}

if (isset($_POST['update_nav'])){
	$nid  = trim($_POST['nid']);
	$sort = trim($_POST['sort']);
	$name = trim($_POST['name']);
	$link = trim($_POST['link']);
	$category = strtolower(trim($_POST['category']));
	$permit = trim($_POST['permit']);
	$update_nav_query  ="UPDATE navigation SET sort='$sort', name='$name', link='$link', category='$category', permit='$permit' WHERE id ='$nid';";
	@mysql_query($update_nav_query) or die(mysql_error());
	log_hist("33",$name);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did  = trim($_GET['did']);
	$del  = "UPDATE navigation SET del = '1' WHERE id ='$did';";
	@mysql_query($del) or die(mysql_error());
	log_hist("34",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>MENU EDITING</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td ><?=$status?></td></tr>
		<tr><td >
			<div class="well">
<?php 	$d_perm = array();
		while($level_list_array = mysql_fetch_array($level_list_SQL)) { 
			array_push($d_perm, $level_list_array[0]." = ".ucwords($level_list_array[1]));
 		} 
 		echo implode(", ", $d_perm);?>
		</div></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><form method="POST" action="" class="well">
			<table border="0" cellpadding="1" cellspacing="1" width="100%">	
            	<tr valign="middle"> 
                 	<td><b>PID</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" name="pid" size="2" maxlength="2" id="pid" class="input-small">
                 	<script language="JavaScript" type="text/javascript">if(document.getElementById) document.getElementById('pid').focus();</script></td>
					<td><b>SORT</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" name="sort" class="input-small"></td>
					<td><b>NAME</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" name="name"></td>
					<td><b>LINK</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" name="link"></td>
					<td><b>CATEGORY</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" name="category"></td>
					<td><b>PERMISSION</b>&nbsp;<font color="Red">*</font>:<br /><input type="text" name="permit"></td>
					<td width="*" align="middle"><br /><input type="submit" name="add_nav" class="btn-info btn-small" value="  ADD MENU  "></td>
				</tr></table></form></td></tr>
		<tr><td >&nbsp;</td></tr>
		<tr><td >
		<?=$pagingResult->pagingMenu()?>
		<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
            	<tr><th width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td> 
            		<th width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>PID</b>&nbsp;</td>
                 	<th width="40" align="left">&nbsp;<b>SORT</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>NAME</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>LINK</b>&nbsp;</td>
                 	<th width="*" align="left">&nbsp;<b>CATEGORY</b>&nbsp;</td>
                 	<th width="*" align="right">&nbsp;<b>PERMISSION</b>&nbsp;</td>
                 	<th width="30" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
<?php 	if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $array["id"]) {?>
				<form method="POST" action="">
				<input type="hidden" name="nid" value="<?=$array["id"]?>">
				<tr bgcolor="#ffcc99" align="left" valign="top">
					<td width="25" align="left">&nbsp;<?=$count?>.</td> 
					<td>#<?=$array["id"]?></td>
					<td><input type="text" name="pid" value="<?=($array["pid"])?$array["pid"]:"";?>" size="2" class="input-small">&nbsp;</td>
					<td><input type="text" name="sort" value="<?=($array["sort"])?$array["sort"]:"-";?>" size="2" class="input-small">&nbsp;</td>
					<td><input type="text" name="name" value="<?=($array["name"])?ucwords($array["name"]):"-";?>">&nbsp;</td>
					<td><input type="text" name="link" value="<?=($array["link"])?$array["link"]:"-";?>">&nbsp;</td>
					<td><input type="text" name="category" value="<?=($array["category"])?$array["category"]:"-";?>">&nbsp;</td>
					<td align="right"><input type="text" name="permit" value="<?=($array["permit"])?$array["permit"]:"-";?>" size="20">&nbsp;</td>
					<td width="*" align="center" colspan="2">&nbsp;<input type="submit" class="btn-info btn-small" name="update_nav" value="UPDATE">&nbsp;<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL ">&nbsp;</td>
				</tr>
				</form>
			<?php } else { ?>
				<tr align="left">
					<td width="25" align="left">&nbsp;<?=$count?>.</td> 
					<td>&nbsp;#<?=($array["id"])?$array["id"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["pid"])?$array["pid"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["sort"])?$array["sort"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["name"])?ucwords($array["name"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["link"])?$array["link"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($array["category"])?strtoupper($array["category"]):"-";?>&nbsp;</td>
					<td align="right">&nbsp;<?=$array["permit"];?>&nbsp;</td>
					<td width="25" align="center"><a title="Edit Menu" href="<?=$this_page?>&nid=<?=$array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a title="Delete Menu" href="<?=$this_page?>" onclick="return confirmBox(this,'del','\nLink <?=ucwords($array["name"])?>', '<?=$array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
<?php			} $count++;  
			}
		} else {?>
				<tr><td colspan="9" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php } ?>
		</tbody>
			</table>
				<?=$pagingResult->pagingMenu()?>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>