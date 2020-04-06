<?php

/**
 * @version    CVS: 1.0.0
 * @package    Com_Knowus
 * @author     JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/ <sam_lin@justher.tw>
 * @copyright  JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license    GPL-2.0+
 */
// No direct access
defined('_JEXEC') or die;

JLoader::registerPrefix('Knowus', JPATH_SITE . '/components/com_knowus/');

/**
 * Class KnowusRouter
 *
 * @since  3.3
 */
class KnowusRouter extends \Joomla\CMS\Component\Router\RouterBase
{
	/**
	 * Build method for URLs
	 * This method is meant to transform the query parameters into a more human
	 * readable form. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$query  An array of URL arguments
	 *
	 * @return  array  The URL arguments to use to assemble the subsequent URL.
	 *
	 * @since   3.3
	 */
	public function build(&$query)
	{
		$segments = array();
		$view     = null;

		if (isset($query['task']))
		{
			$taskParts  = explode('.', $query['task']);
			$segments[] = implode('/', $taskParts);
			$view       = $taskParts[0];
			unset($query['task']);
		}

		if (isset($query['view']))
		{
			$segments[] = $query['view'];
			$view = $query['view'];
			
			unset($query['view']);
		}

		if (isset($query['id']))
		{
//			if ($view !== null)
//			{
//                $db = JFactory::getDbo();
//                $dbQuery = $db->getQuery(true)
//                    ->select('alias')
//                    ->from('#__knowus')
//                    ->where('id=' . (int)$query['id']);
//                $db->setQuery($dbQuery);
//                $alias = $db->loadResult();
//
//				$segments[] = $alias;
//			}
//			else
//			{
//				$segments[] = $query['id'];
//			}
            $db = JFactory::getDbo();
            $dbQuery = $db->getQuery(true)
                ->select('alias')
                ->from('#__knowus')
                ->where('id=' . (int)$query['id']);
            $db->setQuery($dbQuery);
            $alias = $db->loadResult();

            $segments[] = $alias;

			unset($query['id']);
		}

		return $segments;
	}

	/**
	 * Parse method for URLs
	 * This method is meant to transform the human readable URL back into
	 * query parameters. It is only executed when SEF mode is switched on.
	 *
	 * @param   array  &$segments  The segments of the URL to parse.
	 *
	 * @return  array  The URL attributes to be used by the application.
	 *
	 * @since   3.3
	 */
	public function parse(&$segments)
	{
		$vars = array();

		// View is always the first element of the array
//		$vars['view'] = array_shift($segments);
        $vars['view'] = 'list';
		$model        = KnowusHelpersKnowus::getModel($vars['view']);

		while (!empty($segments))
		{
			$segment = array_pop($segments);

			// If it's the ID, let's put on the request
			if (is_numeric($segment))
			{
				$vars['id'] = $segment;
			}
			else
			{
//				$vars['task'] = $vars['view'] . '.' . $segment;
                $vars['alias'] = $segment;
			}
		}

		return $vars;
	}
}
