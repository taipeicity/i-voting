<?php
/**
 * @package         Surveyforce
 * @version           1.2-modified
 * @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */

foreach ($this->range as $this->date) {
    echo sprintf("<h2>%s</h2>", $this->date);
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
                    echo $this->loadTemplate("day_open");
                } else {
                    if ($this->sub_fields[$this->key]) {
                        // 子選項
                        echo $this->loadTemplate("day_sub_fields");
                    } else {
                        // 無子選項
                        echo $this->loadTemplate("day_fields");
                    }
                }
                ?>
            </div>
        </div>
        <?php
    }
}

if ($this->totalPage > 1) {
    echo $this->loadTemplate("pagination");
}
?>