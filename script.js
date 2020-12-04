var tplmod_aside_width = 0; 
var tplmod_content_width = 0;
var tplmod_toggle_open = 0;
var tplmod_content_padding = {'b':0,'t':0};
jQuery(document).ready(function() { 

 // get default values 

if(JSINFO['tmplft_template'] == 'bootstrap3') {
	tplmod_aside_width = parseInt(jQuery("aside#dokuwiki__aside").css('width')); 
	tplmod_content_width = parseInt(jQuery("article#dokuwiki__content").css('width'));
}
else {
	tplmod_aside_width = parseInt(jQuery("div#dokuwiki__aside").css('width')); 
	tplmod_content_width = parseInt(jQuery("div#dokuwiki__content").css('width'));	
}
tplmod_toggle_open = tplmod_aside_width + tplmod_content_width;
tplmod_content_padding.t =jQuery("div.wrapper").css('padding-top');
tplmod_content_padding.b =jQuery("div.wrapper").css('padding-bottom');



if(isNaN(JSINFO['tmplftacl']))  {
    JSINFO['tmplftacl'] = 0;
}

tplmod_toggle_aside(JSINFO['tmplft_sbxcl']);
//alert(JSINFO['tmplft_sbxcl']);



if(JSINFO['tmplft_template'] == 'monochrome' && JSINFO['tmplft_bgcolor']) {
    jQuery("html").css('background-color',JSINFO['tmplft_bgcolor'] );    
    jQuery("body").css('background-color',JSINFO['tmplft_bgcolor'] );    
    jQuery("div#dw__toc").css('background-color',JSINFO['tmplft_bgcolor'] ); 
}

var acl = ((JSINFO['tmplftacl'] >= 0)  && (JSINFO['tmplftacl'] <= JSINFO['tmplft_aclgen'])) ? true: false;

if(JSINFO['tmplft_template'] == 'monochrome'  && !JSINFO['tmplft_logo'])   {
     jQuery("div.pad div.headings img").first().css('padding-right','4px');  
 }    
if(JSINFO['tmplft_logo']) { 
 if(JSINFO['tmplft_template']    == 'monochrome')   {
       jQuery("div.pad div.headings img").first().attr("src", function( i, val ) { 
       if(JSINFO['tmplft_logo_width']) { 
          this.width = JSINFO['tmplft_logo_width'] ;
          }
      var elems = val.split(/[\/\\]/);
      this.style.padding = '4px';
      var img = elems.pop();
          return val.replace(img,JSINFO['tmplft_logo']);  
    });      
 }
 else {
jQuery("div.headings.group h1 img,div.navbar-header img").attr("src", function( i, val ) { 
      if(JSINFO['tmplft_logo_width']) { 
  this.width = JSINFO['tmplft_logo_width'] ;
      }
  var elems = val.split(/[\/\\]/);
  var img = elems.pop();
      return val.replace(img,JSINFO['tmplft_logo']);  
});          
}
}


if(JSINFO['tmplft_tag'] ) {
jQuery("p.claim,span#dw__tagline").html(function(i,val) {
  
       if(val.match(/<.*?>/)) {
          val = val.replace(/^\s*(<.*?>)(.*?)(<\/)/,function(m,m1,m2,m3) {
              return m1 + JSINFO['tmplft_tag']  +  m3;
         } );
     }
     else val = JSINFO['tmplft_tag'] ;
     if(JSINFO['tmplft_template'] == 'bootstrap3') {
         jQuery("span#dw__tagline").css("padding-top", "2px");
     }
    return val;
});
}

if(JSINFO['tmplft_title'] ) {   
     jQuery("div.headings.group h1 span").html(function(index,val) {     
         return  JSINFO['tmplft_title'] ; 
    });
    
     jQuery("span#dw__title").html(function(index,val) {   
         var elems = val.split('<span');      
         return  JSINFO['tmplft_title']  + '<span' +elems[1] ;
    });
}


if(acl && JSINFO['tmplft_template'] == 'bootstrap3') {    var regex;
       if(JSINFO['tmplft_mobile'])
          regex = new RegExp(JSINFO['tmplft_mobile']);   
      else regex = '/NONE/';
     jQuery("ul.dropdown-menu.tools li").each (function( index ) {
      var  _html =  jQuery( this ).html();
       if( _html.match(regex))  {     
           jQuery(this). html("");
       }
    });
}

if(acl && JSINFO['tmplft_pagetools']) {
     var regex = new RegExp(JSINFO['tmplft_pagetools'].replace(/,/g,"|"));
     if(JSINFO['tmplft_ptools_xcl']) {
         var xcludes = new RegExp(JSINFO['tmplft_ptools_xcl'].replace(/,/g,"|"));                  
     }
      if (typeof xcludes == 'undefined') {
             xcludes = new RegExp("NONE");      
       }
     /* nav#dw__pagetools => bootstrap3 */
    jQuery( "#dokuwiki__pagetools a, nav#dw__pagetools li a" ).each(function( index ) {  
        var url  = jQuery( this ).attr('href');
      var _class = jQuery(this).attr('class');
      
      if(_class && _class.match(/show/)) {     
       return 1;  //  continue: keep show icon
      }  

       if(url.match(regex)  && !url.match(xcludes)) {         
              jQuery( this ).hide();
        }      
    });
}

jQuery("div.mobileTools option") .each(function(index, opt) { 
    var opts, regex, xcludes, optparent;
    optparent = jQuery(this).parent("optgroup");
    if(optparent.attr('label') && optparent.attr('label').match(/User\s+Tools/i)) return;

    if(JSINFO['tmplft_ptools_xcl']) {
         xcludes = new RegExp(JSINFO['tmplft_ptools_xcl'].replace(/,/g,"|")); 
         if (acl && typeof xcludes != 'undefined') {         
              if(opt.value.match(xcludes)) return;     
         }
    }        
    var regex = new RegExp(JSINFO['tmplft_mobile']);
    if(acl && regex) {
        if(opt.value.match(regex))  jQuery(this).hide();
    }
});

if(acl && JSINFO['tmplft_sitetools']) {
    var regex = new RegExp(JSINFO['tmplft_sitetools'].replace(/,/g,"|"));
    jQuery( "#dokuwiki__sitetools a,#sidebar-site-tools a").each(function( index ) {  
        var url  = jQuery( this ).attr('href');       
        if(url.match(regex)) {
            jQuery( this ).hide();
        }      
    });
}


if(JSINFO['tmplft_profile']) { 
   	if(JSINFO['tmplft_template'] == 'bootstrap3') {	
	     jQuery("li a.action.profile").parent().html("");
	}
    else {
		jQuery("div#dokuwiki__usertools li.profile, div#dokuwiki__usertools a[href$='profile']").hide();
	}
}
if(acl && JSINFO['tmplft_search'] ) jQuery("form#dw__search").hide();

});

function tplmod_toggle_aside(status) {
	var display,content_width;
    if(!status) return;
   	if(JSINFO['tmplft_template'] == 'bootstrap3') {	
        content_width = parseInt(jQuery("article#dokuwiki__content").css('width'));
	}
	else {
	    content_width = parseInt(jQuery("div#dokuwiki__content").css('width'))	
	}
    if(content_width == tplmod_toggle_open) {
        content_width = tplmod_content_width;
        if(JSINFO['tmplft_template']    == 'monochrome')   {
           jQuery("div#dokuwiki__content").css('padding-left','0px');
           jQuery("div.wrapper").css('padding-top',tplmod_content_padding.t);    
           jQuery("div.wrapper").css('padding-bottom',tplmod_content_padding.b);    
        }
        display = true;
    }
    else {
        content_width = tplmod_toggle_open;
        if(JSINFO['tmplft_template']    == 'monochrome')   {        
            jQuery("div.wrapper").css('padding-top','0px');    
            jQuery("div.wrapper").css('padding-bottom','0px');    
             jQuery("div#dokuwiki__content").css('padding-left','8px'); 
             jQuery("div#dokuwiki__content").css('background-color','white');    
        }
        display = false;
    }
	if(JSINFO['tmplft_template'] == 'bootstrap3') {
		jQuery("aside#dokuwiki__aside").toggle(display);
		jQuery("article#dokuwiki__content").css("width", content_width +'px' );
	}
    else {
	    jQuery("div#dokuwiki__aside").toggle(display);
        jQuery("div#dokuwiki__content").css("width", content_width +'px' );
    }  
}
 function tplmod_setui_lang(m,client,dw_val_obj) {
       
        var params = "tplmod_val=" +  m;   params += '&call=tplmod_ui_lang';
		params += "&tplmod_client=" + client;
        jQuery.post( DOKU_BASE + 'lib/exe/ajax.php', params,
                function (data) {   
                    if(data == 'done') { 
                         alert(LANG.plugins.tplmod.ui_updated + m); 
                    }
                      else  {                    
                         alert(LANG.plugins.tplmod.ui_save_err + data); 
                      }   
                    },
                'html'
            );     
 }


