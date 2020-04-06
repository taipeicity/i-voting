CREATE TABLE IF NOT EXISTS `#__knowus` (
`id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,

`asset_id` INT(10) UNSIGNED NOT NULL DEFAULT '0',

`ordering` INT(11)  NOT NULL ,
`state` TINYINT(1)  NOT NULL ,
`checked_out` INT(11)  NOT NULL ,
`checked_out_time` DATETIME NOT NULL ,
`created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`created_by` int(11) NOT NULL,
`modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
`modified_by` INT(11)  NOT NULL ,
`title` VARCHAR(255)  NOT NULL ,
`alias` varchar(255) NOT NULL,
`youtube_url` VARCHAR(255)  NOT NULL ,
`unit` VARCHAR(255)  NOT NULL ,
`content` TEXT NOT NULL ,
`img` TEXT NOT NULL ,
`selectimg` tinyint(2) NOT NULL ,
`catid` int(11) NOT NULL DEFAULT '0',
PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT COLLATE=utf8_general_ci;


INSERT INTO `#__content_types` (`type_title`, `type_alias`, `table`, `content_history_options`)
SELECT * FROM ( SELECT 'detail','com_knowus.detail','{"special":{"dbtable":"#__knowus","key":"id","type":"Detail","prefix":"KnowusTable"}}', '{"formFile":"administrator\/components\/com_knowus\/models\/forms\/detail.xml", "hideFields":["checked_out","checked_out_time","params","language" ,"img"], "ignoreChanges":["modified_by", "modified", "checked_out", "checked_out_time"], "convertToInt":["publish_up", "publish_down"], "displayLookup":[{"sourceColumn":"catid","targetTable":"#__categories","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"group_id","targetTable":"#__usergroups","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"created_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"access","targetTable":"#__viewlevels","targetColumn":"id","displayColumn":"title"},{"sourceColumn":"modified_by","targetTable":"#__users","targetColumn":"id","displayColumn":"name"},{"sourceColumn":"unit","targetTable":"efa_unit","targetColumn":"66060","displayColumn":"title"}]}') AS tmp
WHERE NOT EXISTS (
	SELECT type_alias FROM `#__content_types` WHERE (`type_alias` = 'com_knowus.detail')
) LIMIT 1;
