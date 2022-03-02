<?php

/**
 * @package            Surveyforce
 * @version            1.0-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modeladmin');

class SurveyforceModelAnalyze extends JModelAdmin {

	protected $context = 'com_surveyforce';

	public function getTable($type = 'Analyze', $prefix = 'SurveyforceTable', $config = array ()) {
		$this->addTablePath(JPATH_COMPONENT_ADMINISTRATOR . '/tables');

		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array (), $loadData = true) {
		$form = $this->loadForm('analyze-form', 'analyze', array ('control' => 'jform', 'load_data' => false));

		if (empty($form)) {
			return false;
		}

		$item = $this->getItem();
		$form->bind($item);

		return $form;
	}


	public function getFields() {
		$app = JFactory::getApplication();
		$id  = $app->input->getInt('id');

		$db    = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select('f.id AS fid, f.quest_id, field_title');
		$query->from($db->quoteName('#__survey_force_analyze_quests', 'q'));
		$query->join('LEFT', $db->quoteName('#__survey_force_analyze_fields', 'f') . ' ON ' . $db->quoteName('f.quest_id') . ' = ' . $db->quoteName('q.id'));
		$query->where($db->quoteName('q.id') . ' = ' . $db->quote($id));

		$db->setQuery($query);

		return $db->loadObjectList();
	}


	public function save($data) {


		if ($data['id'] != 0) {

			$app  = JFactory::getApplication();
			$post = $app->input->getArray($_POST);

			$Field = $this->getTable('Field'); //選項欄位

			unset($post['jform']);

			foreach ($post as $key => $items) {
				if ($key == 'option_id') {
					$i = 0;
					foreach ($items as $item) {
						$forms[$i]['id'] = $item;
						$i++;
					}
				}
				if ($key == 'option_ftext') {
					$i = 0;
					foreach ($items as $item) {
						$forms[$i]['field_title'] = $item;
						$forms[$i]['quest_id']    = $data['id'];
						$i++;
					}
				}
			}

			if (!$forms) {
				$data['state'] = 0;
			}

			foreach ($forms as $form) {
				$Field->bind($form);
				$Field->save($form);
			}

			if ($post['delete_row'] != 0) {
				$db    = $this->getDBO();
				$query = $db->getQuery(true);

				$query->delete($db->quoteName('#__survey_force_analyze_fields'));
				$query->where($db->quoteName('id') . ' IN(' . $post["delete_row"] . ')');

				$db->setQuery($query);

				$db->execute();
			}
		} else {
			$data['state'] = 0;
		}


		parent::save($data);

		return true;
	}

	public function getResult() {

		$app     = JFactory::getApplication();
		$surv_id = $app->input->getInt('surv_id');

		$db    = $this->getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__survey_force_analyze_count', 'c'));
		$query->join('LEFT', $db->quoteName('#__survey_force_analyze', 'a') . ' ON ' . $db->quoteName('a.quest_id') . ' = ' . $db->quoteName('c.quest_id'));
		$query->where($db->quoteName('c.survey_id') . ' = ' . $db->quote($surv_id));
		$query->where($db->quoteName('a.surv_id') . ' = ' . $db->quote($surv_id));
		$query->where($db->quoteName('a.publish') . ' = ' . $db->quote(1));
		$query->order($db->quoteName('c.quest_id'));
		$query->order($db->quoteName('c.field_id'));

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		// 將分析資料分類
		$result = [];
		$i      = 0;
		foreach ($rows as $k => $row) {
			if ($result[$i]['quest_title'] != $row->quest_title) {
				$i++;
			}
			$result[$i]['quest_title']               = $row->quest_title;
			$result[$i]['detail'][$row->field_title] = (int) $row->count;
		}

		return $result;

	}

	/**
     * 取得指定議題編號的性別分析-男性總計
     * 
	 * 
	 * @return  int 總計
	 */
    public function getMaleCount()
    {
        $app = JFactory::getApplication();
        $survey_id = $app->input->getInt('surv_id');

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__survey_force_analyze_sex');
        $query->where($db->qn('survey_id').' = '.$db->q($survey_id));
        $query->where($db->qn('sex').' = '.$db->q(1));

        $db->setQuery($query);
        $db->execute();

        return $db->getNumRows();
    }

	/**
     * 取得指定議題編號的性別分析-女性總計
     * 
	 * 
	 * @return  int 總計
	 */
    public function getFemaleCount()
    {
        $app = JFactory::getApplication();
        $survey_id = $app->input->getInt('surv_id');

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__survey_force_analyze_sex');
        $query->where($db->qn('survey_id').' = '.$db->q($survey_id));
        $query->where($db->qn('sex').' = '.$db->q(2));

        $db->setQuery($query);
        $db->execute();

        return $db->getNumRows();
    }

	/**
     * 取得指定議題編號的年齡分析
     * 
	 * 
	 * @return  int 總計
	 */
    public function getAgeCount()
    {
        $app = JFactory::getApplication();
        $survey_id = $app->input->getInt('surv_id');

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('age, count(*) as count');
        $query->from('#__survey_force_analyze_age');
        $query->where($db->qn('survey_id').' = '.$db->q($survey_id));
        $query->group($db->qn('age'));

        $db->setQuery($query);

        return $db->loadObjectList('age');
    }

	/**
     * 取得指定議題編號的API性別分析-男性總計
     * 
	 * 
	 * @return  int 總計
	 */
    public function getApiMaleCount()
    {
        $app = JFactory::getApplication();
        $survey_id = $app->input->getInt('surv_id');

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__survey_force_analyze_sex_api');
        $query->where($db->qn('survey_id').' = '.$db->q($survey_id));
        $query->where($db->qn('sex').' = '.$db->q(1));

        $db->setQuery($query);
        $db->execute();

        return $db->getNumRows();
    }

	/**
     * 取得指定議題編號的API性別分析-女性總計
     * 
	 * 
	 * @return  int 總計
	 */
    public function getApiFemaleCount()
    {
        $app = JFactory::getApplication();
        $survey_id = $app->input->getInt('surv_id');

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('*');
        $query->from('#__survey_force_analyze_sex_api');
        $query->where($db->qn('survey_id').' = '.$db->q($survey_id));
        $query->where($db->qn('sex').' = '.$db->q(2));

        $db->setQuery($query);
        $db->execute();

        return $db->getNumRows();
    }

	/**
     * 取得指定議題編號的API年齡分析
     * 
	 * 
	 * @return  int 總計
	 */
    public function getApiAgeCount()
    {
        $app = JFactory::getApplication();
        $survey_id = $app->input->getInt('surv_id');

        $db = $this->getDbo();
        $query = $db->getQuery(true);

        $query->select('age, count(*) as count');
        $query->from('#__survey_force_analyze_age_api');
        $query->where($db->qn('survey_id').' = '.$db->q($survey_id));
        $query->group($db->qn('age'));

        $db->setQuery($query);

        return $db->loadObjectList('age');
    }
}
