<?php
/* -----------------------------------------------------
 * File name	: help.php								
 * Created by 	: M. Aryo N. Pratama		
 * -----------------------------------------------------				            
 * Purpose		: Manage help data.											                 			
 * -----------------------------------------------------
 */

require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Help Page";
$page_id_left 	= "14";
$page_id_right 	= "38";
$category_page 	= "mgmt";
chkSecurity($page_id_right);

$query ="SELECT help.id, CONCAT(user.fname,' ',user.lname) as creator, title, location, user_level.name as level, CONCAT(upd.fname, ' ',upd.lname) as updater, help.lastupd 
		 FROM help 
			LEFT JOIN user ON (user.id = help.user_id_fk) 
			LEFT JOIN user as upd ON (upd.id = help.upd_id_fk) 
			LEFT JOIN user_level ON (user_level.id = help.level_id_fk) 
		 WHERE help.del = '0' ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "";

$level_list_query 	= "SELECT id, name FROM user_level WHERE del = '0' ORDER BY id ASC ;";
$level_list_SQL 	= @mysql_query($level_list_query) or die(mysql_error());
$level_list_SQL_2 	= @mysql_query($level_list_query) or die(mysql_error());

if (isset($_POST['add_help'])){
	$createby 	= $_SESSION['uid'];
	$title 		= trim($_POST['title']);
	$location 	= file_target("help",$_FILES['help-file']['name']);
	$level 		= trim($_POST['level']);
	$updby 		= $_SESSION['uid'];
	$lastupd	= date('Y-m-d H:i:s');
	
	if ($level != "-" AND !empty($title)) {	
		
		if(move_uploaded_file($_FILES['help-file']['tmp_name'], $location)) {
			$add_help_query  ="INSERT INTO help (user_id_fk,title,location,level_id_fk,upd_id_fk,lastupd) VALUES ('$createby','$title','$location','$level','$updby','$lastupd');"; 
			@mysql_query($add_help_query) or die(mysql_error());
			log_hist("26",$title);
			header("location:$this_page");
		}
		
		else { 
			$status = "<p class=\"alert-alert-error\">Sorry, there was a problem uploading your file.</p>";
		}
		
	}
	
	else { $status ="<p class=\"alert\">Missing required information!</p>"; }
	
}

if (isset($_POST['update_help'])){
	$nid = trim($_POST['nid']);
	$level 		= trim($_POST['level_new']);
	$title		= trim($_POST['title_new']);
	$update_help_query  ="UPDATE help ";
	$update_help_query .="SET title = '$title', level_id_fk = '$level', upd_id_fk='".$_SESSION['uid']."', lastupd = '".date('Y-m-d H:i:s')."' "; 
	$update_help_query .="WHERE id ='".$nid."';";
	@mysql_query($update_help_query) or die(mysql_error());
	log_hist("27",$nid);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$delete_help_query  ="UPDATE help SET del = '1' WHERE id ='".$did."' AND del = '0';";
	@mysql_query($delete_help_query) or die(mysql_error());
	log_hist("28",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
<form method="POST" enctype="multipart/form-data" action="<?=$_SERVER['PHP_SELF']?>">
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>HELP LIST</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
			<div class="well">
			<table border="0" cellpadding="1" cellspacing="1" >
		<tr valign=top>
			<td align="right"><b>TITLE</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td><input type=text size=50 name='title' id="title">
					<script language="JavaScript" type="text/javascript">
						if(document.getElementById) document.getElementById('title').focus();
					</script></td>
			<td>&nbsp;</td>
			<td align="right"><b>LOCATION</b></td>
			<td>:</td>
			<td><input type="file" size="30" name="help-file">&nbsp;&nbsp;(Max: <?=ini_get('post_max_size');?>)</td></tr>
		<tr valign=top>
			<td align="right"><b>LEVEL</b>&nbsp;<font color="Red">*</font></td>
			<td>:</td>
			<td>
			<select name="level">
    				<option value="-">---------------------</option>
<?php while($level_list_array = mysql_fetch_array($level_list_SQL, MYSQL_ASSOC)){?>
    	<option value="<?=$level_list_array["id"]?>"><?=ucwords($level_list_array["name"])?></option>
<?php } ?>
			</td></select>
			<td>&nbsp;</td>
			</tr>
			</table></div></td></tr>
		<tr><td><input type="submit" name="add_help" class="btn-info btn-small" value="  ADD HELP  "></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td>
		<?=$pagingResult->pagingMenu()?>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
			<thead>
				<tr><td width="25" align="left">&nbsp;<b>NO</b>&nbsp;</td> 
                 	<td width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<td width="40" align="left">&nbsp;<b>TITLE</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>LEVEL</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>SIZE</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>LAST UPD.</b>&nbsp;</td>
                 	<td width="30" colspan="3" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
<?php 
   if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($help_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $help_list_array["id"]) {?>
				<input type="hidden" name="nid" value="<?=$help_list_array["id"]?>">
				<tr bgcolor="#ffcc99" align="left" valign="top">
					<td width="25" align="left">&nbsp;<?=$count?>.&nbsp;</td> 
					<td>&nbsp;#<?=($help_list_array["id"])?$help_list_array["id"]:"-";?>&nbsp;</td>
					<td><input type="text" name="title_new" value="<?=($help_list_array["title"])?trim(ucwords($help_list_array["title"])):"-";?>" size="30"></td>
					<td>
					<select name="level_new">
    				<option value="-">---------------------</option>
<?php 		while($level_list_array_2 = mysql_fetch_array($level_list_SQL_2, MYSQL_ASSOC)){
  				$compare_level = ($level_list_array_2["name"] == $help_list_array["level"])?"SELECTED":"";?>
    			<option value="<?=$level_list_array_2["id"]?>" <?=$compare_level?>><?=ucwords($level_list_array_2["name"])?></option>
<?php 			} ?>
					</select>
					</td>
					<td>&nbsp;<?=($help_list_array["location"])?filesize($help_list_array["location"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($help_list_array["lastupd"] != "0000-00-00 00:00:00")?cplday('d M Y H:i:s',$help_list_array["lastupd"]):"-";?>&nbsp;</td>
					<td width="*" align="center" colspan="3">
						<input type="submit" class="btn-info btn-small" name="update_help" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL "></td>
				</tr>
			<?php } else { ?>
				<tr align="left">
					<td width="25" align="left">&nbsp;<?=$count?>.</td> 
					<td>&nbsp;#<?=($help_list_array["id"])?$help_list_array["id"]:"-";?>&nbsp;</td>
					<td>&nbsp;<?=($help_list_array["title"])?ucwords($help_list_array["title"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($help_list_array["level"])?ucwords($help_list_array["level"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($help_list_array["location"])?filesize($help_list_array["location"]):"-";?>&nbsp;</td>
					<td>&nbsp;<?=($help_list_array["lastupd"] != "0000-00-00 00:00:00")?cplday('d M Y H:i:s',$help_list_array["lastupd"]):"-";?>&nbsp;</td>
					<td width="25" align="center"><a title="View" href="<?=$help_list_array["location"]?>">
						<img src="<?=IMG_PATH?>pdfdoc.png"></a></td>
					<td width="25" align="center"><a title="Edit" href="<?=$this_page?>&nid=<?=$help_list_array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center"><a title="Delete" href="<?=$this_page?>" onclick="return confirmBox(this, 'del','\nHelp ID # <?=$help_list_array["id"]?> ?', '<?=$help_list_array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
			<?php	
				} $count++;  
			}
		} else {?>
				<tr><td colspan="9" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
				<?php } ?></tbody>
		</table></div>
				<?=$pagingResult->pagingMenu()?>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
</form>
<//-----------------CONTENT-END-------------------------------------------------//>

<?php include THEME_DEFAULT.'footer.php'; ?>
