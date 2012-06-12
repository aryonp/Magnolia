<?php
require_once 'init.php';
require_once LIB_PATH.'functions.lib.php';
require_once LIB_PATH.'paging.lib.php';
chkSession();

$page_title		= "Request Details Summary";
$page_id_left 	= "10";
$category_page 	= "archive";
chkSecurity($page_id_left);

$param 	= ($_GET['param']!="")?trim($_GET['param']):"";

switch($param){
	case "acc":
		$fltr = "AND r.req_type LIKE '%account%'";
		break;
	case "per":
		$fltr = "AND r.req_type LIKE '%peripheral%'"; 
		break;
	default :
		$fltr = ""; 
}

$query = "SELECT r.id, r.code, 
				 CONCAT(u.fname,' ',u.lname) AS fullname, 
				 r.req_type AS type, 
				 r.emp_name AS emp, 
				 d.name AS dname,
                 r.req_date AS rdate, 
                 r.auth_date AS l2date, 
                 r.appr_date AS adate, 
                 r.status 
          FROM req r 
          LEFT JOIN user u ON (u.id = r.user_id_fk) 
          LEFT JOIN departments d ON (d.id = r.dept_id_fk)
          WHERE r.del = '0' $fltr
          ORDER BY r.req_date DESC "; 

$pagingResult 	= new Pagination();
$pagingResult->setPageQuery($query);
$pagingResult->paginate();
$this_page = $_SERVER['PHP_SELF']."?".$pagingResult->getPageQString();

function req_det($id) {
	$req_det_q  = "SELECT rd.id, 
                      rt.name, 
                      rd.status, 
                      al.lname,
                      rd.confID, 
                      CONCAT(u.fname,' ',u.lname) AS cname, 
                      rd.confNote, 
                      rd.confDate, 
                      rd.confirm 
               FROM req_det rd 
					LEFT JOIN req_items rt ON (rd.item_id_fk = rt.id) 
					LEFT JOIN acc_level al ON (al.id = rd.acclvl_id_fk) 
					LEFT JOIN user u ON (u.id = rd.confID)
			   WHERE rd.req_id_fk = '$id' AND rd.del = '0';";
	$req_det_SQL = @mysql_query($req_det_q) or die(mysql_error());
	echo "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\"\'>\n";
	while($req_det_array = mysql_fetch_array($req_det_SQL,MYSQL_ASSOC)){ ?>
			<tr>
					<td>&nbsp;- #<?=ucwords($req_det_array["id"]);?> -> <?=ucwords($req_det_array["name"]);?>&nbsp;
					->&nbsp;(Grup/Level : <?=($req_det_array["lname"])?strtoupper($req_det_array["lname"]):"-";?>&nbsp;)*&nbsp;
					->&nbsp;<?=strtoupper($req_det_array["status"]);?>&nbsp;</td>
			</tr>
			<tr>	
					<td>&nbsp;<?=($req_det_array["confID"])?"Confirmed by : ".ucwords($req_det_array["cname"]):"";?>&nbsp;<br/>
					&nbsp;<?=($req_det_array["confNote"])?"Note : ".nl2br(trim($req_det_array["confNote"])):"";?>&nbsp;</td>
			</tr>
			<tr><td>&nbsp;</td></tr>
	<?php }  

	echo "</table>\n";
}

?>
<!DOCTYPE html>
<html>
<head>
<title><?=$page_title?> - <?=PRODUCT?> <?=VERSION?></title>
<link rel="stylesheet" type="text/css" media="all" href="<?=CSS_PATH?>bootstrap.css"/>
<link rel="shortcut icon" href="<?=IMG_PATH?>favicon.ico" type="image/x-icon" />
</head>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0" >
<//-----------------CONTENT-START-------------------------------------------------//>
	<table border="0" cellpadding="1" cellspacing="1" width="100%">
		<tr><td><h2><?=strtoupper($page_title)?></h2></td></tr>
		<tr><td height="1" bgcolor="#ccccff"></td></tr>
		<tr><td>&nbsp;</td></tr>
		<tr><td><?=$pagingResult->pagingMenu();?></td></tr>
        <tr><td>
        	<table border="0" cellpadding="1" cellspacing="1" width="100%" class="table table-striped table-bordered table-condensed">	
				<thead>
            	<tr valign="middle">  
                 	<td width="25" align="left">&nbsp;<b>NO.</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>ID</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>FILE NO.</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>REQUESTER</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>REQ. TYPE</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>FOR</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>DEPT.</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>REQ. DATE</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>L2 DATE</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>IT APPR.</b>&nbsp;</td>
                 	<td width="*" align="left">&nbsp;<b>DETAILS</b>&nbsp;</td>
				</tr>
				</thead>
				<tbody>
<?php 	if ($pagingResult->getPageRows()>= 1) {	
			$count = $pagingResult->getPageOffset() + 1;
			$result = $pagingResult->getPageArray();
			while ($array = mysql_fetch_array($result, MYSQL_ASSOC)) { ?>
				<tr valign="top">
					<td align="left">&nbsp;<?=$count?>.&nbsp;</td>
					<td align="left">&nbsp;<a href="./req_arc_det.php?id=<?=$array["id"]?>" target="_blank">#<?=$array["id"]?></a>&nbsp;</td>
					<td align="left">&nbsp;<a href="./req_arc_det.php?id=<?=$array["id"]?>" target="_blank"><?=$array["code"]?></a>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($array["fullname"])?>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($array["type"])?>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($array["emp"])?>&nbsp;</td>
					<td align="left">&nbsp;<?=ucwords($array["dname"])?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["rdate"] != "0000-00-00 00:00:00")?cplday('D, d.m.y',$array["rdate"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["l2date"] != "0000-00-00 00:00:00")?cplday('D, d.m.y',$array["l2date"]):"-";?>&nbsp;</td>
					<td align="left">&nbsp;<?=($array["adate"] != "0000-00-00 00:00:00")?cplday('D, d.m.y',$array["adate"]):"-";?>&nbsp;</td>
					<td align="left" valign="top"><?=req_det($array["id"])?></td>
				</tr>
<?php			$count++;  
				}
			} else {?>
				<tr><td colspan="11" align="center" bgcolor="#e5e5e5"><br />No Data Entries<br /><br /></td></tr>
<?php }		?></tbody>
			</table>
		</td></tr>
        <tr><td><?=$pagingResult->pagingMenu();?></td></tr>
		<tr><td>&nbsp;</td></tr>
	</table>
<//-----------------CONTENT-END-------------------------------------------------//>
</body>
</html>