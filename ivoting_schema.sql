-- phpMyAdmin SQL Dump
-- version 4.4.2
-- http://www.phpmyadmin.net
--
-- 主機: localhost
-- 產生時間： 2017 年 06 月 14 日 14:51
-- 伺服器版本: 5.5.52-MariaDB
-- PHP 版本： 5.4.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- 資料庫： `temp_tcg006`
--

-- --------------------------------------------------------

--
-- 資料表結構 `efa_assets`
--

CREATE TABLE IF NOT EXISTS `efa_assets` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary Key',
  `parent_id` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set parent.',
  `lft` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
  `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
  `level` int(10) unsigned NOT NULL COMMENT 'The cached level in the nested tree.',
  `name` varchar(50) NOT NULL COMMENT 'The unique name for the asset.\n',
  `title` varchar(100) NOT NULL COMMENT 'The descriptive title for the asset.',
  `rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_assign_summary`
--

CREATE TABLE IF NOT EXISTS `efa_assign_summary` (
  `id` int(10) unsigned NOT NULL,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `table_suffix` varchar(50) NOT NULL COMMENT 'table後綴字',
  `column_num` smallint(5) unsigned NOT NULL COMMENT '欄位號碼',
  `title` varchar(200) NOT NULL COMMENT '名稱',
  `note` varchar(200) NOT NULL COMMENT '備註'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='可投票人名單總覽';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_associations`
--

CREATE TABLE IF NOT EXISTS `efa_associations` (
  `id` int(11) NOT NULL COMMENT 'A reference to the associated item.',
  `context` varchar(50) NOT NULL COMMENT 'The context of the associated item.',
  `key` char(32) NOT NULL COMMENT 'The key for the association computed from an md5 on associated ids.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_banners`
--

CREATE TABLE IF NOT EXISTS `efa_banners` (
  `id` int(11) NOT NULL,
  `cid` int(11) NOT NULL DEFAULT '0',
  `type` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `imptotal` int(11) NOT NULL DEFAULT '0',
  `impmade` int(11) NOT NULL DEFAULT '0',
  `clicks` int(11) NOT NULL DEFAULT '0',
  `clickurl` varchar(200) NOT NULL DEFAULT '',
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `description` text NOT NULL,
  `custombannercode` varchar(2048) NOT NULL,
  `sticky` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `metakey` text NOT NULL,
  `params` text NOT NULL,
  `own_prefix` tinyint(1) NOT NULL DEFAULT '0',
  `metakey_prefix` varchar(255) NOT NULL DEFAULT '',
  `purchase_type` tinyint(4) NOT NULL DEFAULT '-1',
  `track_clicks` tinyint(4) NOT NULL DEFAULT '-1',
  `track_impressions` tinyint(4) NOT NULL DEFAULT '-1',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `reset` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `language` char(7) NOT NULL DEFAULT '',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `version` int(10) unsigned NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_banner_clients`
--

CREATE TABLE IF NOT EXISTS `efa_banner_clients` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `contact` varchar(255) NOT NULL DEFAULT '',
  `email` varchar(255) NOT NULL DEFAULT '',
  `extrainfo` text NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `metakey` text NOT NULL,
  `own_prefix` tinyint(4) NOT NULL DEFAULT '0',
  `metakey_prefix` varchar(255) NOT NULL DEFAULT '',
  `purchase_type` tinyint(4) NOT NULL DEFAULT '-1',
  `track_clicks` tinyint(4) NOT NULL DEFAULT '-1',
  `track_impressions` tinyint(4) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_banner_tracks`
--

CREATE TABLE IF NOT EXISTS `efa_banner_tracks` (
  `track_date` datetime NOT NULL,
  `track_type` int(10) unsigned NOT NULL,
  `banner_id` int(10) unsigned NOT NULL,
  `count` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_categories`
--

CREATE TABLE IF NOT EXISTS `efa_categories` (
  `id` int(11) NOT NULL,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '',
  `extension` varchar(50) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `metadesc` varchar(1024) NOT NULL COMMENT 'The meta description for the page.',
  `metakey` varchar(1024) NOT NULL COMMENT 'The meta keywords for the page.',
  `metadata` varchar(2048) NOT NULL COMMENT 'JSON encoded metadata properties.',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_city`
--

CREATE TABLE IF NOT EXISTS `efa_city` (
  `id` int(10) unsigned NOT NULL COMMENT '編號',
  `title` varchar(100) NOT NULL COMMENT '名稱',
  `state` tinyint(4) NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_contact_details`
--

CREATE TABLE IF NOT EXISTS `efa_contact_details` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `con_position` varchar(255) DEFAULT NULL,
  `address` text,
  `suburb` varchar(100) DEFAULT NULL,
  `state` varchar(100) DEFAULT NULL,
  `country` varchar(100) DEFAULT NULL,
  `postcode` varchar(100) DEFAULT NULL,
  `telephone` varchar(255) DEFAULT NULL,
  `fax` varchar(255) DEFAULT NULL,
  `misc` mediumtext,
  `image` varchar(255) DEFAULT NULL,
  `email_to` varchar(255) DEFAULT NULL,
  `default_con` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `user_id` int(11) NOT NULL DEFAULT '0',
  `catid` int(11) NOT NULL DEFAULT '0',
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `mobile` varchar(255) NOT NULL DEFAULT '',
  `webpage` varchar(255) NOT NULL DEFAULT '',
  `sortname1` varchar(255) NOT NULL,
  `sortname2` varchar(255) NOT NULL,
  `sortname3` varchar(255) NOT NULL,
  `language` char(7) NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `metadata` text NOT NULL,
  `featured` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Set if article is featured.',
  `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `hits` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_content`
--

CREATE TABLE IF NOT EXISTS `efa_content` (
  `id` int(10) unsigned NOT NULL,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `title` varchar(255) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `introtext` mediumtext NOT NULL,
  `fulltext` mediumtext NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `attribs` varchar(5120) NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `metadata` text NOT NULL,
  `featured` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Set if article is featured.',
  `language` char(7) NOT NULL COMMENT 'The language code for the article.',
  `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_contentitem_tag_map`
--

CREATE TABLE IF NOT EXISTS `efa_contentitem_tag_map` (
  `type_alias` varchar(255) NOT NULL DEFAULT '',
  `core_content_id` int(10) unsigned NOT NULL COMMENT 'PK from the core content table',
  `content_item_id` int(11) NOT NULL COMMENT 'PK from the content type table',
  `tag_id` int(10) unsigned NOT NULL COMMENT 'PK from the tag table',
  `tag_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Date of most recent save for this tag-item',
  `type_id` mediumint(8) NOT NULL COMMENT 'PK from the content_type table'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Maps items from content tables to tags';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_content_frontpage`
--

CREATE TABLE IF NOT EXISTS `efa_content_frontpage` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_content_rating`
--

CREATE TABLE IF NOT EXISTS `efa_content_rating` (
  `content_id` int(11) NOT NULL DEFAULT '0',
  `rating_sum` int(10) unsigned NOT NULL DEFAULT '0',
  `rating_count` int(10) unsigned NOT NULL DEFAULT '0',
  `lastip` varchar(50) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_content_types`
--

CREATE TABLE IF NOT EXISTS `efa_content_types` (
  `type_id` int(10) unsigned NOT NULL,
  `type_title` varchar(255) NOT NULL DEFAULT '',
  `type_alias` varchar(255) NOT NULL DEFAULT '',
  `table` varchar(255) NOT NULL DEFAULT '',
  `rules` text NOT NULL,
  `field_mappings` text NOT NULL,
  `router` varchar(255) NOT NULL DEFAULT '',
  `content_history_options` varchar(5120) DEFAULT NULL COMMENT 'JSON string for com_contenthistory options'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_core_log_searches`
--

CREATE TABLE IF NOT EXISTS `efa_core_log_searches` (
  `search_term` varchar(128) NOT NULL DEFAULT '',
  `hits` int(10) unsigned NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_extensions`
--

CREATE TABLE IF NOT EXISTS `efa_extensions` (
  `extension_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `type` varchar(20) NOT NULL,
  `element` varchar(100) NOT NULL,
  `folder` varchar(100) NOT NULL,
  `client_id` tinyint(3) NOT NULL,
  `enabled` tinyint(3) NOT NULL DEFAULT '1',
  `access` int(10) unsigned NOT NULL DEFAULT '1',
  `protected` tinyint(3) NOT NULL DEFAULT '0',
  `manifest_cache` text NOT NULL,
  `params` text NOT NULL,
  `custom_data` text NOT NULL,
  `system_data` text NOT NULL,
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) DEFAULT '0',
  `state` int(11) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_googlecount`
--

CREATE TABLE IF NOT EXISTS `efa_googlecount` (
  `id` int(11) NOT NULL,
  `t_count` int(11) NOT NULL,
  `total` int(20) NOT NULL,
  `lang` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_languages`
--

CREATE TABLE IF NOT EXISTS `efa_languages` (
  `lang_id` int(11) unsigned NOT NULL,
  `lang_code` char(7) NOT NULL,
  `title` varchar(50) NOT NULL,
  `title_native` varchar(50) NOT NULL,
  `sef` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL,
  `description` varchar(512) NOT NULL,
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `sitename` varchar(1024) NOT NULL DEFAULT '',
  `published` int(11) NOT NULL DEFAULT '0',
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_mail_record`
--

CREATE TABLE IF NOT EXISTS `efa_mail_record` (
  `id` int(10) unsigned NOT NULL,
  `from` varchar(100) NOT NULL,
  `fromname` varchar(50) NOT NULL,
  `recipient` text NOT NULL,
  `subject` varchar(200) NOT NULL,
  `body` text NOT NULL,
  `mode` tinyint(4) NOT NULL,
  `cc` text NOT NULL,
  `bcc` text NOT NULL,
  `attachment` varchar(200) NOT NULL,
  `replyto` text NOT NULL,
  `replytoname` varchar(50) NOT NULL,
  `status` tinyint(4) NOT NULL,
  `created` datetime NOT NULL,
  `repeate_time` datetime NOT NULL,
  `repeate_num` tinyint(3) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_menu`
--

CREATE TABLE IF NOT EXISTS `efa_menu` (
  `id` int(11) NOT NULL,
  `menutype` varchar(24) NOT NULL COMMENT 'The type of menu this item belongs to. FK to #__menu_types.menutype',
  `title` varchar(255) NOT NULL COMMENT 'The display title of the menu item.',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL COMMENT 'The SEF alias of the menu item.',
  `note` varchar(255) NOT NULL DEFAULT '',
  `path` varchar(1024) NOT NULL COMMENT 'The computed path of the menu item based on the alias field.',
  `link` varchar(1024) NOT NULL COMMENT 'The actually link the menu item refers to.',
  `type` varchar(16) NOT NULL COMMENT 'The type of link: Component, URL, Alias, Separator',
  `published` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'The published state of the menu link.',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '1' COMMENT 'The parent menu item in the menu tree.',
  `level` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The relative level in the tree.',
  `component_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to #__extensions.id',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to #__users.id',
  `checked_out_time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'The time the menu item was checked out.',
  `browserNav` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'The click behaviour of the link.',
  `access` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'The access level required to view the menu item.',
  `img` varchar(255) NOT NULL COMMENT 'The image of the menu item.',
  `template_style_id` int(10) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL COMMENT 'JSON encoded data for the menu item.',
  `lft` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
  `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
  `home` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Indicates if this menu item is the home or default page.',
  `language` char(7) NOT NULL DEFAULT '',
  `client_id` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_menu_types`
--

CREATE TABLE IF NOT EXISTS `efa_menu_types` (
  `id` int(10) unsigned NOT NULL,
  `asset_id` int(11) NOT NULL,
  `menutype` varchar(24) COLLATE utf8mb4_unicode_ci NOT NULL,
  `title` varchar(48) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_messages`
--

CREATE TABLE IF NOT EXISTS `efa_messages` (
  `message_id` int(10) unsigned NOT NULL,
  `user_id_from` int(10) unsigned NOT NULL DEFAULT '0',
  `user_id_to` int(10) unsigned NOT NULL DEFAULT '0',
  `folder_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `date_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `priority` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(255) NOT NULL DEFAULT '',
  `message` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_messages_cfg`
--

CREATE TABLE IF NOT EXISTS `efa_messages_cfg` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `cfg_name` varchar(100) NOT NULL DEFAULT '',
  `cfg_value` varchar(255) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_modules`
--

CREATE TABLE IF NOT EXISTS `efa_modules` (
  `id` int(11) NOT NULL,
  `asset_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'FK to the #__assets table.',
  `title` varchar(100) NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `content` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  `position` varchar(50) NOT NULL DEFAULT '',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `module` varchar(50) DEFAULT NULL,
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `showtitle` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `params` text NOT NULL,
  `client_id` tinyint(4) NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_modules_menu`
--

CREATE TABLE IF NOT EXISTS `efa_modules_menu` (
  `moduleid` int(11) NOT NULL DEFAULT '0',
  `menuid` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_newsfeeds`
--

CREATE TABLE IF NOT EXISTS `efa_newsfeeds` (
  `catid` int(11) NOT NULL DEFAULT '0',
  `id` int(10) unsigned NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `link` varchar(200) NOT NULL DEFAULT '',
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `numarticles` int(10) unsigned NOT NULL DEFAULT '1',
  `cache_time` int(10) unsigned NOT NULL DEFAULT '3600',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `rtl` tinyint(4) NOT NULL DEFAULT '0',
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `metadata` text NOT NULL,
  `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `description` text NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `images` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_overrider`
--

CREATE TABLE IF NOT EXISTS `efa_overrider` (
  `id` int(10) NOT NULL COMMENT 'Primary Key',
  `constant` varchar(255) NOT NULL,
  `string` text NOT NULL,
  `file` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_phone_record`
--

CREATE TABLE IF NOT EXISTS `efa_phone_record` (
  `id` int(10) unsigned NOT NULL,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `msgid` varchar(100) NOT NULL COMMENT '簡訊序號',
  `sms_user` varchar(100) NOT NULL COMMENT '加密後發送的帳號',
  `phone` varchar(200) NOT NULL COMMENT '加密後接收的手機號碼',
  `body` text NOT NULL COMMENT '簡訊內容',
  `status` varchar(10) NOT NULL COMMENT '發送的狀態',
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='簡訊記錄';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_postinstall_messages`
--

CREATE TABLE IF NOT EXISTS `efa_postinstall_messages` (
  `postinstall_message_id` bigint(20) unsigned NOT NULL,
  `extension_id` bigint(20) NOT NULL DEFAULT '700' COMMENT 'FK to #__extensions',
  `title_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Lang key for the title',
  `description_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'Lang key for description',
  `action_key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `language_extension` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'com_postinstall' COMMENT 'Extension holding lang keys',
  `language_client_id` tinyint(3) NOT NULL DEFAULT '1',
  `type` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'link' COMMENT 'Message type - message, link, action',
  `action_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'RAD URI to the PHP file containing action method',
  `action` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT '' COMMENT 'Action method name or URL',
  `condition_file` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'RAD URI to file holding display condition method',
  `condition_method` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'Display condition method, must return boolean',
  `version_introduced` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '3.2.0' COMMENT 'Version when this message was introduced',
  `enabled` tinyint(3) NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_redirect_links`
--

CREATE TABLE IF NOT EXISTS `efa_redirect_links` (
  `id` int(10) unsigned NOT NULL,
  `old_url` varchar(255) NOT NULL,
  `new_url` varchar(255) NOT NULL,
  `referer` varchar(150) NOT NULL,
  `comment` varchar(255) NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `published` tinyint(4) NOT NULL,
  `created_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_rokadminaudit`
--

CREATE TABLE IF NOT EXISTS `efa_rokadminaudit` (
  `id` bigint(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `session_id` varchar(50) DEFAULT NULL,
  `option` varchar(100) DEFAULT NULL,
  `task` varchar(100) DEFAULT NULL,
  `cid` int(50) DEFAULT NULL,
  `page` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `title` text,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_rokuserstats`
--

CREATE TABLE IF NOT EXISTS `efa_rokuserstats` (
  `id` bigint(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `ip` varchar(50) DEFAULT NULL,
  `session_id` varchar(50) DEFAULT NULL,
  `page` varchar(255) DEFAULT NULL,
  `referrer` varchar(255) DEFAULT NULL,
  `timestamp` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_schemas`
--

CREATE TABLE IF NOT EXISTS `efa_schemas` (
  `extension_id` int(11) NOT NULL,
  `version_id` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_session`
--

CREATE TABLE IF NOT EXISTS `efa_session` (
  `session_id` varchar(200) NOT NULL DEFAULT '',
  `client_id` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `guest` tinyint(4) unsigned DEFAULT '1',
  `time` varchar(14) DEFAULT '',
  `data` mediumtext,
  `userid` int(11) DEFAULT '0',
  `username` varchar(150) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_sitemap`
--

CREATE TABLE IF NOT EXISTS `efa_sitemap` (
  `id` int(11) unsigned NOT NULL,
  `catid` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `exclude` varchar(255) NOT NULL,
  `editor` text NOT NULL,
  `menu` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_email_notice`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_email_notice` (
  `id` int(10) unsigned NOT NULL,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `email` varchar(200) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '類別(1:投票前,2:催票,3:結束)',
  `is_send` tinyint(3) unsigned NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_fields`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_fields` (
  `id` int(11) NOT NULL,
  `quest_id` int(11) NOT NULL DEFAULT '0' COMMENT '題目ID',
  `ftext` text NOT NULL COMMENT '標題',
  `image` varchar(200) NOT NULL COMMENT '圖片',
  `desc` text NOT NULL COMMENT '描述',
  `file1` varchar(200) NOT NULL COMMENT '附加檔',
  `alt_field_id` int(11) NOT NULL DEFAULT '0',
  `is_main` int(11) NOT NULL DEFAULT '0',
  `is_true` int(11) NOT NULL DEFAULT '0',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `is_other` tinyint(4) NOT NULL COMMENT '是否為其他欄位',
  `catid` int(10) unsigned NOT NULL COMMENT '分類ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_iscales`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_iscales` (
  `id` int(11) NOT NULL,
  `iscale_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_phone_notice`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_phone_notice` (
  `id` int(10) unsigned NOT NULL,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `phone` varchar(200) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '類別',
  `is_send` tinyint(3) unsigned NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_qsections`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_qsections` (
  `id` int(11) NOT NULL,
  `sf_name` varchar(250) NOT NULL DEFAULT '',
  `addname` tinyint(4) NOT NULL DEFAULT '0',
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `sf_survey_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_qtypes`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_qtypes` (
  `id` int(11) NOT NULL,
  `sf_qtype` varchar(50) NOT NULL DEFAULT '',
  `sf_plg_name` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_quests`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_quests` (
  `id` int(10) unsigned NOT NULL,
  `sf_survey` int(10) unsigned NOT NULL DEFAULT '0' COMMENT '議題ID',
  `sf_qtype` int(11) NOT NULL DEFAULT '0',
  `sf_qtext` text NOT NULL COMMENT '標題',
  `sf_impscale` int(11) NOT NULL DEFAULT '0',
  `sf_rule` int(11) NOT NULL DEFAULT '0',
  `sf_fieldtype` varchar(255) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0' COMMENT '排序',
  `sf_compulsory` tinyint(4) NOT NULL DEFAULT '1',
  `sf_section_id` int(11) NOT NULL DEFAULT '0',
  `published` tinyint(4) NOT NULL DEFAULT '0',
  `sf_qstyle` int(11) NOT NULL DEFAULT '0',
  `sf_num_options` tinyint(4) NOT NULL DEFAULT '0',
  `sf_default_hided` tinyint(4) NOT NULL DEFAULT '0',
  `is_final_question` tinyint(3) NOT NULL DEFAULT '0',
  `sf_qdescr` text NOT NULL,
  `question_type` varchar(50) NOT NULL COMMENT '題目類型',
  `is_multi` tinyint(3) unsigned NOT NULL COMMENT '是否為複選題',
  `multi_limit` tinyint(3) unsigned NOT NULL COMMENT '複選_限定應投',
  `multi_min` tinyint(3) unsigned NOT NULL COMMENT '複選_最少投',
  `multi_max` tinyint(3) unsigned NOT NULL COMMENT '複選_最多投'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_quests_cat`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_quests_cat` (
  `id` int(10) unsigned NOT NULL,
  `question_id` int(10) NOT NULL COMMENT '題目ID',
  `title` varchar(50) NOT NULL COMMENT '標題',
  `ordering` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='選項的分類';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_quest_show`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_quest_show` (
  `id` int(11) NOT NULL,
  `quest_id` int(11) NOT NULL DEFAULT '0',
  `survey_id` int(11) NOT NULL DEFAULT '0',
  `quest_id_a` int(11) NOT NULL DEFAULT '0',
  `answer` int(11) NOT NULL DEFAULT '0',
  `ans_field` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_rules`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_rules` (
  `id` int(11) NOT NULL,
  `quest_id` int(11) NOT NULL DEFAULT '0',
  `answer_id` int(11) NOT NULL DEFAULT '0',
  `next_quest_id` int(11) NOT NULL DEFAULT '0',
  `alt_field_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_scales`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_scales` (
  `id` int(11) NOT NULL,
  `quest_id` int(11) NOT NULL DEFAULT '0',
  `stext` varchar(250) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_sub_fields`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_sub_fields` (
  `id` int(11) NOT NULL,
  `quest_id` int(10) unsigned NOT NULL COMMENT '題目ID',
  `title` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_survs`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_survs` (
  `id` int(11) NOT NULL,
  `sf_cat` int(11) NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL DEFAULT '' COMMENT '標題',
  `desc` text NOT NULL COMMENT '完整描述',
  `short_desc` text COMMENT '簡短描述',
  `voters_eligibility` text NOT NULL COMMENT '投票人資格',
  `voters_authentication` text NOT NULL COMMENT '投票人驗證方式',
  `during_vote` text NOT NULL COMMENT '投票期間',
  `promotion` text NOT NULL COMMENT '宣傳推廣方式',
  `results_using` text NOT NULL COMMENT '投票結果運用方式',
  `announcement_date` text NOT NULL COMMENT '公布方式及日期',
  `announcement_method` text NOT NULL COMMENT '公佈方式',
  `precautions` text NOT NULL COMMENT '注意事項',
  `image` varchar(50) NOT NULL DEFAULT '' COMMENT '圖片',
  `layout` varchar(20) NOT NULL,
  `remind_text` text NOT NULL COMMENT '投票前提醒',
  `drumup_text` text NOT NULL COMMENT '催票提醒',
  `end_text` text NOT NULL COMMENT '投票結束提醒',
  `phone_remind_text` text NOT NULL COMMENT '手機訊息-投票前提醒',
  `phone_drumup_text` text NOT NULL COMMENT '手機訊息-催票提醒',
  `phone_end_text` text NOT NULL COMMENT '手機訊息-投票結束通知提醒',
  `sms_user` varchar(200) NOT NULL COMMENT '加密後的帳號',
  `sms_passwd` varchar(200) NOT NULL COMMENT '加密後的密碼',
  `is_public` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否公開',
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `asset_id` int(10) NOT NULL DEFAULT '0',
  `published` tinyint(4) NOT NULL DEFAULT '0' COMMENT '發佈',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL,
  `vote_start` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `vote_end` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `is_notice_phone` tinyint(3) unsigned NOT NULL,
  `is_notice_email` tinyint(3) unsigned NOT NULL,
  `is_checked` tinyint(4) NOT NULL DEFAULT '0',
  `is_complete` tinyint(4) NOT NULL,
  `checked` datetime NOT NULL,
  `checked_by` int(10) unsigned NOT NULL,
  `display_result` tinyint(3) unsigned NOT NULL COMMENT '結果顯示方式 (0:不顯示,1:投票中顯示, 2:結束後顯示)',
  `result_num_type` tinyint(4) unsigned NOT NULL COMMENT '投票結果數設定類別(0:全部顯示,1:指定數目)',
  `result_num` tinyint(3) unsigned NOT NULL COMMENT '投票結果數目',
  `result_orderby` tinyint(3) unsigned NOT NULL COMMENT '結果項目排序方式 (0: 依選項, 1: 依票數)',
  `result_desc` text NOT NULL COMMENT '總結果說明',
  `vote_num_params` text NOT NULL COMMENT '投票數設定',
  `verify_required` tinyint(4) NOT NULL,
  `verify_type` text NOT NULL,
  `verify_params` text NOT NULL COMMENT '驗證方式的參數',
  `verify_sms_user` varchar(200) NOT NULL COMMENT '驗證用-加密後的帳號',
  `verify_sms_passwd` varchar(200) NOT NULL COMMENT '驗證用-加密後的密碼',
  `total_vote` int(10) unsigned NOT NULL COMMENT '總票數',
  `is_lottery` tinyint(3) unsigned NOT NULL COMMENT '是否抽獎',
  `results_proportion` text NOT NULL COMMENT '預定結果參採比重',
  `part` text NOT NULL COMMENT '部分參採',
  `other` text NOT NULL COMMENT '其他',
  `other_data` text COMMENT '其他參考資料',
  `other_data2` text COMMENT '其他參考資料',
  `other_data3` text COMMENT '其他參考資料',
  `other_url` text COMMENT '其他參考網址',
  `followup_caption` text NOT NULL COMMENT '後續辦理情形說明',
  `at_present` text NOT NULL COMMENT '目前進度',
  `discuss_source` text NOT NULL COMMENT '討論管道',
  `is_place` tinyint(4) NOT NULL COMMENT '是否有現地投票',
  `place_image` varchar(50) NOT NULL COMMENT '掃描標的物圖片'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_verify_result`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_verify_result` (
  `id` int(10) unsigned NOT NULL,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `verify_method` varchar(50) NOT NULL COMMENT '驗證方式',
  `state` tinyint(4) NOT NULL COMMENT '驗證狀態',
  `client_ip` varchar(50) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='驗證結果記錄';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_vote`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote` (
  `id` int(10) unsigned NOT NULL,
  `ticket_num` varchar(255) NOT NULL COMMENT '票號',
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='票箱';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_vote_count`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote_count` (
  `id` int(10) unsigned NOT NULL,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `question_id` int(10) unsigned NOT NULL COMMENT '題目ID',
  `question_title` text NOT NULL COMMENT '題目名稱',
  `question_type` varchar(50) NOT NULL COMMENT '題目類型',
  `field_id` int(10) unsigned NOT NULL COMMENT '選項ID',
  `field_title` text NOT NULL COMMENT '選項名稱',
  `count` int(10) unsigned NOT NULL COMMENT '票數',
  `created` datetime NOT NULL COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='票數統計';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_vote_detail`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote_detail` (
  `id` bigint(20) unsigned NOT NULL,
  `ticket_num` varchar(255) NOT NULL COMMENT '票號',
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `question_id` int(10) unsigned NOT NULL COMMENT '題目ID',
  `field_id` int(10) unsigned NOT NULL COMMENT '選項ID',
  `other` text NOT NULL COMMENT '開放欄位',
  `sub_field_id` int(10) unsigned NOT NULL COMMENT '子選項ID',
  `is_place` tinyint(3) unsigned NOT NULL COMMENT '是否為現地投票',
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='票箱及選項';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_vote_lock`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote_lock` (
  `survey_id` int(11) NOT NULL,
  `identify` varchar(200) NOT NULL,
  `verify_type` varchar(100) NOT NULL,
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_vote_paper`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote_paper` (
  `id` bigint(20) unsigned NOT NULL,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `question_id` int(10) unsigned NOT NULL COMMENT '題目ID',
  `field_id` int(10) unsigned NOT NULL COMMENT '選項ID',
  `sub_field_id` int(10) unsigned NOT NULL COMMENT '子選項ID',
  `vote_num` int(10) unsigned NOT NULL COMMENT '票數',
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='紙本票數';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_vote_place`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote_place` (
  `id` bigint(20) unsigned NOT NULL,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `question_id` int(10) unsigned NOT NULL COMMENT '題目ID',
  `field_id` int(10) unsigned NOT NULL COMMENT '選項ID',
  `sub_field_id` int(10) unsigned NOT NULL COMMENT '子選項ID',
  `vote_num` int(10) unsigned NOT NULL COMMENT '票數',
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='現地票數';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_vote_sub_count`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote_sub_count` (
  `id` int(10) unsigned NOT NULL,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `field_id` int(10) unsigned NOT NULL COMMENT '選項ID',
  `sub_field_id` int(10) unsigned NOT NULL COMMENT '子選項ID',
  `sub_field_title` text NOT NULL COMMENT '子選項名稱',
  `count` int(10) unsigned NOT NULL COMMENT '票數',
  `created` datetime NOT NULL COMMENT '建立時間'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='票數統計-子選項';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_tags`
--

CREATE TABLE IF NOT EXISTS `efa_tags` (
  `id` int(10) unsigned NOT NULL,
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0',
  `lft` int(11) NOT NULL DEFAULT '0',
  `rgt` int(11) NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `path` varchar(255) NOT NULL DEFAULT '',
  `title` varchar(255) NOT NULL,
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `note` varchar(255) NOT NULL DEFAULT '',
  `description` mediumtext NOT NULL,
  `published` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `access` int(10) unsigned NOT NULL DEFAULT '0',
  `params` text NOT NULL,
  `metadesc` varchar(1024) NOT NULL COMMENT 'The meta description for the page.',
  `metakey` varchar(1024) NOT NULL COMMENT 'The meta keywords for the page.',
  `metadata` varchar(2048) NOT NULL COMMENT 'JSON encoded metadata properties.',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `images` text NOT NULL,
  `urls` text NOT NULL,
  `hits` int(10) unsigned NOT NULL DEFAULT '0',
  `language` char(7) NOT NULL,
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_template_styles`
--

CREATE TABLE IF NOT EXISTS `efa_template_styles` (
  `id` int(10) unsigned NOT NULL,
  `template` varchar(50) NOT NULL DEFAULT '',
  `client_id` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `home` char(7) NOT NULL DEFAULT '0',
  `title` varchar(255) NOT NULL DEFAULT '',
  `params` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_town`
--

CREATE TABLE IF NOT EXISTS `efa_town` (
  `id` int(10) unsigned NOT NULL,
  `city_id` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `zip` smallint(5) unsigned NOT NULL,
  `state` tinyint(4) NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_ucm_base`
--

CREATE TABLE IF NOT EXISTS `efa_ucm_base` (
  `ucm_id` int(10) unsigned NOT NULL,
  `ucm_item_id` int(10) NOT NULL,
  `ucm_type_id` int(11) NOT NULL,
  `ucm_language_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_ucm_content`
--

CREATE TABLE IF NOT EXISTS `efa_ucm_content` (
  `core_content_id` int(10) unsigned NOT NULL,
  `core_type_alias` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '' COMMENT 'FK to the content types table',
  `core_title` varchar(400) COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_alias` varchar(400) CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL DEFAULT '',
  `core_body` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_state` tinyint(1) NOT NULL DEFAULT '0',
  `core_checked_out_time` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `core_checked_out_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `core_access` int(10) unsigned NOT NULL DEFAULT '0',
  `core_params` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_featured` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `core_metadata` varchar(2048) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'JSON encoded metadata properties.',
  `core_created_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `core_created_by_alias` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `core_created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `core_modified_user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Most recent user that modified',
  `core_modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `core_language` char(7) COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_publish_up` datetime NOT NULL,
  `core_publish_down` datetime NOT NULL,
  `core_content_item_id` int(10) unsigned DEFAULT NULL COMMENT 'ID from the individual type table',
  `asset_id` int(10) unsigned DEFAULT NULL COMMENT 'FK to the #__assets table.',
  `core_images` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_urls` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_hits` int(10) unsigned NOT NULL DEFAULT '0',
  `core_version` int(10) unsigned NOT NULL DEFAULT '1',
  `core_ordering` int(11) NOT NULL DEFAULT '0',
  `core_metakey` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_metadesc` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `core_catid` int(10) unsigned NOT NULL DEFAULT '0',
  `core_xreference` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
  `core_type_id` int(10) unsigned DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Contains core content data in name spaced fields';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_ucm_history`
--

CREATE TABLE IF NOT EXISTS `efa_ucm_history` (
  `version_id` int(10) unsigned NOT NULL,
  `ucm_item_id` int(10) unsigned NOT NULL,
  `ucm_type_id` int(10) unsigned NOT NULL,
  `version_note` varchar(255) NOT NULL DEFAULT '' COMMENT 'Optional version name',
  `save_date` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `editor_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `character_count` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Number of characters in this version.',
  `sha1_hash` varchar(50) NOT NULL DEFAULT '' COMMENT 'SHA1 hash of the version_data column.',
  `version_data` mediumtext NOT NULL COMMENT 'json-encoded string of version data',
  `keep_forever` tinyint(4) NOT NULL DEFAULT '0' COMMENT '0=auto delete; 1=keep'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_unit`
--

CREATE TABLE IF NOT EXISTS `efa_unit` (
  `id` int(11) unsigned NOT NULL COMMENT '編號',
  `title` varchar(100) DEFAULT NULL COMMENT '科室名稱',
  `parent_id` int(11) unsigned NOT NULL DEFAULT '0',
  `level` int(10) unsigned NOT NULL DEFAULT '0',
  `state` tinyint(4) NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='科室單位管理';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_updates`
--

CREATE TABLE IF NOT EXISTS `efa_updates` (
  `update_id` int(11) NOT NULL,
  `update_site_id` int(11) DEFAULT '0',
  `extension_id` int(11) DEFAULT '0',
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `description` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `element` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `type` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `folder` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `client_id` tinyint(3) DEFAULT '0',
  `version` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT '',
  `data` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `detailsurl` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `infourl` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `extra_query` varchar(1000) COLLATE utf8mb4_unicode_ci DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Available Updates';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_update_sites`
--

CREATE TABLE IF NOT EXISTS `efa_update_sites` (
  `update_site_id` int(11) NOT NULL,
  `name` varchar(100) DEFAULT '',
  `type` varchar(20) DEFAULT '',
  `location` text NOT NULL,
  `enabled` int(11) DEFAULT '0',
  `last_check_timestamp` bigint(20) DEFAULT '0',
  `extra_query` varchar(1000) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Update Sites';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_update_sites_extensions`
--

CREATE TABLE IF NOT EXISTS `efa_update_sites_extensions` (
  `update_site_id` int(11) NOT NULL DEFAULT '0',
  `extension_id` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Links extensions to update sites';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_usergroups`
--

CREATE TABLE IF NOT EXISTS `efa_usergroups` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary Key',
  `parent_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Adjacency List Reference Id',
  `lft` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set lft.',
  `rgt` int(11) NOT NULL DEFAULT '0' COMMENT 'Nested set rgt.',
  `title` varchar(100) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_users`
--

CREATE TABLE IF NOT EXISTS `efa_users` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(150) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(100) NOT NULL DEFAULT '',
  `block` tinyint(4) NOT NULL DEFAULT '0',
  `sendEmail` tinyint(4) DEFAULT '0',
  `registerDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `lastvisitDate` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `activation` varchar(100) NOT NULL DEFAULT '',
  `params` text NOT NULL,
  `lastResetTime` datetime NOT NULL DEFAULT '0000-00-00 00:00:00' COMMENT 'Date of last password reset',
  `resetCount` int(11) NOT NULL DEFAULT '0' COMMENT 'Count of password resets since lastResetTime',
  `otpKey` varchar(1000) NOT NULL DEFAULT '' COMMENT 'Two factor authentication encrypted keys',
  `otep` varchar(1000) NOT NULL DEFAULT '' COMMENT 'One time emergency passwords',
  `requireReset` tinyint(4) NOT NULL DEFAULT '0' COMMENT 'Require user to reset password on next login',
  `unit_id` int(10) unsigned NOT NULL COMMENT '科室單位ID'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_user_keys`
--

CREATE TABLE IF NOT EXISTS `efa_user_keys` (
  `id` int(10) unsigned NOT NULL,
  `user_id` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `series` varchar(255) NOT NULL,
  `invalid` tinyint(4) NOT NULL,
  `time` varchar(200) NOT NULL,
  `uastring` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_user_notes`
--

CREATE TABLE IF NOT EXISTS `efa_user_notes` (
  `id` int(10) unsigned NOT NULL,
  `user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `catid` int(10) unsigned NOT NULL DEFAULT '0',
  `subject` varchar(100) NOT NULL DEFAULT '',
  `body` text NOT NULL,
  `state` tinyint(3) NOT NULL DEFAULT '0',
  `checked_out` int(10) unsigned NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_user_id` int(10) unsigned NOT NULL DEFAULT '0',
  `created_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_user_id` int(10) unsigned NOT NULL,
  `modified_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `review_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_user_profiles`
--

CREATE TABLE IF NOT EXISTS `efa_user_profiles` (
  `user_id` int(11) NOT NULL,
  `profile_key` varchar(100) NOT NULL,
  `profile_value` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Simple user profile storage table';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_user_usergroup_map`
--

CREATE TABLE IF NOT EXISTS `efa_user_usergroup_map` (
  `user_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__users.id',
  `group_id` int(10) unsigned NOT NULL DEFAULT '0' COMMENT 'Foreign Key to #__usergroups.id'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_utf8_conversion`
--

CREATE TABLE IF NOT EXISTS `efa_utf8_conversion` (
  `converted` tinyint(4) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_verify_idnum`
--

CREATE TABLE IF NOT EXISTS `efa_verify_idnum` (
  `id` int(10) unsigned NOT NULL,
  `num_id` varchar(50) NOT NULL COMMENT '身分證字號',
  `birth_date` varchar(20) NOT NULL COMMENT '出生日期',
  `survey_id` int(10) NOT NULL COMMENT '議題ID',
  `created` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_viewlevels`
--

CREATE TABLE IF NOT EXISTS `efa_viewlevels` (
  `id` int(10) unsigned NOT NULL COMMENT 'Primary Key',
  `title` varchar(100) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `rules` varchar(5120) NOT NULL COMMENT 'JSON encoded access control.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_village`
--

CREATE TABLE IF NOT EXISTS `efa_village` (
  `id` int(10) unsigned NOT NULL,
  `town_id` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_weblinks`
--

CREATE TABLE IF NOT EXISTS `efa_weblinks` (
  `id` int(10) unsigned NOT NULL,
  `catid` int(11) NOT NULL DEFAULT '0',
  `title` varchar(250) NOT NULL DEFAULT '',
  `alias` varchar(255) CHARACTER SET utf8 COLLATE utf8_bin NOT NULL DEFAULT '',
  `url` varchar(250) NOT NULL DEFAULT '',
  `description` text NOT NULL,
  `hits` int(11) NOT NULL DEFAULT '0',
  `state` tinyint(1) NOT NULL DEFAULT '0',
  `checked_out` int(11) NOT NULL DEFAULT '0',
  `checked_out_time` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `ordering` int(11) NOT NULL DEFAULT '0',
  `access` int(11) NOT NULL DEFAULT '1',
  `params` text NOT NULL,
  `language` char(7) NOT NULL DEFAULT '',
  `created` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created_by` int(10) unsigned NOT NULL DEFAULT '0',
  `created_by_alias` varchar(255) NOT NULL DEFAULT '',
  `modified` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `modified_by` int(10) unsigned NOT NULL DEFAULT '0',
  `metakey` text NOT NULL,
  `metadesc` text NOT NULL,
  `metadata` text NOT NULL,
  `featured` tinyint(3) unsigned NOT NULL DEFAULT '0' COMMENT 'Set if link is featured.',
  `xreference` varchar(50) NOT NULL COMMENT 'A reference to enable linkages to external data sets.',
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `version` int(10) unsigned NOT NULL DEFAULT '1',
  `images` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `efa_wf_profiles`
--

CREATE TABLE IF NOT EXISTS `efa_wf_profiles` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `users` text NOT NULL,
  `types` text NOT NULL,
  `components` text NOT NULL,
  `area` tinyint(3) NOT NULL,
  `device` varchar(255) NOT NULL,
  `rows` text NOT NULL,
  `plugins` text NOT NULL,
  `published` tinyint(3) NOT NULL,
  `ordering` int(11) NOT NULL,
  `checked_out` tinyint(3) NOT NULL,
  `checked_out_time` datetime NOT NULL,
  `params` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- 已匯出資料表的索引
--

--
-- 資料表索引 `efa_assets`
--
ALTER TABLE `efa_assets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_asset_name` (`name`),
  ADD KEY `idx_lft_rgt` (`lft`,`rgt`),
  ADD KEY `idx_parent_id` (`parent_id`);

--
-- 資料表索引 `efa_assign_summary`
--
ALTER TABLE `efa_assign_summary`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_associations`
--
ALTER TABLE `efa_associations`
  ADD PRIMARY KEY (`context`,`id`),
  ADD KEY `idx_key` (`key`);

--
-- 資料表索引 `efa_banners`
--
ALTER TABLE `efa_banners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_state` (`state`),
  ADD KEY `idx_own_prefix` (`own_prefix`),
  ADD KEY `idx_metakey_prefix` (`metakey_prefix`),
  ADD KEY `idx_banner_catid` (`catid`),
  ADD KEY `idx_language` (`language`);

--
-- 資料表索引 `efa_banner_clients`
--
ALTER TABLE `efa_banner_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_own_prefix` (`own_prefix`),
  ADD KEY `idx_metakey_prefix` (`metakey_prefix`);

--
-- 資料表索引 `efa_banner_tracks`
--
ALTER TABLE `efa_banner_tracks`
  ADD PRIMARY KEY (`track_date`,`track_type`,`banner_id`),
  ADD KEY `idx_track_date` (`track_date`),
  ADD KEY `idx_track_type` (`track_type`),
  ADD KEY `idx_banner_id` (`banner_id`);

--
-- 資料表索引 `efa_categories`
--
ALTER TABLE `efa_categories`
  ADD PRIMARY KEY (`id`),
  ADD KEY `cat_idx` (`extension`,`published`,`access`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_checkout` (`checked_out`),
  ADD KEY `idx_path` (`path`),
  ADD KEY `idx_left_right` (`lft`,`rgt`),
  ADD KEY `idx_alias` (`alias`),
  ADD KEY `idx_language` (`language`);

--
-- 資料表索引 `efa_city`
--
ALTER TABLE `efa_city`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_contact_details`
--
ALTER TABLE `efa_contact_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_checkout` (`checked_out`),
  ADD KEY `idx_state` (`published`),
  ADD KEY `idx_catid` (`catid`),
  ADD KEY `idx_createdby` (`created_by`),
  ADD KEY `idx_featured_catid` (`featured`,`catid`),
  ADD KEY `idx_language` (`language`),
  ADD KEY `idx_xreference` (`xreference`);

--
-- 資料表索引 `efa_content`
--
ALTER TABLE `efa_content`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_checkout` (`checked_out`),
  ADD KEY `idx_state` (`state`),
  ADD KEY `idx_catid` (`catid`),
  ADD KEY `idx_createdby` (`created_by`),
  ADD KEY `idx_featured_catid` (`featured`,`catid`),
  ADD KEY `idx_language` (`language`),
  ADD KEY `idx_xreference` (`xreference`);

--
-- 資料表索引 `efa_contentitem_tag_map`
--
ALTER TABLE `efa_contentitem_tag_map`
  ADD UNIQUE KEY `uc_ItemnameTagid` (`type_id`,`content_item_id`,`tag_id`),
  ADD KEY `idx_tag_type` (`tag_id`,`type_id`),
  ADD KEY `idx_date_id` (`tag_date`,`tag_id`),
  ADD KEY `idx_tag` (`tag_id`),
  ADD KEY `idx_type` (`type_id`),
  ADD KEY `idx_core_content_id` (`core_content_id`);

--
-- 資料表索引 `efa_content_frontpage`
--
ALTER TABLE `efa_content_frontpage`
  ADD PRIMARY KEY (`content_id`);

--
-- 資料表索引 `efa_content_rating`
--
ALTER TABLE `efa_content_rating`
  ADD PRIMARY KEY (`content_id`);

--
-- 資料表索引 `efa_content_types`
--
ALTER TABLE `efa_content_types`
  ADD PRIMARY KEY (`type_id`),
  ADD KEY `idx_alias` (`type_alias`);

--
-- 資料表索引 `efa_extensions`
--
ALTER TABLE `efa_extensions`
  ADD PRIMARY KEY (`extension_id`),
  ADD KEY `element_clientid` (`element`,`client_id`),
  ADD KEY `element_folder_clientid` (`element`,`folder`,`client_id`),
  ADD KEY `extension` (`type`,`element`,`folder`,`client_id`);

--
-- 資料表索引 `efa_languages`
--
ALTER TABLE `efa_languages`
  ADD PRIMARY KEY (`lang_id`),
  ADD UNIQUE KEY `idx_sef` (`sef`),
  ADD UNIQUE KEY `idx_image` (`image`),
  ADD UNIQUE KEY `idx_langcode` (`lang_code`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_ordering` (`ordering`);

--
-- 資料表索引 `efa_mail_record`
--
ALTER TABLE `efa_mail_record`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_menu`
--
ALTER TABLE `efa_menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_client_id_parent_id_alias_language` (`client_id`,`parent_id`,`alias`,`language`),
  ADD KEY `idx_componentid` (`component_id`,`menutype`,`published`,`access`),
  ADD KEY `idx_menutype` (`menutype`),
  ADD KEY `idx_left_right` (`lft`,`rgt`),
  ADD KEY `idx_alias` (`alias`),
  ADD KEY `idx_path` (`path`(255)),
  ADD KEY `idx_language` (`language`);

--
-- 資料表索引 `efa_menu_types`
--
ALTER TABLE `efa_menu_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_menutype` (`menutype`);

--
-- 資料表索引 `efa_messages`
--
ALTER TABLE `efa_messages`
  ADD PRIMARY KEY (`message_id`),
  ADD KEY `useridto_state` (`user_id_to`,`state`);

--
-- 資料表索引 `efa_messages_cfg`
--
ALTER TABLE `efa_messages_cfg`
  ADD UNIQUE KEY `idx_user_var_name` (`user_id`,`cfg_name`);

--
-- 資料表索引 `efa_modules`
--
ALTER TABLE `efa_modules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `published` (`published`,`access`),
  ADD KEY `newsfeeds` (`module`,`published`),
  ADD KEY `idx_language` (`language`);

--
-- 資料表索引 `efa_modules_menu`
--
ALTER TABLE `efa_modules_menu`
  ADD PRIMARY KEY (`moduleid`,`menuid`);

--
-- 資料表索引 `efa_newsfeeds`
--
ALTER TABLE `efa_newsfeeds`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_checkout` (`checked_out`),
  ADD KEY `idx_state` (`published`),
  ADD KEY `idx_catid` (`catid`),
  ADD KEY `idx_createdby` (`created_by`),
  ADD KEY `idx_language` (`language`),
  ADD KEY `idx_xreference` (`xreference`);

--
-- 資料表索引 `efa_overrider`
--
ALTER TABLE `efa_overrider`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_phone_record`
--
ALTER TABLE `efa_phone_record`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_postinstall_messages`
--
ALTER TABLE `efa_postinstall_messages`
  ADD PRIMARY KEY (`postinstall_message_id`);

--
-- 資料表索引 `efa_redirect_links`
--
ALTER TABLE `efa_redirect_links`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_link_old` (`old_url`),
  ADD KEY `idx_link_modifed` (`modified_date`);

--
-- 資料表索引 `efa_rokadminaudit`
--
ALTER TABLE `efa_rokadminaudit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ip` (`ip`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `timestamp` (`timestamp`);

--
-- 資料表索引 `efa_rokuserstats`
--
ALTER TABLE `efa_rokuserstats`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `ip` (`ip`),
  ADD KEY `session_id` (`session_id`),
  ADD KEY `timestamp` (`timestamp`);

--
-- 資料表索引 `efa_schemas`
--
ALTER TABLE `efa_schemas`
  ADD PRIMARY KEY (`extension_id`,`version_id`);

--
-- 資料表索引 `efa_session`
--
ALTER TABLE `efa_session`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `userid` (`userid`),
  ADD KEY `time` (`time`);

--
-- 資料表索引 `efa_sitemap`
--
ALTER TABLE `efa_sitemap`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_survey_force_email_notice`
--
ALTER TABLE `efa_survey_force_email_notice`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_survey_force_fields`
--
ALTER TABLE `efa_survey_force_fields`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quest_id` (`quest_id`);

--
-- 資料表索引 `efa_survey_force_iscales`
--
ALTER TABLE `efa_survey_force_iscales`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_survey_force_phone_notice`
--
ALTER TABLE `efa_survey_force_phone_notice`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_survey_force_qsections`
--
ALTER TABLE `efa_survey_force_qsections`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_survey_force_qtypes`
--
ALTER TABLE `efa_survey_force_qtypes`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_survey_force_quests`
--
ALTER TABLE `efa_survey_force_quests`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sf_survey` (`sf_survey`);

--
-- 資料表索引 `efa_survey_force_quests_cat`
--
ALTER TABLE `efa_survey_force_quests_cat`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_survey_force_quest_show`
--
ALTER TABLE `efa_survey_force_quest_show`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quest_id` (`quest_id`);

--
-- 資料表索引 `efa_survey_force_rules`
--
ALTER TABLE `efa_survey_force_rules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quest_id` (`quest_id`);

--
-- 資料表索引 `efa_survey_force_scales`
--
ALTER TABLE `efa_survey_force_scales`
  ADD PRIMARY KEY (`id`),
  ADD KEY `quest_id` (`quest_id`);

--
-- 資料表索引 `efa_survey_force_sub_fields`
--
ALTER TABLE `efa_survey_force_sub_fields`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_survey_force_survs`
--
ALTER TABLE `efa_survey_force_survs`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_survey_force_verify_result`
--
ALTER TABLE `efa_survey_force_verify_result`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- 資料表索引 `efa_survey_force_vote`
--
ALTER TABLE `efa_survey_force_vote`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- 資料表索引 `efa_survey_force_vote_count`
--
ALTER TABLE `efa_survey_force_vote_count`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- 資料表索引 `efa_survey_force_vote_detail`
--
ALTER TABLE `efa_survey_force_vote_detail`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_id` (`survey_id`),
  ADD KEY `question_id` (`question_id`),
  ADD KEY `field_id` (`field_id`);

--
-- 資料表索引 `efa_survey_force_vote_lock`
--
ALTER TABLE `efa_survey_force_vote_lock`
  ADD UNIQUE KEY `survey_id` (`survey_id`,`identify`,`verify_type`);

--
-- 資料表索引 `efa_survey_force_vote_paper`
--
ALTER TABLE `efa_survey_force_vote_paper`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_survey_force_vote_place`
--
ALTER TABLE `efa_survey_force_vote_place`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_survey_force_vote_sub_count`
--
ALTER TABLE `efa_survey_force_vote_sub_count`
  ADD PRIMARY KEY (`id`),
  ADD KEY `survey_id` (`survey_id`);

--
-- 資料表索引 `efa_tags`
--
ALTER TABLE `efa_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `tag_idx` (`published`,`access`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_checkout` (`checked_out`),
  ADD KEY `idx_path` (`path`),
  ADD KEY `idx_left_right` (`lft`,`rgt`),
  ADD KEY `idx_alias` (`alias`),
  ADD KEY `idx_language` (`language`);

--
-- 資料表索引 `efa_template_styles`
--
ALTER TABLE `efa_template_styles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_template` (`template`),
  ADD KEY `idx_home` (`home`);

--
-- 資料表索引 `efa_town`
--
ALTER TABLE `efa_town`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_ucm_base`
--
ALTER TABLE `efa_ucm_base`
  ADD PRIMARY KEY (`ucm_id`),
  ADD KEY `idx_ucm_item_id` (`ucm_item_id`),
  ADD KEY `idx_ucm_type_id` (`ucm_type_id`),
  ADD KEY `idx_ucm_language_id` (`ucm_language_id`);

--
-- 資料表索引 `efa_ucm_content`
--
ALTER TABLE `efa_ucm_content`
  ADD PRIMARY KEY (`core_content_id`),
  ADD KEY `tag_idx` (`core_state`,`core_access`),
  ADD KEY `idx_access` (`core_access`),
  ADD KEY `idx_language` (`core_language`),
  ADD KEY `idx_modified_time` (`core_modified_time`),
  ADD KEY `idx_created_time` (`core_created_time`),
  ADD KEY `idx_core_modified_user_id` (`core_modified_user_id`),
  ADD KEY `idx_core_checked_out_user_id` (`core_checked_out_user_id`),
  ADD KEY `idx_core_created_user_id` (`core_created_user_id`),
  ADD KEY `idx_core_type_id` (`core_type_id`),
  ADD KEY `idx_alias` (`core_alias`(100)),
  ADD KEY `idx_title` (`core_title`(100)),
  ADD KEY `idx_content_type` (`core_type_alias`(100));

--
-- 資料表索引 `efa_ucm_history`
--
ALTER TABLE `efa_ucm_history`
  ADD PRIMARY KEY (`version_id`),
  ADD KEY `idx_ucm_item_id` (`ucm_type_id`,`ucm_item_id`),
  ADD KEY `idx_save_date` (`save_date`);

--
-- 資料表索引 `efa_unit`
--
ALTER TABLE `efa_unit`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_updates`
--
ALTER TABLE `efa_updates`
  ADD PRIMARY KEY (`update_id`);

--
-- 資料表索引 `efa_update_sites`
--
ALTER TABLE `efa_update_sites`
  ADD PRIMARY KEY (`update_site_id`);

--
-- 資料表索引 `efa_update_sites_extensions`
--
ALTER TABLE `efa_update_sites_extensions`
  ADD PRIMARY KEY (`update_site_id`,`extension_id`);

--
-- 資料表索引 `efa_usergroups`
--
ALTER TABLE `efa_usergroups`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_usergroup_parent_title_lookup` (`parent_id`,`title`),
  ADD KEY `idx_usergroup_title_lookup` (`title`),
  ADD KEY `idx_usergroup_adjacency_lookup` (`parent_id`),
  ADD KEY `idx_usergroup_nested_set_lookup` (`lft`,`rgt`) USING BTREE;

--
-- 資料表索引 `efa_users`
--
ALTER TABLE `efa_users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_name` (`name`),
  ADD KEY `idx_block` (`block`),
  ADD KEY `username` (`username`),
  ADD KEY `email` (`email`);

--
-- 資料表索引 `efa_user_keys`
--
ALTER TABLE `efa_user_keys`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `series` (`series`),
  ADD UNIQUE KEY `series_2` (`series`),
  ADD UNIQUE KEY `series_3` (`series`),
  ADD KEY `user_id` (`user_id`);

--
-- 資料表索引 `efa_user_notes`
--
ALTER TABLE `efa_user_notes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_category_id` (`catid`);

--
-- 資料表索引 `efa_user_profiles`
--
ALTER TABLE `efa_user_profiles`
  ADD UNIQUE KEY `idx_user_id_profile_key` (`user_id`,`profile_key`);

--
-- 資料表索引 `efa_user_usergroup_map`
--
ALTER TABLE `efa_user_usergroup_map`
  ADD PRIMARY KEY (`user_id`,`group_id`);

--
-- 資料表索引 `efa_verify_idnum`
--
ALTER TABLE `efa_verify_idnum`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_viewlevels`
--
ALTER TABLE `efa_viewlevels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_assetgroup_title_lookup` (`title`);

--
-- 資料表索引 `efa_village`
--
ALTER TABLE `efa_village`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `efa_weblinks`
--
ALTER TABLE `efa_weblinks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_access` (`access`),
  ADD KEY `idx_checkout` (`checked_out`),
  ADD KEY `idx_state` (`state`),
  ADD KEY `idx_catid` (`catid`),
  ADD KEY `idx_createdby` (`created_by`),
  ADD KEY `idx_featured_catid` (`featured`,`catid`),
  ADD KEY `idx_language` (`language`),
  ADD KEY `idx_xreference` (`xreference`);

--
-- 資料表索引 `efa_wf_profiles`
--
ALTER TABLE `efa_wf_profiles`
  ADD PRIMARY KEY (`id`);

--
-- 在匯出的資料表使用 AUTO_INCREMENT
--

--
-- 使用資料表 AUTO_INCREMENT `efa_assets`
--
ALTER TABLE `efa_assets`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';
--
-- 使用資料表 AUTO_INCREMENT `efa_assign_summary`
--
ALTER TABLE `efa_assign_summary`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_banners`
--
ALTER TABLE `efa_banners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_banner_clients`
--
ALTER TABLE `efa_banner_clients`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_categories`
--
ALTER TABLE `efa_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_city`
--
ALTER TABLE `efa_city`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '編號';
--
-- 使用資料表 AUTO_INCREMENT `efa_contact_details`
--
ALTER TABLE `efa_contact_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_content`
--
ALTER TABLE `efa_content`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_content_types`
--
ALTER TABLE `efa_content_types`
  MODIFY `type_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_extensions`
--
ALTER TABLE `efa_extensions`
  MODIFY `extension_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_languages`
--
ALTER TABLE `efa_languages`
  MODIFY `lang_id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_mail_record`
--
ALTER TABLE `efa_mail_record`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_menu`
--
ALTER TABLE `efa_menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_menu_types`
--
ALTER TABLE `efa_menu_types`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_messages`
--
ALTER TABLE `efa_messages`
  MODIFY `message_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_modules`
--
ALTER TABLE `efa_modules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_newsfeeds`
--
ALTER TABLE `efa_newsfeeds`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_overrider`
--
ALTER TABLE `efa_overrider`
  MODIFY `id` int(10) NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';
--
-- 使用資料表 AUTO_INCREMENT `efa_phone_record`
--
ALTER TABLE `efa_phone_record`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_postinstall_messages`
--
ALTER TABLE `efa_postinstall_messages`
  MODIFY `postinstall_message_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_redirect_links`
--
ALTER TABLE `efa_redirect_links`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_rokadminaudit`
--
ALTER TABLE `efa_rokadminaudit`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_rokuserstats`
--
ALTER TABLE `efa_rokuserstats`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_sitemap`
--
ALTER TABLE `efa_sitemap`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_email_notice`
--
ALTER TABLE `efa_survey_force_email_notice`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_fields`
--
ALTER TABLE `efa_survey_force_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_iscales`
--
ALTER TABLE `efa_survey_force_iscales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_phone_notice`
--
ALTER TABLE `efa_survey_force_phone_notice`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_qsections`
--
ALTER TABLE `efa_survey_force_qsections`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_qtypes`
--
ALTER TABLE `efa_survey_force_qtypes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_quests`
--
ALTER TABLE `efa_survey_force_quests`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_quests_cat`
--
ALTER TABLE `efa_survey_force_quests_cat`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_quest_show`
--
ALTER TABLE `efa_survey_force_quest_show`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_rules`
--
ALTER TABLE `efa_survey_force_rules`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_scales`
--
ALTER TABLE `efa_survey_force_scales`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_sub_fields`
--
ALTER TABLE `efa_survey_force_sub_fields`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_survs`
--
ALTER TABLE `efa_survey_force_survs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_verify_result`
--
ALTER TABLE `efa_survey_force_verify_result`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_vote`
--
ALTER TABLE `efa_survey_force_vote`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_vote_count`
--
ALTER TABLE `efa_survey_force_vote_count`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_vote_detail`
--
ALTER TABLE `efa_survey_force_vote_detail`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_vote_paper`
--
ALTER TABLE `efa_survey_force_vote_paper`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_vote_place`
--
ALTER TABLE `efa_survey_force_vote_place`
  MODIFY `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_survey_force_vote_sub_count`
--
ALTER TABLE `efa_survey_force_vote_sub_count`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_tags`
--
ALTER TABLE `efa_tags`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_template_styles`
--
ALTER TABLE `efa_template_styles`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_town`
--
ALTER TABLE `efa_town`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_ucm_content`
--
ALTER TABLE `efa_ucm_content`
  MODIFY `core_content_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_ucm_history`
--
ALTER TABLE `efa_ucm_history`
  MODIFY `version_id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_unit`
--
ALTER TABLE `efa_unit`
  MODIFY `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '編號';
--
-- 使用資料表 AUTO_INCREMENT `efa_updates`
--
ALTER TABLE `efa_updates`
  MODIFY `update_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_update_sites`
--
ALTER TABLE `efa_update_sites`
  MODIFY `update_site_id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_usergroups`
--
ALTER TABLE `efa_usergroups`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';
--
-- 使用資料表 AUTO_INCREMENT `efa_users`
--
ALTER TABLE `efa_users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_user_keys`
--
ALTER TABLE `efa_user_keys`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_user_notes`
--
ALTER TABLE `efa_user_notes`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_verify_idnum`
--
ALTER TABLE `efa_verify_idnum`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_viewlevels`
--
ALTER TABLE `efa_viewlevels`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'Primary Key';
--
-- 使用資料表 AUTO_INCREMENT `efa_village`
--
ALTER TABLE `efa_village`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_weblinks`
--
ALTER TABLE `efa_weblinks`
  MODIFY `id` int(10) unsigned NOT NULL AUTO_INCREMENT;
--
-- 使用資料表 AUTO_INCREMENT `efa_wf_profiles`
--
ALTER TABLE `efa_wf_profiles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
