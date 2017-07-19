<?php
/**
 * 影音式
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSurveyVideo {

	public function plgSurveyVideo() {
		return true;
	}


	// 讀取選項清單
	public function onGetAdminOptions($_question_id) {
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*');
		$query->from($db->quoteName('#__survey_force_fields'));
		$query->where($db->quoteName('quest_id') . " = '{$_question_id}'");
		$query->order('ordering ASC');

		$db->setQuery($query);
		$rows = $db->loadObjectList();

		ob_start();
		include_once(JPATH_SITE . "/plugins/survey/video/admin/html.php");
		include_once(JPATH_SITE . "/plugins/survey/video/admin/js.php");
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

				unset($matches);
				preg_match("/^(?:http(?:s)?:\/\/)+(?:www\.)?(?:m\.)?(?:youtu\.be\/|youtube\.com\/(?:(?:watch)?\?(?:.*&)?v(?:i)?=|(?:embed|v|vi|user)\/))([^\?&\"'>]+)/", $option_file1[$key], $matches);
				$video_id = $matches[1];
	

				if ($id) { // 修改
					$fields = array(
						$db->quoteName('quest_id') . ' = ' . $db->quote($_question_id),
						$db->quoteName('ftext') . ' = ' . $db->quote($option_ftext[$key]),
						$db->quoteName('file1') . ' = ' . $db->quote($video_id),
						$db->quoteName('ordering') . ' = ' . $db->quote($option_order[$key])
					);

					$conditions = array(
						$db->quoteName('id') . ' = ' . $db->quote($id)
					);

					$query->update($db->quoteName('#__survey_force_fields'))->set($fields)->where($conditions);

					$db->setQuery($query);

					$db->execute();
				} else { // 新增
					$columns = array('quest_id', 'ftext', 'file1', 'ordering');

					$values = array(
						$db->quote($_question_id),
						$db->quote($option_ftext[$key]),
						$db->quote($video_id),
						$db->quote($option_order[$key])
					);

					$query->insert($db->quoteName('#__survey_force_fields'))->columns( $db->quoteName($columns) )->values(implode(',', $values));

					$db->setQuery($query);
					$db->execute();

					$id = $db->insertid();
				}


			}
		}


		// 刪除選項
		$del_option_ids = $post["del_option_ids"];
		if ($del_option_ids) {
			// 讀取要刪除的筆數的檔案欄位
			$query = $db->getQuery(true);
			$query->select('file1');
			$query->from($db->quoteName('#__survey_force_fields'));
			$query->where($db->quoteName('id') . " IN (" . $del_option_ids . ")");
			$db->setQuery($query);
			$rows = $db->loadObjectList();

			foreach ($rows as $row) {

				if ($row->file1 && ($row->file1 != $new_file)) {
					JFile::delete(JPATH_SITE . "/" . $row->file1);
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
	}


	// 前台讀取選項表單與JS
	public function onGetOptionsHtml($_question, $_options, $_sub_options = null) {

		ob_start();
		include_once(JPATH_SITE . "/plugins/survey/video/site/html.php");
		include_once(JPATH_SITE . "/plugins/survey/video/site/js.php");
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

		unset($msges);
		$msges = array();


		unset($option_ids);
		$option_ids = array();
		if ($_question->is_multi) {		// 複選
			$selected_option_count = count($_post["selected_option"]);

			if ($_question->multi_limit > 0) { // 限定應投幾項
				if ( $selected_option_count != $_question->multi_limit ) {
					$msges[] = "限定應投". $_question->multi_limit. "項";
				} else {
					foreach ($_post["selected_option"] as $option_id) {		// 記錄選項ID
						$option_ids[] = $option_id;
					}
				}

			} else {	// 可投幾項
				if ($selected_option_count < $_question->multi_min ||  $selected_option_count > $_question->multi_max ) {
					$msges[] = "可投". $_question->multi_min. "至". $_question->multi_max. "項。";
				} else {
					foreach ($_post["selected_option"] as $option_id) {		// 記錄選項ID
						$option_ids[] = $option_id;
					}
				}

			}

		} else {		// 單選
			if ( $_post["selected_option"] == "" ) {
				$msges[] = "請選擇其中一項。";
			} else {	// 記錄選項ID
				$option_ids[] = $_post["selected_option"];
			}

		}

		// 是否是題目其中之一
		if (count($option_ids) > 0) {
			// 先取得該題目的所有選項
			$query->select('id');
			$query->from($db->quoteName('#__survey_force_fields'));
			$query->where($db->quoteName('quest_id') . " = '{$_question->id}'");
			$db->setQuery($query);

			$db->setQuery($query);
			$question_options = $db->loadColumn();

			foreach ($option_ids as $option_id) {		// 是否有在陣列中
				if ( !in_array($option_id, $question_options) ) {
					$msges[] = "所選擇的選項不屬於該題目之一。";
					break;
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

		unset($answers);
		$answers = array();
		if ($_question->is_multi) {		// 複選
			$selected_option_count = count($_post["selected_option"]);

			foreach ($_post["selected_option"] as $option_id) {		// 記錄選項ID
				$option_ids[] = $option_id;
				array_push( $answers, array("field_id" => $option_id, "logstr" => $question_options[$option_id]["ftext"]) );
			}


		} else {		// 單選
			array_push( $answers, array("field_id" => $_post["selected_option"], "logstr" => $question_options[$_post["selected_option"]]["ftext"]) );
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
			ob_start();
			echo '<table border="0" class="option-list">';
			echo '<tr>';
			echo '<td>#</td>';
			echo '<td>選項名稱</td>';
			echo '<td>影片</td>';
			echo '</tr>';
			foreach ($options as $key => $option) {
				echo '<tr>';
				echo '<td>'. ($key+1). '</td>';
				echo '<td>'. $option->ftext. '</td>';
				echo '<td>';
				echo '<video width="320px" controls>';
				echo '<source src="'. JURI::root() . $option->file1. '"" type="video/mp4">';
				echo '</video>';
				echo '</td>';
				echo '</tr>';
			}
			echo '</table>';


			$content = ob_get_contents();
			ob_clean();
		}

		return $content;
	}


}