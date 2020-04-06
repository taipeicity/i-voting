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

jimport('joomla.application.component.view');
jimport('joomla.document.error.error');

use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\MVC\View\HtmlView;

/**
 * View class for a list of Knowus.
 *
 * @since  1.6
 */
class KnowusViewList extends HtmlView
{
    protected $items;

    protected $pagination;

    protected $state;

    protected $params;

    /**
     * Display the view
     *
     * @param string $tpl Template name
     *
     * @return void
     *
     * @throws Exception
     */
    public function display($tpl = null)
    {
        $app = Factory::getApplication();

        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->params = $app->getParams('com_knowus');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');
        $this->itemid = $app->input->getInt('Itemid');

        if ($this->getState('list.alias') || $this->getState('list.id')) {
            $this->isDetail();
        } else {
            $this->items = KnowusHelper::addVideoID($this->items);
        }

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->_prepareDocument();
        parent::display($tpl);
    }

    /**
     * Prepares the document
     *
     * @return void
     *
     * @throws Exception
     */
    protected function _prepareDocument()
    {
        $app = Factory::getApplication();
        $menus = $app->getMenu();
        $title = null;

        // Because the application sets a default page title,
        // we need to get it from the menu item itself
        $menu = $menus->getActive();

        if ($menu) {
            $this->params->def('page_heading', $this->params->get('page_title', $menu->title));
        } else {
            $this->params->def('page_heading', Text::_('COM_KNOWUS_DEFAULT_PAGE_TITLE'));
        }

        $title = $this->params->get('page_title', '');

        if (empty($title)) {
            $title = $app->get('sitename');
        } elseif ($app->get('sitename_pagetitles', 0) == 1) {
            $title = Text::sprintf('JPAGETITLE', $app->get('sitename'), $title);
        } elseif ($app->get('sitename_pagetitles', 0) == 2) {
            $title = Text::sprintf('JPAGETITLE', $title, $app->get('sitename'));
        }

        if ($this->getLayout() === 'detail') {
            $title = Text::sprintf('JPAGETITLE', $this->item->title, $title);
            $this->document->setMetadata('og:title', $title);
            $this->document->setMetadata('og:type', 'website');
            $this->document->setMetadata('og:url', $this->document->getBase());
            $this->document->setMetadata('og:site_name', 'i-Voting');
            $this->document->setDescription(strip_tags($this->item->content));
        }

        $this->document->setTitle($title);

        if ($this->params->get('menu-meta_description')) {
            $this->document->setDescription($this->params->get('menu-meta_description'));
        }

        if ($this->params->get('menu-meta_keywords')) {
            $this->document->setMetadata('keywords', $this->params->get('menu-meta_keywords'));
        }

        if ($this->params->get('robots')) {
            $this->document->setMetadata('robots', $this->params->get('robots'));
        }
    }

    /**
     * Check if state is set
     *
     * @param mixed $state State
     *
     * @return bool
     */
    public function getState($state)
    {
        return isset($this->state->{$state}) ? $this->state->{$state} : false;
    }

    public function isDetail()
    {
        $config = Factory::getConfig();
        $isSef = $config->get('sef');
        $value = $isSef ? $this->getState('list.alias') : $this->getState('list.id');

        $isDetail = new IsDetail($this->items);
        $isDetail->match($value);
        $item = $isDetail->get();

        $this->exist($item);
        $this->setLayout('detail');
    }

    public function exist($item)
    {
        if (empty($item)) {
            JError::raiseError(404, '找不到網頁，請確認網址連結是否正確。');
        } else {
            $this->item = $item;
        }
    }
}
