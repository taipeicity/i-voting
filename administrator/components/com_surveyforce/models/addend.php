<?php

/**
*   @package         Surveyforce
*   @version           1.2-modified
*   @copyright       JooPlce Team, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license            GPL-2.0+
*   @author            JooPlace Team, 臺北市政府資訊局- http://doit.gov.taipei/
*/

defined('_JEXEC') or die;

jimport('joomla.application.component.modeladmin');

/**
 * Addend model.
 *
 */
class SurveyforceModelAddend extends JModelList {

    protected $text_prefix = 'COM_SURVEYFORCE';

    public function __construct($config = array()) {


        parent::__construct($config);
    }


    protected function getListQuery() {

	}


}