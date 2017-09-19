<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');

$app = JFactory::getApplication();
$surv_id = $app->input->getInt("surv_id");
$items = $this->item;

$Users = $this->BackStageRecord_User;
$Ips = $this->BackStageRecord_Ip;
$Nums = $this->VoteLogSum;
?>

<script type="text/javascript">


    Joomla.submitbutton = function (task)
    {
        if (task == 'autocheck.cancel' || document.formvalidator.isValid(document.id('autocheck-form'))) {
            Joomla.submitform(task, document.getElementById('autocheck-form'));
        } else {
            alert('<?php echo $this->escape(JText::_('JGLOBAL_VALIDATION_FORM_FAILED')); ?>');
        }
    }


</script>

<form action="<?php echo JRoute::_('index.php?option=com_surveyforce&view=autocheck'); ?>" method="post" name="autocheck-form" id="autocheck-form" class="form-validate">
    <input type="hidden" name="task" value="" />
    <input type="hidden" name="option" value="com_surveyforce" />
    <?php echo JHtml::_('form.token'); ?>
</form>

<div class="fontsize">

    <p class="">投票時間：<span><?php echo JHtml::_('date', $items->vote_start, JText::_('DATE_FORMAT_LC5')); ?>&nbsp;~&nbsp;<?php echo JHtml::_('date', $items->vote_end, JText::_('DATE_FORMAT_LC5')); ?></span></p>

    <div class="vote_record">
        <strong>投票紀錄</strong>
        <table>
            <tr>
                <td rowspan="2" width="32%">投票前</td>
                <td width="47%">票數統計</td>
                <td width="21%"><?php echo $this->before_votenum->num; ?>票</td>
            </tr>
            <tr>
                <td>投票者統計</td>
                <td><?php echo $this->before_peopleNum->num; ?>位</td>
            </tr>
            <tr>
                <td rowspan="2">投票後</td>
                <td>票數統計</td>
                <td><?php echo $this->after_votenum->num; ?>票</td>
            </tr>
            <tr>
                <td>投票者統計</td>
                <td><?php echo $this->after_peopleNum->num; ?>位</td>
            </tr>
        </table>
    </div>	

    <div class="login_record">
        <?php if ($Users) { ?>
            <strong>後台登入紀錄</strong>
            <table>

                <?php
                foreach ($Users as $i => $User) {
                    ?>
                    <tr>
                        <?php
                        if ($i == 0) {
                            if (count($Users) == 1) {
                                ?>                    
                                <td width="27%">依使用者</td>
                            <?php } else { ?>
                                <td rowspan="<?php echo count($Users); ?>"  width="27%">依使用者</td>
                                <?php
                            }
                        }
                        ?>
                        <td width="47%"><?php echo $User->name; ?></td>
                        <td width="26%"><?php echo $User->title; ?></td>
                    </tr>
                    <?php
                }
                ?>

                <?php
                foreach ($Ips as $j => $Ip) {
                    ?>
                    <tr>
                        <?php
                        if ($j == 0) {
                            if (count($Ips) == 1) {
                                ?>       
                                <td>依IP</td>
                            <?php } else { ?>
                                <td rowspan="<?php echo count($Ips); ?>">依IP</td>
                                <?php
                            }
                        }
                        ?>
                        <td><?php echo $Ip->user_ip; ?></td>
                        <td><?php echo $Ip->title; ?></td>
                    </tr>
                    <?php
                }
                ?>
            </table>
        <?php } else { ?>
            <strong>後台登入紀錄無資料</strong>
        <?php } ?>
    </div>

    <div class="vote_log">
        <?php if ($Nums) { ?>
            <strong>投票日誌檔統計</strong>

            <table>
                <tr>
                    <td width="50%">日期</td>
                    <td width="50%">投票者人數</td>
                </tr>
                <?php
                $sum = 0;
                foreach ($Nums as $Num) {
                    ?>
                    <tr>
                        <td><?php echo $Num->vote_date; ?></td>
                        <td><?php echo $Num->num; ?></td>
                    </tr>
                    <?php
                    $sum += $Num->num;
                }
                ?>
                <tr>
                    <td>總計</td>
                    <td><?php echo $sum; ?>位</td>
                </tr>
            </table>
        <?php } else { ?>
            <strong>投票日誌檔統計無資料</strong>
        <?php } ?>

    </div>

</div>
