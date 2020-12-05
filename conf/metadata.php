<?php
$meta['sitetools']  = array('multicheckbox','_choices' => array('Recent Changes','Media Manager','Sitemap'));
$meta['pagetools']  = array('multicheckbox','_choices' => array('Edit/View Source','Old Revisions','Backlinks', 'Subscribe','All'));
$meta['ptools_xcl'] = array('string');
$meta['profile'] = array('onoff');
$meta['restricted_group'] = array('string');
$meta['search'] = array('onoff');
$meta['taglines']  = array('string');
$meta['tag_date_format'] = array('string');
$meta['logos'] = array('string');
$meta['ips']  = array('string');
$meta['rotate_title'] = array('onoff');
$meta['wiki_names'] = array('string');
$meta['acl_all'] = array('multichoice','_choices'=>array('NONE','READ','EDIT','CREATE','UPLOAD','DELETE'));
$meta['dateorip'] =  array('multichoice','_choices' => array('NEITHER','IP','DAY'));
$meta['rotatewhich'] =  array('multichoice','_choices' => array('NONE','LOGO','TAG','BOTH'));
$meta['toggle_sidebar']  = array('onoff');
$meta['background_color']  = array('multicheckbox','_choices' => array('default'));
$meta['blocking']= array('onoff');
$meta['deflang'] = array('multicheckbox','_choices'=>array('Albanian SQ','Arabic AR','Armenian HY','Basque EU','Bulgarian BG','Chinese ZH','Croatian HR',
'Czech CS','Danish DA','Dutch NL','English EN','Esperanto EO','Estonian ET','Finnish FI','French FR','German DE','Greek EL','Greenlandic KL','Hebrew IW','Hindi HI',
'Hungarian HU','Icelandic IS','Italian IT','Japanese JA','Korean KO','Latvian LV','Lithuanian LT','Macedonian MK','Norwegian NO','Persian FA','Polish PL',
'Portuguese PT','Romanian  RO','Russian RU','Serbian SR','Slovak SK','Spanish ES','Swedish SV','Taiwanese zh-tw','Turkish TR','Ukrainian UK'));
$meta['xcl_sidebar'] = array('string');