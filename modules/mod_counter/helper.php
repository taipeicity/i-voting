<?php
/**
*   @package         Counter
*   @version         1.0-modified
*   @copyright       臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          臺北市政府資訊局- http://doit.gov.taipei/
*/
// no direct access
defined('_JEXEC') or die('Restricted access');

class modCounterHelper {

	function getList($params) {
		$db = & JFactory::getDBO();
		$lang = $params->get('lang');

		$query	= $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__googlecount'));
		$query->where($db->quoteName('lang')." = ".$db->quote($lang));

		$db->setQuery($query);

		return $db->loadObject();
	}

}

?>
