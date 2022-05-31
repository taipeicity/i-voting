<?php
/**
 * 紀錄管理 - 資料編輯
 * 
 * @version    CVS: 1.0.0
 * @package    com_record
 * @author     JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/ <sam_lin@justher.tw>
 * @copyright  JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license    GPL-2.0+
 */

// No direct access to this file
defined('_JEXEC') or die;

/**
 * helper.
 *
 * @package com_record
 */
class RecordHelper {

	/**
	 * Configure the Linkbar.
	 *
	 * @param   string	The name of the active view.
	 * @since   1.6
	 */
	public static function addSubmenu($vName = 'items') {
		JHtmlSidebar::addEntry(
				"API紀錄", 'index.php?option=com_record&view=items', $vName == 'items'
		);
		JHtmlSidebar::addEntry(
				"區塊鏈紀錄", 'index.php?option=com_record&view=blockchains', $vName == 'blockchains'
		);
		
	}

	/**
	 * Gets a list of the actions that can be performed.
	 *
	 * @param   integer  The category ID.
	 * @return  JObject
	 * @since   1.6
	 */
	public static function getActions($categoryId = 0) {
		$user = JFactory::getUser();
		$result = new JObject;

		if (empty($categoryId)) {
			$assetName = 'com_record';
			$level = 'component';
		} else {
			$assetName = 'com_record.category.' . (int) $categoryId;
			$level = 'category';
		}

		$actions = JAccess::getActions('com_record', $level);

		foreach ($actions as $action) {
			$result->set($action->name, $user->authorise($action->name, $assetName));
		}

		return $result;
	}

}
