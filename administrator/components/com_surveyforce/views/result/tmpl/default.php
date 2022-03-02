<?php
/**
 * @package         Surveyforce
 * @version           1.2-modified
 * @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
//JHtml::_('formbehavior.chosen', 'select');

$app = JFactory::getApplication();

$quest_index = 0;  // 第幾題用
$field_count = []; // 票數
$total_count = []; // 總票數
$result_num = $this->item->result_num; // 顯示數目
$qtype = ["select", "number", "table"]; // 有子選項的題目類型
?>

<script type="text/javascript">
  Joomla.submitbutton = function (task) {
    if (task == "result.cancel" || document.formvalidator.isValid(document.id("result-form"))) {
      Joomla.submitform(task, document.getElementById("result-form"));
    } else {
      alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
    }
  };
</script>


<div id="j-main-container" class="form-horizontal span11">

    <form action="<?php echo JRoute::_("index.php?option=com_surveyforce&view=result&id=" . $this->surv_id); ?>"
          method="post" name="adminForm" id="adminForm" class="form-validate">
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="option" value="com_surveyforce"/>
        <?php echo JHtml::_('form.token'); ?>


        <h1><?php echo $this->item->title; ?></h1>

        <ul class="nav nav-tabs" id="configTabs">
            <li class="<?php echo $this->mark == 'total' ? 'active' : ''; ?>">
                <a href="#total" data-toggle="tab">總投票 / 驗證結果</a>
            </li>
            <li class="<?php echo $this->mark == 'day' ? 'active' : ''; ?>">
                <a href="#day" data-toggle="tab">每日投票結果</a>
            </li>
        </ul>

        <div class="tab-content">

            <?php /*--- 總投票 / 驗證結果 ---*/ ?>
            <div class="tab-pane <?php echo $this->mark == 'total' ? 'active' : ''; ?>" id="total">
                <?php if ($this->total_voters) { ?>
                    <div class="survey_total_voters">
					<?php
						// 總投票人數 = 網路 + 現地 + 紙本
						$total_vote = $this->total_voters + $this->item->paper_total_vote;
                        echo sprintf("<h2>總投票人數：%s (即時更新)</h2>", $total_vote);
                        if ($this->item->is_place) {
                            echo sprintf("<h2>網路投票人數：%s</h2>", $this->resultNum);
                            echo sprintf("<h2>現地投票人數：%s</h2>", $this->total_voters - $this->resultNum);
                        }
                        if ($this->item->paper_total_vote) {
                            echo sprintf("<h2>紙本投票人數：%s</h2>", $this->item->paper_total_vote);
                        }
						
						// 投票率計算
                        $percent = ($total_vote / $this->quantity->quantity) * 100;
                    ?>
					<?php if($this->item->state) { ?>
						<h2>投票率：<?php echo $total_vote; ?> / <?php echo $this->quantity->quantity; ?> (<?php
							echo sprintf("%.2f", $percent); ?>%)</h2>
					<?php } ?>
                    </div>
                    <br>
                    <?php /*--- 驗證方式 ---*/ ?>
                    <div class="vote-type">
                        <?php echo $this->loadTemplate("verify"); ?>
                    </div>

                    <br>
                    <?php /*--- 題目明細 ---*/ ?>
                    <div class="vote-type">
                        <?php echo $this->loadTemplate("total"); ?>
                    </div>
                <?php } else {
                    echo "無投票資料。";
                }
                ?>
            </div>


            <?php /*--- 每日投票結果 ---*/ ?>
            <div class="tab-pane <?php echo $this->mark == 'day' ? 'active' : ''; ?>" id="day">
                <?php
                if ($this->total_voters) {
                    echo $this->loadTemplate("day");
                } else {
                    echo "該日無投票資料。";
                }
                ?>
            </div>


        </div>


    </form>
</div>
