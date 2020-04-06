<?php
/**
 * @version    CVS: 1.0.0
 * @package    Com_Knowus
 * @author     JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/ <sam_lin@justher.tw>
 * @copyright  JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license    GPL-2.0+
 */

// No direct access.
defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

use \Joomla\CMS\Table\Table;
use \Joomla\CMS\Factory;
use \Joomla\CMS\Language\Text;
use \Joomla\CMS\Plugin\PluginHelper;

/**
 * Knowus model.
 *
 * @since  1.6
 */
class KnowusModelDetail extends \Joomla\CMS\MVC\Model\AdminModel
{
    /**
     * @var      string    The prefix to use with controller messages.
     * @since    1.6
     */
    protected $text_prefix = 'COM_KNOWUS';

    /**
     * @var    string    Alias to manage history control
     * @since   3.2
     */
    public $typeAlias = 'com_knowus.detail';

    /**
     * @var null  Item data
     * @since  1.6
     */
    protected $item = null;


    /**
     * Returns a reference to the a Table object, always creating it.
     *
     * @param string $type The table type to instantiate
     * @param string $prefix A prefix for the table class name. Optional.
     * @param array $config Configuration array for model. Optional.
     *
     * @return    JTable    A database object
     *
     * @since    1.6
     */
    public function getTable($type = 'Detail', $prefix = 'KnowusTable', $config = array())
    {
        return Table::getInstance($type, $prefix, $config);
    }

    /**
     * Method to get the record form.
     *
     * @param array $data An optional array of data for the form to interogate.
     * @param boolean $loadData True if the form is to load its own data (default case), false if not.
     *
     * @return  JForm  A JForm object on success, false on failure
     *
     * @throws
     * @since    1.6
     *
     */
    public function getForm($data = array(), $loadData = true)
    {
        // Initialise variables.
        $app = Factory::getApplication();

        // Get the form.
        $form = $this->loadForm(
            'com_knowus.detail', 'detail',
            array('control' => 'jform',
                'load_data' => $loadData
            )
        );


        if (empty($form)) {
            return false;
        }

        return $form;
    }

    /**
     * Method to get the data that should be injected in the form.
     *
     * @return   mixed  The data for the form.
     *
     * @throws
     * @since    1.6
     *
     */
    protected function loadFormData()
    {
        // Check the session for previously entered form data.
        $data = Factory::getApplication()->getUserState('com_knowus.edit.detail.data', array());

        if (empty($data)) {
            if ($this->item === null) {
                $this->item = $this->getItem();
            }

            $data = $this->item;

        }

        return $data;
    }

    /**
     * Method to get a single record.
     *
     * @param integer $pk The id of the primary key.
     *
     * @return  mixed    Object on success, false on failure.
     *
     * @since    1.6
     */
    public function getItem($pk = null)
    {

        if ($item = parent::getItem($pk)) {
            // Do any procesing on fields here if needed
        }

        return $item;

    }

    /**
     * Method to duplicate an Detail
     *
     * @param array  &$pks An array of primary key IDs.
     *
     * @return  boolean  True if successful.
     *
     * @throws  Exception
     */
    public function duplicate(&$pks)
    {
        $user = Factory::getUser();

        // Access checks.
        if (!$user->authorise('core.create', 'com_knowus')) {
            throw new Exception(Text::_('JERROR_CORE_CREATE_NOT_PERMITTED'));
        }

        $dispatcher = JEventDispatcher::getInstance();
        $context = $this->option . '.' . $this->name;

        // Include the plugins for the save events.
        PluginHelper::importPlugin($this->events_map['save']);

        $table = $this->getTable();

        foreach ($pks as $pk) {

            if ($table->load($pk, true)) {
                // Reset the id to create a new record.
                $table->id = 0;

                if (!$table->check()) {
                    throw new Exception($table->getError());
                }

                if (!empty($table->unit)) {
                    if (is_array($table->unit)) {
                        $table->unit = implode(',', $table->unit);
                    }
                } else {
                    $table->unit = '';
                }

                if (!empty($table->img)) {
                    if (is_array($table->img)) {
                        $table->img = implode(',', $table->img);
                    }
                } else {
                    $table->img = '';
                }


                // Trigger the before save event.
                $result = $dispatcher->trigger($this->event_before_save, array($context, &$table, true));

                if (in_array(false, $result, true) || !$table->store()) {
                    throw new Exception($table->getError());
                }

                // Trigger the after save event.
                $dispatcher->trigger($this->event_after_save, array($context, &$table, true));
            } else {
                throw new Exception($table->getError());
            }

        }

        // Clean cache
        $this->cleanCache();

        return true;
    }

    /**
     * Prepare and sanitise the table prior to saving.
     *
     * @param JTable $table Table Object
     *
     * @return void
     *
     * @since    1.6
     */
    protected function prepareTable($table)
    {
        jimport('joomla.filter.output');

        if (empty($table->id)) {
            // Set ordering to the last item if not set
            if (@$table->ordering === '') {
                $db = Factory::getDbo();
                $db->setQuery('SELECT MAX(ordering) FROM #__knowus');
                $max = $db->loadResult();
                $table->ordering = $max + 1;
            }
        }
    }

    public function save($data)
    {
        $app = Factory::getApplication();
        $input = $app->input;
        $table = $this->getTable();

        if (in_array($input->get('task'), array('apply', 'save', 'save2new'))) {
            if ($data['alias'] == null) {
                if (JFactory::getConfig()->get('unicodeslugs') == 1) {
                    $data['alias'] = JFilterOutput::stringURLUnicodeSlug($data['title']);
                } else {
                    $data['alias'] = JFilterOutput::stringURLSafe($data['title']);
                }

                if ($table->load(array('alias' => $data['alias'], 'catid' => $data['catid']))) {
                    $msg = JText::_('COM_KNOWUS_SAVE_SAME_ALIAS_WARNING');
                }

                list($title, $alias) = $this->generateNewTitle($data['catid'], $data['alias'], $data['title']);
                $data['alias'] = $alias;

                if (isset($msg)) {
                    JFactory::getApplication()->enqueueMessage($msg, 'warning');
                }
            }
        }

        $table->load(array('alias' => $data['alias']));

        if ($table->alias && ($table->alias == $data['alias']) && $table->id != $data['id']) {
            $this->setError(JText::_('COM_KNOWUS_SAVE_SAME_ALIAS') . "<br>別名: " . $data['alias']);
            return false;
        }

        return parent::save($data); // TODO: Change the autogenerated stub
    }
}
