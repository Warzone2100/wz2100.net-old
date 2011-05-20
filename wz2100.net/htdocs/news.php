<?php
include_once('../lib/global.lib.php');
include_once('lib/warzone.inc.php');

$t = (int)@$_REQUEST['t'];
if (!$t && substr($_SERVER['REQUEST_URI'],0,6)=='/news/')
{
	$t = substr($_SERVER['REQUEST_URI'],6);
	if (substr($t,-1) == '/') $t = substr($t,0,-1);
	$t = (int)$t;
}

if ($t)
{

$sql = 'SELECT t.*, p.*
	FROM ' . TOPICS_TABLE . " t
		LEFT JOIN " . POSTS_TABLE . " p
		ON t.topic_first_post_id = p.post_id
	WHERE t.topic_id = $t";
$result = $db->sql_query_limit($sql, 1, 0);

$topic = $db->sql_fetchrow($result);
$db->sql_freeresult($result);

$sql = 'SELECT *
	FROM ' . ATTACHMENTS_TABLE . '
	WHERE post_msg_id = ' . $topic['post_id'] . '
		AND in_message = 0
	ORDER BY filetime DESC, post_msg_id ASC';
	$result = $db->sql_query($sql);

	while ($row = $db->sql_fetchrow($result))
	{
		$attachments[$row['post_msg_id']][] = $row;
	}
	$db->sql_freeresult($result);
}

?>
<!DOCTYPE html>
<html xml:lang="en" lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <title><?php if (@$topic) echo $topic['topic_title'].' - '; ?>Warzone 2100 News</title> 

    <link rel="shortcut icon" href="http://static.wz2100.net/favicon.ico" type="image/x-icon" />
    <link rel="alternate" type="application/atom+xml" title="Warzone 2100 News" href="http://wz2100.net/feed.atom" />

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

<script>
<!--

var isadmin = <?php echo $isadmin?'true':'false'; ?>;

-->
</script>

<!-- Piwik -->
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://stats.page4me.ch/" : "http://stats.page4me.ch/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 2);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://stats.page4me.ch/piwik.php?idsite=2" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Tag -->

  </head>
  <body><div id="wrapper">
<div class="overall-header-home"><div id="overall-header">
  <div><a href="http://wz2100.net"><span><img src="http://static.wz2100.net/theme/warzone2100.png" id="overall-logo" width="217" height="100" alt="Warzone 2100" /></span></a></div>
  <ul>
    <!-- these are all on one line because of an IE6/IE7 bug. Don't touch. -->
    <li class="tab-home cur"><a href="http://wz2100.net/" title="About Warzone 2100"><span>Home</span></a></li><li class="tab-download"><a href="http://wz2100.net/download" title="Free download"><span>Download</span></a></li><li class="tab-addons"><a href="http://addons.wz2100.net/" title="Maps and mods"><span>Addons</span></a></li><li class="tab-faq"><a href="http://wz2100.net/faq" title="Frequently Asked Questions"><span>FAQ</span></a></li><li class="tab-guide"><a href="http://guide.wz2100.net/" title="User manual"><span>Guide</span></a></li><li class="tab-forum"><a href="http://forums.wz2100.net/" title="Community forums"><span>Forum</span></a></li><li class="tab-dev"><a href="http://developer.wz2100.net/" title="Development resources"><span>Development</span></a></li></ul>
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

if (@$topic)
{
	$row = $topic;

	if ($row['forum_id'] != $settings['newforum'])
	{
?>
<style>
	a.error404
	{
	  text-decoration: none;
	}
	a.error404 img
	{
	  border: 0;
	}
	a.error404 em
	{
	  visibility: hidden;
	  color: #555555;
	}
	a.error404:hover em
	{
	  visibility: visible;
	}
</style>
<div style="padding:40px 0;text-align:center"><a href="/" style="border:0" class="error404"><img src="/images/404.gif" alt="404" style="border:0" /><br /><em>(Click to go back to the home page)</em></a></div>
<?php
	}
	else
	{
		// Parse the message and subject
		$message = censor_text($row['post_text']);
		
	//var_export($row['bbcode_bitfield']);
	//echo '<pre>['.htmlentities($row['bbcode_bitfield']).']</pre>';
		// Second parse bbcode here
		if ($row['bbcode_bitfield'])
		{
			$user->setup('viewtopic');
			$bbcode_bitfield = base64_decode($row['bbcode_bitfield']);
			$bbcode = new bbcode(base64_encode($bbcode_bitfield));
			$bbcode->bbcode_second_pass($message, $row['bbcode_uid'], $row['bbcode_bitfield']);
		}

		$message = bbcode_nl2br($message);
		$message = smiley_text($message);
		
		$update_count = array();
		if (!empty($attachments[$row['post_id']]))
		{
			parse_attachments($settings['newforum'], $message, $attachments[$row['post_id']], $update_count);
		}
		
		$message = str_replace('"../forums.wz2100.net/htdocs/','"http://forums.wz2100.net/',$message);
		$message = str_replace('<a href="http://forums.wz2100.net/download/file.php?id=','<a class="img" rel="lytebox[attachments]" onclick="myLytebox.start(this, false, false); return false;" href="http://forums.wz2100.net/download/file.php?id=',$message)
?>
<div class="content-box" style="margin-top: 1em;padding-bottom:10px;">
	<h2><?php echo $row['topic_title']; ?></h2>
	<p class="byline">by <?php if ($user->data['is_registered']) echo '<a href="http://forums.wz2100.net/memberlist.php?mode=viewprofile&u=',$row['topic_poster'],'">',$row['topic_first_poster_name'],'</a>'; else echo $row['topic_first_poster_name']; ?> on <?php echo date('Y F j', (int)$row['topic_time']) ?></p>
	<?php
		if ($isadmin)
		{
			echo '<div style="float:right"><a href="http://forums.wz2100.net/posting.php?mode=edit&f=1&p=',$row['post_id'],'">Edit</a></div>';
		}
		echo $message;
	?>
</div>
<div class="content-box">
	<p><a href="http://forums.wz2100.net/viewtopic.php?f=1&amp;t=<?php echo $row['topic_id']; ?>">(View <?php echo $row['topic_replies']; ?> comment<?php if ((int)$row['topic_replies'] != 1) echo 's'; ?>)</a></p>
</div>
<?php
	}
}
else
{

?>
<h1 id="news"><a href="http://wz2100.net/feed.atom" style="float:right"><img src="images/feed-icon.gif" alt="RSS" /></a> Latest news</h1> 
<?php

//$sql = 'SELECT t.topic_id, t.topic_title, t.topic_time, t.topic_poster, t.topic_first_poster_name, t.topic_replies
$sql = 'SELECT t.topic_id, t.topic_title, t.topic_time, t.topic_poster, t.topic_first_poster_name, t.topic_replies, t.*
	FROM ' . TOPICS_TABLE . " t
	WHERE t.forum_id = " . $settings['newforum'] . "
		AND t.topic_type IN (" . POST_NORMAL . ")
		AND t.topic_status IN (0,1)
	ORDER BY t.topic_id DESC";
$result = $db->sql_query_limit($sql, 20, 0);

$topics = array();
while ($row = $db->sql_fetchrow($result))
{
	$topics[] = $row;
}
$db->sql_freeresult($result);

if ($isadmin && @$_POST['toedit'])
{
	$WARZONE['topicsummaries'][(int)$_POST['toedit']] = array();
	$editedtext = $_POST['editform_'.$_POST['toedit']];
	$WARZONE['topicsummaries'][(int)$_POST['toedit']]['src'] = $editedtext;
	$editedtext = str_replace("\r",'',$editedtext);
	$editedtext = str_replace("\n\n",'</p><p>',$editedtext);
	$editedtext = str_replace("\n",'<br />',$editedtext);
	$WARZONE['topicsummaries'][(int)$_POST['toedit']]['html'] = '<p>'.$editedtext.'</p>';
	persist_save('WARZONE', dirname(__FILE__) . '/lib/warzone.inc.php') || print('error');
	echo '<p>[edit successful]</p>';
}

foreach ($topics as $i => $row)
{
	//var_export($row);
?>
<div class="content-box news"<?php if ($i==3) echo ' id="more"' ?>>
	<h2><a href="/news/<?php echo $row['topic_id']; ?>"><?php echo $row['topic_title']; ?></a></h2>
	<p class="byline">by <?php if ($user->data['is_registered']) echo '<a href="http://forums.wz2100.net/memberlist.php?mode=viewprofile&u=',$row['topic_poster'],'">',$row['topic_first_poster_name'],'</a>'; else echo $row['topic_first_poster_name']; ?> on <?php echo date('Y F j', (int)$row['topic_time']) ?></p>
<?php
	if ($isadmin)
	{
		echo '<div style="float:right"><a onclick="document.getElementById(\'edit_',$row['topic_id'],'\').style.display=\'block\';return false" href="#">Edit</a></div>';
		echo '<div id="edit_',$row['topic_id'],'" style="display:none"><form action="/news" method="post"><input type="hidden" name="toedit" value="',$row['topic_id'],'" /><textarea class="textbox" style="display:block;width:100%;height:160px;" name="editform_',$row['topic_id'],'">',htmlentities(@$WARZONE['topicsummaries'][(int)$row['topic_id']]['src']),'</textarea><div><input type="submit" value="Ok" /> <input type="button" value="Cancel" onclick="document.getElementById(\'edit_',$row['topic_id'],'\').style.display=\'none\';return false" /></div></form></div>';
	}
 	if (@$WARZONE['topicsummaries'][(int)$row['topic_id']]['html'])
	{
		echo $WARZONE['topicsummaries'][(int)$row['topic_id']]['html'];
	}
?>
	<p><a href="/news/<?php echo $row['topic_id']; ?>">Read more</a> <small> | <a href="http://forums.wz2100.net/viewtopic.php?f=1&amp;t=<?php echo $row['topic_id']; ?>"><?php echo $row['topic_replies']; ?> comment<?php if ((int)$row['topic_replies'] != 1) echo 's'; ?></a></small></p>
</div>
<?php
}

?>
<p class="backforward">
<a href="http://forums.wz2100.net/viewforum.php?f=1">Older news &raquo;</a>
</p>
<?php
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
  
<!-- Piwik -->
<script type="text/javascript">
var pkBaseURL = (("https:" == document.location.protocol) ? "https://stats.page4me.ch/" : "http://stats.page4me.ch/");
document.write(unescape("%3Cscript src='" + pkBaseURL + "piwik.js' type='text/javascript'%3E%3C/script%3E"));
</script><script type="text/javascript">
try {
var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", 2);
piwikTracker.trackPageView();
piwikTracker.enableLinkTracking();
} catch( err ) {}
</script><noscript><p><img src="http://stats.page4me.ch/piwik.php?idsite=2" style="border:0" alt="" /></p></noscript>
<!-- End Piwik Tracking Tag -->

   </body> 
</html>
