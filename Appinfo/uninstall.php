
<?php
if (!defined('SITE_PATH')) exit();

$db_prefix = C('DB_PREFIX');

$sql = array(
	// Task数据
	"DROP TABLE IF EXISTS `{$db_prefix}tasks`;",
	"DROP TABLE IF EXISTS `{$db_prefix}task_category`;",
	"DROP TABLE IF EXISTS `{$db_prefix}task_detail`;",
	"DROP TABLE IF EXISTS `{$db_prefix}task_meta`;",
		"DROP TABLE IF EXISTS ts_task_type;",
		"DROP TABLE IF EXISTS ts_task_private_type;",
		"DROP TABLE IF EXISTS ts_task_status_type;",
		"DROP TABLE IF EXISTS ts_task_priority_type;",
		"DROP TABLE IF EXISTS ts_task_share_type;",
		"DROP TABLE IF EXISTS ts_task_behavior_type;",
		"DROP TABLE IF EXISTS ts_task_task_rel;",
		"DROP TABLE IF EXISTS ts_task_notices;",
		"DROP TABLE IF EXISTS ts_task_notes;",
	"DROP TABLE IF EXISTS `{$db_prefix}task_tag`;",
	"DROP TABLE IF EXISTS `{$db_prefix}task_item`;",
	"DROP TABLE IF EXISTS `{$db_prefix}task_mention`;",
	"DROP TABLE IF EXISTS `{$db_prefix}task_outline`;",
	"DROP TABLE IF EXISTS `{$db_prefix}task_source`;",
	"DROP TABLE IF EXISTS `{$db_prefix}task_subscribe`;",
	"DROP TABLE IF EXISTS `{$db_prefix}task_notes`;",
	"DROP TABLE IF EXISTS `{$db_prefix}task_notices`;",
	// ts_system_data数据
	"DELETE FROM `{$db_prefix}system_data` WHERE `list` = 'task'",
	// 模板数据
	"DELETE FROM `{$db_prefix}template` WHERE `name` = 'task_create_weibo' OR `name` = 'task_share_weibo' OR `name` = 'task_note_create_weibo' OR `name` = 'task_note_share_weibo' OR `name` = 'task_notice_create_weibo' OR `name` = 'task_notice_share_weibo';",
	// 积分规则
	"DELETE FROM `{$db_prefix}credit_setting` WHERE `type` = 'task';",
);

foreach ($sql as $v)
	M('')->execute($v);
	
?>

