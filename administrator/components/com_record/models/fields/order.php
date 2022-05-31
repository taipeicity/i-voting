<?php
/**
 * 紀錄管理 - 資料排序
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
 * order field of com_record
 *
 * @package com_record
 */
class JFormFieldOrder extends JFormField
{
	/**
	 * The form field type.
	 *
	 * @var		string
	 * @since   1.6
	 */
	protected $type = 'Order';

	/**
	 * Method to get the field input markup.
	 *
	 * @return  string	The field input markup.
	 * @since   1.6
	 */
	protected function getInput()
	{
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->element['class'] ? ' class="'.(string) $this->element['class'].'"' : '';
		$attr .= ((string) $this->element['disabled'] == 'true') ? ' disabled="disabled"' : '';
		$attr .= $this->element['size'] ? ' size="'.(int) $this->element['size'].'"' : '';

		// Initialize JavaScript field attributes.
		$attr .= $this->element['onchange'] ? ' onchange="'.(string) $this->element['onchange'].'"' : '';

		// Get some field values from the form.
		$itemId	= (int) $this->form->getValue('id');
		$categoryId	= (int) $this->form->getValue('catid');

		// Build the query for the ordering list.
		$query = 'SELECT ordering AS value, title AS text' .
				' FROM #__record' .
				' WHERE catid = ' . (int) $categoryId .
				' ORDER BY ordering';

		// Create a read-only list (no name) with a hidden input to store the value.
		if ((string) $this->element['readonly'] == 'true')
		{
			$html[] = JHtml::_('list.ordering', '', $query, trim($attr), $this->value, $itemId ? 0 : 1);
			$html[] = '<input type="hidden" name="'.$this->name.'" value="'.$this->value.'"/>';
		}
		// Create a regular list.
		else {
			$html[] = JHtml::_('list.ordering', $this->name, $query, trim($attr), $this->value, $itemId ? 0 : 1);
		}

		return implode($html);
	}
}
