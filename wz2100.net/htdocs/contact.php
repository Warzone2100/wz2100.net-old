<?php

// boo magic quotes
if (get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

$curpage = 'contact';

//== persist ==
include_once(dirname(__FILE__).'/../lib/global.lib.php');
include_once(dirname(__FILE__).'/lib/warzone.inc.php');

//== forum ==

/* 
define('PHPBB_ROOT_PATH', '../forums.wz2100.net/htdocs/');
define('IN_PHPBB', true);
$phpbb_root_path = (defined('PHPBB_ROOT_PATH')) ? PHPBB_ROOT_PATH : './';
$phpEx = 'php';
include($phpbb_root_path.'common.php');

//== auth ==
$user->session_begin();
$auth->acl($user->data);
$isadmin = false;
// dev, artist, mod, admin
$adminlist = array(6, 22, 19, 20, 1); //29
if (in_array($user->data['group_id'], $adminlist))
{
	$isadmin = true;
}
function isadmin() { return $GLOBALS['isadmin']; }
*/

?>
<!DOCTYPE html PUBLIC>
<html xml:lang="en" lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <title>Contact Us - Warzone 2100</title> 

    <link rel="shortcut icon" href="http://static.wz2100.net/favicon.ico" type="image/x-icon" />

    <link rel="stylesheet" type="text/css" href="http://static.wz2100.net/theme/warzone.css" />
    <!--[if lt IE 7]>
    <style type="text/css">
        /* nothing... yet */
    </style>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="http://wz2100.net/css/tracwiki-content.css" /><style type="text/css"> 
      /* Some wiki pages contain a lot of external links, lets not decorate those with icons. */
      @media screen {
       a.ext-link .icon {
        background: none;
        padding-left: 0px;
       }
      }
    </style> 
<script type="text/javascript" src="http://static.wz2100.net/theme/lytebox.js"></script>
<script type="text/javascript" src="http://static.wz2100.net/theme/jquery-1.4.1.min.js"></script>
<link rel="stylesheet" href="http://static.wz2100.net/theme/lytebox.css" type="text/css" media="screen" />

    <!-- On the downside, I've wasted five hours before finding something that works in IE.
         On the upside, I've discovered three IE bugs. -->
    <!--[if lte IE 6]>
    <style> .tree li { height: 1px; } #overall-logo { display:none; } #overall-header div a span { display: block; width:217px;height:100px; filter: progid:DXImageTransform.Microsoft.AlphaImageLoader(src='http://static.wz2100.net/theme/warzone2100.png', sizingMethod='crop'); } .warzone-footer { position: static; } #overall-header li a { background-image: none !important; padding-left: 5px !important; } #msg { width: 40em !important; }
     </style>
    <![endif]-->
    <!--[if IE 7]>
    <style> .tree li { zoom: 1; } #introbox p { min-height: 0; } </style>
    <![endif]-->

  </head>
  <body><div id="wrapper">
<div class="overall-header-home"><div id="overall-header">
  <div><a href="http://wz2100.net"><span><img src="http://static.wz2100.net/theme/warzone2100.png" id="overall-logo" width="217" height="100" alt="Warzone 2100" /></span></a></div>
  <ul>
    <!-- these are all on one line because of an IE6/IE7 bug. Don't touch. -->
    <li class="tab-home"><a href="http://wz2100.net/" title="About Warzone 2100"><span>Home</span></a></li><li class="tab-download"><a href="http://wz2100.net/download" title="Free download"><span>Download</span></a></li><li class="tab-addons"><a href="http://addons.wz2100.net/" title="Maps and mods"><span>Addons</span></a></li><li class="tab-faq"><a href="http://wz2100.net/faq" title="Frequently Asked Questions"><span>FAQ</span></a></li><li class="tab-guide"><a href="http://guide.wz2100.net/" title="User manual"><span>Guide</span></a></li><li class="tab-forum"><a href="http://forums.wz2100.net/" title="Community forums"><span>Forum</span></a></li><li class="tab-dev"><a href="http://developer.wz2100.net/" title="Development resources"><span>Development</span></a></li></ul>
<div class="overall-header-shadow"></div>
</div></div>
<?php
if ($user->data['user_unread_privmsg'])
{
	echo '<div class="warzone-alert"><a href="http://forums.wz2100.net/ucp.php?i=pm&folder=inbox">You have <strong>',$user->data['user_unread_privmsg'],'</strong> unread message',($user->data['user_unread_privmsg']==1?'':'s'),'</a></div>';
}
?>
    <div class="warzone-content">
<?php
if ($isadmin && @$_POST['toedit'])
{
	$editedtext = $_POST['editform_page'];
	$WARZONE['pages'][$curpage]['src'] = $editedtext;
	$editedtext = str_replace("\r",'',$editedtext);
	//$editedtext = str_replace("\n\n",'</p><p>',$editedtext);
	//$editedtext = str_replace("\n",'<br />',$editedtext);
	$WARZONE['pages'][$curpage]['html'] = '<p>'.$editedtext.'</p>';
	persist_save('WARZONE') || print('error');
	echo '<p>[edit successful]</p>';
}
	if ($isadmin)
	{
		echo '<div style="float:right"><a onclick="document.getElementById(\'edit_page\').style.display=\'block\';return false" href="#">Edit</a></div>';
		echo '<div id="edit_page" style="display:none"><form action="/',$curpage,'" method="post"><input type="hidden" name="toedit" value="page" /><textarea class="textbox" style="display:block;width:100%;height:480px;" name="editform_page">',htmlentities(@$WARZONE['pages'][$curpage]['src']),'</textarea><div><input type="submit" value="Ok" /> <input type="button" value="Cancel" onclick="document.getElementById(\'edit_page\').style.display=\'none\';return false" /></div></form></div>';
	}
 	if (@$WARZONE['pages'][$curpage]['html'])
	{
		echo $WARZONE['pages'][$curpage]['html'];
	}
?>
      <div style="clear: both;"></div> 
    </div> 
    <div id="g_foot" class="warzone-footer">
      <ul>
        <li>
          <a href="http://wz2100.net/contact">Contact</a>
        </li><li>
          <a href="http://wz2100.net/feed.atom">RSS</a>
        </li><li>
          <a href="http://wz2100.net/site-policy">Terms of Service</a>
        </li><li>
          <a href="http://wz2100.net/privacy-policy">Privacy</a>
        </li><li>
          <a href="http://wz2100.net/imprint">Imprint</a>
        </li><li>
          <a href="http://wz2100.net/credits">Credits</a>
        </li><li>
          <a href="http://wz2100.net/license">License</a>
        </li>
      </ul>
    </div>
  </div>
  </body> 
</html>
