<?php
if (!defined('DOKU_INC')) 
{    
    die();
}

class action_plugin_tplmod extends DokuWiki_Action_Plugin {
    private $html_bg_color, $act_blocking, $ui_priority_metafn, $xcl_sbar;
    function register(Doku_Event_Handler $controller) {
        $controller->register_hook('DOKUWIKI_STARTED', 'BEFORE', $this, 'dwstarted');
        $controller->register_hook('TEMPLATE_SITETOOLS_DISPLAY', 'BEFORE', $this, 'action_link', array('site'));     
        $controller->register_hook('MENU_ITEMS_ASSEMBLY', 'AFTER', $this, 'addsvgbutton', array());
		$controller->register_hook('ACTION_ACT_PREPROCESS', 'BEFORE', $this, 'handle_act');
        $controller->register_hook('HTML_UPDATEPROFILEFORM_OUTPUT', 'BEFORE', $this, 'handle_profile_form');            
        $controller->register_hook('AJAX_CALL_UNKNOWN', 'BEFORE', $this,'_ajax_call');             
  		
    }
    function __construct() {
	    $this->ui_priority_metafn = metaFN(':tplmod:ui_lang', '.ser');
        if(!file_exists($this->ui_priority_metafn)) {
           io_saveFile($this->ui_priority_metafn, serialize(array()));       
        }
  
         $ini = parse_ini_file( tpl_incdir() . 'style.ini');
         if(isset($ini['__background_alt__']))
         {
         $this->html_bg_color=$ini['__background_alt__'];         
         }
    }

	function _ajax_call(Doku_Event $event, $param) { 
	    if ($event->data != 'tplmod_ui_lang' ) {
			return;
		}	
        global $INPUT;
        $event->stopPropagation();
        $event->preventDefault();              
        $ar = unserialize(file_get_contents($this->ui_priority_metafn));
        $ln = $INPUT->str('tplmod_val');
        $client = $INPUT->str('tplmod_client');
        $ar[$client] = $ln;
         $retv = file_put_contents($this->ui_priority_metafn,serialize($ar));  
         if($retv === false) {
             echo $this->ui_priority_metafn;            
         }
         else echo "done";
         return;             
	}
	
    function handle_profile_form(Doku_Event $event, $param) {
		 $language = trim($this->getConf('deflang'));
		 if(!isset($language)|| empty($language)) {
			 return;
		 }		 
         $language = explode( ',',$language);

		 $client =   $_SERVER['REMOTE_USER'];
         $ar = unserialize(file_get_contents($this->ui_priority_metafn));   
         if(isset($ar[$client])) {
             $lan = $ar[$client];
         }    
         $pos = $event->data->findElementByAttribute('type', 'reset');
         $_form =  "\n" . '</div></form><br /><form name="tplmodform" action="#"><div class="no">';
         $_form.= '<fieldset ><legend>' . $this->getLang('uprofile_title') .'</legend>';
            
         $num_langs = 0;
         foreach($language as $ln) {
               $checked = "";
               list($name,$val) = preg_split("/\s+/",$ln);        
               $_form .= '<label><span>' .$name .'</span> ';
               $val = strtolower(trim($val));
               if($lan == $val) {
                   $checked = 'checked';
               }

               $_form .='<input type = "radio" value = "' . $val . '" name= "tplmod_selector" ' . $checked .'>&nbsp;&nbsp;&nbsp;</label>';			
			    if( $num_langs > 0 &&  $num_langs % 3 == 0) {
				    $_form .= "<br />\n";
				 }		
				 if($num_langs == 0) {
					  $num_langs++;
				 }
			    $num_langs++;
         }
         $_form.= '<br /><label><span><b>User Name: </b></span> ';
         $_form.= '<input type="textbox" name="tplmod_client" disabled value="' .  $client .'"/></label>';
         $_form.= '<br /><br /><input type="button" value="Save" class="button" ' . "onclick='tplmod_setui_lang(this.form.tplmod_selector.value,this.form.tplmod_client.value,this.form.tplmod_selector);' />&nbsp;";
         $_form.= '<input type="reset" value="Reset" class="button" />';
         $_form.= '</fieldset>';           
         $event->data->insertElement($pos+2, $_form);
    }		
    
    function dwstarted(DOKU_EVENT $event, $param) {
            global $INPUT, $JSINFO, $conf,$ID,$USERINFO;  
          
            if(file_exists($this->ui_priority_metafn)) {
               $client = $_SERVER['REMOTE_USER'];       
               $ar = unserialize(file_get_contents($this->ui_priority_metafn));              
               if(isset($ar[$client])) {
                  $ln = $ar[$client];              
                  init_lang($ln);	
	        	  $conf['lang']= $ln;                   
               }
            }            

            $JSINFO['tmplft_template'] = $conf['template'];
            $JSINFO['tmplftacl'] = auth_quickaclcheck( $ID);
            $acl_levels = array('NONE'=>0,'READ'=>1,'EDIT'=>2,'CREATE'=>4,'UPLOAD'=>8,'DELETE'=>16);
            $JSINFO['tmplft_aclgen'] = $acl_levels[$this->getConf('acl_all')];  
            $background_color = $this->getConf('background_color');
            $background_color = trim($background_color);
            if(!empty($background_color)) {
                if($background_color == 'default') $background_color = $this->html_bg_color;
                 $JSINFO['tmplft_bgcolor'] = $background_color;   
            }
             
            /* Suppress sidebar */            
            $xcludes = $this->getConf('xcl_sidebar');           
            if($xcludes) {         
               // msg($xcludes);            
            $xcludes = preg_replace("/\s+/","",$xcludes);
            $xcludes = trim($xcludes,',');            
            $xcludes = str_replace(',','|',$xcludes);                      
                if(preg_match('/('.$xcludes.')/',$ID,$matches)) {
                $conf['sidebar'] = 0;              
                    //msg(print_r($matches,1));   
                }
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
           $restricted_group = $this->getConf('restricted_group');  
           $restricted = false;
           if(isset($USERINFO) && isset($restricted_group)) {               
               $groups = $USERINFO['grps'];
                if(in_array($restricted_group,$groups)) {
                    $restricted = true;
                }
           }
  
           if( $restricted && !empty($profile)) {
               $JSINFO['tmplft_profile'] = '1';
           }
           else {
               $JSINFO['tmplft_profile'] = "";
           }           
          $search = $this->getConf('search');  
           if(!empty($search)) {
               $JSINFO['tmplft_search'] = '1';
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
                 }                    
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
               if(preg_match('/revisions|recent/',$JSINFO['tmplft_actions'])) {
                   $JSINFO['tmplft_actions'] .= ',diff';
               }
            }
            
    function action_link(&$event, $param)  {
         global  $ACT,$conf;
         if(!$conf['sidebar']) return;         
         $sbar = $this->getConf('toggle_sidebar');
         if($ACT != 'show' || !$sbar) return;     
         $name = $this->getLang('toggle_name');
         $event->data['items']['tplmod'] = '<li><a href="javascript:tplmod_toggle_aside();void(0);"  class="tplmodtoggle" rel="nofollow"   title="' .$name. '">'. $name.'</a></li>';
    }
    
    public function addsvgbutton(Doku_Event $event) {          
        global  $ACT,$conf;
        if(!$conf['sidebar']) return;        
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
       if($act == 'logout' || $act == 'login') return;
	   if(isset($JSINFO['tmplft_ptools_xcl']) && !empty($JSINFO['tmplft_ptools_xcl'])) {        
		   if(strpos($JSINFO['tmplft_ptools_xcl'],$act) !== false) {	 // if excluded allow
			   return 1;
		   }
	   }
    
	   if(strpos($JSINFO['tmplft_actions'],$act) === false) { // if allowed action, allow		 
		   return 1;
	   }	   
	    $event->data = 'show';
        return 1;
   }	
           
}
