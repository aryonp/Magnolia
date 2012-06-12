<?php
/* -----------------------------------------------------
 * File name   : paging.class.php								
 * Version	   : 2.1												
 * Created by  : aryonp@gmail.com						           
 * Created on  : 02.06.2008									
 * Implemented : 16.06.2008									
 * Last Update : 18.04.2011	
 * -----------------------------------------------------							
 * Purpose	   : Pagination class directly translated to 
 * OOP from my structured pagination library.	
 * -----------------------------------------------------	
 */

class Pagination {
	
	var $pageOffset;
	var $pageNum;
	var $maxPage;
	var $query;
	var $firstPage;
	var $prevPage;
	var $nextPage;
	var $lastPage;
	var $self;
	var $querystring;
	var $numRows;
	var $rpp;
	var $rpp_array;
	
	function __construct() {
		$this->rpp_array = array(10,25,50,100,200,500,1000);
		$this->self = $_SERVER['PHP_SELF'];
		$this->pageNum = (empty($_GET['page']) OR !is_numeric($_GET['page']))?1:$_GET['page'];	
	}
	
	function setPageQuery($query) {
		$this->query = $query;
	}
	
	function paginate() {	
		$this->rpp = (!in_array($_GET['rpp'],$this->rpp_array))?25:$_GET['rpp'];
		$this->pageOffset = ($this->pageNum - 1) * ($this->rpp);
		$this->maxPage = ceil($this->getPageRows()/$this->rpp);
		
		if($_GET){
			$args = explode("&",$_SERVER['QUERY_STRING']);
			foreach($args as $arg) {
				$keyval = explode("=",$arg);
				if($keyval[0] != "page" && $keyval[0] != "rpp") $this->querystring .= "&" . $arg;
			}
		}
		
		if($_POST) {
			foreach($_POST as $key=>$val){
				if($key != "page" && $key != "rpp") $this->querystring .= "&$key=$val";
			}
		}
		
		if ($this->pageNum > 1) {
   			$page = $this->pageNum - 1;
   			$this->prevPage  = "<a class=\"btn\" href=\"$this->self?page=$page&rpp=$this->rpp$this->querystring\"> < Prev</a>";
			$this->firstPage = "<a class=\"btn\" href=\"$this->self?page=1&rpp=$this->rpp$this->querystring\"> << First</a> ";
		}
		
		else {
   			$this->prevPage  = ''; 
   			$this->firstPage = ''; 
		}
		
		if ($this->pageNum < $this->maxPage) {
			$page = $this->pageNum + 1;
   			$this->nextPage = "<a class=\"btn\" href=\"$this->self?page=$page&rpp=$this->rpp$this->querystring\">Next > </a> ";
   			$this->lastPage = "<a class=\"btn\" href=\"$this->self?page=$this->maxPage&rpp=$this->rpp$this->querystring\">Last >> </a> ";
		}
		
		else {
   			$this->nextPage = ''; 
   			$this->lastPage = ''; 
		}
	}
	
	function getPageRows(){
		$query_rows 	= @mysql_query($this->query) or die(mysql_error());
		$this->numRows 	= mysql_num_rows($query_rows);
		return $this->numRows;
	}
	
	function getPageArray(){
		$limit 			= "$this->query LIMIT ".$this->getPageOffset().", $this->rpp;";
		$limit_query 	= @mysql_query($limit) or die(mysql_error());
		return $limit_query;
	}
	
	function getPageOffset(){
		return $this->pageOffset;
	}
	
	function getPageQString(){
		$query_string = "page=$this->pageNum&rpp=$this->rpp";	
		return $query_string;
	}
	
	function pagingJMenu(){
		if ($this->maxPage <= 1) { 
			$jump_menu = '';
		}
		
		else {
			$page_items_menu = '';
			$jump_menu = "\nPage $this->pageNum of $this->maxPage\n";

		}
		
		return $jump_menu;
	}
	
	function pagingRpp(){
		$items = '';
		foreach($this->rpp_array as $rpp_opt)	{
			$items .= ($rpp_opt == $this->rpp)?"<option class=\"input-small\" value=\"$this->self?page=1&rpp=$rpp_opt$this->querystring\" selected>$rpp_opt</option>\n":"<option class=\"input-small\" value=\"$this->self?page=1&rpp=$rpp_opt$this->querystring\">$rpp_opt</option>\n";
		}
		return "\nRows per page : \n<select class=\"input-small\" onchange=\"window.open(this.options[this.selectedIndex].value,'_top')\">\n$items</select>\n";
	}
	
	function pagingMenu(){
		$dispMenu = "<table border=\"0\" width=\"100%\">\n".
					"	<tr><td align=\"left\" width=\"60%\">".$this->pagingJMenu()."</td>".
		 			"		<td align=\"right\" width=\"40%\"><div class=\"btn-group\">$this->firstPage $this->prevPage $this->nextPage $this->lastPage".$this->pagingRpp()."</div></td>".
		 			"	</tr>\n".
		 			"</table>\n";
		return $dispMenu;
	}
	
}
?>