<?php
/**
*   @package         ITPGoogleSearch
*   @version         1.0-modified
*   @copyright       Todor Iliev, 臺北市政府資訊局, Copyright (C) 2016. All rights reserved.
*   @license         GPL-2.0+
*   @author          Todor Iliev, 臺北市政府資訊局- http://doit.gov.taipei/
*/

// no direct access
defined('JPATH_BASE') or die;

/**
 * Version information
 *
 * @package     ITPrism Components
 * @subpackage  ITPGoogleSearch
  */
class ItpGoogleSearchVersion {
	
    /**
     * Extension name
     * 
     * @var string
     */
    public $product    = 'ITPGoogleSearch';
    
    /**
     * Main Release Level
     * 
     * @var integer
     */
    public $release    = '2';
    
    /**
     * Sub Release Level
     * 
     * @var integer
     */
    public $devLevel   = '5';
    
    /**
     * Development Status
     * 
     * @var string
     */
    public $devStatus = 'Stable';
    
    /**
     * Release Type
     * 
     * @var integer
     */
    public $releaseType  = 'Lite';
    
    /**
     * Date
     * 
     * @var string
     */
    public $releaseDate= '11 April, 2014';
    
    /**
     * License
     * 
     * @var string
     */
    public $license  = '<a href="http://www.gnu.org/copyleft/gpl.html" target="_blank">GNU/GPL</a>';
    
    /**
     * Copyright Text
     * 
     * @var string
     */
    public $copyright  = '&copy; 2014 ITPrism. All rights reserved.';
    
    /**
     * URL
     * 
     * @var string
     */
    public $url        = '<a href="http://itprism.com/free-joomla-extensions/others/google-search-integration" target="_blank">ITPGoogleSearch</a>';
    
    /**
     * URL
     * 
     * @var string
     */
    public $backlink   = '<div style="width:100%; text-align: left; font-size: xx-small;margin: 10px 0;"><a href="http://itprism.com/free-joomla-extensions/others/google-search-integration" target="_blank">Joomla! Google Search</a></div>';
    
    /**
     * Developer
     * 
     * @var string
     */
    public $developer  = '<a href="http://itprism.com" target="_blank">ITPrism</a>';

    /**
     *  Build long format of the version text
     *
     * @return string Long format version
     */
    public function getLongVersion() {
        
    	return 
    	   $this->product .' '. $this->release .'.'. $this->devLevel .' ' . 
    	   $this->devStatus . ' '. $this->releaseDate;
    }
    
    /**
     *  Build medium format of the version text.
     *
     * @return string Medium format version
     */
    public function getMediumVersion() {
        
    	return 
    	   $this->release .'.'. $this->devLevel .' ' . 
    	   $this->releaseType . ' ( ' .$this->devStatus . ' )';
    }   

    /**
     *  Build short format of the version text
     *
     * @return string Short version format
     */
    public function getShortVersion() {
        return $this->release .'.'. $this->devLevel;
    }

}