<?php
$lang['logos'] = 'Comma separated list of image names for home page logo: names only, not paths';
$lang['ips'] = 'Comma separated list of IPs for rotation by IP; rotations follow sequence of IPs';
$lang['sitetools'] = '<b>Site Tools</b><br />Remove the selected site tools. In the Dokuwiki template Site Tools are located at the top of the template.';
$lang['pagetools'] = '<b>Page Tools</b><br />Remove the selected page tools. In the Dokuwiki template these are located along the right-hand side of the template. ' .
' You can remove non-standard tools, using the input box:  enter them as a comma separated list of unique words from their query strings or urls.';
$lang['profile'] = 'Remove User Profile link';
$lang['restricted_group'] = 'Name of group which is restricted from accessing the User Profile dialog. If no group is assigned and the <code>profile</code> option is true, all users will have the User Profile link removed.';
$lang['search'] = 'Remove Search box';
$lang['taglines'] = "Comma separated list of taglines";
$lang['acl_all'] = 'The ACL level required for access to the sitetoools and pagetools. Users with lesser ACL levels will be barred from access to these tools';
$lang['dateorip'] = 'Rotate logos, tag lines, and wiki names  by ip address or days of the week. Select  <code>NEITHER</code> if you do not want rotation.';
$lang['rotatewhich'] = "If rotating the logo and/or tag line, select which to rotate";
$lang['ptools_xcl'] = ' If pagetools is set to <code>All</code>, you can retain selected tools by entering  a comma separated list of unique words from their urls in this text box.'
     . ' The Dokuwiki template and most others use these words: <code>edit,revisions,backlink,subscribe</code>. By default this option protects the top and login icons.'; 
$lang['wiki_names'] = "Comma separated list of wiki names";
$lang['rotate_title'] = "Rotate the wiki name (title)";
$lang['tag_date_format'] = "A php <a href='http://php.net/manual/en/function.date.php'>date format </a> string.  If present, this date string will be output as your tagline.";
$lang['toggle_sidebar'] = "Display a toggle link at the top of the page to hide and show the sidebar";
$lang['background_color'] = 'The browser background color, i.e. surrounding the wiki page. See plugin page for possible advantages of setting this option.';
$lang['blocking'] = 'Prevent attempts to access hidden actions by adding <code> do=&lt;action&gt; </code> parameters to the url.'; 
$lang['deflang'] = 'User Interface Languages.  Select the languages from which your users will be selecting a UI Language in the User Profile dialog. If any do not appear '
    .  'in the listing, they can be entered, as a comma-separated list, into the text box in this form: "Language ISO", where Language is the language name and ISO is the ISO code.';
