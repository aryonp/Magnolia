<?php
/* -----------------------------------------------------
 * File name	: faq.php								
 * Created by 	: M. Aryo N. Pratama		
 * -----------------------------------------------------				            
 * Purpose		: Manage FAQ data.											                 			
 * -----------------------------------------------------
 */

require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "FAQ Page";
$page_id_left 	= "14";
$page_id_right 	= "39";
$category_page 	= "mgmt";
$page_id		= ($page_id_right != "")?$page_id_right:$page_id_left;
chkSecurity($page_id);

$faq_list_query ="SELECT id, question, answer FROM faq WHERE del = '0' ORDER BY id ASC ";
$pagingResult = new Pagination();
$pagingResult->setPageQuery($faq_list_query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

$status = "";

if (isset($_POST['add_faq'])){
	$question = mysql_real_escape_string(strip_tags(trim($_POST['question'])));
	$answer = mysql_real_escape_string(strip_tags(trim($_POST['answer'])));
	$add_faq_query  ="INSERT INTO faq (user_id_fk, question, answer, upd_id_fk, lastupd) VALUES ('".$_SESSION['uid']."', '$question', '$answer', '".$_SESSION['uid']."','".date('Y-m-d H:i:s')."');"; 
	if (!empty($question) AND !empty($answer)){
		@mysql_query($add_faq_query) or die (mysql_error());
		log_hist("29",$question);
		header("location:$this_page");
		exit();
	}
	else {
		$status ="<p class=\"alert\">You can't insert an empty FAQ !</p>";
	}
}

if (isset($_POST['update_faq'])){
	$nid = trim($_POST['nid']);
	$question = mysql_real_escape_string(strip_tags(trim($_POST['question'])));
	$answer = mysql_real_escape_string(strip_tags(trim($_POST['answer'])));
	$update_faq_query  ="UPDATE faq SET question ='$question', answer = '$answer', upd_id_fk = '".$_SESSION['uid']."', lastupd = '".date('Y-m-d H:i:s')."' ".
						"WHERE id ='".$nid."';";
	@mysql_query($update_faq_query) or die(mysql_error());
	log_hist("30",$nid);
	header("location:$this_page");
	exit();
}

if (isset($_GET['did'])){
	$did = trim($_GET['did']);
	$delete_faq_query  ="UPDATE faq SET del = '1', upd_id_fk = '".$_SESSION['uid']."', lastupd = '".date('Y-m-d H:i:s')."' WHERE id ='$did';";
	@mysql_query($delete_faq_query) or die(mysql_error());
	log_hist("31",$did);
	header("location:$this_page");
	exit();
}

if(isset($_POST['cancel'])){
	header("location:$this_page"); 	
}

include THEME_DEFAULT.'header.php'; ?>
<//-----------------CONTENT-START-------------------------------------------------//>
<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2>FAQ LIST</h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td><?=$status?></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><form method="POST" action="" class="well">
			<table border="0" cellpadding="1" cellspacing="1" >	
            	<tr valign="top"> 
					<td><b>QUESTION</b>&nbsp;<font color="Red">*</font></td>
					<td>&nbsp;:</td>
					<td><input type="text" name="question" size="45" id="question">
					<script language="JavaScript" type="text/javascript">
					if(document.getElementById) document.getElementById('question').focus();</script></td>
					<td width="*" rowspan="2">&nbsp;<input type="submit" name="add_faq" class="btn-info btn-small" value=" ADD FAQ "></td></tr>
				<tr valign="top"> 
					<td><b>ANSWER</b>&nbsp;<font color="Red">*</font></td>
					<td>&nbsp;:</td>
					<td><textarea cols="45" rows="3" name="answer" wrap="virtual"></textarea></td>
				</tr></table>
			</form></td></tr>
		<tr><td >&nbsp;</td></tr>
		<tr><td >
		<?=$pagingResult->pagingMenu()?>
			<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
				<tr><td width="25" align="left">&nbsp;<b>NO</b></td>
					<td width="*" align="left">&nbsp;<b>LIST</b></td>
					<td width="50" colspan="2" align="center">&nbsp;<b>CMD</b>&nbsp;</td>
				</tr>
			</thead>
			<tbody>
<?php if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($faq_list_array = mysql_fetch_array($result, MYSQL_ASSOC)) {
				if (isset($_GET['nid']) && $_GET['nid'] == $faq_list_array["id"]) {?>
				<form method="POST" action="">
				<input type="hidden" name="nid" value="<?=$faq_list_array["id"]?>">
				<tr bgcolor="#ffcc99" align="left" valign="top">
					<td width="25">&nbsp;<?=$count?>.</td>
					<td>Q: <input type="text" name="question" size="45" value="<?=($faq_list_array["question"])?strip_tags($faq_list_array["question"]):"-";?>"><br/><br />
					A: <textarea cols="60" rows="2" name="answer" wrap="virtual"><?=($faq_list_array["answer"])?nl2br($faq_list_array["answer"]):"-;"?></textarea></td>
					<td width="50" align="center" colspan="2">
						<input type="submit" class="btn-info btn-small" name="update_faq" value="UPDATE">&nbsp;
						<input type="submit" class="btn-info btn-small" name="cancel" value=" CANCEL "></td>
				</tr>
				</form>
<?php 			} else { ?>
				<tr align="left" valign="top">
					<td width="25">&nbsp;<?=$count?>.</td>
					<td><b><?=($faq_list_array["question"])?ucwords($faq_list_array["question"]):"-";?></b><br /><br />
					    <?=($faq_list_array["answer"])?nl2br($faq_list_array["answer"]):"-;"?></td>
					<td width="25" align="center" width="25"><a title="Edit FAQ" href="<?=$this_page?>&nid=<?=$faq_list_array["id"]?>">
						<img src="<?=IMG_PATH?>edit.png"></a></td>
					<td width="25" align="center" width="25"><a title="Delete FAQ" href="<?=$this_page?>" onclick="return confirmBox(this,'del', '\nFAQ ID #<?=$faq_list_array["id"]?>', '<?=$faq_list_array["id"]?>')">
						<img src="<?=IMG_PATH?>delete.png"></a></td>
				</tr>
<?php	 		} $count++; 
			}
		} else {?>
				<tr><td colspan="5" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php 	} ?></tbody>
		</table>
				<?=$pagingResult->pagingMenu()?>
		</td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
<?php include THEME_DEFAULT.'footer.php'; ?>