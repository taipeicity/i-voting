<?php
/**
 * 開放式欄位
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSurveyOpen {

	// 讀取選項清單
	public function onGetAdminOptions($_question_id) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('id');
		$query->from($db->quoteName('#__survey_force_fields'));
		$query->where($db->quoteName('quest_id') . " = '{$_question_id}'");
		$query->order('ordering ASC');
		$db->setQuery($query);

		$field_id = $db->loadResult();

		ob_start();
		include_once(JPATH_SITE . "/plugins/survey/open/admin/html.php");
		$options = ob_get_contents();
		ob_clean();

		return $options;
	}
	
	// 儲存選項
	public function onSaveQuestion($_question_id) {
		$config = JFactory::getConfig();
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$db = JFactory::getDBO();

		$ivoting_path = $config->get('ivoting_path');

		$post = $jinput->getArray($_POST);


		// 新增與修改選項
		$option_id = $post["option_id"];
		$option_ftext = $post["option_ftext"];
		$option_file1 = $post["option_file1"];
		$option_order = $post["option_order"];
		$option_is_other = $post["option_is_other"];



		if (count($option_id) > 0) {
			foreach ($option_id as $key => $id) {
				$query = $db->getQuery(true);

				if ($id) { // 修改
					$fields = array(
						$db->quoteName('quest_id') . ' = ' . $db->quote($_question_id),
						$db->quoteName('ftext') . ' = ' . $db->quote($option_ftext[$key]),
						$db->quoteName('ordering') . ' = ' . $db->quote($option_order[$key]),
						$db->quoteName('is_other') . ' = ' . $db->quote($option_is_other[$key])
					);

					$conditions = array(
						$db->quoteName('id') . ' = ' . $db->quote($id)
					);

					$query->update($db->quoteName('#__survey_force_fields'))->set($fields)->where($conditions);

					$db->setQuery($query);

					$db->execute();
				} else { // 新增
					$columns = array('quest_id', 'ftext', 'ordering', 'is_other');

					$values = array(
						$db->quote($_question_id),
						$db->quote($option_ftext[$key]),
						$db->quote($option_order[$key]),
						$db->quote($option_is_other[$key])
					);

					$query->insert($db->quoteName('#__survey_force_fields'))->columns( $db->quoteName($columns) )->values(implode(',', $values));

					$db->setQuery($query);
					$db->execute();

					$id = $db->insertid();
				}

				
			}
		}


	}



	// 前台讀取選項表單與JS
	public function onGetOptionsHtml($_question, $_options, $_sub_options = null) {

		ob_start();
		include_once(JPATH_SITE . "/plugins/survey/open/site/html.php");
		include_once(JPATH_SITE . "/plugins/survey/open/site/js.php");
		$html = ob_get_contents();
		ob_clean();

		return $html;
	}



	

	// 檢查欄位是否有填寫、格式是否正確、是否是題目其中之一
	public function onCheckOptionField($_question, $_post) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		unset($result);
		$result = array();
		$result["status"] = 1;


		return json_encode($result);

	}


	// 儲存使用者的答案 (依efa_survey_force_vote_detail欄位做回傳)
	public function onSaveUserOption($_question, $_post) {
		$db = JFactory::getDBO();

		// 先取得該題目的所有選項
		$query = $db->getQuery(true);
		$query->select('id, ftext');
		$query->from($db->quoteName('#__survey_force_fields'));
		$query->where($db->quoteName('quest_id') . " = '{$_question->id}'");
		$query->order("ordering ASC");
		$db->setQuery($query);

		$db->setQuery($query);
		$question_options = $db->loadAssocList('id');

		unset($answers);
		$answers = array();
		$option_id = $_post["selected_option"];

		array_push( $answers, array("field_id" => $option_id, "other" =>  JFilterOutput::cleanText( trim($_post["other_field_". $option_id]) ), "logstr" => $question_options[$_post["selected_option"]]["ftext"] ) );


		return $answers;

	}


	
	// 後台列印選項
	public function onGetAdminPrintOptions($_question_id) {
		
		$content = "此題型無選項。";
		
		return $content;
	}


}