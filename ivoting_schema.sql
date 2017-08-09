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
