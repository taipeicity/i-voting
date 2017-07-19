<?php
/**
 * 表格式
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSurveyTable {

	// 讀取選項清單
	public function onGetAdminOptions($_question_id) {
		$db = JFactory::getDBO();

		// 取得選項
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__survey_force_fields'));
		$query->where($db->quoteName('quest_id') . " = '{$_question_id}'");
		$db->setQuery($query);

		$rows = $db->loadObjectList();


		// 取得子選項
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->quoteName('#__survey_force_sub_fields'));
		$query->where($db->quoteName('quest_id') . " = '{$_question_id}'");
		$db->setQuery($query);

		$sub_rows = $db->loadObjectList();

		ob_start();
		include_once(JPATH_SITE . "/plugins/survey/table/admin/html.php");
		include_once(JPATH_SITE . "/plugins/survey/table/admin/js.php");
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

		if (count($option_id) > 0) {
			foreach ($option_id as $key => $id) {
				$query = $db->getQuery(true);

				if ($id) { // 修改
					$fields = array(
						$db->quoteName('quest_id') . ' = ' . $db->quote($_question_id),
						$db->quoteName('ftext') . ' = ' . $db->quote($option_ftext[$key]),
						$db->quoteName('ordering') . ' = ' . $db->quote($option_order[$key]),
					);

					$conditions = array(
						$db->quoteName('id') . ' = ' . $db->quote($id)
					);

					$query->update($db->quoteName('#__survey_force_fields'))->set($fields)->where($conditions);

					$db->setQuery($query);

					$db->execute();
				} else { // 新增
					$columns = array('quest_id', 'ftext', 'ordering');

					$values = array(
						$db->quote($_question_id),
						$db->quote($option_ftext[$key]),
						$db->quote($option_order[$key]),
					);

					$query->insert($db->quoteName('#__survey_force_fields'))->columns( $db->quoteName($columns) )->values(implode(',', $values));

					$db->setQuery($query);
					$db->execute();

					$id = $db->insertid();
				}

				// 附件檔更新
				if ($option_file1[$key]) {
					$old_folder = pathinfo($option_file1[$key], PATHINFO_DIRNAME);
					$new_folder = $ivoting_path . "/survey/options";

					// 若檔案欄位資料不同，則移動檔案
					if ($new_folder != $old_folder) {
						$new_file = $new_folder . "/" . $id . "_file1_" . pathinfo($option_file1[$key], PATHINFO_BASENAME);
						JFile::move(JPATH_SITE . "/" . $option_file1[$key], JPATH_SITE . "/" . $new_file);
					} else {
						$new_file = $option_file1[$key];
					}
				} else {
					$new_file = "";
				}

				// 讀取舊的檔案欄位，判斷是否要刪除
				$query = $db->getQuery(true);
				$query->select('file1');
				$query->from($db->quoteName('#__survey_force_fields'));
				$query->where($db->quoteName('id') . " = '{$id}'");
				$db->setQuery($query);
				$file1 = $db->loadResult();

				if ($file1 && ($file1 != $new_file)) {
					JFile::delete(JPATH_SITE . "/" . $file1);
				}


				$query = $db->getQuery(true);
				$query->update($db->quoteName('#__survey_force_fields'));
				$query->set($db->quoteName('file1') . " = ". $db->quote($new_file));
				$query->where($db->quoteName('id') . " = '{$id}'");

				$db->setQuery($query);
				$db->execute();
			}
		}


		// 刪除選項
		$del_option_ids = $post["del_option_ids"];
		if ($del_option_ids) {
			// 讀取要刪除的筆數的檔案欄位
			$query = $db->getQuery(true);
			$query->select('id, file1');
			$query->from($db->quoteName('#__survey_force_fields'));
			$query->where($db->quoteName('id') . " IN (" . $del_option_ids . ")");
			$db->setQuery($query);
			$file1s = $db->loadAssocList('id', 'file1');

			foreach ($file1s as $file1) {
				if ($file1 && ($file1 != $new_file)) {
					JFile::delete(JPATH_SITE . "/" . $file1);
				}
			}

			$query = $db->getQuery(true);
			$conditions = array(
				$db->quoteName('quest_id') . ' = ' . $db->quote($_question_id),
				$db->quoteName('id') . ' IN (' . $del_option_ids . ')'
			);
			$query->delete($db->quoteName('#__survey_force_fields'));
			$query->where($conditions);

			$db->setQuery($query);
			$db->execute();
		}









		// 儲存子選項
		// 新增與修改選項
		$sub_option_id = $post["sub_option_id"];
		$sub_option_title = $post["sub_option_title"];
		$sub_option_order = $post["sub_option_order"];

		if (count($sub_option_id) > 0) {
			foreach ($sub_option_id as $key => $id) {
				$query = $db->getQuery(true);

				if ($id) { // 修改
					$fields = array(
						$db->quoteName('quest_id') . ' = ' . $db->quote($_question_id),
						$db->quoteName('title') . ' = ' . $db->quote($sub_option_title[$key]),
						$db->quoteName('ordering') . ' = ' . $db->quote($sub_option_order[$key]),
					);

					$conditions = array(
						$db->quoteName('id') . ' = ' . $db->quote($id)
					);

					$query->update($db->quoteName('#__survey_force_sub_fields'))->set($fields)->where($conditions);

					$db->setQuery($query);

					$db->execute();
				} else { // 新增
					$columns = array('quest_id', 'title', 'ordering');

					$values = array(
						$db->quote($_question_id),
						$db->quote($sub_option_title[$key]),
						$db->quote($sub_option_order[$key]),
					);

					$query->insert($db->quoteName('#__survey_force_sub_fields'))->columns( $db->quoteName($columns) )->values(implode(',', $values));

					$db->setQuery($query);
					$db->execute();

					$id = $db->insertid();
				}

			}
		}


		// 刪除選項
		$del_sub_option_ids = $post["del_sub_option_ids"];
		if ($del_sub_option_ids) {

			$query = $db->getQuery(true);
			$conditions = array(
				$db->quoteName('quest_id') . ' = ' . $db->quote($_question_id),
				$db->quoteName('id') . ' IN (' . $del_sub_option_ids . ')'
			);
			$query->delete($db->quoteName('#__survey_force_sub_fields'));
			$query->where($conditions);

			$db->setQuery($query);
			$db->execute();
		}


	}



	// 前台讀取選項表單與JS
	public function onGetOptionsHtml($_question, $_options, $_sub_options = null) {

		ob_start();
		include_once(JPATH_SITE . "/plugins/survey/table/site/html.php");
		include_once(JPATH_SITE . "/plugins/survey/table/site/js.php");
		$html = ob_get_contents();
		ob_clean();

		return $html;
	}



	

	// 檢查欄位是否有填寫、格式是否正確、是否是題目其中之一
	public function onCheckOptionField($_question, $_post) {
		$db = JFactory::getDBO();

		// 先取得該題目的所有選項
		$query = $db->getQuery(true);
		$query->select('id');
		$query->from($db->quoteName('#__survey_force_fields'));
		$query->where($db->quoteName('quest_id') . " = '{$_question->id}'");
		$query->order("ordering ASC");
		$db->setQuery($query);

		$db->setQuery($query);
		$question_options = $db->loadColumn();

		// 取得所有子選項
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from($db->quoteName('#__survey_force_sub_fields'));
		$query->where($db->quoteName('quest_id') . " = '{$_question->id}'");
		$query->order("ordering ASC");
		$db->setQuery($query);

		$db->setQuery($query);
		$question_sub_options = $db->loadAssocList('id');


		unset($result);
		$result = array();

		unset($msges);
		$msges = array();


		unset($sub_option_value);
		$sub_option_value = array();

		// 檢查是否都有填寫、是否正確
		foreach ($question_options as $option_id) {
			if ($_post["option_field_". $option_id] == "") {
				$msges[] = "請選擇其中一項。";
			} else {
				if ( !is_numeric($_post["option_field_". $option_id]) ) {
					$msges[] = "選項不正確，請重新選擇。";
				} else {
					if ( $question_sub_options[$_post["option_field_". $option_id]] ) {		// 若有值，表示為子選項之一
						$sub_option_value[] = $_post["option_field_". $option_id];
					} else {
						$msges[] = "表格式選項發生問題，請重新選擇。";
					}

				}
			}
		}


		if (count($msges) > 0) {
			$result["status"] = 0;
			$result["msg"] = implode("<br>", $msges);
		} else {
			$result["status"] = 1;
		}



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


		// 取得所有子選項
		$query = $db->getQuery(true);
		$query->select('id, title');
		$query->from($db->quoteName('#__survey_force_sub_fields'));
		$query->where($db->quoteName('quest_id') . " = '{$_question->id}'");
		$query->order("ordering ASC");
		$db->setQuery($query);

		$db->setQuery($query);
		$question_sub_options = $db->loadAssocList('id');



		unset($answers);
		$answers = array();

		foreach ($question_options as $option_id => $option) {

			array_push( $answers, array("field_id" => $option_id, "sub_field_id" =>  $_post["option_field_". $option_id], "logstr" => $option["ftext"]. "-".  $question_sub_options[$_post["option_field_". $option_id]]["title"]) );

		}


		return $answers;

	}


	// 後台列印選項
	public function onGetAdminPrintOptions($_question_id) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__survey_force_fields'));
		$query->where($db->quoteName('quest_id') . " = '{$_question_id}'");
		$query->order('ordering ASC');
		$db->setQuery($query);

		$options = $db->loadObjectList();


		if ($options) {
			// 取得子選項
			$query = $db->getQuery(true);
			$query->select('*');
			$query->from($db->quoteName('#__survey_force_sub_fields'));
			$query->where($db->quoteName('quest_id') . " = '{$_question_id}'");
			$db->setQuery($query);

			$sub_options = $db->loadObjectList();


			ob_start();
			echo '<table border="0" class="option-list">';
			echo '<tr>';
			echo '<td>#</td>';
			echo '<td>選項名稱</td>';
			echo '</tr>';
			foreach ($options as $key => $option) {
				echo '<tr>';
				echo '<td>'. ($key+1). '</td>';
				echo '<td>'. $option->ftext. '</td>';
				echo '</tr>';
			}
			echo '</table>';
			if ($sub_options) {
				echo '<hr>';
				echo '子選項清單<br>';
				echo '<table border="0" class="suboption-list">';
				echo '<tr>';
				echo '<td>#</td>';
				echo '<td>子選項名稱</td>';
				echo '</tr>';
				foreach ($sub_options as $key => $option) {
					echo '<tr>';
					echo '<td>'. ($key+1). '</td>';
					echo '<td>'. $option->title. '</td>';
					echo '</tr>';
				}
				echo '</table>';
			}


			$content = ob_get_contents();
			ob_clean();
		}

		return $content;
	}

	
}