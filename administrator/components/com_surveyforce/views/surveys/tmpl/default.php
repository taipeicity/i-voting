<?php
/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die('Restricted Access');

JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
JHtml::_('dropdown.init');
JHtml::_('formbehavior.chosen', 'select');
$listOrder     = $this->state->get('list.ordering');
$listDirn      = $this->state->get('list.direction');
$saveOrder     = $ordering = $listOrder == 'ordering';
$user          = JFactory::getUser();
$userId        = $user->get('id');
$unit_id       = $user->get('unit_id');
$cross_unit    = $user->get('cross_unit');
$plugin        = JPluginHelper::getPlugin('system', 'switch');
$exercise_host = json_decode($plugin->params, true);

$extension = 'com_surveyforce';

$saveOrder = $listOrder == 'ordering';
if ($saveOrder) {
	$saveOrderingUrl = 'index.php?option=com_surveyforce&task=surveys.saveOrderAjax&tmpl=component';
	JHtml::_('sortablelist.sortable', 'surveyforceList', 'adminForm', strtolower($listDirn), $saveOrderingUrl);
}
$sortFields = $this->getSortFields();

$self_gps    = JUserHelper::getUserGroups($user->get('id'));
$core_review = JComponentHelper::getParams('com_surveyforce')->get('core_review');

$stage = [
	1 => JText::_("COM_SURVEYFORCE_CHECK"), 2 => JText::_("COM_SURVEYFORCE_REVIEW"), 3 => JText::_("COM_SURVEYFORCE_DISCUSS"), 4 => JText::_("COM_SURVEYFORCE_OPTIONS"), 5 => JText::_("COM_SURVEYFORCE_LAUNCHED"), 6 => JText::_("COM_SURVEYFORCE_RESULT")
];
?>
<?php // echo $this->loadTemplate('menu');                             ?>


<script type="text/javascript">
    Joomla.orderTable = function () {
        table = document.getElementById("sortTable");
        direction = document.getElementById("directionTable");
        order = table.options[table.selectedIndex].value;
        if (order != '<?php echo $listOrder; ?>') {
            dirn = 'asc';
        } else {
            dirn = direction.options[direction.selectedIndex].value;
        }
        Joomla.tableOrdering(order, dirn, '');
    };
    Joomla.submitbutton = function (task) {
        if (task == 'surveys.preview') {
            document.adminForm.target = '_blank';
            Joomla.submitform(task);
            document.adminForm.target = '';
        } else
            Joomla.submitform(task);
    };

    jQuery(document).ready(function () {

        jQuery("#btnFormTest").fancybox();


        jQuery("#btn_clear").bind("click", function () {
            jQuery("#filter_search").val("");
            jQuery("#adminForm").submit();
        });

        jQuery(".vote_url").bind("click", function () {
            var index = jQuery(".vote_url").index(this);
            var id = jQuery(".vote_url").eq(index).attr('id');
            console.log(id);
            jQuery("#btnFormLink" + id).fancybox();
            jQuery("#btnFormLink" + id).trigger('click');

        });

        jQuery(".survey_preview").on("show", function () {
            var BodyHeight = jQuery(window).height() - 200;
            jQuery(".preview-body").css("max-height", BodyHeight);
            jQuery("body").addClass("modal-open");
            var modalBody = jQuery(this).find('.preview-body');
            modalBody.find("iframe").remove();
            var id = jQuery(this).attr("itemid");
            modalBody.prepend("<iframe class=\"iframe\" src=\"index.php?option=com_surveyforce&amp;view=surveys&amp;layout=preview&amp;id=" + id + "&amp;tmpl=component\" name=\"議題預覽\" width=\"99%\"></iframe>");
            jQuery(".iframe").css("height", BodyHeight).css("border", 0);
        }).on("hide", function () {
            jQuery("body").removeClass("modal-open");
        });

    });


    function show_test(_survey_id) {
        jQuery("#test_survey_id").val(_survey_id);

        jQuery("#btnFormTest").trigger('click');

    }
</script>

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=surveys'); ?>" method="post" name="adminForm"
      id="adminForm">
	<?php if (!empty($this->sidebar)) : ?>
        <div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
        </div>
	<?php endif; ?>

    <div id="j-main-container" class="span10">
        <div id="filter-bar" class="btn-toolbar">
            <div class="filter-search btn-group pull-left">
                <label for="filter_search"
                       class="element-invisible"><?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG'); ?></label>
                <input type="text" name="filter_search" id="filter_search"
                       placeholder="<?php echo JText::_('COM_SURVEYFORCE_FILETERBYTAG'); ?>"
                       value="<?php echo $this->escape($this->state->get('filter.search')); ?>" />
            </div>
            <div class="btn-group pull-left">
                <button type="submit" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_SUBMIT'); ?>"><i
                            class="icon-search"></i></button>
                <button type="button" class="btn hasTooltip" title="<?php echo JText::_('JSEARCH_FILTER_CLEAR'); ?>"
                        id="btn_clear"><i class="icon-remove"></i></button>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="limit"
                       class="element-invisible"><?php echo JText::_('JFIELD_PLG_SEARCH_SEARCHLIMIT_DESC'); ?></label>
				<?php echo $this->pagination->getLimitBox(); ?>
            </div>
            <div class="btn-group pull-right hidden-phone">
                <label for="directionTable"
                       class="element-invisible"><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></label>
                <select name="directionTable" id="directionTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JFIELD_ORDERING_DESC'); ?></option>
                    <option value="asc" <?php if ($listDirn == 'asc')
						echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_ASCENDING'); ?></option>
                    <option value="desc" <?php if ($listDirn == 'desc')
						echo 'selected="selected"'; ?>><?php echo JText::_('JGLOBAL_ORDER_DESCENDING'); ?></option>
                </select>
            </div>
            <div class="btn-group pull-right">
                <label for="sortTable" class="element-invisible"><?php echo JText::_('JGLOBAL_SORT_BY'); ?></label>
                <select name="sortTable" id="sortTable" class="input-medium" onchange="Joomla.orderTable()">
                    <option value=""><?php echo JText::_('JGLOBAL_SORT_BY'); ?></option>
					<?php echo JHtml::_('select.options', $sortFields, 'value', 'text', $listOrder); ?>
                </select>
            </div>
        </div>
        <div class="clearfix"></div>
        <table class="table table-striped" id="testimonialsList" style="min-width:1100px;">
            <thead>
            <tr>

                <th width="1%" class="hidden-phone">
                    <input type="checkbox" name="checkall-toggle" value=""
                           title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
                </th>

                <th width="3%" class="nowrap center">
                    流程狀態
                </th>

                <th width="10%" class="nowrap center">
                    階段
                </th>

                <th class="nowrap center">
					<?php echo JHtml::_('grid.sort', '名稱', 's.title', $listDirn, $listOrder); ?>
                </th>

                <th width="10%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', '上架時間', 's.publish_up', $listDirn, $listOrder); ?>
                </th>

                <th width="10%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', '開始投票時間', 's.vote_start', $listDirn, $listOrder); ?>
                </th>

                <th width="10%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', '投票結束時間', 's.vote_end', $listDirn, $listOrder); ?>
                </th>

                <th width="10%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', '單位', 'ut.title', $listDirn, $listOrder); ?>
                </th>

                <th width="10%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', '承辦人員', 'u.name', $listDirn, $listOrder); ?>
                </th>

                <th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', '是否公開', 's.is_public', $listDirn, $listOrder); ?>
                </th>

                <th width="1%" class="nowrap center">
                    題目
                </th>

                <th width="5%" class="nowrap center">
                    功能
                </th>

                <th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'COM_SURVEYFORCE_ID', 's.id', $listDirn, $listOrder); ?>
                </th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <td colspan="13">
					<?php echo $this->pagination->getListFooter(); ?>
                </td>
            </tr>
            </tfoot>
            <tbody>
			<?php
			$date              = JFactory::getDate();
			$nowDate           = $date->toSql();

			foreach ($this->items as $i => $item) :
				$verify_type = json_decode($item->verify_type, true);
				$verify_params = json_decode($item->verify_params, true);

				$ordering   = ($listOrder == 'ordering');
				$canEdit    = $user->authorise('core.edit', $extension . '.surveys.' . $item->id);
				$canCheckin = $user->authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canChange  = $user->authorise('core.edit.state', $extension . '.surveys.' . $item->id) && $canCheckin;
				?>
                <tr class="row<?php echo $i % 2; ?>" sortable-group-id="1">

                    <td class="nowrap center">
						<?php echo JHtml::_('grid.id', $i, $item->id); ?>
                    </td>

                    <td class="center">
						<?php
						// 辨別議題狀態
						$is_processing = false;
						$is_finish     = false;
						if ($item->is_complete) {
							if ($item->is_checked) {
								if (strtotime($item->publish_up) < strtotime($nowDate)) {
									if ($item->release_stage > 5) {
										if (strtotime($item->vote_start) < strtotime($nowDate)) {
											if (strtotime($item->vote_end) < strtotime($nowDate)) {
												$is_finish = true;
												if (strtotime($item->publish_down) < strtotime($nowDate)) {
													echo "已下架";
												} else {
													echo JText::_("COM_SURVEYFORCE_LIST_STATUS_COMPLETED");
												}
											} else {
												$is_processing = true;
												echo JText::_("COM_SURVEYFORCE_LIST_STATUS_VOTING");
											}
										} else {
											echo JText::_("COM_SURVEYFORCE_LIST_STATUS_TO_BE_VOTE");
										}
									} else {
										echo JText::_("COM_SURVEYFORCE_LIST_STATUS_VOTING");
									}
								} else {
									echo JText::_("COM_SURVEYFORCE_LIST_STATUS_TO_BE_LAUNCHED");
								}
							} else {
								echo JText::_("COM_SURVEYFORCE_LIST_STATUS_TO_BE_CHECK");
							}
						} else {
							echo JText::_("COM_SURVEYFORCE_LIST_STATUS_DRAFT");
						}
						?>
                    </td>

                    <td class="center">
                        <?php
                        echo $stage[$item->release_stage] ? $stage[$item->release_stage] : "尚未送審";
                        ?>
                    </td>

                    <td class="nowrap">
                        <div style="white-space: nowrap; text-overflow: ellipsis; overflow: hidden; width:200px;">
							<?php if ($canEdit) : ?>
                                <a class="hasTooltip" href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=survey&layout=edit&id=' . $item->id); ?>"
                                   title=" <?php echo $this->escape($item->title); ?>"><?php echo $this->escape($item->title); ?></a>
							<?php else : ?>
								<?php echo $this->escape($item->title); ?>
							<?php endif; ?>
                        </div>
                    </td>

                    <td class="center">
						<?php echo JHtml::_('date', $item->publish_up, JText::_('DATE_FORMAT_LC5')); ?>
                    </td>

					<?php
					$check_stage = $item->release_stage >= 5 ? true : false;
					?>
                    <td class="center">
						<?php
						if ($check_stage) {
							echo JHtml::_('date', $item->vote_start, JText::_('DATE_FORMAT_LC5'));
						} else {
							echo JText::_('COM_SURVEYFORCE_LIST_VOTE_TIME');
						}
						?>

                    </td>

                    <td class="center">
						<?php
						if ($check_stage) {
							echo JHtml::_('date', $item->vote_end, JText::_('DATE_FORMAT_LC5'));
						} else {
							echo JText::_('COM_SURVEYFORCE_LIST_VOTE_TIME');
						}
						?>
                    </td>


                    <td class="center">
						<?php echo $item->unit_title; ?>
                    </td>

                    <td class="center">
						<?php echo $item->create_name; ?>
                    </td>

                    <td class="center">
						<?php
						if ($item->is_public) {
							echo "是";
						} else {
							echo "否";
						}
						?>
                    </td>
                    <td class=" center">
                        <a href="<?php echo JRoute::_('index.php?option=com_surveyforce&view=questions&surv_id=' . $item->id); ?>"><?php echo JText::_('COM_SURVEYFORCE_EDIT'); ?></a>
                    </td>

                    <td class="has-context">
						<?php
						// 作者 或 同單位審核者 或 最高權限 才可使用下列功能
						if ($item->created_by == $userId || ($item->unit_id == $unit_id && in_array($core_review, $self_gps)) || $this->canDo->get('core.own')) {
							?>
                            <div class="center">
								<?php
								unset($funs);
								$funs = array ();

								if ($item->questions == 1) {
									$funs[] = '<a href="#survey_preview' . $item->id . '" class="hasTooltip" role="button" data-toggle="modal" title="議題預覽">議題預覽</a>';
								}

								?>

                                <div style="display: none;" id="survey_preview<?php echo $item->id; ?>" itemid="<?php echo $item->id; ?>" tabindex="-1" class="modal hide fade survey_preview">
                                    <div class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal">×</button>
                                        <h3>議題預覽</h3>
                                    </div>
                                    <div class="preview-body">
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn" data-dismiss="modal" aria-hidden="true">關閉</button>
                                    </div>

                                </div>


								<?php
								if (($this->is_testsite == false && $this->testsite_link) && $item->stage >= 5) {
									$funs[] = '<a class="hasTooltip" href="javascript:void(0);" onclick="show_test(' . $item->id . ')" title="投票測試">投票測試</a>';
								}

								if($item->release_stage) {
									$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=print&surv_id=' . $item->id . '" title="議題列印" target="_blank">議題列印</a>';
								}

								if ($item->is_checked) {
									if ($item->is_public == 0) {
										$url        = JRoute::_($item->id . '-survey-intro' . '?token=' . JHtml::_('utility.endcode', $item->title), false);
										$url_prac   = JRoute::_(str_replace('/', '', $exercise_host['exercise_host']) . '/' . $item->id . '-survey-intro' . '?token=' . JHtml::_('utility.endcode', $item->title), false);
										$url_result = JRoute::_($item->id . '-survey-result' . '?token=' . JHtml::_('utility.endcode', $item->title), false);
									} else {
										$url        = JRoute::_($item->id . '-survey-intro', false);
										$url_prac   = JRoute::_(str_replace('/', '', $exercise_host['exercise_host']) . '/' . $item->id . '-survey-intro', false);
										$url_result = JRoute::_($item->id . '-survey-result', false);
									}

									?>
                                    <a href="#divFormLink<?php echo $item->id; ?>" id="btnFormLink<?php echo $item->id; ?>" title="投票網址" style="display:none">投票網址</a>
                                    <div id="divFormLink<?php echo $item->id; ?>" style="display:none;">
										<?php if ($item->vote_pattern != 2) { ?>
                                            <div>
                                                正式連結：<input type="text" title="正式連結" value="<?php echo JUri::root() . $url; ?>" size="50" style="width:350px;"><br>
                                                <a href="<?php echo '/' . $url; ?>" target="_blank" title="點此開啟前台頁面">點此開啟正式頁面</a>
                                            </div>
										<?php } ?>
										<?php if ($item->vote_pattern != 1) { ?>
                                            <div>
                                                練習連結：<input type="text" title="練習連結" value="<?php echo JUri::root() . $url_prac; ?>" size="50" style="width:350px;"><br>
                                                <a href="<?php echo '/' . $url_prac; ?>" target="_blank" title="點此開啟前台頁面">點此開啟練習頁面</a>
                                            </div>
										<?php } ?>
										<?php if (($item->display_result == 2 && strtotime(JHtml::_('date', $item->vote_end, JText::_('DATE_FORMAT_LC5'))) < time()) || $item->display_result == 1) { ?>
                                            <div>
                                                結果連結：<input type="text" title="結果連結" value="<?php echo JUri::root() . $url_result; ?>" size="50" style="width:350px;">
                                                <br>
                                                <a href="<?php echo '/' . $url_result; ?>" target="_blank" title="點此開啟結果頁面">點此開啟結果頁面</a>
                                            </div>
										<?php } ?>
                                    </div>


									<?php

									$funs[] = '<a class="hasTooltip vote_url" id="' . $item->id . '" href="javascript:void(0);" title="投票網址">投票網址</a>';


									// 開始投票後才會有資料
									if ($is_processing) {
										$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=result&surv_id=' . $item->id . '" title="觀看結果">觀看結果</a>';
										$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=import&surv_id=' . $item->id . '" title="匯入紙本">匯入紙本</a>';
										$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=resultnote&layout=edit&id=' . $item->id . '" title="結果說明">結果說明</a>';
										if (!preg_match('/none/', $item->verify_type)) {
											$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=voted&surv_id=' . $item->id . '" title="投票查詢">投票查詢</a>';
										}
									}

									// 投票結束後才能匯出結果 及 抽獎名單
									if ($is_finish) {
										$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=result&surv_id=' . $item->id . '" title="觀看結果">觀看結果</a>';
										$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=import&surv_id=' . $item->id . '" title="匯入紙本">匯入紙本</a>';
										$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=resultnote&layout=edit&id=' . $item->id . '" title="結果說明">結果說明</a>';
										if (!preg_match('/none/', $item->verify_type)) {
											$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=voted&surv_id=' . $item->id . '" title="投票查詢">投票查詢</a>';
										}
										$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=export&surv_id=' . $item->id . '" title="匯出結果">匯出結果</a>';
										$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=getip&surv_id=' . $item->id . '" title="投票來源">投票來源</a>';
										if ($item->is_lottery) {
											$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=lottery&surv_id=' . $item->id . '" title="抽獎名單">抽獎名單</a>';
										}
										// 投票結束半小時後才可觀看檢核紀錄
										if (time() - (strtotime($item->vote_end) + 28800) >= 1800) {
											$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=autocheck&surv_id=' . $item->id . '" title="檢核紀錄">檢核紀錄</a>';
										}
										if ($item->is_analyze) {
											$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=analyze&layout=result&surv_id=' . $item->id . '" title="投票行為分析">投票分析</a>';
										}
									} else {
										// 若為身分證驗證或是可投票人名單驗證，則可進行補送名單
										if (in_array('idnum', $verify_type) || in_array('assign', $verify_type) || (in_array('any', $verify_type) && array_key_exists('suffix', $verify_params['any']))) {
											$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=addend&surv_id=' . $item->id . '" title="補送名單">補送名單</a>';
										}
									}
								}

								if ($funs) {
									echo implode("<br>", $funs);
								}
								?>

                            </div>
							<?php // 若群組為管理-匯出結果、管理-觀看結果、管理-議題列印
						} else if (in_array($this->export_result, $user->groups) || in_array($this->print, $user->groups) || in_array($this->show_result, $user->groups)) {
							?>
                            <div class="center">
								<?php
								unset($funs);
								$funs = array ();

								if (in_array($this->print, $user->groups) && $item->release_stage) {
									$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=print&surv_id=' . $item->id . '" title="議題列印" target="_blank">議題列印</a>';
								}

								if ($item->is_checked) {

									// 開始投票後才會有資料
									if ($is_processing) {
										if (in_array($this->show_result, $user->groups)) {
											$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=result&surv_id=' . $item->id . '" title="觀看結果">觀看結果</a>';
										}
									}


									// 投票結束後才能匯出結果
									if ($is_finish) {
										if (in_array($this->show_result, $user->groups)) {
											$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=result&surv_id=' . $item->id . '" title="觀看結果">觀看結果</a>';

										}

										if (in_array($this->export_result, $user->groups)) {
											$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=export&surv_id=' . $item->id . '" title="匯出結果">匯出結果</a>';
										}
									}
								}

								if ($funs) {
									echo implode("<br>", $funs);
								}
								?>

                            </div>
							<?php
							// 若為同單位的承辦人群組
						} else if (($item->unit_id == $unit_id && in_array($this->undertaker, $self_gps)) || ($item->unit_id != $unit_id && $cross_unit == 1)) {
							?>
                            <div class="center">
								<?php
								unset($funs);
								$funs = array ();

								if ($item->is_checked) {
									if (!$is_finish) {
										// 若為身分證驗證或是可投票人名單驗證，則可進行補送名單
										if (in_array('idnum', $verify_type) || in_array('assign', $verify_type) || (in_array('any', $verify_type) && array_key_exists('suffix', $verify_params['any']))) {
											$funs[] = '<a class="hasTooltip" href="index.php?option=com_surveyforce&view=addend&surv_id=' . $item->id . '" title="補送名單">補送名單</a>';
										}
									}
								}

								if ($funs) {
									echo implode("<br>", $funs);
								}
								?>

                            </div>
						<?php } ?>
                    </td>


                    <td class="center">
						<?php echo $item->id; ?>
                    </td>

                </tr>
			<?php endforeach; ?>
            </tbody>
        </table>

        <input type="hidden" name="task" value="" /> <input type="hidden" name="boxchecked" value="0" />
        <input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
        <input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

		<?php echo JHtml::_('form.token'); ?>

    </div>

</form>


<a href="#divFormTest" id="btnFormTest" title="投票測試" style="display:none">投票測試</a>
<div id="divFormTest" style="display:none; width:450px;">
    <form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="testForm" id="testForm" target="_blank">
        *請先確認所有議題、題目皆已編輯完成，點擊"開始測試"按鈕後，系統將會把資料發佈至測試站台中，並將議題狀態自動修改為進行中投票。 <br><br>
        <input type="submit" id="test_btn" value="開始測試" style="width:80px; padding: 5px;"><br>
        <input type="hidden" id="test_survey_id" name="test_survey_id" value="">
        <input type="hidden" name="option" value="com_surveyforce"> <input type="hidden" name="task" value="testvote">
    </form>
</div>
