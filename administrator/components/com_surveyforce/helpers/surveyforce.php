<?php

/**
*   @package         Surveyforce
*   @version           1.1-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;

/**
 * Surveyforce component helper.
 */
class SurveyforceHelper
{
	public static function showTitle($submenu, $addition = false)
	{
		$document = JFactory::getDocument();
		$title = JText::_('COM_SURVEYFORCE_' . strtoupper($submenu));
		$document->setTitle($title . ($addition ? ' ' . $addition : ''));
		JToolBarHelper::title($title . ($addition ? ' ' . $addition : ''), $submenu);

		return $title;
	}

	public static function getCSSJS()
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JURI::root() . 'administrator/components/com_surveyforce/assets/css/surveyforce.css');
	}


	static function sfGetOrderingList($sql, $chop = '55')
	{
		$database = JFactory::getDBO();

		$order = array();
		$database->setQuery($sql);
		if (!($orders = ($database->LoadObjectList() == null ? array() : $database->LoadObjectList())))
		{
			if ($database->getErrorNum())
			{
				echo $database->stderr();
				return false;
			}
			else
			{
				$order[] = JHTML::_('select.option', 1, JText::_('COM_SURVEYFORCE_FIRST'));
				return $order;
			}
		}
		$order[] = JHTML::_('select.option', 0, '0 ' . JText::_('COM_SURVEYFORCE_FIRST'));
		for ($i = 0, $n = count($orders); $i < $n; $i++)
		{
			$orders[$i]->text = strip_tags($orders[$i]->text);
			if (strlen($orders[$i]->text) > $chop)
			{
				$text = substr($orders[$i]->text, 0, $chop) . "...";
			}
			else
			{
				$text = $orders[$i]->text;
			}

			$order[] = JHTML::_('select.option', $orders[$i]->value, $orders[$i]->value . ' (' . $text . ')');
		}
		$order[] = JHTML::_('select.option', $orders[$i - 1]->value + 1, ($orders[$i - 1]->value + 1) . JText::_('COM_SURVEYFORCE_LAST'));

		return $order;
	}

	
	public static function getSuveryItem($_survey_id) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from('#__survey_force_survs');
		$query->where('id = '. (int) $_survey_id);

		$db->setQuery($query);

		return $db->loadObject();
	}


}