
<?php
//由ThinkPHP工具箱生成的配置文件
if (!defined('THINK_PATH')) exit();

$miniConfig = array (
   'LANG_SWITCH_ON' =>True,
	'DEBUG_MODE'		=>	false,
	'DEFAULT_ACTION'    =>   'index',
	APP_NAME=>array(
	  "stringcount" => "150",
	  "all" =>  "1",
	  "pagenum" =>"10",
	  "smiletype" =>"mini",
	  "replay" => "1",
	  "fileawaypage" =>  "5",
	  "fileaway" =>  "1",
	  "delete" =>"0",
	)
);
$array = require_once( SITE_PATH.'/config.inc.php' );
$array = array_merge( $array,$miniConfig );
return $array;
?>

