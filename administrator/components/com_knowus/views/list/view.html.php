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

use \Joomla\CMS\Language\Text;

/**
 * View class for a list of Knowus.
 *
 * @since  1.6
 */
class KnowusViewList extends \Joomla\CMS\MVC\View\HtmlView
{
    protected $items;

    protected $pagination;

    protected $state;

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
        $this->state = $this->get('State');
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->filterForm = $this->get('FilterForm');
        $this->activeFilters = $this->get('ActiveFilters');

        // Check for errors.
        if (count($errors = $this->get('Errors'))) {
            throw new Exception(implode("\n", $errors));
        }

        $this->items = KnowusHelper::addVideoID($this->items);

        KnowusHelper::addSubmenu('list');

        $this->addToolbar();

        $this->sidebar = JHtmlSidebar::render();
        parent::display($tpl);
    }

    /**
     * Add the page title and toolbar.
     *
     * @return void
     *
     * @since    1.6
     */
    protected function addToolbar()
    {
        $state = $this->get('State');
        $canDo = KnowusHelper::getActions();

        JToolBarHelper::title("宣導專區", 'list.png');

        // Check if the form exists before showing the add/edit buttons
        $formPath = JPATH_COMPONENT_ADMINISTRATOR . '/views/detail';

        if (file_exists($formPath)) {
            if ($canDo->get('core.create')) {
                JToolBarHelper::addNew('detail.add', 'JTOOLBAR_NEW');

                if (isset($this->items[0])) {
                    JToolbarHelper::custom('list.duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
                }
            }

            if ($canDo->get('core.edit') && isset($this->items[0])) {
                JToolBarHelper::editList('detail.edit', 'JTOOLBAR_EDIT');
            }
        }

        if ($canDo->get('core.edit.state')) {
            if (isset($this->items[0]->state)) {
                JToolBarHelper::divider();
                JToolBarHelper::custom('list.publish', 'publish.png', 'publish_f2.png', 'JTOOLBAR_PUBLISH', true);
                JToolBarHelper::custom('list.unpublish', 'unpublish.png', 'unpublish_f2.png', 'JTOOLBAR_UNPUBLISH', true);
            } elseif (isset($this->items[0])) {
                // If this component does not use state then show a direct delete button as we can not trash
                JToolBarHelper::deleteList('', 'list.delete', 'JTOOLBAR_DELETE');
            }

        }

        // Show trash and delete for components that uses the state field
        if (isset($this->items[0]->state)) {
            if ($state->get('filter.state') == -2 && $canDo->get('core.delete')) {
                JToolBarHelper::deleteList('', 'list.delete', 'JTOOLBAR_EMPTY_TRASH');
                JToolBarHelper::divider();
            } elseif ($canDo->get('core.edit.state')) {
                JToolBarHelper::trash('list.trash', 'JTOOLBAR_TRASH');
                JToolBarHelper::divider();
            }
        }

        if ($canDo->get('core.admin')) {
            JToolBarHelper::preferences('com_knowus');
        }

        // Set sidebar action - New in 3.0
        JHtmlSidebar::setAction('index.php?option=com_knowus&view=list');
    }

    /**
     * Method to order fields
     *
     * @return array
     */
    protected function getSortFields()
    {
        return [
            'a.`id`' => JText::_('JGRID_HEADING_ID'),
            'a.`state`' => JText::_('JSTATUS'),
            'a.`title`' => JText::_('COM_KNOWUS_LIST_TITLE'),
            'a.`unit`' => JText::_('COM_KNOWUS_LIST_UNIT'),
            'a.`created`' => JText::_('建立時間'),
            'a.`modified`' => JText::_('修改時間'),
//            'a.`ordering`' => JText::_('JGRID_HEADING_ORDERING'),
//            'a.`created_by`' => JText::_('COM_KNOWUS_LIST_CREATED_BY'),
//            'a.`modified_by`' => JText::_('COM_KNOWUS_LIST_MODIFIED_BY'),
//            'a.`youtube_url`' => JText::_('COM_KNOWUS_LIST_YOUTUBE_URL'),
//            'a.`content`' => JText::_('COM_KNOWUS_LIST_CONTENT'),
//            'a.`img`' => JText::_('COM_KNOWUS_LIST_IMG'),
        ];
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
}
