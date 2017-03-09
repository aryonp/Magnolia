<?php
/* -----------------------------------------------------
 * File name  : init.php										
 * Created by : aryonp@gmail.com
 * -----------------------------------------------------						           
 * Purpose	  : Initialize basic configuration of the system							                 			
 * -----------------------------------------------------
 */

/* -- Define details here -- */

define("COMP_NAME", "PT ABC");
define("COMP_NPWP", "");
define("COMP_ADDR", "");
define("COMP_PHONE", "");
define("COMP_FAX", "");
define("ADMIN", "");
define("PRODUCT", "STORIX");
define("VERSION", "2012");

/* -- Define default settings here -- */

define('SYS_CODE', 'the-avengers');
define("DEFAULT_MAIL_DOMAIN", "@domain-lo");
define("DEFAULT_PASS", "bsd");
define("URL_INTER", "http://domain.com/storix");
define("URL_INTRA", "http://domain.com/storix");
define("COMP_URL", "http://domain.com");
define('DB_HOST', 'ip-database');
define('DB_USER', 'storix');
define('DB_PASS', 'password-db');
define('DB_USE', 'storix');

/* -- Start defining folder & etc here -- */

$notify 		= false;
$notify_msg 	= "STORIX currently on maintenance mode. Please log off from all of your activities now.";
$library_folder = "library";
$contr_folder 	= "controller";
$view_folder 	= "view";
$themes_folder 	= "themes";
$plugins_folder = "plugins";
$files_folder 	= "files";
$img_folder 	= "img";
$css_folder 	= "css";
$js_folder 		= "js";
$default_theme 	= "bootstrap";
$login_OK 		= "index.php";
$login_FAIL 	= "login.php";

/* -- Start defining path -- */

define('BASEPATH', realpath(dirname(__FILE__)).'/');
define('SYSPATH', '/'.basename(dirname(__FILE__)).'/');
define('LIB_PATH', BASEPATH.$library_folder.'/');
define('CONT_PATH', BASEPATH.$contr_folder.'/');
define('VIEW_PATH', BASEPATH.$view_folder.'/');
define('THEME_PATH', BASEPATH.$themes_folder.'/');
define('THEME_DEFAULT', THEME_PATH.$default_theme.'/');
define('PLUGINS_PATH', BASEPATH.$plugins_folder.'/');
define('IMG_PATH', SYSPATH.$themes_folder.'/'.$default_theme.'/'.$img_folder.'/');
define('CSS_PATH', SYSPATH.$themes_folder.'/'.$default_theme.'/'.$css_folder.'/');
define('JS_PATH', SYSPATH.$library_folder.'/'.$js_folder.'/');
define('FILES_PATH', SYSPATH.$files_folder.'/');
define('LOGIN_OK', SYSPATH.$login_OK);
define('LOGIN_FAIL', SYSPATH.$login_FAIL);

/* -- Path for 3rd parties plugins -- */

$jquery_folder = "jquery";
$phplot_folder = "phplot";
define('JQUERY_PATH', SYSPATH.$plugins_folder.'/'.$jquery_folder.'/');
define('PHPLOT_PATH', PLUGINS_PATH.$phplot_folder.'/');

/* -- Start DB conn -- */

include_once LIB_PATH.'config.lib.php';

?>