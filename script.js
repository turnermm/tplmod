var tplmod_aside_width = 0; 
var tplmod_content_width = 0;
var tplmod_toggle_open = 0;
var tplmod_content_padding = {'b':0,'t':0};
jQuery(document).ready(function() { 

 // get default values 
tplmod_aside_width = parseInt(jQuery("div#dokuwiki__aside").css('width')); 
tplmod_content_width = parseInt(jQuery("div#dokuwiki__content").css('width'));
tplmod_toggle_open = tplmod_aside_width + tplmod_content_width;
tplmod_content_padding.t =jQuery("div.wrapper").css('padding-top');
tplmod_content_padding.b =jQuery("div.wrapper").css('padding-bottom');

if(isNaN(JSINFO['tmplftacl']))  {
    JSINFO['tmplftacl'] = 0;
}

if(JSINFO['tmplft_bgcolor']) {
    jQuery("html").css('background-color',JSINFO['tmplft_bgcolor'] );    
    jQuery("body").css('background-color',JSINFO['tmplft_bgcolor'] );    
}

var acl = ((JSINFO['tmplftacl'] >= 0)  && JSINFO['tmplftacl'] <= JSINFO['tmplft_aclgen']) ? true: false;

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
jQuery("div.headings.group h1 img").attr("src", function( i, val ) { 
      if(JSINFO['tmplft_logo_width']) { 
  this.width = JSINFO['tmplft_logo_width'] ;
      }
  var elems = val.split(/[\/\\]/);
  var img = elems.pop();
      return val.replace(img,JSINFO['tmplft_logo']);  
});          
}
}

if(JSINFO['tmplft_title'] ) {
     jQuery("div.headings.group h1 span").html(function(index,val) {  
         return JSINFO['tmplft_title'] ; 
    });
}
if(JSINFO['tmplft_tag'] ) {
jQuery("p.claim").html(function(i,val) {
       if(val.match(/<.*?>/)) {
          val = val.replace(/^\s*(<.*?>)(.*?)(<\/)/,function(m,m1,m2,m3) {
              return m1 + JSINFO['tmplft_tag']  +  m3;
         } );
     }
     else val = JSINFO['tmplft_tag'] ;
    return val;
});
}
if(acl && JSINFO['tmplft_pagetools']) {
     var regex = new RegExp(JSINFO['tmplft_pagetools'].replace(/,/g,"|"));
     if(JSINFO['tmplft_ptools_xcl']) {
         var xcludes = new RegExp(JSINFO['tmplft_ptools_xcl'].replace(/,/g,"|"));                  
     }
     
    jQuery( "#dokuwiki__pagetools a" ).each(function( index ) {  
        var url  = jQuery( this ).attr('href');
     
      var _class = jQuery(this).attr('class');
      var show = false;
      if(_class && _class.match(/show/)) {     
       return 1;  //  continue: keep show icon
      }  
   
       if(url.match(regex)  && !url.match(xcludes)) {         
              jQuery( this ).hide();
        }      
    });
}

if(acl && JSINFO['tmplft_sitetools']) {
    var regex = new RegExp(JSINFO['tmplft_sitetools'].replace(/,/g,"|"));
    jQuery( "#dokuwiki__sitetools a" ).each(function( index ) {  
        var url  = jQuery( this ).attr('href');       
        if(url.match(regex)) {
            jQuery( this ).hide();
        }      
    });
}

if(acl && JSINFO['tmplft_profile']) jQuery("#dokuwiki__usertools  a.profile").hide();
if(acl && JSINFO['tmplft_search'] ) jQuery("form#dw__search").hide();

});

function tplmod_toggle_aside() {
    var content_width = parseInt(jQuery("div#dokuwiki__content").css('width'));
    var content_height = parseInt(jQuery("div#dokuwiki__content").css('height'));
    var display;
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
    jQuery("div#dokuwiki__aside").toggle(display);
    jQuery("div#dokuwiki__content").css("width", content_width +'px' );
}


