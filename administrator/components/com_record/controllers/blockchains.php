<?php
/**
 * 紀錄管理 - 清單Controller
 * 
 * @version    CVS: 1.0.0
 * @package    com_blockchain
 * @author     JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/ <sam_lin@justher.tw>
 * @copyright  JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license    GPL-2.0+
 */
 

// No direct access to this file
defined('_JEXEC') or die;

/**
 * list controller class.
 *
 * @package com_blockchain
 */
class RecordControllerBlockchains extends JControllerAdmin
{
	/**
	 * Proxy for getModel.
	 * @since   1.6
	 */
	public function getModel($name = 'Item', $prefix = 'BlockchainModel', $config = array('ignore_request' => true))
	{
		$model = parent::getModel($name, $prefix, $config);
		return $model;
	}

	/**
	 * Method to provide child classes the opportunity to process after the delete task.
	 *
	 * @param   JModelLegacy   $model   The model for the component
	 * @param   mixed          $ids     array of ids deleted.
	 *
	 * @return  void
	 *
	 * @since   3.1
	 */
	protected function postDeleteHook(JModelLegacy $model, $ids = null)
	{
	}
}
