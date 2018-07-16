<?php

namespace dokuwiki\plugin\tplmod;

use dokuwiki\Menu\Item\AbstractItem;

/**
 * Class MenuItem
 *
 *
 * @package dokuwiki\plugin\tplmod
 */
class MenuItem extends AbstractItem {

    /** @var string do action for this plugin */
    protected $type = '';
    private  $btn_name;

    /** @var string icon file */   
     protected $svg = __DIR__ . '/06-revert_replay.svg';
  
    /**
     * MenuItem constructor.
     * @param string $btn_name (can be passed in from the  event handler)
     */
    public function __construct($btn_name = "") {
         parent::__construct();        
         $this->params['do']=""; 
         if($btn_name)  {
            $this->btn_name = $btn_name;     
         }               
         
    }

    /**
     * Get label from plugin language file
     *
     * @return string
     */
    public function getLabel() {        
        return $this->btn_name;
     }
    
     public function getLink() {
         return 'javascript:tplmod_toggle_aside();void(0);';
     }
}
