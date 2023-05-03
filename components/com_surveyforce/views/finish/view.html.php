<?php

/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

/**
 * HTML View class for the Surveyforce Deluxe Component
 */
class SurveyforceViewFinish extends JViewLegacy {

	public function __construct() {
		parent::__construct();

	}

	public function display($tpl = null) {
		$app   = JFactory::getApplication();
		$model = $this->getModel();
		$config     = JFactory::getConfig();

		$this->itemid    = $app->input->getInt('Itemid');
		$this->survey_id = $app->input->getInt('sid');
		$this->preview   = false;

		$this->state  = $this->get('state');
		$this->params = $this->state->get('params');

		unset($prac);
		$session = &JFactory::getSession();
		$prac    = $session->get('practice');
		$is_testsite = $config->get( 'is_testsite', false );

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JFactory::getApplication()->enqueueMessage(implode('<br />', $errors), 'error');

			return false;
		}

		// Display the view
		$layout = $app->input->getString('layout', 'default');
		$this->setLayout($layout);

		// 檢查
		$category_link = JRoute::_("index.php?option=com_surveyforce&view=category&Itemid={$this->itemid}", false);
		$intro_link    = JRoute::_("index.php?option=com_surveyforce&view=intro&sid={$this->survey_id}&Itemid={$this->itemid}", false);
		$success_link  = JRoute::_("index.php?option=com_surveyforce&view=finish&layout=success&sid={$this->survey_id}&Itemid={$this->itemid}", false);


		// 檢查投票模式是否正確
		$result = json_decode(SurveyforceVote::checkVotePattern($this->survey_id), true);
		if ($result['status']) {
			$app->redirect($category_link, $result['msg']);
		}


		switch ($layout) {
			case "default":
				// 檢查議題是否有效
				if (SurveyforceVote::isSurveyValid($this->survey_id) == false) {
					$msg = "該議題目前未在可投票時間內，請重新選擇。";
					$app->redirect($category_link, $msg);
				}

				// 檢查是否閒置過久
				if (SurveyforceVote::isSurveyExpired($this->survey_id) == false) {
					$msg = "網頁已閒置過久，請重新點選議題進行投票。";
					$app->redirect($category_link, $msg);
				}

				// 檢查未公開議題是否有token碼
				if (SurveyforceVote::getSurveyItem($this->survey_id)->is_public == 0) {
					if (SurveyforceVote::checkSurveyStep($this->survey_id, "token") == false) {
						$msg = "該議題不存在，請重新選擇正確的議題。";
						$app->redirect($category_link, $msg);
					}
				}

				// 檢查是否有依序執行步驟
				if (SurveyforceVote::checkSurveyStep($this->survey_id, "finish") == false) {
					$msg = "未從該議題投票啟始頁進入，請重新執行。";
					$app->redirect($intro_link, $msg);
				}

				// 檢查是否通過留存步驟
				if (SurveyforceVote::checkSurveyStep($this->survey_id, "success")) {
					$app->redirect($success_link);
				}

				break;

			case "success":
				// 檢查是否有依序執行步驟
				if (SurveyforceVote::checkSurveyStep($this->survey_id, "success") == false) {
					$msg = "未從該議題投票啟始頁進入，請重新執行。";
					$app->redirect($intro_link, $msg);
				}

				break;
			case "resend":
				// 檢查議題是否有效
				if (SurveyforceVote::isSurveyValid($this->survey_id) == false) {
					$msg = "該議題目前未在可投票時間內，補送抽獎資料時間已截止。";
					$app->redirect($category_link, $msg);
				}

				// 檢查票號是否存在
				$ticket_num = $app->input->getString('ticket');
				if ($ticket_num == "") {
					$msg = "該票號不存在，請重新點選議題進行投票。";
					$app->redirect($category_link, $msg);
				} else {
					if ($model->getVoteDetail($ticket_num, $this->survey_id) == false) {
						$msg = "該議題不存在，請重新點選議題進行投票。";
						$app->redirect($category_link, $msg);
					} else {
						if (SurveyforceVote::checkJoinLottery($ticket_num, $this->survey_id)) {
							$msg = "該議題已補送抽獎資料。";
							$app->redirect($category_link, $msg);
						}

					}
				}

				break;
			default:
				$msg = "該議題不存在，請重新選擇正確的議題。";
				$app->redirect($category_link, $msg);
		}

		if (!$prac) {

			$survs = SurveyforceVote::getSurveyItem($this->survey_id); //取得議題資料

			if ($layout == "default") {

				$this->is_notice_email = SurveyforceVote::getSurveyData($this->survey_id, "is_notice_email");
				$this->is_notice_phone = SurveyforceVote::getSurveyData($this->survey_id, "is_notice_phone");
				$this->display_result  = SurveyforceVote::getSurveyData($this->survey_id, "display_result");
				$this->is_lottery      = SurveyforceVote::getSurveyData($this->survey_id, "is_lottery");


				if ($this->is_lottery) {
					// 判斷是否完成填抽獎資料的步驟
					if (SurveyforceVote::checkSurveyStep($this->survey_id, "lottery") == false) {
						$this->task           = 'setLotteryStep';
						$this->lottery_remind = false;
					} else {
						$this->task         = 'check_finish_form';
						$this->join_lottery = SurveyforceVote::checkSurveyStep($this->survey_id, "join_lottery");

						// 抽獎提醒文字只會出現一次
						if (SurveyforceVote::checkSurveyStep($this->survey_id, "check_column")) {
							if ($this->join_lottery) {
								$this->lottery_remind = false;
							} else {
								$this->lottery_remind = true;
							}
						} else {
							$this->lottery_remind = true;
						}
					}
				} else {
					$this->task           = 'check_finish_form';
					$this->lottery_remind = false;
				}


				$this->ticket_num = SurveyforceVote::getSurveyData($this->survey_id, "ticket");

				if ($this->task == "check_finish_form") {
                    $this->display_result = SurveyforceVote::getSurveyData($this->survey_id, "display_result");
                    $this->is_public = SurveyforceVote::getSurveyData($this->survey_id, "is_public");
                    $this->is_test = SurveyforceVote::getSurveyData($this->survey_id, "is_test");

                    // 取得短網址
					if ($this->ticket_num) {

					    if($this->is_test) {
					        $this->display_result = 1;
					        $this->is_public = 1;
                        }

						
						$vote_detail_url = JURI::root() . "vote/detail/" . $this->ticket_num;

						$this->short_url = JHtml::_('utility.getShortUrl', $vote_detail_url);
						if ($this->short_url == "") {
							$this->short_url = JHtml::_('utility.getShortUrl2', $vote_detail_url);  // 呼叫第2組API

							if ($this->short_url == "") {
								sleep(1);
								$this->short_url = JHtml::_('utility.getShortUrl3', $vote_detail_url);  // 呼叫第3組API
								if ($this->short_url == "") {
									$this->short_url = $vote_detail_url;
								}
							}
						}

						if ($this->is_lottery && $this->join_lottery == false) {
							$resend_url =  substr(JURI::root(),0,-1) . JRoute::_("index.php?option=com_surveyforce&view=finish&layout=resend&sid={$this->survey_id}&Itemid={$this->itemid}&ticket={$this->ticket_num}", false);
							$this->resend_short_url = JHtml::_('utility.getShortUrl', $resend_url);
							if ($this->resend_short_url == "") {
								$this->resend_short_url = JHtml::_('utility.getShortUrl2', $resend_url);  // 呼叫第2組API
								if ($this->resend_short_url == "") {
									sleep(1);
									$this->resend_short_url = JHtml::_('utility.getShortUrl3', $resend_url);  // 呼叫第3組API
									if ($this->resend_short_url == "") {
										$this->resend_short_url = $resend_url;
									}
								}
							}
							SurveyforceVote::setSurveyData($this->survey_id, "resend_short_url", $this->resend_short_url);
						}
					} else {
						$this->short_url = "";
						JFactory::getApplication()->enqueueMessage("未正確取得票號，請重新投票");
					}

					// 記錄短網址
					SurveyforceVote::setSurveyData($this->survey_id, "short_url", $this->short_url);
				}
			} else if ($layout == 'success') {

				$display_result = SurveyforceVote::getSurveyData($this->survey_id, "display_result");

				if ($display_result) {
					$this->display_result = $display_result;
					$session->clear('tmp_session');
				} else {
					$this->display_result = $survs->display_result;
				}

			} else {
				$this->task       = 'check_resend_form';
				$this->ticket_num = $app->input->getString('ticket');

				SurveyforceVote::setSurveyData($this->survey_id, "ticket_num", $this->ticket_num, true);
				SurveyforceVote::setSurveyData($this->survey_id, "expire_time", time() + (10 * 60));

			}
		} else {
			if ($layout != "default") {
				$msg = "網頁已閒置過久，請重新點選議題進行投票。";
				$app->redirect($category_link, $msg);
			} else {
				$this->display_result = SurveyforceVote::getSurveyData($this->survey_id, "display_result");
			}
		}

		if (!$this->item) {
			$this->item = $this->get('Item');
		}

		$document = JFactory::getDocument();
		$document->setTitle($this->escape($this->item->title));


		parent::display($tpl);

	}

}
