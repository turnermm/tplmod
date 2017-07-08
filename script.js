jQuery(document).ready(function() { 
if(isNaN(JSINFO['tmplftacl']))  {
    JSINFO['tmplftacl'] = 0;
}

var acl = ((JSINFO['tmplftacl'] >= 0)  && JSINFO['tmplftacl'] <= JSINFO['tmplft_aclgen']) ? true: false;

if(JSINFO['tmplft_logo']) { 
jQuery("div.headings.group h1 img").attr("src", function( i, val ) { 
      if(JSINFO['tmplft_logo_width']) { 
  this.width = JSINFO['tmplft_logo_width'] ;
      }
  var elems = val.split(/[\/\\]/);
  var img = elems.pop();
      return val.replace(img,JSINFO['tmplft_logo']);  
});          
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
