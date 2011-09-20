<?php
include_once(dirname(__FILE__).'/../lib/global.lib.php');

// Loads the data for the "WARZONE" variable
include_once(dirname(__FILE__).'/lib/warzone.inc.php');
?>
<!DOCTYPE html>
<html xml:lang="en" lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <title>Warzone 2100: A Real-Time Strategy game</title> 

    <link rel="shortcut icon" href="<?php echo $protocol; ?>static.wz2100.net/favicon.ico" type="image/x-icon" />
    <link rel="alternate" type="application/atom+xml" title="Warzone 2100 News" href="<?php echo $protocol; ?>www.wz2100.net/feed.atom" />

    <link rel="stylesheet" type="text/css" href="<?php echo $protocol; ?>static.wz2100.net/theme/warzone.css" />
    <!--[if lt IE 7]>
    <style type="text/css">
        /* nothing... yet */
    </style>
    <![endif]-->
    <link rel="stylesheet" type="text/css" href="<?php echo $protocol; ?>www.wz2100.net/css/tracwiki-content.css" /><style type="text/css"> 
      /* Some wiki pages contain a lot of external links, lets not decorate those with icons. */
      @media screen {
       a.ext-link .icon {
        background: none;
        padding-left: 0px;
       }
      }
    </style> 
<script type="text/javascript" src="<?php echo $protocol; ?>static.wz2100.net/theme/lytebox.js"></script>
<script type="text/javascript" src="<?php echo $protocol; ?>static.wz2100.net/theme/jquery-1.4.1.min.js"></script>
<link rel="stylesheet" href="<?php echo $protocol; ?>static.wz2100.net/theme/lytebox.css" type="text/css" media="screen" />

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
  </head>
  <body><div id="wrapper">
  
<?php print_header('home'); ?>

    <div class="warzone-content">
      <div id="introbox">
        <div class="screenshot">
          <a href="<?php echo $protocol; ?>www.wz2100.net/images/warzonescreenshot.png" rel="lytebox" onclick="myLytebox.start(this, false, false); return false;"><img src="/images/warzonescreenshotsmall.png" alt="" width="400" height="300" /></a>
        </div>
        <p class="tagline">
          <strong>Warzone 2100</strong> <em>Strategy by Design</em>
        </p>
        <p>
          In <strong>Warzone 2100</strong>, you command the forces of <em>The Project</em> in a battle to rebuild the world after mankind has almost been destroyed by nuclear missiles.
        </p>
        <p>
          The game offers campaign, multi-player, and single-player skirmish modes. An extensive tech tree with <a href="<?php echo $protocol; ?>guide.wz2100.net/r/tech-tree">over 400 different technologies</a>, combined with the unit design system, allows for a wide variety of possible units and tactics.
        </p>
        <p class="backforward">
          <a href="<?php echo $protocol; ?>guide.wz2100.net/intro">Read more &raquo;</a>
        </p>
        <div class="downloadbtn" id="downloadbtn" style="margin-right:415px;">
          <a href="<?php echo $protocol; ?>www.wz2100.net/download"><em>Download</em> <strong>Warzone 2100</strong> <?php echo @$WARZONE['currentversion']['name']; ?></a>
        </div>
        <div style="padding-left:20px;"><small><a href="/download">More download options</a></small></div>
    <p class="backforward" style="font-weight:bold;margin:1em 0 0 ;color:#888888;"><a href="<?php echo $protocol; ?>www.wz2100.net/about">About us</a> <a href="<?php echo $protocol; ?>guide.wz2100.net/">Manual</a> <a href="<?php echo $protocol; ?>www.wz2100.net/lobbyserver">Current games</a>
    </p>
        <div style="clear:both"></div>
      </div>

<script>
<!--
var version = '<?php echo @$WARZONE['currentversion']['name'] ?>';
var version_dl_win = '<?php echo @$WARZONE['currentversion']['dl_win'] ?>';
var version_dl_win_size = '<?php echo @$WARZONE['currentversion']['dl_win_size'] ?>';
var version_dl_mac = '<?php echo @$WARZONE['currentversion']['dl_mac'] ?>';
var version_dl_mac_size = '<?php echo @$WARZONE['currentversion']['dl_mac_size'] ?>';
var version_dl_src = '<?php echo @$WARZONE['currentversion']['dl_src'] ?>';
var version_dl_src_size = '<?php echo @$WARZONE['currentversion']['dl_src_size'] ?>';
var betaversion = '<?php echo @$WARZONE['betaversion']['name'] ?>';
var betaversion_type = '<?php echo @$WARZONE['betaversion']['type'] ?>';
var betaversion_dl_win = '<?php echo @$WARZONE['betaversion']['dl_win'] ?>';
var betaversion_dl_win_size = '<?php echo @$WARZONE['betaversion']['dl_win_size'] ?>';
var betaversion_dl_mac = '<?php echo @$WARZONE['betaversion']['dl_mac_novid'] ?>';
var betaversion_dl_mac_size = '<?php echo @$WARZONE['betaversion']['dl_mac_novid_size'] ?>';
var betaversion_dl_src = '<?php echo @$WARZONE['betaversion']['dl_src'] ?>';
var betaversion_dl_src_size = '<?php echo @$WARZONE['betaversion']['dl_src_size'] ?>';

var version_dl_mac_version = '<?php echo @$WARZONE['currentversion']['dl_mac_version'] ?>';
var betaversion_dl_mac_version = '<?php echo @$WARZONE['betaversion']['dl_mac_version'] ?>';

var BrowserDetect = {
  init: function () {
    this.OS = this.searchString(this.dataOS) || "other";
  },
  searchString: function (data) {
    for (var i=0;i<data.length;i++)  {
      var dataString = data[i].string;
      var dataProp = data[i].prop;
      this.versionSearchString = data[i].versionSearch || data[i].identity;
      if (dataString) {
        if (dataString.indexOf(data[i].subString) != -1)
          return data[i].identity;
      }
      else if (dataProp)
        return data[i].identity;
    }
  },
  dataOS : [
    {
      string: navigator.platform,
      subString: "Win",
      identity: "Win"
    },
    {
      string: navigator.platform,
      subString: "Mac",
      identity: "Mac"
    },
    {
         string: navigator.userAgent,
         subString: "iPhone",
         identity: "iPhone"
      }
  ]
};
BrowserDetect.init();

if (BrowserDetect.OS == 'Win') {
  document.getElementById('downloadbtn').innerHTML = '<a href="'+version_dl_win+'" target="sourceforge"><em>Download</em> <strong>Warzone 2100</strong> '+version+' <small>for Windows 2000+ <sub>'+version_dl_win_size+'</sub></small></a>';
} else if (BrowserDetect.OS == 'Mac') {
  document.getElementById('downloadbtn').innerHTML = '<a href="'+version_dl_mac+'" target="sourceforge"><em>Download</em> <strong>Warzone 2100</strong> '+version+' <small>for '+version_dl_mac_version+' <sub>'+version_dl_mac_size+'</sub></small></a>';
} else if (BrowserDetect.OS == 'iPhone') {
  document.getElementById('downloadbtn').innerHTML = '[not available for iPhone OS]';
} else {
  document.getElementById('downloadbtn').innerHTML = '<a href="'+version_dl_src+'" target="sourceforge"><em>Download</em> <strong>Warzone 2100</strong> '+version+' <small>source code tarball <sub>'+version_dl_src_size+'</sub></small></a>';
}

if (betaversion != '')
{
  if (BrowserDetect.OS == 'Win') { if (betaversion_dl_win)
    document.getElementById('downloadbtn').innerHTML = '<div class="downloadbtn" style="float:left"><a href="'+version_dl_win+'" target="sourceforge"><em>Download</em> <strong>Warzone 2100</strong> '+version+' <small>for Windows 2000+ <sub>'+version_dl_win_size+'</sub></small></a></div><div class="downloadbtn" style="float:left"><a href="'+betaversion_dl_win+'" target="sourceforge" class="betaversion"><em>Test the <?php echo $WARZONE['betaversion']['versiontype'] ?></em> <strong>Warzone 2100</strong> '+betaversion+' <small>for Windows 2000+ <sub>'+betaversion_dl_win_size+'</sub></small></a></div><div style="clear:left"></div>';
  } else if (BrowserDetect.OS == 'Mac') { if (betaversion_dl_mac)
    document.getElementById('downloadbtn').innerHTML = '<div class="downloadbtn" style="float:left"><a href="'+version_dl_mac+'" target="sourceforge"><em>Download</em> <strong>Warzone 2100</strong> '+version+' <small>for Mac OS X 10.4+ <sub>'+version_dl_mac_size+'</sub></small></a></div><div class="downloadbtn" style="float:left"><a href="'+betaversion_dl_mac+'" target="sourceforge" class="betaversion"><em>Test the <?php echo $WARZONE['betaversion']['versiontype'] ?></em> <strong>Warzone 2100</strong> '+betaversion+' <small>for '+betaversion_dl_mac_version+' <sub>'+betaversion_dl_mac_size+'</sub></small></a></div><div style="clear:left"></div>';
  } else if (BrowserDetect.OS == 'iPhone') {
  } else if (version_dl_src) {
    document.getElementById('downloadbtn').innerHTML = '<div class="downloadbtn" style="float:left"><a href="'+version_dl_src+'" target="sourceforge"><em>Download</em> <strong>Warzone 2100</strong> '+version+' <small>source code tarball <sub>'+version_dl_src_size+'</sub></small></a></div><div class="downloadbtn" style="float:left"><a href="'+betaversion_dl_src+'" target="sourceforge" class="betaversion"><em>Test the <?php echo $WARZONE['betaversion']['versiontype'] ?></em> <strong>Warzone 2100</strong> '+betaversion+' <small>source code tarball <sub>'+betaversion_dl_src_size+'</sub></small></a></div><div style="clear:left"></div>';
  }
}

//-->
</script>

<div style="float:right;width:350px;padding-top:1px;">
<h1 style="margin-top:0;">Screenshots</h1>
<div style="text-align: center;" class="content-box">
<p>
<a style="padding:0; border:none" href="<?php echo $protocol; ?>www.wz2100.net/screenshots"><img src="<?php echo $protocol; ?>developer.wz2100.net/raw-attachment/wiki/Website/Frontpage/1_thumb.jpg" alt="" title="" height="120" /></a> 
<a style="padding:0; border:none" href="<?php echo $protocol; ?>www.wz2100.net/screenshots"><img src="<?php echo $protocol; ?>developer.wz2100.net/raw-attachment/wiki/Website/Frontpage/2_thumb.jpg" alt="" title="" height="120" /></a> 
<a style="padding:0; border:none" href="<?php echo $protocol; ?>www.wz2100.net/screenshots"><img src="<?php echo $protocol; ?>developer.wz2100.net/raw-attachment/wiki/Website/Frontpage/3_thumb.jpg" alt="" title="" height="120" /></a> 
<a style="padding:0; border:none" href="<?php echo $protocol; ?>www.wz2100.net/screenshots"><img src="<?php echo $protocol; ?>developer.wz2100.net/raw-attachment/wiki/Website/Frontpage/4_thumb.jpg" alt="" title="" height="120" /></a> 
</p>
<p class="backforward">
<a href="<?php echo $protocol; ?>wz2100.net/screenshots">More screenshots &raquo;</a>
</p>
</div>
<h1>Community</h1>
<div class="content-box">
<p>
  If you want to arrange online games, you might want to use our online chat room!
</p>
<p class="backforward">
  <a href="<?php echo $protocol; ?>webchat.freenode.net/?channels=warzone2100-games" target="chat">Enter chat room &raquo;</a>
</p>
<p>
  Experienced users can also enter this chat room by visiting <a href="irc://irc.freenode.net/warzone2100-games"><code>#warzone2100-games</code> in FreeNode</a> with an IRC client.
</p>
</div>
</div>
<div style="margin-right:360px;padding-top:1px;">
<h1 id="news" style="margin-top:0;"><a href="<?php echo $protocol; ?>wz2100.net/feed.atom" style="float:right"><img src="images/feed-icon.gif" alt="RSS" /></a> Latest news</h1> 
<?php

// Grab just the sorted topic ids
$newsforumid = 1;

$sql = 'SELECT t.topic_id, t.topic_title, t.topic_time, t.topic_poster, t.topic_first_poster_name, t.topic_replies
  FROM ' . TOPICS_TABLE . " t
  WHERE t.forum_id = $newsforumid
    AND t.topic_type IN (" . POST_NORMAL . ")
    AND t.topic_status IN (0,1)
  ORDER BY t.topic_id DESC";
$result = $db->sql_query_limit($sql, 3, 0);

$topics = array();
while ($row = $db->sql_fetchrow($result))
{
  $topics[] = $row;
}
$db->sql_freeresult($result);

if ($isadmin && @$_POST['toedit'])
{
  $editedtext = $_POST['editform_'.$_POST['toedit']];
  $WARZONE['topicsummaries'][(int)$_POST['toedit']]['src'] = $editedtext;
  $editedtext = str_replace("\r",'',$editedtext);
  $editedtext = str_replace("\n\n",'</p><p>',$editedtext);
  $editedtext = str_replace("\n",'<br />',$editedtext);
  $WARZONE['topicsummaries'][(int)$_POST['toedit']]['html'] = '<p>'.$editedtext.'</p>';
  persist_save('WARZONE', dirname(__FILE__) . '/lib/warzone.inc.php') || print('error');
  echo '<p>[edit successful]</p>';
}

foreach ($topics as $row)
{
?>
<div class="content-box news">
  <h2><a href="/news/<?php echo $row['topic_id']; ?>"><?php echo $row['topic_title']; ?></a></h2>
  <p class="byline">by <?php if ($user->data['is_registered']) echo '<a href="http://forums.wz2100.net/memberlist.php?mode=viewprofile&u=',$row['topic_poster'],'">',$row['topic_first_poster_name'],'</a>'; else echo $row['topic_first_poster_name']; ?> on <?php echo date('Y F j', (int)$row['topic_time']) ?></p>
<?php
  if ($isadmin)
  {
    echo '<div style="float:right"><a onclick="document.getElementById(\'edit_',$row['topic_id'],'\').style.display=\'block\';return false" href="#">Edit</a></div>';
    echo '<div id="edit_',$row['topic_id'],'" style="display:none"><form action="',basename(__FILE__),'" method="post"><input type="hidden" name="toedit" value="',$row['topic_id'],'" /><textarea class="textbox" style="display:block;width:100%;height:160px;" name="editform_',$row['topic_id'],'">',htmlentities(@$WARZONE['topicsummaries'][(int)$row['topic_id']]['src']),'</textarea><div><input type="submit" value="Ok" /> <input type="button" value="Cancel" onclick="document.getElementById(\'edit_',$row['topic_id'],'\').style.display=\'none\';return false" /></div></form></div>';
  }
   if (@$WARZONE['topicsummaries'][(int)$row['topic_id']]['html'])
  {
    echo $WARZONE['topicsummaries'][(int)$row['topic_id']]['html'];
  }
?>
  <p><a href="/news/<?php echo $row['topic_id']; ?>">Read more</a> <small> | <a href="<?php echo $protocol; ?>forums.wz2100.net/viewtopic.php?f=1&amp;t=<?php echo $row['topic_id']; ?>"><?php echo $row['topic_replies']; ?> comment<?php if ((int)$row['topic_replies'] != 1) echo 's'; ?></a></small></p>
</div>
<?php
}

?>
<p class="backforward">
<a href="/news#more">Older news &raquo;</a>
</p>
</div>

      <div style="clear: both;"></div> 
      <div class="content-box-not" style="margin-top:5em;" id="languages">
        <p style="text-align:center">If you do not understand English, there are fan sites available in other languages:</p>
        <p class="backforward"><a href="http://warzone2100.de">Deutsch<small> (German)</small></a> | <a href="http://warzone2100.org.ua/" target="_blank">&#1056;&#1091;&#1089;&#1089;&#1082;&#1080;&#1081;<small> (Russian)</small></a> | <a href="http://wz2100.info/" target="_blank">Polski<small> (Polish)</small></a> | <a href="http://wz2100.blogspot.com/" target="_blank">&#20013;&#25991;<small> (Chinese)</small></a><!-- | <a href="http://warzonefrance.probb.fr/" target="_blank">Fran&#231;ais<small> (French)</small></a>--></p>
      </div>
    </div> 
<?php print_footer(); ?>
  </div>
</body> 
</html>
