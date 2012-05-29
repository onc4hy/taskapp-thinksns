
DROP TABLE IF EXISTS `ts_tasks`;

CREATE TABLE `ts_tasks` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(20) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `category` mediumint(5) DEFAULT NULL,
  `category_title` varchar(255) default NULL,
  `cover` varchar(255) DEFAULT NULL,
  `content` longtext,
  `readCount` int(11) NOT NULL DEFAULT '0',
  `commentCount` int(11) NOT NULL DEFAULT '0',
  `recommendCount` int(11) NOT NULL DEFAULT '0',
  `tags` varchar(255) DEFAULT NULL,
  `cTime` int(11) DEFAULT NULL,
  `mTime` int(11) DEFAULT NULL,
  `rTime` int(11) NOT NULL DEFAULT '0',
  `isHot` varchar(1) NOT NULL DEFAULT '0',
  `type` int(1) DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT '1',
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `private_data` varchar(255) DEFAULT NULL,
  `hot` int(11) NOT NULL DEFAULT '0',
  `canableComment` tinyint(1) NOT NULL DEFAULT '1',
  `attach` text,
		share_type varchar(20),
		share_source text,
		share_target text,
		parent_id int(11),
		level int(11),
		task_type int(11),
		task_status int(11),
		task_priority int(11),
		start_date date,
		due_date date,
		estimated_time decimal(10,2),
		spent_time decimal(10,2),
		done_ratio decimal(10,2),
		detail_summary text,
		rel_digest text,
		rel_tasks text,
  PRIMARY KEY (`id`),
  KEY `hot` (`hot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

	DROP TABLE IF EXISTS `ts_task_meta`;
	CREATE TABLE `ts_task_meta` (
	  `id` bigint(20) NOT NULL AUTO_INCREMENT,
	  `obj_id` bigint(20),
	  `meta_key` varchar(255) DEFAULT NULL,
	  `meta_value` text,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;

	DROP TABLE IF EXISTS `ts_task_detail`;
	CREATE TABLE `ts_task_detail` (
	  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
		task_id int(11),
		behavior_type int(11),
		start_date date,
		start_time time,
		stop_date date,
		stop_time time,
		spent_time decimal(10,2),
		done_ratio decimal(10,2),
		description text,
	  PRIMARY KEY (`id`)
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;

		DROP TABLE IF EXISTS ts_task_type;
		CREATE TABLE ts_task_type (
			id int(11) not null auto_increment,
			name varchar(100),
			uid int(11),
			pid int(11),
		  PRIMARY KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		
		insert into ts_task_type(name,uid,pid) value('开发',0,0);
		insert into ts_task_type(name,uid,pid) value('测试',0,0);
		insert into ts_task_type(name,uid,pid) value('生产',0,0);
		insert into ts_task_type(name,uid,pid) value('生活',0,0);
		
		DROP TABLE IF EXISTS ts_task_private_type;
		CREATE TABLE ts_task_private_type (
			id int(11) not null auto_increment,
			name varchar(100),
			uid int(11),
			pid int(11),
		  PRIMARY KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		
		insert into ts_task_private_type(name,uid,pid) value('公开',0,0);
		insert into ts_task_private_type(name,uid,pid) value('私人',0,0);
		
		DROP TABLE IF EXISTS ts_task_status_type;
		CREATE TABLE ts_task_status_type (
			id int(11) not null auto_increment,
			name varchar(100),
			uid int(11),
			pid int(11),
		  PRIMARY KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		
		insert into ts_task_status_type(name,uid,pid) value('新建',0,0);
		insert into ts_task_status_type(name,uid,pid) value('进行中',0,0);
		insert into ts_task_status_type(name,uid,pid) value('已完成',0,0);
		insert into ts_task_status_type(name,uid,pid) value('冻结',0,0);
		
		DROP TABLE IF EXISTS ts_task_priority_type;
		CREATE TABLE ts_task_priority_type (
			id int(11) not null auto_increment,
			name varchar(100),
			uid int(11),
			pid int(11),
		  PRIMARY KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		
		insert into ts_task_priority_type(name,uid,pid) value('低',0,0);
		insert into ts_task_priority_type(name,uid,pid) value('普通',0,0);
		insert into ts_task_priority_type(name,uid,pid) value('高',0,0);
		insert into ts_task_priority_type(name,uid,pid) value('紧急',0,0);
		insert into ts_task_priority_type(name,uid,pid) value('立刻',0,0);
		
		DROP TABLE IF EXISTS ts_task_share_type;
		CREATE TABLE ts_task_share_type (
			id int(11) not null auto_increment,
			name varchar(100),
			uid int(11),
			pid int(11),
		  PRIMARY KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		
		insert into ts_task_share_type(name,uid,pid) value('独立类型',0,0);
		insert into ts_task_share_type(name,uid,pid) value('发起分享',0,0);
		insert into ts_task_share_type(name,uid,pid) value('接受分享',0,0);
		insert into ts_task_share_type(name,uid,pid) value('传递分享',0,0);
		
		DROP TABLE IF EXISTS ts_task_behavior_type;
		CREATE TABLE ts_task_behavior_type (
			id int(11) not null auto_increment,
			name varchar(100),
			uid int(11),
			pid int(11),
		  PRIMARY KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		
		insert into ts_task_behavior_type(name,uid,pid) value('开发',0,0);
		insert into ts_task_behavior_type(name,uid,pid) value('测试',0,0);
		insert into ts_task_behavior_type(name,uid,pid) value('生产',0,0);
		insert into ts_task_behavior_type(name,uid,pid) value('生活',0,0);
		
		DROP TABLE IF EXISTS ts_task_task_rel;
		CREATE TABLE ts_task_task_rel (
			task_id_l int(11),
			task_id_r int(11),
			type int(11),
		  PRIMARY KEY (task_id_l,task_id_r)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		
		
		DROP TABLE IF EXISTS ts_task_notices;
		CREATE TABLE ts_task_notices (
			id int(11) not null auto_increment,
			uid int(11),
			pid int(11),
			name varchar(100),
			title varchar(255),
			content text,
			category int(11),
			category_title varchar(255),
			cover varchar(255),
			tags varchar(255),
			type int(11),
			status int(11),
			private int(11),
			cTime int(11),
			mTime int(11),
			rTime int(11),
			readCount int(11),
			commentCount int(11),
			recommendCount int(11),
			publish_begin int(11),
			publish_end int(11),
		  PRIMARY KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		
		
		DROP TABLE IF EXISTS ts_task_notes;
		CREATE TABLE ts_task_notes (
			id int(11) not null auto_increment,
			uid int(11),
			pid int(11),
			name varchar(100),
			title varchar(255),
			content text,
			category int(11),
			category_title varchar(255),
			cover varchar(255),
			tags varchar(255),
			type int(11),
			status int(11),
			private int(11),
			cTime int(11),
			mTime int(11),
			rTime int(11),
			readCount int(11),
			commentCount int(11),
			recommendCount int(11),
		  PRIMARY KEY (id)
		) ENGINE=MyISAM DEFAULT CHARSET=utf8;
		
		

DROP TABLE IF EXISTS `ts_task_category`;

CREATE TABLE `ts_task_category` (
  `id` mediumint(5) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `pid` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `pid` (`pid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

INSERT INTO `ts_task_category` (`name`,`uid`,`pid`) VALUES ('未分类',0,0);
INSERT INTO `ts_task_category` (name,uid,pid) VALUES ('待整理事宜',0,0);
INSERT INTO `ts_task_category` (name,uid,pid) VALUES ('未来/某天事项',0,0);
INSERT INTO `ts_task_category` (name,uid,pid) VALUES ('参考资料',0,0);
INSERT INTO `ts_task_category` (name,uid,pid) VALUES ('垃圾箱',0,0);
INSERT INTO `ts_task_category` (name,uid,pid) VALUES ('下一步行动清单',0,0);
INSERT INTO `ts_task_category` (name,uid,pid) VALUES ('等待清单',0,0);
INSERT INTO `ts_task_category` (name,uid,pid) VALUES ('未来/某天清单',0,0);
INSERT INTO `ts_task_category` (name,uid,pid) VALUES ('已完成处理',0,0);

DROP TABLE IF EXISTS `ts_task_tag`;

CREATE TABLE `ts_task_tag` (
  id int(11) NOT NULL AUTO_INCREMENT,
  name varchar(100) DEFAULT NULL,
  obj_id int(11) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ts_task_item`;

CREATE TABLE `ts_task_item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sourceId` int(11) DEFAULT NULL,
  `snapday` int(11) DEFAULT NULL,
  `pubdate` int(11) DEFAULT NULL,
  `boot` tinyint(1) NOT NULL DEFAULT '0',
  `title` varchar(255) DEFAULT NULL,
  `link` varchar(255) DEFAULT NULL,
  `summary` text,
  PRIMARY KEY (`id`),
  KEY `source_id` (`sourceId`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `ts_task_mention`;

CREATE TABLE `ts_task_mention` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `taskid` int(20) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `name` varchar(20) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `type` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `taskid` (`taskid`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ts_task_outline`;

CREATE TABLE `ts_task_outline` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(20) DEFAULT NULL,
  `title` varchar(255) DEFAULT NULL,
  `category` mediumint(5) DEFAULT NULL,
  `cover` varchar(255) DEFAULT NULL,
  `content` longtext,
  `readCount` int(11) NOT NULL DEFAULT '0',
  `commentCount` int(11) NOT NULL DEFAULT '0',
  `tags` varchar(255) DEFAULT NULL,
  `cTime` int(11) DEFAULT NULL,
  `mTime` int(11) DEFAULT NULL,
  `isHot` varchar(1) NOT NULL DEFAULT '0',
  `type` int(1) DEFAULT NULL,
  `status` varchar(1) NOT NULL DEFAULT '1',
  `private` tinyint(1) NOT NULL DEFAULT '0',
  `hot` int(11) NOT NULL DEFAULT '0',
  `friendId` text,
  `canableComment` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `hot` (`hot`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ts_task_source`;

CREATE TABLE `ts_task_source` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `service` varchar(10) DEFAULT NULL,
  `username` char(30) DEFAULT NULL,
  `cTime` int(11) DEFAULT NULL,
  `lastSnap` int(11) DEFAULT NULL,
  `isNew` tinyint(1) DEFAULT NULL,
  `changes` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `username` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



DROP TABLE IF EXISTS `ts_task_subscribe`;

CREATE TABLE `ts_task_subscribe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sourceId` int(11) DEFAULT NULL,
  `uid` int(11) DEFAULT NULL,
  `type` tinyint(4) DEFAULT '0',
  `newNum` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `sourceId` (`sourceId`,`uid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

# 模版数据
DELETE FROM `ts_template` WHERE `name` = 'task_create_weibo' OR `name` = 'task_share_weibo' OR `name` = 'task_note_create_weibo' OR `name` = 'task_note_share_weibo' OR `name` = 'task_notice_create_weibo' OR `name` = 'task_notice_share_weibo';
INSERT INTO `ts_template` (`name`, `alias`, `title`, `body`, `lang`, `type`, `type2`, `is_cache`, `ctime`) 
VALUES
    ('task_create_weibo','发布任务','','我发布了一份任务:【{title}】 {url}','zh','task','weibo',0,1290417734);
INSERT INTO `ts_template` (`name`, `alias`, `title`, `body`, `lang`, `type`, `type2`, `is_cache`, `ctime`) 
VALUES
    ('task_share_weibo','分享任务','','分享@{author} 的任务:【{title}】 {url}','zh','task','weibo',0,1290595552);

INSERT INTO `ts_template` (`name`, `alias`, `title`, `body`, `lang`, `type`, `type2`, `is_cache`, `ctime`) 
VALUES
    ('task_notice_create_weibo','发布任务通告','','我发布了一份任务通告:【{title}】 {url}','zh','task','weibo',0,1290417734);
INSERT INTO `ts_template` (`name`, `alias`, `title`, `body`, `lang`, `type`, `type2`, `is_cache`, `ctime`) 
VALUES
    ('task_notice_share_weibo','分享任务通告','','分享@{author} 的任务通告:【{title}】 {url}','zh','task','weibo',0,1290595552);

INSERT INTO `ts_template` (`name`, `alias`, `title`, `body`, `lang`, `type`, `type2`, `is_cache`, `ctime`) 
VALUES
    ('task_note_create_weibo','发布任务便签','','我发布了一份任务便签:【{title}】 {url}','zh','task','weibo',0,1290417734);
INSERT INTO `ts_template` (`name`, `alias`, `title`, `body`, `lang`, `type`, `type2`, `is_cache`, `ctime`) 
VALUES
    ('task_note_share_weibo','分享任务便签','','分享@{author} 的任务便签:【{title}】 {url}','zh','task','weibo',0,1290595552);

# 积分配置
DELETE FROM `ts_credit_setting` WHERE `type` = 'task';
INSERT INTO `ts_credit_setting` (name,alias,type,info,score,experience) VALUES ('add_task','发布任务','task','{action}{sign}了{score}{typecn}','5','5');
INSERT INTO `ts_credit_setting` (name,alias,type,info,score,experience) VALUES ('delete_task','删除任务','task','{action}{sign}了{score}{typecn}','-5','-5');

#添加ts_system_data数据

DELETE FROM `ts_system_data` WHERE `list` = 'task';
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES (0,'task','version_number','s:5:"28172";','2012-02-14 00:30:00');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','allorder','year','2010-12-02 18:18:16');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','savetime','5','2010-11-19 10:52:26');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','smiletype','mini','2010-11-19 10:52:38');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','leadingnum','100','2010-11-19 10:52:56');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','leadingin','1','2010-11-19 10:53:05');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','notifyfriend','1','2010-11-19 10:53:27');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','fileaway','0','2010-12-03 16:26:02');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','fileawaypage','6','2010-12-03 11:03:53');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','all','1','2010-12-02 19:05:40');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','delete','1','2010-12-02 19:05:40');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','suffix','...','2010-11-19 10:54:58');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','titleshort','200','2010-12-03 14:50:57');
INSERT INTO `ts_system_data` (`uid`,`list`,`key`,`value`,`mtime`) VALUES 
    (0,'task','limitpage','20','2010-12-03 13:11:32');

