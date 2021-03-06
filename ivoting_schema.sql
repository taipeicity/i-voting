



--
-- 資料表格式： `efa_survey_force_email_notice`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_email_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `email` varchar(200) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '類別(1:投票前,2:催票,3:結束)',
  `is_send` tinyint(3) unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=51 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_fields`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
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
  `catid` int(10) unsigned NOT NULL COMMENT '分類ID',
  PRIMARY KEY (`id`),
  KEY `quest_id` (`quest_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1097 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_iscales`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_iscales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `iscale_name` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_phone_notice`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_phone_notice` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `phone` varchar(200) NOT NULL,
  `type` tinyint(4) NOT NULL COMMENT '類別',
  `is_send` tinyint(3) unsigned NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_qsections`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_qsections` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sf_name` varchar(250) NOT NULL DEFAULT '',
  `addname` tinyint(4) NOT NULL DEFAULT '0',
  `ordering` tinyint(4) NOT NULL DEFAULT '0',
  `sf_survey_id` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_qtypes`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_qtypes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sf_qtype` varchar(50) NOT NULL DEFAULT '',
  `sf_plg_name` varchar(128) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=10 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_quests`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_quests` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `multi_max` tinyint(3) unsigned NOT NULL COMMENT '複選_最多投',
  PRIMARY KEY (`id`),
  KEY `sf_survey` (`sf_survey`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=270 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_quests_cat`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_quests_cat` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `question_id` int(10) NOT NULL COMMENT '題目ID',
  `title` varchar(50) NOT NULL COMMENT '標題',
  `ordering` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='選項的分類' AUTO_INCREMENT=31 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_quest_show`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_quest_show` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quest_id` int(11) NOT NULL DEFAULT '0',
  `survey_id` int(11) NOT NULL DEFAULT '0',
  `quest_id_a` int(11) NOT NULL DEFAULT '0',
  `answer` int(11) NOT NULL DEFAULT '0',
  `ans_field` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `quest_id` (`quest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_rules`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_rules` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quest_id` int(11) NOT NULL DEFAULT '0',
  `answer_id` int(11) NOT NULL DEFAULT '0',
  `next_quest_id` int(11) NOT NULL DEFAULT '0',
  `alt_field_id` int(11) NOT NULL DEFAULT '0',
  `priority` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `quest_id` (`quest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_scales`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_scales` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quest_id` int(11) NOT NULL DEFAULT '0',
  `stext` varchar(250) NOT NULL DEFAULT '',
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `quest_id` (`quest_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_sub_fields`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_sub_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quest_id` int(10) unsigned NOT NULL COMMENT '題目ID',
  `title` text NOT NULL,
  `ordering` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=272 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_survs`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_survs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` varchar(250) NOT NULL DEFAULT '' COMMENT '標題',
  `desc` text NOT NULL COMMENT '完整描述',
  `short_desc` text COMMENT '簡短描述',
  `vote_way` text NOT NULL COMMENT '投票方式',
  `voters_eligibility` text NOT NULL COMMENT '投票人資格',
  `voters_authentication` text NOT NULL COMMENT '投票人驗證方式',
  `during_vote` text NOT NULL COMMENT '投票期間',
  `announcement_date` datetime NOT NULL COMMENT '公布方式及日期',
  `precautions` text NOT NULL COMMENT '注意事項',
  `results_proportion` text CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '預定結果參採比重',
  `part` text NOT NULL COMMENT '部分參採',
  `other` text NOT NULL COMMENT '其他',   
  `discuss_source` text NOT NULL COMMENT '討論管道',
  `image` varchar(50) NOT NULL DEFAULT '' COMMENT '圖片',
  `remind_text` text NOT NULL COMMENT '投票前提醒',
  `drumup_text` text NOT NULL COMMENT '催票提醒',
  `end_text` text NOT NULL COMMENT '投票結束提醒',
  `phone_remind_text` text NOT NULL COMMENT '手機訊息-投票前提醒',
  `phone_drumup_text` text NOT NULL COMMENT '手機訊息-催票提醒',
  `phone_end_text` text NOT NULL COMMENT '手機訊息-投票結束通知提醒',
  `sms_user` varchar(200) NOT NULL COMMENT '加密後的帳號',
  `sms_passwd` varchar(200) NOT NULL COMMENT '加密後的密碼',
  `is_public` tinyint(4) NOT NULL DEFAULT '0' COMMENT '是否公開',
  `un_public_tmpl` tinyint(4) unsigned NOT NULL COMMENT '非公開投票版型',
  `is_define` tinyint(4) NOT NULL DEFAULT '1' COMMENT '是否成案',
  `vote_pattern` tinyint(4) NOT NULL DEFAULT '3' COMMENT '投票模式',
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
  `is_place` tinyint(4) NOT NULL COMMENT '是否有現地投票',
  `place_image` varchar(50) NOT NULL COMMENT '掃描標的物圖片',
  `is_analyze` tinyint(4) NOT NULL DEFAULT '0' COMMENT '分析功能',
  `stage` TINYINT( 3 ) NOT NULL DEFAULT  '1' COMMENT  '議題階段(1：提案檢核階段 2：提案初審階段 3：提案討論階段 4：形成選項階段 5：宣傳準備上下階段 6：投票、結果公布及執行)',
  `is_store` VARCHAR( 255 ) NOT NULL COMMENT  '檢查儲存階段',
  `proposer` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '提案人',
  `plan_quest` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '初擬投票議題',
  `plan_options` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '初擬選項方案',
  `proposal` TINYINT( 2 ) NOT NULL COMMENT  '初擬提案計畫書資料',
  `proposal_download` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '行政機關(檔案上傳)',
  `proposal_url` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '非行政機關(超連結)',
  `second_the_motion` INT( 21 ) NOT NULL COMMENT '已附議票數', ADD `deadline` DATETIME NOT NULL COMMENT '截止時間',
  `review_result` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT '初審結果說明',
  `review_download` VARCHAR( 255 ) NOT NULL COMMENT '初審會議下載(一)',
  `review_download_ii` VARCHAR( 255 ) NOT NULL COMMENT '初審會議下載(二)',
  `discuss_plan_options` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '議題與選項方案規劃',
  `discuss_qualifications` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '投票人資格規劃',
  `discuss_verify` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '預計投票人驗證方式規劃',
  `discuss_vote_time` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '預計投票時間',
  `discuss_vote_start` DATETIME NOT NULL COMMENT  '預計投票時間(開始)',
  `discuss_vote_end` DATETIME NOT NULL COMMENT  '預計投票時間(結束)',
  `discuss_threshold` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '預計投票通過門檻',
  `discuss_download` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '提案計畫書下載',
  `options_cohesion` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '議題與選項方案凝聚',
  `options_agree` MEDIUMINT( 8 ) NOT NULL COMMENT  '討論意見比例(贊成)',
  `options_oppose` MEDIUMINT( 8 ) NOT NULL COMMENT  '討論意見比例(反對)',
  `options_caption` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '討論意見綜整說明'
  `launched_condition` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '投票通過門檻',
  `launched_date` TINYINT( 2 ) NOT NULL COMMENT  '投票公布日期參數',
  `launched_download` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '完整提案計畫書下載',
  `result_instructions` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '投票結果說明',
  `how_to_use` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL COMMENT  '運用方式說明',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_survs_release`
--

CREATE TABLE `efa_survey_force_survs_release` LIKE `efa_survey_force_survs`;
ALTER TABLE  `efa_survey_force_survs_release` CHANGE  `id`  `id` INT( 11 ) NOT NULL;
INSERT `efa_survey_force_survs_release` SELECT * FROM `efa_survey_force_survs`;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_verify_result`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_verify_result` (
  `id` int(10) unsigned NOT NULL,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `verify_method` varchar(50) NOT NULL COMMENT '驗證方式',
  `state` tinyint(4) NOT NULL COMMENT '驗證狀態',
  `client_ip` varchar(50) NOT NULL,
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='驗證結果記錄';

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_vote`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_num` varchar(255) NOT NULL COMMENT '票號',
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `is_lottery` tinyint(4) unsigned NOT NULL DEFAULT '0' COMMENT '是否參加過抽獎?',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='票箱';

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_vote_count`
--


CREATE TABLE IF NOT EXISTS `efa_survey_force_vote_count` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `question_id` int(10) unsigned NOT NULL COMMENT '題目ID',
  `question_title` text NOT NULL COMMENT '題目名稱',
  `question_type` varchar(50) NOT NULL COMMENT '題目類型',
  `field_id` int(10) unsigned NOT NULL COMMENT '選項ID',
  `field_title` text NOT NULL COMMENT '選項名稱',
  `count` int(10) unsigned NOT NULL COMMENT '票數',
  `created` datetime NOT NULL COMMENT '建立時間',
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COMMENT='票數統計';

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_vote_detail`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote_detail` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `ticket_num` varchar(255) NOT NULL COMMENT '票號',
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `question_id` int(10) unsigned NOT NULL COMMENT '題目ID',
  `field_id` int(10) unsigned NOT NULL COMMENT '選項ID',
  `other` text NOT NULL COMMENT '開放欄位',
  `sub_field_id` int(10) unsigned NOT NULL COMMENT '子選項ID',
  `is_place` tinyint(3) unsigned NOT NULL COMMENT '是否為現地投票',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='票箱及選項' AUTO_INCREMENT=1 ;



-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_vote_lock`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote_lock` (
  `survey_id` int(11) NOT NULL,
  `identify` varchar(200) NOT NULL,
  `verify_type` varchar(100) NOT NULL,
  `created` datetime NOT NULL,
  UNIQUE KEY `survey_id` (`survey_id`,`identify`,`verify_type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_vote_paper`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote_paper` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `question_id` int(10) unsigned NOT NULL COMMENT '題目ID',
  `field_id` int(10) unsigned NOT NULL COMMENT '選項ID',
  `sub_field_id` int(10) unsigned NOT NULL COMMENT '子選項ID',
  `vote_num` int(10) unsigned NOT NULL COMMENT '票數',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='紙本票數' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_vote_place`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote_place` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `question_id` int(10) unsigned NOT NULL COMMENT '題目ID',
  `field_id` int(10) unsigned NOT NULL COMMENT '選項ID',
  `sub_field_id` int(10) unsigned NOT NULL COMMENT '子選項ID',
  `vote_num` int(10) unsigned NOT NULL COMMENT '票數',
  `created` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='現地票數' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_vote_sub_count`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_vote_sub_count` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(10) unsigned NOT NULL COMMENT '議題ID',
  `field_id` int(10) unsigned NOT NULL COMMENT '選項ID',
  `sub_field_id` int(10) unsigned NOT NULL COMMENT '子選項ID',
  `sub_field_title` text NOT NULL COMMENT '子選項名稱',
  `count` int(10) unsigned NOT NULL COMMENT '票數',
  `created` datetime NOT NULL COMMENT '建立時間',
  PRIMARY KEY (`id`),
  KEY `survey_id` (`survey_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='票數統計-子選項' AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- 資料表格式： `efa_sitemap`
--
CREATE TABLE IF NOT EXISTS `efa_sitemap` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `catid` int(11) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  `exclude` varchar(255) NOT NULL,
  `editor` text NOT NULL,
  `menu` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------


--
-- 資料表格式： `efa_city`
--
CREATE TABLE IF NOT EXISTS `efa_city` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT '編號',
  `title` varchar(100) NOT NULL COMMENT '名稱',
  `state` tinyint(4) NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=24 ;

-- --------------------------------------------------------


--
-- 列出以下資料庫的數據： `efa_city`
--

INSERT INTO `efa_city` (`id`, `title`, `state`, `ordering`, `publish_up`, `publish_down`, `created`, `created_by`, `modified`, `modified_by`) VALUES
(1, '臺北市', 1, 1, '2015-01-18 13:04:48', '0000-00-00 00:00:00', '2015-01-18 13:04:48', 185, '0000-00-00 00:00:00', 0),
(2, '基隆市', 1, 2, '2015-01-18 13:04:59', '0000-00-00 00:00:00', '2015-01-18 13:04:59', 185, '0000-00-00 00:00:00', 0),
(3, '新北市', 1, 3, '2015-01-18 13:05:04', '0000-00-00 00:00:00', '2015-01-18 13:05:04', 185, '0000-00-00 00:00:00', 0),
(4, '宜蘭縣', 1, 4, '2015-01-18 13:05:18', '0000-00-00 00:00:00', '2015-01-18 13:05:18', 185, '0000-00-00 00:00:00', 0),
(5, '新竹市', 1, 5, '2015-01-18 13:05:27', '0000-00-00 00:00:00', '2015-01-18 13:05:27', 185, '0000-00-00 00:00:00', 0),
(6, '新竹縣', 1, 6, '2015-01-18 13:05:33', '0000-00-00 00:00:00', '2015-01-18 13:05:33', 185, '0000-00-00 00:00:00', 0),
(7, '桃園市', 1, 7, '2015-01-18 13:05:38', '0000-00-00 00:00:00', '2015-01-18 13:05:38', 185, '0000-00-00 00:00:00', 0),
(8, '苗栗縣', 1, 8, '2015-01-18 13:05:45', '0000-00-00 00:00:00', '2015-01-18 13:05:45', 185, '0000-00-00 00:00:00', 0),
(9, '臺中市', 1, 9, '2015-01-18 13:05:50', '0000-00-00 00:00:00', '2015-01-18 13:05:50', 185, '0000-00-00 00:00:00', 0),
(10, '彰化縣', 1, 10, '2015-01-18 13:06:00', '0000-00-00 00:00:00', '2015-01-18 13:06:00', 185, '0000-00-00 00:00:00', 0),
(11, '南投縣', 1, 11, '2015-01-18 13:06:07', '0000-00-00 00:00:00', '2015-01-18 13:06:07', 185, '0000-00-00 00:00:00', 0),
(12, '嘉義市', 1, 12, '2015-01-18 13:06:41', '0000-00-00 00:00:00', '2015-01-18 13:06:41', 185, '0000-00-00 00:00:00', 0),
(13, '嘉義縣', 1, 13, '2015-01-18 13:06:46', '0000-00-00 00:00:00', '2015-01-18 13:06:46', 185, '0000-00-00 00:00:00', 0),
(14, '雲林縣', 1, 14, '2015-01-18 13:06:56', '0000-00-00 00:00:00', '2015-01-18 13:06:56', 185, '0000-00-00 00:00:00', 0),
(15, '臺南市', 1, 15, '2015-01-18 13:07:02', '0000-00-00 00:00:00', '2015-01-18 13:07:02', 185, '0000-00-00 00:00:00', 0),
(16, '高雄市', 1, 16, '2015-01-18 13:07:14', '0000-00-00 00:00:00', '2015-01-18 13:07:14', 185, '0000-00-00 00:00:00', 0),
(17, '南海諸島', 0, 17, '2015-01-18 13:07:34', '0000-00-00 00:00:00', '2015-01-18 13:07:34', 185, '0000-00-00 00:00:00', 0),
(18, '澎湖縣', 1, 18, '2015-01-18 13:07:40', '0000-00-00 00:00:00', '2015-01-18 13:07:40', 185, '0000-00-00 00:00:00', 0),
(19, '屏東縣', 1, 19, '2015-01-18 13:07:46', '0000-00-00 00:00:00', '2015-01-18 13:07:46', 185, '0000-00-00 00:00:00', 0),
(20, '臺東縣', 1, 20, '2015-01-18 13:07:58', '0000-00-00 00:00:00', '2015-01-18 13:07:58', 185, '0000-00-00 00:00:00', 0),
(21, '花蓮縣', 1, 21, '2015-01-18 13:08:04', '0000-00-00 00:00:00', '2015-01-18 13:08:04', 185, '0000-00-00 00:00:00', 0),
(22, '金門縣', 1, 22, '2015-01-18 13:08:10', '0000-00-00 00:00:00', '2015-01-18 13:08:10', 185, '0000-00-00 00:00:00', 0),
(23, '連江縣', 1, 23, '2015-01-18 13:08:17', '0000-00-00 00:00:00', '2015-01-18 13:08:17', 185, '0000-00-00 00:00:00', 0);



--
-- 資料表格式： `efa_town`
--

CREATE TABLE IF NOT EXISTS `efa_town` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `modified_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=372 ;

-- --------------------------------------------------------


--
-- 列出以下資料庫的數據： `efa_town`
--

INSERT INTO `efa_town` (`id`, `city_id`, `title`, `zip`, `state`, `ordering`, `publish_up`, `publish_down`, `created`, `created_by`, `modified`, `modified_by`) VALUES
(1, 1, '中正區', 100, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(2, 1, '大同區', 103, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(3, 1, '中山區', 104, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(4, 1, '松山區', 105, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(5, 1, '大安區', 106, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(6, 1, '萬華區', 108, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(7, 1, '信義區', 110, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(8, 1, '士林區', 111, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(9, 1, '北投區', 112, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(10, 1, '內湖區', 114, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(11, 1, '南港區', 115, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(12, 1, '文山區', 116, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(13, 2, '仁愛區', 200, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(14, 2, '信義區', 201, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(15, 2, '中正區', 202, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(16, 2, '中山區', 203, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(17, 2, '安樂區', 204, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(18, 2, '暖暖區', 205, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(19, 2, '七堵區', 206, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(20, 3, '萬里區', 207, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(21, 3, '金山區', 208, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(22, 3, '板橋區', 220, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(23, 3, '汐止區', 221, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(24, 3, '深坑區', 222, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(25, 3, '石碇區', 223, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(26, 3, '瑞芳區', 224, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(27, 3, '平溪區', 226, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(28, 3, '雙溪區', 227, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(29, 3, '貢寮區', 228, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(30, 3, '新店區', 231, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(31, 3, '坪林區', 232, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(32, 3, '烏來區', 233, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(33, 3, '永和區', 234, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(34, 3, '中和區', 235, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(35, 3, '土城區', 236, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(36, 3, '三峽區', 237, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(37, 3, '樹林區', 238, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(38, 3, '鶯歌區', 239, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(39, 3, '三重區', 241, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(40, 3, '新莊區', 242, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(41, 3, '泰山區', 243, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(42, 3, '林口區', 244, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(43, 3, '蘆洲區', 247, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(44, 3, '五股區', 248, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(45, 3, '八里區', 249, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(46, 3, '淡水區', 251, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(47, 3, '三芝區', 252, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(48, 3, '石門區', 253, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(49, 4, '宜蘭', 260, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(50, 4, '頭城', 261, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(51, 4, '礁溪', 262, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(52, 4, '壯圍', 263, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(53, 4, '員山', 264, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(54, 4, '羅東', 265, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(55, 4, '三星', 266, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(56, 4, '大同', 267, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(57, 4, '五結', 268, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(58, 4, '冬山', 269, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(59, 4, '蘇澳', 270, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(60, 4, '南澳', 272, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(61, 4, '釣魚台列嶼', 290, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(62, 5, '東區', 300, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(63, 5, '北區', 300, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(64, 5, '香山區', 300, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(65, 6, '竹北', 302, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(66, 6, '湖口', 303, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(67, 6, '新豐', 304, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(68, 6, '新埔', 305, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(69, 6, '關西', 306, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(70, 6, '芎林', 307, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(71, 6, '寶山', 308, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(72, 6, '竹東', 310, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(73, 6, '五峰', 311, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(74, 6, '橫山', 312, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(75, 6, '尖石', 313, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(76, 6, '北埔', 314, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(77, 6, '峨眉', 315, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(78, 7, '中壢區', 320, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(79, 7, '平鎮區', 324, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(80, 7, '龍潭區', 325, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(81, 7, '楊梅區', 326, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(82, 7, '新屋區', 327, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(83, 7, '觀音區', 328, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(84, 7, '桃園區', 330, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(85, 7, '龜山區', 333, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(86, 7, '八德區', 334, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(87, 7, '大溪區', 335, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(88, 7, '復興區', 336, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(89, 7, '大園區', 337, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(90, 7, '蘆竹區', 338, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(91, 8, '竹南', 350, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(92, 8, '頭份', 351, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(93, 8, '三灣', 352, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(94, 8, '南庄', 353, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(95, 8, '獅潭', 354, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(96, 8, '後龍', 356, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(97, 8, '通霄', 357, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(98, 8, '苑裡', 358, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(99, 8, '苗栗', 360, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(100, 8, '造橋', 361, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(101, 8, '頭屋', 362, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(102, 8, '公館', 363, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(103, 8, '大湖', 364, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(104, 8, '泰安', 365, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(105, 8, '銅鑼', 366, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(106, 8, '三義', 367, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(107, 8, '西湖', 368, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(108, 8, '卓蘭', 369, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(109, 9, '中區', 400, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(110, 9, '東區', 401, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(111, 9, '南區', 402, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(112, 9, '西區', 403, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(113, 9, '北區', 404, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(114, 9, '北屯區', 406, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(115, 9, '西屯區', 407, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(116, 9, '南屯區', 408, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(117, 9, '太平區', 411, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(118, 9, '大里區', 412, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(119, 9, '霧峰區', 413, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(120, 9, '烏日區', 414, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(121, 9, '豐原區', 420, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(122, 9, '后里區', 421, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(123, 9, '石岡區', 422, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(124, 9, '東勢區', 423, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(125, 9, '和平區', 424, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(126, 9, '新社區', 426, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(127, 9, '潭子區', 427, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(128, 9, '大雅區', 428, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(129, 9, '神岡區', 429, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(130, 9, '大肚區', 432, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(131, 9, '沙鹿區', 433, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(132, 9, '龍井區', 434, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(133, 9, '梧棲區', 435, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(134, 9, '清水區', 436, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(135, 9, '大甲區', 437, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(136, 9, '外埔區', 438, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(137, 9, '大安區', 439, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(138, 10, '彰化', 500, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(139, 10, '芬園', 502, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(140, 10, '花壇', 503, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(141, 10, '秀水', 504, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(142, 10, '鹿港', 505, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(143, 10, '福興', 506, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(144, 10, '線西', 507, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(145, 10, '和美', 508, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(146, 10, '伸港', 509, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(147, 10, '員林', 510, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(148, 10, '社頭', 511, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(149, 10, '永靖', 512, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(150, 10, '埔心', 513, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(151, 10, '溪湖', 514, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(152, 10, '大村', 515, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(153, 10, '埔鹽', 516, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(154, 10, '田中', 520, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(155, 10, '北斗', 521, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(156, 10, '田尾', 522, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(157, 10, '埤頭', 523, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(158, 10, '溪州', 524, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(159, 10, '竹塘', 525, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(160, 10, '二林', 526, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(161, 10, '大城', 527, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(162, 10, '芳苑', 528, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(163, 10, '二水', 530, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(164, 11, '南投', 540, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(165, 11, '中寮', 541, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(166, 11, '草屯', 542, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(167, 11, '國姓', 544, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(168, 11, '埔里', 545, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(169, 11, '仁愛', 546, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(170, 11, '名間', 551, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(171, 11, '集集', 552, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(172, 11, '水里', 553, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(173, 11, '魚池', 555, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(174, 11, '信義', 556, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(175, 11, '竹山', 557, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(176, 11, '鹿谷', 558, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(177, 12, '東區', 600, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(178, 12, '西區', 600, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(179, 13, '番路', 602, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(180, 13, '梅山', 603, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(181, 13, '竹崎', 604, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(182, 13, '阿里山', 605, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(183, 13, '中埔', 606, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(184, 13, '大埔', 607, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(185, 13, '水上', 608, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(186, 13, '鹿草', 611, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(187, 13, '太保', 612, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(188, 13, '朴子', 613, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(189, 13, '東石', 614, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(190, 13, '六腳', 615, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(191, 13, '新港', 616, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(192, 13, '民雄', 621, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(193, 13, '大林', 622, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(194, 13, '溪口', 623, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(195, 13, '義竹', 624, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(196, 13, '布袋', 625, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(197, 14, '斗南', 630, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(198, 14, '大埤', 631, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(199, 14, '虎尾', 632, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(200, 14, '土庫', 633, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(201, 14, '褒忠', 634, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(202, 14, '東勢', 635, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(203, 14, '臺西', 636, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(204, 14, '崙背', 637, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(205, 14, '麥寮', 638, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(206, 14, '斗六', 640, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(207, 14, '林內', 643, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(208, 14, '古坑', 646, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(209, 14, '莿桐', 647, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(210, 14, '西螺', 648, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(211, 14, '二崙', 649, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(212, 14, '北港', 651, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(213, 14, '水林', 652, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(214, 14, '口湖', 653, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(215, 14, '四湖', 654, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(216, 14, '元長', 655, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(217, 15, '中西區', 700, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(218, 15, '東區', 701, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(219, 15, '南區', 702, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(220, 15, '北區', 704, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(221, 15, '安平區', 708, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(222, 15, '安南區', 709, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(223, 15, '永康區', 710, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(224, 15, '歸仁區', 711, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(225, 15, '新化區', 712, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(226, 15, '左鎮區', 713, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(227, 15, '玉井區', 714, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(228, 15, '楠西區', 715, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(229, 15, '南化區', 716, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(230, 15, '仁德區', 717, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(231, 15, '關廟區', 718, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(232, 15, '龍崎區', 719, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(233, 15, '官田區', 720, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(234, 15, '麻豆區', 721, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(235, 15, '佳里區', 722, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(236, 15, '西港區', 723, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(237, 15, '七股區', 724, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(238, 15, '將軍區', 725, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(239, 15, '學甲區', 726, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(240, 15, '北門區', 727, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(241, 15, '新營區', 730, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(242, 15, '後壁區', 731, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(243, 15, '白河區', 732, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(244, 15, '東山區', 733, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(245, 15, '六甲區', 734, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(246, 15, '下營區', 735, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(247, 15, '柳營區', 736, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(248, 15, '鹽水區', 737, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(249, 15, '善化區', 741, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(250, 15, '大內區', 742, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(251, 15, '山上區', 743, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(252, 15, '新市區', 744, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(253, 15, '安定區', 745, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(254, 16, '新興區', 800, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 08:00:00', 185, '2015-01-31 17:55:11', 404),
(255, 16, '前金區', 801, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-01-31 17:55:20', 404),
(256, 16, '苓雅區', 802, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-01-31 17:55:24', 404),
(257, 16, '鹽埕區', 803, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-01-31 17:55:28', 404),
(258, 16, '鼓山區', 804, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-01-31 17:55:32', 404),
(259, 16, '旗津區', 805, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-08 10:37:16', 404),
(260, 16, '前鎮區', 806, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-01-31 17:55:42', 404),
(261, 16, '三民區', 807, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-01-31 17:55:46', 404),
(262, 16, '楠梓區', 811, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-13 13:38:27', 404),
(263, 16, '小港區', 812, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-01-31 17:55:55', 404),
(264, 16, '左營區', 813, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-01-31 17:56:02', 404),
(265, 16, '仁武區', 814, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-13 13:37:03', 404),
(266, 16, '大社區', 815, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-08 10:38:08', 404),
(267, 16, '岡山區', 820, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-01-31 17:59:03', 404),
(268, 16, '路竹區', 821, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(269, 16, '阿蓮區', 822, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(270, 16, '田寮區', 823, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(271, 16, '燕巢區', 824, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(272, 16, '橋頭區', 825, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(273, 16, '梓官區', 826, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(274, 16, '彌陀區', 827, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(275, 16, '永安區', 828, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(276, 16, '湖內區', 829, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(277, 16, '鳳山區', 830, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-13 13:36:46', 404),
(278, 16, '大寮區', 831, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(279, 16, '林園區', 832, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(280, 16, '鳥松區', 833, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-13 13:36:19', 404),
(281, 16, '大樹區', 840, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(282, 16, '旗山區', 842, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(283, 16, '美濃區', 843, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(284, 16, '六龜區', 844, 0, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(285, 16, '內門區', 845, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(286, 16, '杉林區', 846, 0, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(287, 16, '甲仙區', 847, 0, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(288, 16, '桃源區', 848, 0, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(289, 16, '那瑪夏區', 849, 0, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(290, 16, '茂林區', 851, 0, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(291, 16, '茄萣區', 852, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(292, 17, '東沙', 817, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(293, 17, '南沙', 819, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(294, 18, '馬公', 880, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(295, 18, '西嶼', 881, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(296, 18, '望安', 882, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(297, 18, '七美', 883, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(298, 18, '白沙', 884, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(299, 18, '湖西', 885, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(300, 19, '屏東市', 900, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-08 11:32:07', 404),
(301, 19, '三地門鄉', 901, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(302, 19, '霧臺鄉', 902, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(303, 19, '瑪家鄉', 903, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(304, 19, '九如鄉', 904, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-08 11:32:25', 404),
(305, 19, '里港鄉', 905, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(306, 19, '高樹鄉', 906, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(307, 19, '鹽埔鄉', 907, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(308, 19, '長治鄉', 908, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(309, 19, '麟洛鄉', 909, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(310, 19, '竹田', 911, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-08 11:32:31', 404),
(311, 19, '內埔鄉', 912, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(312, 19, '萬丹鄉', 913, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-08 11:32:18', 404),
(313, 19, '潮州鎮', 920, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-08 11:33:01', 404),
(314, 19, '泰武鄉', 921, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(315, 19, '來義鄉', 922, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(316, 19, '萬巒鄉', 923, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(317, 19, '崁頂鄉', 924, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(318, 19, '新埤', 925, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(319, 19, '南州鄉', 926, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(320, 19, '林邊鄉', 927, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(321, 19, '東港鎮', 928, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-08 11:33:29', 404),
(322, 19, '琉球鄉', 929, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(323, 19, '佳冬鄉', 931, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(324, 19, '新園鄉', 932, 1, 1, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '2015-03-08 11:33:38', 404),
(325, 19, '枋寮鄉', 940, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(326, 19, '枋山鄉', 941, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(327, 19, '春日鄉', 942, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(328, 19, '獅子鄉', 943, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(329, 19, '車城鄉', 944, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(330, 19, '牡丹鄉', 945, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(331, 19, '恆春鎮', 946, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(332, 19, '滿州鄉', 947, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(333, 20, '臺東', 950, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(334, 20, '綠島', 951, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(335, 20, '蘭嶼', 952, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(336, 20, '延平', 953, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(337, 20, '卑南', 954, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(338, 20, '鹿野', 955, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(339, 20, '關山', 956, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(340, 20, '海端', 957, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(341, 20, '池上', 958, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(342, 20, '東河', 959, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(343, 20, '成功', 961, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(344, 20, '長濱', 962, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(345, 20, '太麻里', 963, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(346, 20, '金峰', 964, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(347, 20, '大武', 965, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(348, 20, '達仁', 966, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(349, 21, '花蓮', 970, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(350, 21, '新城', 971, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(351, 21, '秀林', 972, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(352, 21, '吉安', 973, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(353, 21, '壽豐', 974, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(354, 21, '鳳林', 975, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(355, 21, '光復', 976, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(356, 21, '豐濱', 977, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(357, 21, '瑞穗', 978, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(358, 21, '萬榮', 979, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(359, 21, '玉里', 981, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(360, 21, '卓溪', 982, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(361, 21, '富里', 983, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(362, 22, '金沙', 890, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(363, 22, '金湖', 891, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(364, 22, '金寧', 892, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(365, 22, '金城', 893, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(366, 22, '烈嶼', 894, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(367, 22, '烏坵', 896, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(368, 23, '南竿', 209, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(369, 23, '北竿', 210, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(370, 23, '莒光', 211, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0),
(371, 23, '東引', 212, 1, 0, '2015-01-20 00:00:00', '0000-00-00 00:00:00', '2015-01-20 00:00:00', 185, '0000-00-00 00:00:00', 0);


--
-- 資料表格式： `efa_village`
--

CREATE TABLE IF NOT EXISTS `efa_village` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `town_id` int(10) unsigned NOT NULL,
  `title` varchar(100) NOT NULL,
  `state` tinyint(4) NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  `publish_up` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `publish_down` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
  `created` datetime NOT NULL,
  `created_by` int(10) unsigned NOT NULL,
  `modified` datetime NOT NULL,
  `modified_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=457 ;

-- --------------------------------------------------------

--
-- 列出以下資料庫的數據： `efa_village`
--

INSERT INTO `efa_village` (`id`, `town_id`, `title`, `state`, `ordering`, `publish_up`, `publish_down`, `created`, `created_by`, `modified`, `modified_by`) VALUES
(1, 4, '三民里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(2, 4, '中崙里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(3, 4, '中正里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(4, 4, '中華里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(5, 4, '介壽里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(6, 4, '吉仁里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(7, 4, '吉祥里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(8, 4, '安平里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(9, 4, '富錦里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(10, 4, '富錦里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(11, 4, '復勢里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(12, 4, '復建里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(13, 4, '復源里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(14, 4, '復盛里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(15, 4, '慈祐里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(16, 4, '敦化里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(17, 4, '新東里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(18, 4, '新益里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(19, 4, '新聚里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(20, 4, '東光里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(21, 4, '東勢里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(22, 4, '東昌里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(23, 4, '東榮里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(24, 4, '松基里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(25, 4, '民有里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(26, 4, '民福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(27, 4, '福成里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(28, 4, '精忠里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(29, 4, '美仁里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(30, 4, '自強里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(31, 4, '莊敬里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(32, 4, '鵬程里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(33, 4, '龍田里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(34, 7, '三張里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(35, 7, '三犁里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(36, 7, '中坡里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(37, 7, '中興里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(38, 7, '中行里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(39, 7, '五全里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(40, 7, '五常里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(41, 7, '六合里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(42, 7, '六藝里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(43, 7, '嘉興里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(44, 7, '四維里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(45, 7, '四育里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(46, 7, '國業里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(47, 7, '大仁里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(48, 7, '大道里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(49, 7, '安康里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(50, 7, '富台里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(51, 7, '廣居里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(52, 7, '惠安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(53, 7, '敦厚里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(54, 7, '新仁里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(55, 7, '景勤里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(56, 7, '景新里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(57, 7, '景聯里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(58, 7, '松光里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(59, 7, '松友里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(60, 7, '松隆里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(61, 7, '正和里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(62, 7, '永吉里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(63, 7, '永春里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(64, 7, '泰和里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(65, 7, '興隆里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(66, 7, '興雅里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(67, 7, '西村里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(68, 7, '長春里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(69, 7, '雅祥里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(70, 7, '雙和里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(71, 7, '黎安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(72, 7, '黎平里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(73, 7, '黎忠里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(74, 7, '黎順里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(75, 5, '仁愛里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(76, 5, '仁慈里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(77, 5, '住安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(78, 5, '光信里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(79, 5, '光明里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(80, 5, '光武里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(81, 5, '全安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(82, 5, '古莊里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(83, 5, '古風里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(84, 5, '和安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(85, 5, '大學里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(86, 5, '學府里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(87, 5, '建倫里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(88, 5, '建安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(89, 5, '德安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(90, 5, '敦安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(91, 5, '敦煌里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(92, 5, '新龍里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(93, 5, '昌隆里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(94, 5, '正聲里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(95, 5, '民炤里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(96, 5, '民輝里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(97, 5, '永康里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(98, 5, '法治里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(99, 5, '福住里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(100, 5, '群英里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(101, 5, '群賢里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(102, 5, '義安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(103, 5, '義村里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(104, 5, '臥龍里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(105, 5, '臨江里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(106, 5, '芳和里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(107, 5, '華聲里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(108, 5, '虎嘯里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(109, 5, '誠安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(110, 5, '車層里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(111, 5, '通化里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(112, 5, '通安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(113, 5, '錦安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(114, 5, '錦泰里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(115, 5, '錦華里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(116, 5, '黎元里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(117, 5, '黎和里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(118, 5, '黎孝里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(119, 5, '龍圖里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(120, 5, '龍坡里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(121, 5, '龍安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(122, 5, '龍泉里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(123, 5, '龍淵里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(124, 5, '龍生里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(125, 5, '龍門里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(126, 5, '龍陣里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(127, 5, '龍雲里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(128, 3, '下埤里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(129, 3, '中原里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(130, 3, '中吉里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(131, 3, '中央里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(132, 3, '中山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(133, 3, '中庄里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(134, 3, '劍潭里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(135, 3, '力行里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(136, 3, '北安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(137, 3, '圓山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(138, 3, '埤頭里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(139, 3, '大佳里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(140, 3, '大直里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(141, 3, '康樂里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(142, 3, '復華里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(143, 3, '恆安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(144, 3, '成功里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(145, 3, '新喜里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(146, 3, '新庄里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(147, 3, '新生里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(148, 3, '新福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(149, 3, '晴光里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(150, 3, '朱園里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(151, 3, '朱崙里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(152, 3, '朱馥里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(153, 3, '松江里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(154, 3, '正守里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(155, 3, '正得里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(156, 3, '正義里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(157, 3, '民安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(158, 3, '永安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(159, 3, '江寧里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(160, 3, '江山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(161, 3, '聚盛里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(162, 3, '聚葉里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(163, 3, '興亞里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(164, 3, '行仁里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(165, 3, '行孝里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(166, 3, '行政里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(167, 3, '金泰里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(168, 3, '集英里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(169, 3, '龍洲里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(170, 1, '三愛里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(171, 1, '光復里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(172, 1, '南福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(173, 1, '南門里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(174, 1, '富水里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(175, 1, '幸市里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(176, 1, '幸福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(177, 1, '廈安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(178, 1, '建國里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(179, 1, '忠勤里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(180, 1, '愛國里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(181, 1, '文北里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(182, 1, '文盛里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(183, 1, '文祥里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(184, 1, '新營里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(185, 1, '東門里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(186, 1, '板溪里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(187, 1, '林興里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(188, 1, '梅花里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(189, 1, '水源里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(190, 1, '永功里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(191, 1, '永昌里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(192, 1, '河堤里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(193, 1, '網溪里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(194, 1, '螢圃里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(195, 1, '螢雪里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(196, 1, '頂東里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(197, 1, '黎明里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(198, 1, '龍光里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(199, 1, '龍福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(200, 1, '龍興里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(201, 2, '保安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(202, 2, '光能里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(203, 2, '南芳里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(204, 2, '國慶里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(205, 2, '國順里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(206, 2, '大有里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(207, 2, '延平里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(208, 2, '建功里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(209, 2, '建明里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(210, 2, '建泰里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(211, 2, '揚雅里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(212, 2, '斯文里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(213, 2, '星明里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(214, 2, '景星里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(215, 2, '朝陽里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(216, 2, '民權里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(217, 2, '永樂里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(218, 2, '玉泉里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(219, 2, '老師里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(220, 2, '至聖里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(221, 2, '蓬萊里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(222, 2, '鄰江里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(223, 2, '重慶里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(224, 2, '隆和里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(225, 2, '雙連里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(226, 6, '仁德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(227, 6, '保德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(228, 6, '全德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(229, 6, '凌霄里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(230, 6, '和平里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(231, 6, '和德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(232, 6, '壽德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(233, 6, '孝德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(234, 6, '富民里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(235, 6, '富福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(236, 6, '忠德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(237, 6, '忠貞里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(238, 6, '新和里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(239, 6, '新安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(240, 6, '新忠里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(241, 6, '新起里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(242, 6, '日善里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(243, 6, '日祥里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(244, 6, '柳鄉里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(245, 6, '榮德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(246, 6, '福星里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(247, 6, '福音里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(248, 6, '糖蔀里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(249, 6, '綠堤里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(250, 6, '興德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(251, 6, '菜園里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(252, 6, '華中里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(253, 6, '華江里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(254, 6, '萬壽里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(255, 6, '西門里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(256, 6, '銘德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(257, 6, '錦德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(258, 6, '雙園里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(259, 6, '青山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(260, 6, '頂碩里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(261, 6, '騰雲里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(262, 12, '博嘉里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(263, 12, '忠順里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(264, 12, '指南里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(265, 12, '政大里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(266, 12, '明義里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(267, 12, '明興里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(268, 12, '景仁里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(269, 12, '景慶里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(270, 12, '景東里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(271, 12, '景美里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(272, 12, '景華里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(273, 12, '景行里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(274, 12, '木新里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(275, 12, '木柵里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(276, 12, '樟文里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(277, 12, '樟新里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(278, 12, '樟林里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(279, 12, '樟樹里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(280, 12, '樟腳里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(281, 12, '老泉里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(282, 12, '興光里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(283, 12, '興安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(284, 12, '興家里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(285, 12, '興得里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(286, 12, '興旺里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(287, 12, '興昌里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(288, 12, '興業里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(289, 12, '興泰里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(290, 12, '興福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(291, 12, '興豐里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(292, 12, '興邦里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(293, 12, '華興里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(294, 12, '萬和里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(295, 12, '萬年里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(296, 12, '萬有里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(297, 12, '萬盛里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(298, 12, '萬祥里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(299, 12, '萬美里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(300, 12, '萬興里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(301, 12, '萬芳里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(302, 12, '萬隆里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(303, 12, '試院里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(304, 12, '順興里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(305, 11, '三重里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(306, 11, '中南里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(307, 11, '中研里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(308, 11, '九如里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(309, 11, '仁福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(310, 11, '南港里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(311, 11, '合成里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(312, 11, '成福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(313, 11, '新光里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(314, 11, '新富里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(315, 11, '東新里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(316, 11, '東明里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(317, 11, '玉成里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(318, 11, '百福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(319, 11, '聯成里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(320, 11, '舊莊里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(321, 11, '萬福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(322, 11, '西新里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(323, 11, '重陽里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(324, 11, '鴻福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(325, 10, '五分里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(326, 10, '內湖里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(327, 10, '內溝里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(328, 10, '南湖里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(329, 10, '大湖里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(330, 10, '安泰里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(331, 10, '安湖里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(332, 10, '寶湖里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(333, 10, '康寧里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(334, 10, '明湖里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(335, 10, '東湖里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(336, 10, '樂康里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(337, 10, '清白里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(338, 10, '港墘里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(339, 10, '港富里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(340, 10, '港華里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(341, 10, '港都里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(342, 10, '湖元里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(343, 10, '湖濱里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(344, 10, '湖興里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(345, 10, '瑞光里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(346, 10, '瑞陽里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(347, 10, '石潭里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(348, 10, '碧山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(349, 10, '秀湖里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(350, 10, '紫星里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(351, 10, '紫陽里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(352, 10, '紫雲里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(353, 10, '葫洲里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(354, 10, '蘆洲里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(355, 10, '行善里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(356, 10, '西安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(357, 10, '西康里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(358, 10, '西湖里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(359, 10, '週美里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(360, 10, '金湖里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(361, 10, '金瑞里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(362, 10, '金龍里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(363, 10, '麗山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(364, 8, '三玉里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(365, 8, '仁勇里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(366, 8, '公館里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(367, 8, '前港里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(368, 8, '名山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(369, 8, '天和里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(370, 8, '天壽里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(371, 8, '天山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(372, 8, '天母里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(373, 8, '天玉里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(374, 8, '天祿里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(375, 8, '天福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(376, 8, '富光里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(377, 8, '富洲里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(378, 8, '岩山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(379, 8, '平等里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(380, 8, '後港里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(381, 8, '德華里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(382, 8, '德行里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(383, 8, '忠誠里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(384, 8, '承德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(385, 8, '新安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(386, 8, '明勝里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(387, 8, '東山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(388, 8, '永倫里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(389, 8, '永福里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(390, 8, '溪山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(391, 8, '百齡里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(392, 8, '社園里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0);
INSERT INTO `efa_village` (`id`, `town_id`, `title`, `state`, `ordering`, `publish_up`, `publish_down`, `created`, `created_by`, `modified`, `modified_by`) VALUES
(393, 8, '社子里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(394, 8, '社新里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(395, 8, '福中里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(396, 8, '福佳里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(397, 8, '福安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(398, 8, '福德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(399, 8, '福志里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(400, 8, '福林里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(401, 8, '福華里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(402, 8, '福順里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(403, 8, '義信里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(404, 8, '翠山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(405, 8, '聖山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(406, 8, '臨溪里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(407, 8, '舊佳里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(408, 8, '芝山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(409, 8, '菁山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(410, 8, '葫東里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(411, 8, '葫蘆里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(412, 8, '蘭興里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(413, 8, '蘭雅里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(414, 8, '陽明里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(415, 9, '一德里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(416, 9, '中和里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(417, 9, '中央里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(418, 9, '中庸里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(419, 9, '中心里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(420, 9, '八仙里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(421, 9, '吉利里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(422, 9, '吉慶里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(423, 9, '大同里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(424, 9, '大屯里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(425, 9, '奇岩里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(426, 9, '尊賢里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(427, 9, '建民里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(428, 9, '振華里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(429, 9, '文化里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(430, 9, '文林里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(431, 9, '智仁里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(432, 9, '東華里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(433, 9, '林泉里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(434, 9, '桃源里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(435, 9, '榮光里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(436, 9, '榮華里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(437, 9, '永和里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(438, 9, '永明里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(439, 9, '永欣里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(440, 9, '泉源里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(441, 9, '洲美里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(442, 9, '清江里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(443, 9, '湖山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(444, 9, '湖田里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(445, 9, '溫泉里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(446, 9, '石牌里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(447, 9, '福興里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(448, 9, '秀山里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(449, 9, '稻香里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(450, 9, '立賢里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(451, 9, '立農里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(452, 9, '裕民里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(453, 9, '豐年里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(454, 9, '長安里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(455, 9, '開明里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0),
(456, 9, '關渡里', 1, 0, '2015-12-01 00:00:00', '0000-00-00 00:00:00', '2015-12-01 00:00:00', 185, '0000-00-00 00:00:00', 0);


--
-- 資料表格式： `efa_googlecount`
--

CREATE TABLE IF NOT EXISTS `efa_googlecount` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `t_count` int(11) NOT NULL,
  `total` int(20) NOT NULL,
  `lang` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

--
-- 列出以下資料庫的數據： `efa_googlecount`
--

INSERT INTO `efa_googlecount` (`id`, `t_count`, `total`, `lang`) VALUES
(1, 97, 49224, 1),
(2, 0, 0, 2),
(3, 0, 0, 3);

-- --------------------------------------------------------

--
-- 資料表格式： `efa_unit`
--

CREATE TABLE IF NOT EXISTS `efa_unit` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT '編號',
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
  `modified_by` int(10) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='科室單位管理' AUTO_INCREMENT=1 ;

-----------------------------------------------------------


--
-- 資料表補欄位： `efa_users`
--

ALTER TABLE  `efa_users` ADD  `unit_id` TINYINT( 4 ) UNSIGNED NOT NULL;
ALTER TABLE  `efa_users` ADD  `cross_unit` TINYINT( 4 ) UNSIGNED NOT NULL DEFAULT  '0' COMMENT  '跨單位(0:否、1:是)';
-----------------------------------------------------------

--
-- 資料表格式： `efa_survey_force_analyze`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_analyze` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `surv_id` int(11) NOT NULL,
  `quest_id` int(11) NOT NULL,
  `publish` tinyint(4) NOT NULL DEFAULT '0',
  `required` tinyint(4) NOT NULL DEFAULT '0',
  `order` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分析資料題目設定參數';

-----------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_analyze_count`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_analyze_count` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `survey_id` int(11) unsigned NOT NULL COMMENT '議題ID',
  `quest_id` int(11) unsigned NOT NULL COMMENT '題目ID',
  `quest_title` text NOT NULL COMMENT '題目名稱',
  `field_id` int(11) unsigned NOT NULL COMMENT '選項ID',
  `field_title` text NOT NULL COMMENT '選項名稱',
  `count` int(11) unsigned NOT NULL COMMENT '票數',
  `created` datetime NOT NULL COMMENT '建立時間',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分析資料票數統計';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_analyze_quests`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_analyze_quests` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `title` text NOT NULL,
  `state` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分析資料題目';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_survey_force_analyze_fields`
--

CREATE TABLE IF NOT EXISTS `efa_survey_force_analyze_fields` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `quest_id` int(11) NOT NULL,
  `field_title` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='分析資料選項';

-- --------------------------------------------------------

--
-- 資料表結構 `efa_mail_record`
--

CREATE TABLE IF NOT EXISTS `efa_mail_record` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
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
  `repeate_num` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
