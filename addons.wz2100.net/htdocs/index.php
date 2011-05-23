<?php
include_once('../../wz2100.net/lib/global.lib.php');
include_once('lib/wzaddons.lib.php');

$p = strval(@$_REQUEST['p']);

?>
<!DOCTYPE html PUBLIC>
<html>
  <head> 
    <meta http-equiv="content-type" content="text/html; charset=utf-8" /> 
    <meta http-equiv="content-language" content="en" /> 
    <link rel="shortcut icon" href="<?php echo $protocol; ?>static.wz2100.net/favicon.ico" type="image/x-icon" /> 
    <link rel="stylesheet" type="text/css" href="<?php echo $protocol; ?>static.wz2100.net/theme/warzone.css" />
    <link rel="stylesheet" type="text/css" href="<?php echo $protocol; ?>static.wz2100.net/theme/lytebox.css" />

    <script src="<?php echo $protocol; ?>static.wz2100.net/theme/jquery-1.4.1.min.js" type="text/javascript"></script>
    <script src="<?php echo $protocol; ?>static.wz2100.net/theme/lytebox.js" type="text/javascript"></script>
    <!--[if lt IE 7]>
    <style type="text/css">
        /* nothing... yet */
    </style>
    <![endif]--> 
    <title>Warzone 2100 Add-ons</title> 
<script>
<!--

var isadmin = <?php echo $isadmin?'true':'false'; ?>;

function htmlentities(text)
{
  if (!text) return '';
  return $('<div/>').text(text).html();
}
function addontype(curaddon)
{
  if (curaddon.type == 'map')
    return ''+curaddon.players+'-player map';
  else if (curaddon.type == 'mod')
    return 'Skirmish mod';
  else if (curaddon.type == 'gmod')
    return 'Global mod';
  else if (curaddon.type == 'cam')
    return 'Campaign mod';
}

function getform(curaddon)
{
  // generate a form for editing this addon
  var formdata = '';
  formdata += '<form action="/submit" method="post">';
  formdata += '<input type="hidden" id="act" name="act" value="editaddon" />';
  formdata += '<input type="hidden" id="fullid" name="fullid" value="'+curaddon.fullid+'" />';

  formdata += '<fieldset><legend>File</legend>';
  formdata += '<div class="formrow"><label>File:</label><cite><span style="display:block;padding-top:2px;"><code id="filename">'+htmlentities(curaddon.filename)+'</code> <em>(<a href="/'+htmlentities(curaddon.dir+curaddon.filename)+'">Download</a>)</em></span><iframe src="/fileform.php?type=wz&amp;fullid='+curaddon.fullid+'" style="width:340px;height:32px;"></iframe></cite></div>';
  formdata += '</fieldset>';

  formdata += '<fieldset><legend>General</legend>';
  formdata += '<div class="formrow"><label>ID:</label><cite>'+curaddon.id+'</cite></div>';
  formdata += '<div class="formrow"><label>Type:</label><cite>'+addontype(curaddon)+'</cite></div>';
  formdata += '<div class="formrow"><label for="name">Name:</label><input type="text" class="textbox" id="name" name="name" value="'+htmlentities(curaddon.name)+'" /></div>';
  formdata += '<div class="formrow"><label for="version">Version:</label><input type="text" class="textbox" id="version" name="version" value="'+htmlentities(curaddon.version)+'" /> <em>(Optional)</em></div>';
  formdata += '<div class="formrow"><label for="author">Author:</label><input type="text" class="textbox" id="author" name="author" value="'+htmlentities(curaddon.author)+'" /></div>';
  formdata += '<div class="formrow"><label for="desc">Description:</label><textarea class="textbox" id="desc" name="desc" style="width:340px;height:160px">'+htmlentities(curaddon.desc)+'</textarea></div>';
  if (curaddon.type == 'map')
  {
    formdata += '<div class="formrow"><em>If your map is not a normal free-for-all map (e.g. it is a team map, or it is a noobs-vs-pros map), you must mention this in the description.</em></div>';
  }
  if (curaddon.type == 'map')
  {
  formdata += '<div class="formrow"><label>Tileset:</label><select name="tileset" id="tileset">';
  formdata += '<option value="none"'+selected(!curaddon.tileset || curaddon.tileset=='none')+'> </option>'
  formdata += '<option value="arizona"'+selected(curaddon.tileset=='arizona')+'>Arizona</option>'
  formdata += '<option value="urban"'+selected(curaddon.tileset=='urban')+'>Urban</option>'
  formdata += '<option value="mountain"'+selected(curaddon.tileset=='mountain')+'>Mountain</option>'
  formdata += '</select></div>';
  }
  formdata += '</fieldset>';

  formdata += '<fieldset><legend>Images</legend>';
  formdata += '<div class="formrow"><label>Main picture:</label><cite><div id="addonpic">'+getaddonpic(curaddon)+'</div><iframe src="/fileform.php?type=pic&amp;fullid='+curaddon.fullid+'" style="width:300px;height:32px;"></iframe></cite></div>';
  formdata += '<div class="formrow"><label>Screenshots:</label><cite><div id="addonmorepics">'+getaddonmorepics(curaddon)+'</div><iframe src="/fileform.php?type=morepics&amp;fullid='+curaddon.fullid+'" style="width:300px;height:32px;" id="morepics" name="morepics"></iframe></cite></div>';
  formdata += '</fieldset>';
  
  formdata += '<fieldset><legend>Copyright</legend>';
  formdata += '<p><strong>Q:</strong> Why is this section so big?<br /><strong>A:</strong> Lawyers.</p>';
  formdata += '<div class="formrow" style="text-align:left"><label>License:</label><cite><input type="radio" name="license" id="cc0" value="CC-0"'+checked(curaddon.license=='CC-0')+' onclick="$(\'.assignrow\').css(\'display\',\'none\')" /> <label for="cc0" class="radio"><strong>CC-0</strong>: Users can do whatever they want with your work.</label>';
  formdata += '<br /><input type="radio" name="license" id="ccby" value="CC-BY-3.0+/GPLv2+"'+checked(curaddon.license=='CC-BY-3.0+/GPLv2+')+' onclick="$(\'.assignrow\').css(\'display\',\'block\')" /> <label for="ccby" class="radio"><strong>CC-BY-3.0 + GPLv2</strong>: As above, but users must attribute you when modifying/redistributing your work.</label>';
  formdata += '<br /><input type="radio" name="license" id="ccbysa" value="CC-BY-SA-3.0+/GPLv2+"'+checked(curaddon.license=='CC-BY-SA-3.0+/GPLv2+')+' onclick="$(\'.assignrow\').css(\'display\',\'block\')" /> <label for="ccbysa" class="radio"><strong>CC-BY-SA-3.0 + GPLv2</strong>: As above, but users must allow modifying/redistributing of anything they create using your work.</label></cite></div>';
  formdata += '<p>If you don\'t know which license to choose, we recommend CC-0. There\'s no need to restrict what users can do with your '+curaddon.category+', if you don\'t care about what they do with it.</p>'
  formdata += '<div class="formrow"><label>Certify:</label><cite><input type="checkbox" id="certify" name="certify"'+checked(curaddon.license)+(curaddon.license?' disabled="disabled"':'')+' /><label for="certify" class="radio">I certify that everything I upload here has been licensed under the above selected license by its creator.</label> <em>(Required)</em></cite></div>';
  formdata += '<div class="formrow assignrow"'+(curaddon.license=='CC-0'?' style="display:none"':'')+'><label>Assign:</label><cite><input type="checkbox" id="assigncopyright" name="assigncopyright"'+checked(curaddon.assignedcopyright)+' /><label for="assigncopyright" class="radio">I certify that I own the copyright of this '+curaddon.category+', and that reassigning copyright is allowed by my jurisdiction, and assign my copyright to Warzone 2100 Project</label> <em>(Optional)</em></cite></div>';
  formdata += '<p class="assignrow"'+(curaddon.license=='CC-0'?' style="display:none"':'')+'>If your work becomes orphaned (i.e. we cannot contact you), if you assign your copyright to us, we will be able to continue providing your '+curaddon.category+' to Warzone users. (Note: If you use the CC-0 license, this is not necessary.)</p>'
  formdata += '</fieldset>';

  if (isadmin)
  {
  formdata += '<div style="border:2px solid #112299;padding:5px">';
  formdata += '<div class="formrow" style="text-align:left"><label>Submitter:</label><cite><a href="<?php echo $protocol; ?>forums.wz2100.net/memberlist.php?mode=viewprofile&u='+curaddon.submitterid+'">'+curaddon.submitter+'</a> <code>'+curaddon.submitterid+'</code></cite></div>';
  formdata += '<div class="formrow" style="text-align:left"><label>Status:</label><cite><input type="radio" name="status" id="unapproved" value="unapproved"'+checked(!curaddon.approved)+' /> <label for="unapproved" class="radio">Unapproved</label>';
  formdata += '<br /><input type="radio" name="status" id="approved" value="approved"'+checked(curaddon.approved)+' /> <label for="approved" class="radio">Approved</label>';
  if (!curaddon.approved)
  formdata += '<br /><br /><input type="radio" name="status" id="spambin" value="spambin" /> <label for="spambin" class="radio">Spambin - CAUTION: CANNOT BE UNDONE - USE ONLY FOR SPAM</label>';
  formdata += '</div></div>';
  }

  formdata += '<div class="formrow"><br /><input type="submit" value="Submit" /> <input type="button" value="Cancel" onclick="window.location.href = \'/index.php?p=submit\'" /></div>';
  formdata += '</form>';
  return formdata;
}
function checked(val)
{
  return val?' checked="checked"':'';
}
function selected(val)
{
  return val?' selected="selected"':'';
}
function hidden(val)
{
  return val?' style="display:none"':'';
}
function getaddonpic(curaddon)
{
  if (curaddon.pic)
  {
    return '<a href="/'+curaddon.dir+curaddon.pic+'?'+Math.random()+'" rel="lytebox" onclick="myLytebox.start(this, false, false); return false"><img src="/'+curaddon.dir+'_thumb_80_pic.gif?'+Math.random()+'" alt="" /></a>';
  }
  else if (curaddon.category == 'map')
  {
    return '<em>This should be the map preview in the game setup screen.</em>';
  }
  else
  {
    return '<em>This should be its logo.</em>';
  }
}
function getaddonmorepics(curaddon)
{
  if (curaddon.morepics && curaddon.morepics.length)
  {
    var output = '';
    output += '<ul class="gallery">';
    for (var i=0; i<curaddon.morepics.length; i++)
    {
      output += '<li><a href="/'+curaddon.dir+curaddon.morepics[i]+'?'+Math.random()+'" rel="lytebox" onclick="myLytebox.start(this, false, false); return false"><img src="/'+curaddon.dir+'_thumb_80_pic'+(i+1)+'.gif?'+Math.random()+'" alt="" /></a><a href="#" onclick="deletemorepic('+i+')"><em>Delete</em></a></li>';
    }
    output += '</ul><div style="font-size:1px;overflow:visible;height:1px;clear:both;"></div>';
    return output;
  }
  else
  {
    return '<em>Screenshots of your '+curaddon.category+' can be uploaded here.</em>';
  }
}
function deletemorepic(picnum)
{
  morepics.action();
  morepics.document.getElementById('picnum').value = picnum;
  morepics.document.getElementById('delmorepic').submit();
}

function fileform_callback_morepics(curaddon)
{
  $('#addonmorepics').html(getaddonmorepics(curaddon));
}
function fileform_callback_pic(curaddon)
{
  $('#addonpic').html(getaddonpic(curaddon));
}
function fileform_callback_wz(curaddon)
{
  $('#filename').text(curaddon.filename);
}

-->
</script>
<style>
<!--

legend
{
  display: block;
  padding: 5px 0 2px 0;
  border-bottom: 1px solid #BBB;
}
.formrow cite
{
  display: block;
  font-style: normal;
  padding: 3px;
}
code
{
  font-size: 10pt;
}
ul.gallery
{
  list-style-type: none;
  display: block;
  margin: 0 !important;
  padding: 0 !important;
}
ul.gallery li
{
  list-style-type: none;
  display: block;
  margin: 0 !important;
  padding: 2px !important;
  float: left;
}
ul.gallery li a,
ul.gallery li img
{
  display: block;
  margin: 0;
  padding: 0; 
  border: 0 !important;
  text-align: center;
}

.addon
{
  margin:1em 0;
  border:1px solid #BCD4E3;
  background: #F3F7FA none;
  padding:10px;
  min-height: 80px;
}
.addon .addonlink
{
  display: block;
  border: 0;
  margin: 0;
  padding: 0;
  text-decoration: none;
  color: #000000;
}
.addon .addonlink:hover
{
  color: #2A556B;
}
.addon .addonlink strong
{
  /* border-bottom: 0; */
  text-decoration: none;
}
.addon .addonlink:hover strong
{
  /* border-bottom: 1px solid #7C94A3; */
  text-decoration: underline;
}
.addon .a,
.addon .more
{
  color: #7C94A3;
}
.addon .addonlink .more
{  display: none; }
.addon .addonlink:hover .more
{  display: inline; }
.addon .addonlink img,
.gallery a img
{  opacity: 1; }
.addon .addonlink:hover img,
.gallery a:hover img
{  opacity: 0.8; }
.addon .addonlink .nopic
{  color: #AAAAAA; border-color: #AAAAAA; }
.addon .addonlink:hover .nopic
{  color: #C6C6C6; border-color: #C6C6C6; }
.addon .addonlink:hover .addontype,
.addon .addonlink:hover .byline
{  color: #BBBBBB; }
.addon .downloadbtn
{
  margin-top: 0.8em;
}
.addon .pic:hover img
{  opacity: 0.9; }

.addon .p,
.addon div
{
  display: block;
  margin: 3px 0 5px 90px;
}
.addon .lp,
.addon div.lp
{
  display: block;
  margin: 3px 0 5px 330px;
}
.addon .pic img
{
  display: block;
}
.addon .pic,
.addon .nopic
{
  display: block;
  float: left;
}
.addon .pic
{
  border: 0 !important;
}
.addon .nopic
{
  width: 78px;
  height: 58px;
  border: 1px solid #AAAAAA;
  padding: 20px 0 0 0;
  color: #AAAAAA;
  font-style: italic;
  text-align: center;
}
.addon strong
{
  font-size: 13pt;
}
.addon .byline
{
  display: block;
  padding-left: 2em;
}
.addon .addontype
{
  font-style: normal;
  color: #777777;
  padding-left: 1em;
}

.rating
{
  float: right;
  font-size: 10pt;
  padding-left: 5px;
  padding-bottom: 5px;
}
.addon .rating em,
.rating em
{
  float:left;padding-right:0.5em;
  font-size: 10pt;
  font-style: normal;
  color: #555555;
}
.addon .rating strong,
.rating strong
{
  float:left;padding-left:0.5em;
  font-size: 10pt;
}

-->
</style>

  </head> 
  <body><div id="wrapper">

<?php print_header('addons'); ?>

    <div id="g_cont" class="warzone-content"> 
<?php
if (!$p)
{
  include 'addons-list.inc.php';
}
else if ($p == 'master')
{
  $showmaster = true;
  include 'addons-list.inc.php';
}
else if ($p == 'submit')
{
  include 'addons-submit.inc.php';
}
else if ($p == 'view')
{
  include 'addons-view.inc.php';
}
else if ($p == 'review')
{
  include 'addons-review.inc.php';
}
else
{
  echo '404';
}
?>
    <div style="clear: both;"></div> 
    </div> 
<?php print_footer(); ?>
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
