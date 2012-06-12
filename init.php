<?php
/* -----------------------------------------------------
 * File name  : init.php										
 * Created by : aryonp@gmail.com
 * -----------------------------------------------------						           
 * Purpose	  : Initialize basic configuration of the system							                 			
 * -----------------------------------------------------
 */

/* -- Define details here -- */

define("COMP_NAME", "PT. SCHENKER PETROLOG UTAMA");
define("COMP_NPWP", "01.356.884.5-058.000");
define("COMP_ADDR", "WISMA RAHARJA LT. 5<br>JL.TB SIMATUPANG KAV. 1<br>CILANDAK TIMUR - PASAR MINGGU<br>JAKARTA SELATAN - 12560");
define("COMP_PHONE", "+62 21 788 43 788");
define("COMP_FAX", "+62 21 788 333 69");
define("ADMIN", "muhammad.pratama@dbschenker.com");
define("PRODUCT", "STORIX");
define("VERSION", "2012");

/* -- Define default settings here -- */

define('SYS_CODE', 'the-avengers');
define("DEFAULT_MAIL_DOMAIN", "@dbschenker.com");
define("DEFAULT_PASS", "bsd");
define("URL_INTER", "http://valhalla.schenker.co.id/storix");
define("URL_INTRA", "http://10.213.141.50/storix");
define("COMP_URL", "http://www.schenker.co.id");
define('DB_HOST', '10.213.141.24');
define('DB_USER', 'storix');
define('DB_PASS', 'storix-123');
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