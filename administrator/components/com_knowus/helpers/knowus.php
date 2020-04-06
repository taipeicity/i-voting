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

use \Joomla\CMS\Factory;

/**
 * Knowus helper.
 *
 * @since  1.6
 */
class KnowusHelper
{
    /**
     * Configure the Linkbar.
     *
     * @param string $vName string
     *
     * @return void
     */
    public static function addSubmenu($vName = '')
    {
        JHtmlSidebar::addEntry(
            JText::_('COM_KNOWUS_TITLE_LIST'),
            'index.php?option=com_knowus&view=list',
            $vName == 'list'
        );

        JHtmlSidebar::addEntry(
            JText::_('分類'),
            'index.php?option=com_categories&view=categories&extension=com_knowus',
            $vName == 'categories'
        );

    }

    /**
     * Gets the files attached to an item
     *
     * @param int $pk The item's id
     *
     * @param string $table The table's name
     *
     * @param string $field The field's name
     *
     * @return  array  The files
     */
    public static function getFiles($pk, $table, $field)
    {
        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        $query
            ->select($field)
            ->from($table)
            ->where('id = ' . (int)$pk);

        $db->setQuery($query);

        return explode(',', $db->loadResult());
    }

    /**
     * Gets a list of the actions that can be performed.
     *
     * @return    JObject
     *
     * @since    1.6
     */
    public static function getActions()
    {
        $user = Factory::getUser();
        $result = new JObject;

        $assetName = 'com_knowus';

        $actions = array(
            'core.admin', 'core.manage', 'core.create', 'core.edit', 'core.edit.own', 'core.edit.state', 'core.delete'
        );

        foreach ($actions as $action) {
            $result->set($action, $user->authorise($action, $assetName));
        }

        return $result;
    }

    public static function addVideoID($items)
    {
        return array_map(function ($item) {
            $item->videoId = self::getVideoId($item->youtube_url);
            return $item;
        }, $items);
    }

    public static function getVideoId($url)
    {
        $videoId = '';
        if (preg_match("/([?&])v=([^&#]+)/", $url)) {
            preg_match("/([?&])v=([^&#]+)/", $url, $matches);
            return array_pop($matches);
        } else if (preg_match("/(\.be\/)+([^\/]+)/", $url)) {
            preg_match("/(\.be\/)+([^\/]+)/", $url, $matches);
            return array_pop($matches);
        } else if (preg_match("/(\embed\/)+([^\/]+)/", $url)) {
            preg_match("/(\embed\/)+([^\/]+)/", $url, $matches);
            return array_pop($matches);
        }

        return $videoId;
    }
}

