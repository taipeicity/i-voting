<?php
/**
 * @package         Surveyforce
 * @version           1.2-modified
 * @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
 * @license            GPL-2.0+
 * @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
 */
?>

<div class="qtable">
    <?php if (isset($this->weekOpenResults[$this->date][$this->key])) { ?>
        <table class="table">
            <thead>
            <tr>
                <th>開放式欄位</th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($this->weekOpenResults[$this->date][$this->key] as $other) {
                ?>
                <tr>
                    <td><?php echo $other; ?></td>
                </tr>
                <?php
            }
            ?>
            </tbody>
        </table>
    <?php } else { ?>
    <div class="day-nodata">該日無資料</div>
    <?php } ?>
</div>
