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
    <table class="table">
        <thead>
        <tr>
            <th>開放式欄位</th>
        </tr>
        </thead>
        <tbody>
        <?php
        foreach ($this->open as $open) {
            if ($open->question_id == $this->key) {
                ?>
                <tr>
                    <td><?php echo $open->other; ?></td>
                </tr>
                <?php
            }
        }
        ?>
        </tbody>
    </table>
</div>
