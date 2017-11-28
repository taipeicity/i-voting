DROP TABLE IF EXISTS `#__sitemap`;

CREATE TABLE `#__sitemap` ( 
	`id` INT (11) UNSIGNED NOT NULL  AUTO_INCREMENT, 
	`catid` INT (11) UNSIGNED NOT NULL  , 
	`title` varchar(255) NOT NULL  , 
	`exclude` varchar(255) NOT NULL  , 
	`editor` text NOT NULL  , 
	`menu` text  NOT NULL  ,
	PRIMARY KEY (`id`)
)  ENGINE=InnoDB  DEFAULT CHARSET=utf8 ;