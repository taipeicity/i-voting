<?php
/**
 * @package         Surveyforce
 * @version           1.0-modified
 * @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
?>

<h3>各題投票明細（每10分鐘進行更新）</h3>
<?php
$quest_index = 0;
foreach ($this->questions as $this->key => $this->question) {
    $quest_index++;
    ?>
    <div class="result_block">
        <div class="quest_title">
            <?php
            echo (count($this->questions) > 1) ? "第".$quest_index."題：" : "";
            echo $this->question->quest_title;
            ?>
        </div>

        <div class="quest_result">
            <?php // 開放式欄位 ?>
            <?php if ($this->question->quest_type == "open") {
                echo $this->loadTemplate("total_open");
            } else {
                if ($this->sub_fields[$this->key]) {
                    // 子選項
                    echo $this->loadTemplate("total_sub_fields");
                } else {
                    // 無子選項
                    echo $this->loadTemplate("total_fields");
                }
            }
            ?>
        </div>
    </div>
    <?php
}
?>