<?php
/**
 * @package            Surveyforce
 * @version            1.3-modified
 * @copyright          JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author             JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');

?>
<div class="survey_finish">

    <div class="page-header red">
		<?php if ($this->preview == false && SurveyforceVote::getSurveyData($this->survey_id, "resend_lottery") != "") { ?>
            補送抽獎資料成功
		<?php } else { ?>
            訊息寄送成功
		<?php } ?>
    </div>
    <div class="finish">
        <br> <br>

        <div class="btns">

			<?php if ($this->preview == true) { ?>
                <a href="<?php echo $this->back_link; ?>" class="submit">上一頁</a>
			<?php } else { ?>
				<?php if ($this->display_result == 1) { ?>
                    <a href="<?php echo $this->preview == false ? JRoute::_('index.php?option=com_surveyforce&view=result&sid=' . $this->survey_id . '&Itemid=' . $this->itemid, false) : "javascript:void(0)"; ?>" class="submit"> 觀看投票結果 </a>
				<?php } ?>
                <a class="btn" href="<?php echo $this->preview == false ? JURI::root() : "javascript:void(0)"; ?>" id="return_index"> 回首頁 </a>
			<?php } ?>

        </div>
    </div>


</div>