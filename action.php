<?php
if (!defined('DOKU_INC')) 
{    
    die();
}

class action_plugin_tplmod extends DokuWiki_Action_Plugin {
    private $html_bg_color, $act_blocking;
    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, 'dwstarted');
        $controller->register_hook('TEMPLATE_SITETOOLS_DISPLAY', 'BEFORE', $this, 'action_link', array('site'));     
        $controller->register_hook('MENU_ITEMS_ASSEMBLY', 'AFTER', $this, 'addsvgbutton', array());
		$controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handle_act');
		
    }
    function __construct() {
         $ini = parse_ini_file( tpl_incdir() . 'style.ini');
         if(isset($ini['__background_alt__']))
         {
         $this->html_bg_color=$ini['__background_alt__'];         
         }
    }
    function dwstarted(DOKU_EVENT $event, $param) {
            global $INPUT, $JSINFO, $conf,$ID;
            $JSINFO['tmplft_template'] = $conf['template'];
            $JSINFO['tmplftacl'] = auth_quickaclcheck( $ID);
            $acl_levels = array('NONE'=>0,'READ'=>1,'EDIT'=>2,'CREATE'=>4,'UPLOAD'=>8);
            $JSINFO['tmplft_aclgen'] = $acl_levels[$this->getConf('acl_all')];  
            $background_color = $this->getConf('background_color');
            $background_color = trim($background_color);
            if(!empty($background_color)) {
                if($background_color == 'default') $background_color = $this->html_bg_color;
                 $JSINFO['tmplft_bgcolor'] = $background_color;   
            }
             
            
           $this->tools();
           
          $ips = $this->getConf('ips');
          $ips = trim($ips);
          if(!empty($ips)) {
             $remote_addr = $INPUT->server->str('REMOTE_ADDR'); 
             $ips = explode(',',$ips);
           }
           else {
               $ips = array();
               $remote_addr = "";
           }  
           $which = $this->getConf('rotatewhich');
           $dateorip = $this->getConf('dateorip');
   
           if($which == 'BOTH') {
               $this->logos($ips,$remote_addr,$dateorip);        
               $this->tags($ips,$remote_addr,$dateorip);  
           }
           else if($which == 'LOGO') {
               $this->logos($ips,$remote_addr,$dateorip);        
           }
           else if($which == 'TAG') {
               $this->tags($ips,$remote_addr,$dateorip);        
           }
           if($this->getConf('rotate_title')) {
              $this->wiki_names($ips,$remote_addr,$dateorip);                 
           }
           
           $profile = $this->getConf('profile');  
           if(!empty($profile)) {
               $JSINFO[tmplft_profile] = '1';
           }
           else $JSINFO['tmplft_profile'] = "";
           
          $search = $this->getConf('search');  
           if(!empty($search)) {
               $JSINFO[tmplft_search] = '1';
           }
          else $JSINFO['tmplft_search'] = "";
		  $this->act_blocking = $this->getConf('blocking');

           /*   debuging  
                if($JSINFO['tmplftacl'] == '255')  { msg('<pre>' . print_r($JSINFO,1) .'</pre>');}
           */

  }
  
   function logos($ips,$remote_addr, $dateorip) {      
         global $JSINFO;         
      
         if($dateorip == 'NEITHER') return;
         $logos = $this->getConf('logos');   
         if(empty($logos)) return;
         $logos = explode(',',$logos);
         
         if($dateorip == 'DAY') {
             $nday = date('w');            
        
             if(isset($logos[$nday])) {
                 $slot = $nday;
             }
             else $slot = 0;
             list($logo,$width) = preg_split("/\s+/", trim($logos[$slot]));                     
             $JSINFO['tmplft_logo'] = trim($logo);
             $JSINFO['tmplft_logo_width'] = trim($width);
              return;
             }

             for($i=0; $i<count($ips); $i++) {
                 $addr = trim($ips[$i]);                       
                if($remote_addr == $addr) {                   
                    if(!empty($logos[$i])) {
                        list($logo,$width) = preg_split("/\s+/", trim($logos[$i]));
                        $JSINFO['tmplft_logo'] = trim($logo);
                       $JSINFO['tmplft_logo_width'] = trim($width);
                    }
                else {
                    list($logo,$width) = preg_split("/\s+/", trim($logos[0]));                     
                    $JSINFO['tmplft_logo'] = trim($logo);
                    $JSINFO['tmplft_logo_width'] = trim($width);
                }   
               }
           }
       }

    function wiki_names($ips,$remote_addr, $dateorip) {      
         global $JSINFO;         
          if($dateorip == 'NEITHER') return;
         
         $names = $this->getConf('wiki_names');          
         $names = explode(',',$names);
 
         if($dateorip == 'DAY') {
             $nday = date('w');  
             if(isset($names[$nday])) {
                 $slot = $nday;
             }
             else $slot = 0;              
             $JSINFO['tmplft_title'] = trim($names[$slot]);
              return;
             }

         for($i=0; $i<count($ips); $i++) {
             $addr = trim($ips[$i]);          
             if($remote_addr == $addr) {                   
                 if(!empty($names[$i])) {                                        
                     $JSINFO['tmplft_title'] = trim($names[$i]);                    
                }
                else $JSINFO['tmplft_title'] = trim($names[0]);
           }
       }
   }  
   
    function tags($ips,$remote_addr, $dateorip) {      
         global $JSINFO;         
         
          $opt = $this->getConf('tag_date_format');
          if($opt) {
          $JSINFO['tmplft_tag'] = date($opt); 
          return;              
          }
      
         if($dateorip == 'NEITHER') return;
         $tags = $this->getConf('taglines');   
         if(empty($tags)) return;
         $tags = explode(',',$tags);
 
         if($dateorip == 'DAY') {
             $nday = date('w');  
             if(isset($tags[$nday])) {
                 $slot = $nday;
             }
             else $slot = 0;              
             $JSINFO['tmplft_tag'] = trim($tags[$slot]);
              return;
             }

         for($i=0; $i<count($ips); $i++) {
             $addr = trim($ips[$i]);          
             if($remote_addr == $addr) {                   
                 if(!empty($tags[$i])) {                                        
                     $JSINFO['tmplft_tag'] = trim($tags[$i]);                    
                }
                else $JSINFO['tmplft_tag'] = trim($tags[0]);
           }
       }
   }     
  
    function tools() {
             global $JSINFO, $INPUT;

            $sitetools = $this->getConf('sitetools') ;
            if(!empty($sitetools)) {
                $pat = array('/Changes/', '/Manager/','/Sitemap/', '/\s/' );
                $repl = array("","","index","");               
                $sitetools=strtolower(preg_replace($pat, $repl,$sitetools));       
                 $JSINFO['tmplft_sitetools']  = "$sitetools";          
            }
            else $JSINFO['tmplft_sitetools'] = "";
            
            $pagetools = $this->getConf('pagetools') ;
            if(!empty($pagetools)) {
                $pat = array('/Old/','/\s/ ', '/Backlinks/');
                $repl = array("","","backlink");     
                $pagetools=strtolower(preg_replace($pat, $repl,$pagetools));
                if(strpos($pagetools,'all') !== false) {
                   $pagetools_conf = 'edit,revisions,backlink,subscribe';
                   $pagetools  = '\w+';               
                }                
                $JSINFO['tmplft_pagetools'] = $pagetools;         
            }
            else $JSINFO['tmplft_pagetools'] = "";
            
             $JSINFO['tmplft_ptools_xcl'] =  "";
			 if(strpos($pagetools,'\w+') !== false) {
                 $xcl = $this->getConf('ptools_xcl');
                 $xcl = preg_replace("/\s/","",$xcl);
			 }
			 else $xcl = 'NONE';
             if(!empty($xcl)) $JSINFO['tmplft_ptools_xcl'] = $xcl;          
             
            $mobile_pt = explode(',',$JSINFO['tmplft_pagetools']); 
            $mobile_st = explode(',',$JSINFO['tmplft_sitetools']); 
            $mobile_ar = array_merge($mobile_pt,$mobile_st);
            $mobile_ar = array_unique($mobile_ar);
            $JSINFO['tmplft_mobile'] = implode('|',$mobile_ar);
            if(isset($pagetools_conf)) {  // $pagetools is set to \w+, i.e. all,
                $pt_conf = explode(',',$pagetools_conf);
                if(isset($JSINFO['tmplft_ptools_xcl'] )) {
                $pt_conf = array_diff($pt_conf, explode(',',$JSINFO['tmplft_ptools_xcl'] ));
                $actions_ar = array_merge($pt_conf,$mobile_st);
                $actions_ar = array_unique($actions_ar);
                $JSINFO['tmplft_actions'] = implode(',',$actions_ar);
                $JSINFO['tmplft_mobile'] = implode('|',$actions_ar);
            }
            else $JSINFO['tmplft_actions'] = implode(',',$mobile_ar);
         
             if($this->getConf('search')) {                
                    $JSINFO['tmplft_actions'] .= ',search';
                }
              if($this->getConf('profile')) {
                    $JSINFO['tmplft_actions'] .= ',profile';
                }
            }
            
    function action_link(&$event, $param)  {
         global  $ACT,$conf;
         $sbar = $this->getConf('toggle_sidebar');
         if($ACT != 'show' || !$sbar) return;     
         $name = $this->getLang('toggle_name');
         $event->data['items']['tplmod'] = '<li><a href="javascript:tplmod_toggle_aside();void(0);"  class="tplmodtoggle" rel="nofollow"   title="' .$name. '">'. $name.'</a></li>';
    }
    
    public function addsvgbutton(Doku_Event $event) {          
        global  $ACT;     
        if($event->data['view'] != 'site') return;
        $sbar = $this->getConf('toggle_sidebar');
        if($ACT != 'show' || !$sbar) return;     
        $btn = $this->getLang('toggle_name');    
        if(!$btn) $btn = 'Sidebar toggle';           
        array_splice($event->data['items'], -1, 0, [new \dokuwiki\plugin\tplmod\MenuItem($btn)]);
   }    
    public function handle_act(Doku_Event $event) {
	   global $JSINFO;
       
	   if(!$this->act_blocking){
		  return;
	   }
       
	   if(empty($JSINFO['tmplftacl'])) {
		   $JSINFO['tmplftacl']=0;
	   }
	   $acl = (($JSINFO['tmplftacl'] >= 0)  && $JSINFO['tmplftacl'] <= $JSINFO['tmplft_aclgen']) ? true: false;
       if(!$acl) return;
       $act = act_clean($event->data); 
	  
	   if(isset($JSINFO['tmplft_ptools_xcl']) && !empty($JSINFO['tmplft_ptools_xcl'])) {
		   if(strpos($JSINFO['tmplft_ptools_xcl'],$act) !== false) {
			   $event->data = 'show';
			   return;
		   }
	   }
	   if(strpos($JSINFO['tmplft_actions'],$act) !== false) {
		   $event->data = 'show';
		   return;
	   }	   
	  
   }	
           
}
