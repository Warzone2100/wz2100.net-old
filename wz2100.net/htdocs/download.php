<?php
include_once(dirname(__FILE__).'/../lib/global.lib.php');

// Loads the data for the "WARZONE" variable
include_once(dirname(__FILE__).'/lib/warzone.inc.php');


?>
<!DOCTYPE html>
<html xml:lang="en" lang="en">
  <head>
    <meta http-equiv="content-type" content="text/html; charset=utf-8" />

    <title>Download Warzone 2100</title> 

    <link rel="shortcut icon" href="<?php echo $protocol; ?>static.wz2100.net/favicon.ico" type="image/x-icon" />
    <link rel="alternate" type="application/atom+xml" title="Warzone 2100 News" href="http://wz2100.net/feed.atom" />

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

    <?php print_header('download'); ?>

    <div class="warzone-content">
<?php
// edit button
  if ($isadmin && @$_POST['vname'])
  {
    $WARZONE['currentversion']['name'] = $_POST['vname'];

    $WARZONE['currentversion']['dl_win'] = $_POST['dl_win'];
    $WARZONE['currentversion']['dl_win_size'] = $_POST['dl_win_size'];
    //$WARZONE['currentversion']['dl_win_md5'] = $_POST['dl_win_md5'];
    $WARZONE['currentversion']['dl_mac'] = $_POST['dl_mac'];
    $WARZONE['currentversion']['dl_mac_size'] = $_POST['dl_mac_size'];
    $WARZONE['currentversion']['dl_mac_version'] = $_POST['dl_mac_version'];
    //$WARZONE['currentversion']['dl_mac_md5'] = $_POST['dl_mac_md5'];
    $WARZONE['currentversion']['dl_mac_lq'] = $_POST['dl_mac_lq'];
    $WARZONE['currentversion']['dl_mac_lq_size'] = $_POST['dl_mac_lq_size'];
    //$WARZONE['currentversion']['dl_mac_lq_md5'] = $_POST['dl_mac_lq_md5'];
    $WARZONE['currentversion']['dl_mac_novid'] = $_POST['dl_mac_novid'];
    $WARZONE['currentversion']['dl_mac_novid_size'] = $_POST['dl_mac_novid_size'];
    //$WARZONE['currentversion']['dl_mac_novid_md5'] = $_POST['dl_mac_novid_md5'];
    $WARZONE['currentversion']['dl_src'] = $_POST['dl_src'];
    $WARZONE['currentversion']['dl_src_size'] = $_POST['dl_src_size'];
    //$WARZONE['currentversion']['dl_src_md5'] = $_POST['dl_src_md5'];

    $WARZONE['betaversion']['name'] = $_POST['dlbeta_vname'];
    $WARZONE['betaversion']['versiontype'] = $_POST['dlbeta_type'];
    
    $WARZONE['betaversion']['dl_win'] = $_POST['dlbeta_win'];
    $WARZONE['betaversion']['dl_win_size'] = $_POST['dlbeta_win_size'];
    //$WARZONE['betaversion']['dl_win_md5'] = $_POST['dlbeta_win_md5'];
    $WARZONE['betaversion']['dl_mac'] = $_POST['dlbeta_mac'];
    $WARZONE['betaversion']['dl_mac_size'] = $_POST['dlbeta_mac_size'];
    $WARZONE['betaversion']['dl_mac_version'] = $_POST['dlbeta_mac_version'];
    //$WARZONE['betaversion']['dl_mac_md5'] = $_POST['dlbeta_mac_md5'];
    $WARZONE['betaversion']['dl_mac_lq'] = $_POST['dlbeta_mac_lq'];
    $WARZONE['betaversion']['dl_mac_lq_size'] = $_POST['dlbeta_mac_lq_size'];
    //$WARZONE['betaversion']['dl_mac_lq_md5'] = $_POST['dlbeta_mac_lq_md5'];
    $WARZONE['betaversion']['dl_mac_novid'] = $_POST['dlbeta_mac_novid'];
    $WARZONE['betaversion']['dl_mac_novid_size'] = $_POST['dlbeta_mac_novid_size'];
    //$WARZONE['betaversion']['dl_mac_novid_md5'] = $_POST['dlbeta_mac_novid_md5'];
    $WARZONE['betaversion']['dl_src'] = $_POST['dlbeta_src'];
    $WARZONE['betaversion']['dl_src_size'] = $_POST['dlbeta_src_size'];
    //$WARZONE['betaversion']['dl_src_md5'] = $_POST['dlbeta_src_md5'];

    $WARZONE['videos']['hq'] = $_POST['dlvid_hq'];
    $WARZONE['videos']['hq_size'] = $_POST['dlvid_hq_size'];
    $WARZONE['videos']['hq_md5'] = $_POST['dlvid_hq_md5'];
    $WARZONE['videos']['lq'] = $_POST['dlvid_lq'];
    $WARZONE['videos']['lq_size'] = $_POST['dlvid_lq_size'];
    $WARZONE['videos']['lq_md5'] = $_POST['dlvid_lq_md5'];

    persist_save('WARZONE', dirname(__FILE__) . '/lib/warzone.inc.php') || print('error');
    echo '<p>[edit successful]</p>';
  }
  if ($isadmin)
  {
    echo '<div style="float:right"><a onclick="document.getElementById(\'edit_dl\').style.display=\'block\';return false" href="#">Edit</a></div>';
?>
      <div id="edit_dl" style="display:none"><form action="/download" method="post"><input type="hidden" name="toedit" value="dl" />
        <h1>Stable version</h1>
        <p>
          <label for="vname">Current version: Warzone 2100 </label><input id="vname" name="vname" type="text" class="textbox" size="15" value="<?php echo @$WARZONE['currentversion']['name']; ?>" />
        </p>
        <h2>Windows</h2>
        <p>
          <label for="dl_win">URL: </label><input id="dl_win" name="dl_win" type="text" class="textbox" size="70" value="<?php echo @$WARZONE['currentversion']['dl_win']; ?>" /><br />
          <label for="dl_win_size">Size: </label><input id="dl_win_size" name="dl_win_size" type="text" class="textbox" size="10" value="<?php echo @$WARZONE['currentversion']['dl_win_size']; ?>" />
          <label for="dl_win_md5">MD5: </label><input id="dl_win_md5" name="dl_win_md5" type="text" class="textbox" size="33" value="<?php echo @$WARZONE['currentversion']['dl_win_md5']; ?>" />
        </p>
        <h2>Mac OS X - standard quality videos</h2>
        <p>
          <label for="dl_mac">URL: </label><input id="dl_mac" name="dl_mac" type="text" class="textbox" size="70" value="<?php echo @$WARZONE['currentversion']['dl_mac']; ?>" /><br />
          <label for="dl_mac_size">Size: </label><input id="dl_mac_size" name="dl_mac_size" type="text" class="textbox" size="10" value="<?php echo @$WARZONE['currentversion']['dl_mac_size']; ?>" />
          <label for="dl_mac_md5">MD5: </label><input id="dl_mac_md5" name="dl_mac_md5" type="text" class="textbox" size="33" value="<?php echo @$WARZONE['currentversion']['dl_mac_md5']; ?>" /><br />
          <label for="dl_mac_size">For </label><input id="dl_mac_version" name="dl_mac_version" type="text" class="textbox" size="20" value="<?php echo @$WARZONE['currentversion']['dl_mac_version']; ?>" />
        </p>
        <h2>Mac OS X - low quality videos</h2>
        <p>
          <label for="dl_mac_lq">URL: </label><input id="dl_mac_lq" name="dl_mac_lq" type="text" class="textbox" size="70" value="<?php echo @$WARZONE['currentversion']['dl_mac_lq']; ?>" /><br />
          <label for="dl_mac_lq_size">Size: </label><input id="dl_mac_lq_size" name="dl_mac_lq_size" type="text" class="textbox" size="10" value="<?php echo @$WARZONE['currentversion']['dl_mac_lq_size']; ?>" />
          <label for="dl_mac_lq_md5">MD5: </label><input id="dl_mac_lq_md5" name="dl_mac_lq_md5" type="text" class="textbox" size="33" value="<?php echo @$WARZONE['currentversion']['dl_mac_lq_md5']; ?>" />
        </p>
        <h2>Mac OS X - no videos</h2>
        <p>
          <label for="dl_mac_novid">URL: </label><input id="dl_mac_novid" name="dl_mac_novid" type="text" class="textbox" size="70" value="<?php echo @$WARZONE['currentversion']['dl_mac_novid']; ?>" /><br />
          <label for="dl_mac_novid_size">Size: </label><input id="dl_mac_novid_size" name="dl_mac_novid_size" type="text" class="textbox" size="10" value="<?php echo @$WARZONE['currentversion']['dl_mac_novid_size']; ?>" />
          <label for="dl_mac_novid_md5">MD5: </label><input id="dl_mac_novid_md5" name="dl_mac_novid_md5" type="text" class="textbox" size="33" value="<?php echo @$WARZONE['currentversion']['dl_mac_novid_md5']; ?>" />
        </p>
        <h2>Source tarball</h2>
        <p>
          <label for="dl_src">URL: </label><input id="dl_src" name="dl_src" type="text" class="textbox" size="70" value="<?php echo @$WARZONE['currentversion']['dl_src']; ?>" /><br />
          <label for="dl_src_size">Size: </label><input id="dl_src_size" name="dl_src_size" type="text" class="textbox" size="10" value="<?php echo @$WARZONE['currentversion']['dl_src_size']; ?>" />
          <label for="dl_src_md5">MD5: </label><input id="dl_src_md5" name="dl_src_md5" type="text" class="textbox" size="33" value="<?php echo @$WARZONE['currentversion']['dl_src_md5']; ?>" />
        </p>
        <h1>Beta or RC version</h1>
        <p>
        	<strong style="color:red">ONLY PUT BETA OR RC VERSIONS HERE.</strong> This will place a "Try beta" or "Try RC" button on the front page, next to the Download button. No other builds should go in that precise location, since users expect a fairly stable version from a download from that location. Feel free to manually modify the front page if you have other preview builds you think should go there. (The version type dropdown also allows "alpha" to be selected, in case we ever decide to use the "stable but not feature-complete" definition of "alpha".)
        </p>
        <p>
          <label for="vname">Testing version: Warzone 2100 </label><input id="dlbeta_vname" name="dlbeta_vname" type="text" class="textbox" size="15" value="<?php echo @$WARZONE['betaversion']['name']; ?>" /> (leave blank to disable) <br />
          This is: <select name="dlbeta_type"><option value="alpha"<?php if (@$WARZONE['betaversion']['versiontype']=='alpha') echo ' selected="selected"'; ?>>alpha</option><option value="beta"<?php if (@$WARZONE['betaversion']['versiontype']=='beta') echo ' selected="selected"'; ?>>beta</option><option value="RC"<?php if (@$WARZONE['betaversion']['versiontype']=='RC') echo ' selected="selected"'; ?>>RC</option></select>
        </p>
        <h2>Windows</h2>
        <p>
          <label for="dl_win">URL: </label><input id="dlbeta_win" name="dlbeta_win" type="text" class="textbox" size="70" value="<?php echo @$WARZONE['betaversion']['dl_win']; ?>" /><br />
          <label for="dl_win_size">Size: </label><input id="dlbeta_win_size" name="dlbeta_win_size" type="text" class="textbox" size="10" value="<?php echo @$WARZONE['betaversion']['dl_win_size']; ?>" />
          <label for="dl_win_md5">MD5: </label><input id="dlbeta_win_md5" name="dlbeta_win_md5" type="text" class="textbox" size="33" value="<?php echo @$WARZONE['betaversion']['dl_win_md5']; ?>" />
        </p>
        <h2>Mac OS X - standard quality videos</h2>
        <p>
          <label for="dl_mac">URL: </label><input id="dlbeta_mac" name="dlbeta_mac" type="text" class="textbox" size="70" value="<?php echo @$WARZONE['betaversion']['dl_mac']; ?>" /><br />
          <label for="dl_mac_size">Size: </label><input id="dlbeta_mac_size" name="dlbeta_mac_size" type="text" class="textbox" size="10" value="<?php echo @$WARZONE['betaversion']['dl_mac_size']; ?>" />
          <label for="dl_mac_md5">MD5: </label><input id="dlbeta_mac_md5" name="dlbeta_mac_md5" type="text" class="textbox" size="33" value="<?php echo @$WARZONE['betaversion']['dl_mac_md5']; ?>" /><br />
          <label for="dl_mac_size">For </label><input id="dlbeta_mac_version" name="dlbeta_mac_version" type="text" class="textbox" size="20" value="<?php echo @$WARZONE['betaversion']['dl_mac_version']; ?>" />
        </p>
        <h2>Mac OS X - low quality videos</h2>
        <p>
          <label for="dl_mac_lq">URL: </label><input id="dlbeta_mac_lq" name="dlbeta_mac_lq" type="text" class="textbox" size="70" value="<?php echo @$WARZONE['betaversion']['dl_mac_lq']; ?>" /><br />
          <label for="dl_mac_lq_size">Size: </label><input id="dlbeta_mac_lq_size" name="dlbeta_mac_lq_size" type="text" class="textbox" size="10" value="<?php echo @$WARZONE['betaversion']['dl_mac_lq_size']; ?>" />
          <label for="dl_mac_lq_md5">MD5: </label><input id="dlbeta_mac_lq_md5" name="dlbeta_mac_lq_md5" type="text" class="textbox" size="33" value="<?php echo @$WARZONE['betaversion']['dl_mac_lq_md5']; ?>" />
        </p>
        <h2>Mac OS X - no videos</h2>
        <p>
          <label for="dl_mac_novid">URL: </label><input id="dlbeta_mac_novid" name="dlbeta_mac_novid" type="text" class="textbox" size="70" value="<?php echo @$WARZONE['betaversion']['dl_mac_novid']; ?>" /><br />
          <label for="dl_mac_novid_size">Size: </label><input id="dlbeta_mac_novid_size" name="dlbeta_mac_novid_size" type="text" class="textbox" size="10" value="<?php echo @$WARZONE['betaversion']['dl_mac_novid_size']; ?>" />
          <label for="dl_mac_novid_md5">MD5: </label><input id="dlbeta_mac_novid_md5" name="dlbeta_mac_novid_md5" type="text" class="textbox" size="33" value="<?php echo @$WARZONE['betaversion']['dl_mac_novid_md5']; ?>" />
        </p>
        <h2>Source tarball</h2>
        <p>
          <label for="dl_src">URL: </label><input id="dlbeta_src" name="dlbeta_src" type="text" class="textbox" size="70" value="<?php echo @$WARZONE['betaversion']['dl_src']; ?>" /><br />
          <label for="dl_src_size">Size: </label><input id="dlbeta_src_size" name="dlbeta_src_size" type="text" class="textbox" size="10" value="<?php echo @$WARZONE['betaversion']['dl_src_size']; ?>" />
          <label for="dl_src_md5">MD5: </label><input id="dlbeta_src_md5" name="dlbeta_src_md5" type="text" class="textbox" size="33" value="<?php echo @$WARZONE['betaversion']['dl_src_md5']; ?>" />
        </p>
        <h3>Videos</h3>
        <p>
          <label for="dlvid_hq">HQ: </label><input id="dlvid_hq" name="dlvid_hq" type="text" class="textbox" size="70" value="<?php echo @$WARZONE['videos']['hq']; ?>" /><br />
          <label for="dlvid_hq_size">Size: </label><input id="dlvid_hq_size" name="dlvid_hq_size" type="text" class="textbox" size="10" value="<?php echo @$WARZONE['videos']['hq_size']; ?>" />
          <label for="dlvid_hq_md5">MD5: </label><input id="dlvid_hq_md5" name="dlvid_hq_md5" type="text" class="textbox" size="33" value="<?php echo @$WARZONE['videos']['hq_md5']; ?>" />
        </p>
        <p>
          <label for="dlvid_lq">LQ: </label><input id="dlvid_lq" name="dlvid_lq" type="text" class="textbox" size="70" value="<?php echo @$WARZONE['videos']['lq']; ?>" /><br />
          <label for="dlvid_lq_size">Size: </label><input id="dlvid_lq_size" name="dlvid_lq_size" type="text" class="textbox" size="10" value="<?php echo @$WARZONE['videos']['lq_size']; ?>" />
          <label for="dlvid_lq_md5">MD5: </label><input id="dlvid_lq_md5" name="dlvid_lq_md5" type="text" class="textbox" size="33" value="<?php echo @$WARZONE['videos']['lq_md5']; ?>" />
        </p>
        <div><input type="submit" value="Change" /> <input type="button" value="Cancel" onclick="document.getElementById('edit_dl').style.display='none';return false" /></div></form>
      </div>
<?php
  }
?>
      <h1>Latest stable version &ndash; <?php echo @$WARZONE['currentversion']['name']; ?></h1>
      <div class="content-box-margin" id="download-win">
        <h2>Windows</h2>
        <p class="byline">
          For Windows XP, Vista, or 7
        </p>
        <div class="downloadbtn">
          <a href="<?php echo @$WARZONE['currentversion']['dl_win'] ?>"><em>Download</em> <strong>Warzone 2100</strong> <?php echo @$WARZONE['currentversion']['name'] ?> <small>for Windows XP+ <sub><?php echo @$WARZONE['currentversion']['dl_win_size'] ?></sub></small></a>
          <small><?php echo @$WARZONE['currentversion']['dl_win_md5']; ?></small>
        </div>
        <div style="clear:both"></div>
        <h3 id="videos-win"><span>Videos</span></h3>
        <div class="downloadbtn" style="float:left;">
          <a href="<?php echo @$WARZONE['videos']['hq'] ?>" class="betaversion"><em>Download videos</em> <strong>standard quality</strong> <sub>545 MB</sub></a>
        </div>
        <div class="downloadbtn" style="float:left;">
          <a href="<?php echo @$WARZONE['videos']['lq'] ?>" class="betaversion"><em>Download videos</em> <strong>low quality</strong> <sub>162 MB</sub></a>
        </div>
        <div style="clear:both"></div>
        <p style="margin-top:1em">
          <strong>Note:</strong> The installer will install these videos automatically. Follow these instructions only if the installer fails, or if you wish to install offline.
        </p>
        <p>
          <strong>Video instructions:</strong> Place <code>sequences.wz</code> in <code><strong>Program Files/Warzone 2100/sequences.wz</strong></code>.
        </p>
        <p>
          <strong>Warning:</strong> If the file you downloaded is <code>sequences.zip</code>, please rename it to <code>sequences.wz</code> . <strong>DO NOT EXTRACT IT</strong>.
        </p>
      </div>

      <div class="content-box-margin" id="download-mac">
        <h2>Mac OS X</h2>
        <p class="byline">
          For Mac OS X 10.4&ndash;10.6
        </p>
        <div class="downloadbtn" style="float:left;">
          <a href="<?php echo @$WARZONE['currentversion']['dl_mac'] ?>"><em>Download standard</em> <strong>Warzone 2100</strong> <?php echo @$WARZONE['currentversion']['name']; ?> <small>with standard quality videos</small> <small>for <?php echo @$WARZONE['currentversion']['dl_mac_version']; ?><sub><?php echo @$WARZONE['currentversion']['dl_mac_size']; ?></sub></small></a>
        </div>
        <div class="downloadbtn" style="float:left;">
          <a href="<?php echo @$WARZONE['currentversion']['dl_mac_lq'] ?>" class="betaversion"><em>Download LQ</em> <strong>Warzone 2100</strong> <?php echo @$WARZONE['currentversion']['name']; ?> <small>with low quality videos</small> <small>for <?php echo @$WARZONE['currentversion']['dl_mac_version']; ?> <sub><?php echo @$WARZONE['currentversion']['dl_mac_lq_size']; ?></sub></small></a>
        </div>
        <div class="downloadbtn" style="float:left;">
          <a href="<?php echo @$WARZONE['currentversion']['dl_mac_novid'] ?>" class="betaversion"><em>Download no video</em> <strong>Warzone 2100</strong> <?php echo @$WARZONE['currentversion']['name']; ?> <small>no videos</small> <small>for <?php echo @$WARZONE['currentversion']['dl_mac_version']; ?> <sub><?php echo @$WARZONE['currentversion']['dl_mac_novid_size']; ?></sub></small></a>
        </div>
        <div style="clear:both"></div>
        <h3>Notes</h3>
        <ol>
          <li>You need X11 installed (should come by default since 10.5), which can be found on the install DVD.</li>
          <li>If you have a Nvidia based Mac on 10.5.6-10.5.9, there is a bug that makes the game look wrong. To fix, upgrade to 10.6 or higher, or use <a href="http://www.insanelymac.com/forum/lofiversion/index.php/t132089.html">these custom Nvidia drivers</a>.</li>
        </ol>
        <h3 id="videos-mac"><span>Videos</span></h3>
        <div class="downloadbtn" style="float:left;">
          <a href="<?php echo @$WARZONE['videos']['hq'] ?>" class="betaversion"><em>Download videos</em> <strong>standard quality</strong> <sub>545 MB</sub></a>
        </div>
        <div class="downloadbtn" style="float:left;">
          <a href="<?php echo @$WARZONE['videos']['lq'] ?>" class="betaversion"><em>Download videos</em> <strong>low quality</strong> <sub>162 MB</sub></a>
        </div>
        <div style="clear:both"></div>
        <p style="margin-top:1em">
          <strong>Note:</strong> The standard download comes with standard quality videos. If you wish, to make upgrades faster, you can download the no-video version and the videos separately, and install them using the instructions below.
        </p>
        <p>
          <strong>Video instructions:</strong> Place <code>sequences.wz</code> in <code><strong>~/Library/Application Support/Warzone 2100 2.3/sequences.wz</strong></code>
        </p>
      </div>

      <div class="content-box-margin" id="download-src">
        <h2>Linux and other</h2>
        <p class="byline">
          For Linux, BSD, or other operating systems
        </p>
        <p>
          Warzone is available in most distributions' repositories, although it may be out of date. The latest version can be downloaded here.
        </p>
        <div class="downloadbtn">
          <a href="<?php echo @$WARZONE['currentversion']['dl_src'] ?>"><em>Download</em> <strong>Warzone 2100</strong> <?php echo @$WARZONE['currentversion']['name']; ?> <small>Source code tarball <sub><?php echo @$WARZONE['currentversion']['dl_src_size']; ?></sub></small></a><?php echo @$WARZONE['currentversion']['dl_src_md5'] ?>
        </div>
        <div style="clear:both"></div>
        <p>
          For instructions, see <a href="<?php echo $protocol; ?>developer.wz2100.net/wiki/CompileGuideLinux">Linux Compile Guide</a>.
        </p>
        <h3 id="videos-src"><span>Videos</span></h3>
        <div class="downloadbtn" style="float:left;">
          <a href="<?php echo @$WARZONE['videos']['hq'] ?>" class="betaversion"><em>Download videos</em> <strong>standard quality</strong> <sub>545 MB</sub></a>
        </div>
        <div class="downloadbtn" style="float:left;">
          <a href="<?php echo @$WARZONE['videos']['lq'] ?>" class="betaversion"><em>Download videos</em> <strong>low quality</strong> <sub>162 MB</sub></a>
        </div>
        <div style="clear:both"></div>
        <p style="margin-top:1em">
          <strong>Video instructions:</strong> Place <code>sequences.wz</code> in <code><strong>~/.warzone2100-2.3/sequences.wz</strong></code>
        </p>
      </div>

<script>
<!--
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

if (window.location.hash == 'win') BrowserDetect.OS = 'Win';
if (window.location.hash == 'mac') BrowserDetect.OS = 'Mac';
if (window.location.hash == 'src') BrowserDetect.OS = 'Linux';

if (BrowserDetect.OS == 'Win') {
  document.getElementById('download-mac').style.display = 'none';
  document.getElementById('download-src').style.display = 'none';
  document.getElementById('videos-win').getElementsByTagName('span')[0].id = 'videos';
} else if (BrowserDetect.OS == 'Mac') {
  document.getElementById('download-win').style.display = 'none';
  document.getElementById('download-src').style.display = 'none';
  document.getElementById('videos-mac').getElementsByTagName('span')[0].id = 'videos';
} else if (BrowserDetect.OS == 'iPhone') {
  document.write('<div class="content-box-margin"><p>Warzone 2100 is not available for the iPhone at this time.</p></div>');
} else {
  document.getElementById('download-win').style.display = 'none';
  document.getElementById('download-mac').style.display = 'none';
  document.getElementById('videos-src').getElementsByTagName('span')[0].id = 'videos';
}
function displayall()
{
  document.getElementById('download-win').style.display = 'block';
  document.getElementById('download-mac').style.display = 'block';
  document.getElementById('download-src').style.display = 'block';
  document.getElementById('download-displayall').style.display = 'none';
}
document.write('<div class="content-box-margin" id="download-displayall"><p><a href="#" onclick="displayall()">Display downloads for other OSes</a></p></div>');

//-->
</script>

      <div class="content-box">
        <h2>System requirements</h2>
        <p>These are approximate.</p>
        <ul>
          <li>At least 1 GHz CPU</li>
          <li>At least 128 MB VRAM</li>
          <li>OpenGL 1.4 (make sure you have the latest drivers installed)</li>
          <li>At least 100 MB free RAM for the game</li>
          <li>At least 100 MB HD space for the game, up to 600 MB extra for the standard videos</li>
        </ul>
        <p>Operating system - one of:</p>
        <ul>
          <li>Windows XP SP3 or higher</li>
          <li>Mac OS X 10.4 or higher</li>
          <li>Linux or BSD</li>
          <li>You will most likely need the latest version of the drivers for your video card.</li>
        </ul>
      </div>

<?php
if (@$WARZONE['betaversion']['name'])
{
?>

      <h1 id="testing">Latest <?php echo $WARZONE['betaversion']['versiontype'] ?> version &ndash; <?php echo @$WARZONE['betaversion']['name']; ?></h1>

      <div class="content-box-margin" id="betadownload-win">
        <h2>Windows</h2>
        <p class="byline">
          For Windows XP, Vista, or 7
        </p>
        <div class="downloadbtn">
          <a href="<?php echo @$WARZONE['betaversion']['dl_win'] ?>" class="betaversion"><em>Test the <?php echo $WARZONE['betaversion']['versiontype'] ?></em> <strong>Warzone 2100</strong> <?php echo @$WARZONE['betaversion']['name'] ?> <small>for Windows XP+ <sub><?php echo @$WARZONE['betaversion']['dl_win_size'] ?></sub></small></a>
          <small><?php echo @$WARZONE['betaversion']['dl_win_md5']; ?></small>
        </div>
        <div style="clear:both"></div>
      </div>

      <div class="content-box-margin" id="betadownload-mac">
        <h2>Mac OS X</h2>
        <p class="byline">
          For Mac OS X 10.4&ndash;10.6
        </p>
        <div class="downloadbtn" style="float:left;">
          <a href="<?php echo @$WARZONE['betaversion']['dl_mac_novid'] ?>" class="betaversion"><em>Test the <?php echo $WARZONE['betaversion']['versiontype'] ?></em> <strong>Warzone 2100</strong> <?php echo @$WARZONE['betaversion']['name']; ?> <small>with no videos</small> <small>for <?php echo @$WARZONE['betaversion']['dl_mac_version']; ?><sub><?php echo @$WARZONE['betaversion']['dl_mac_novid_size']; ?></sub></small></a>
        </div>
        <div style="clear:both"></div>
      </div>

      <div class="content-box-margin" id="betadownload-src">
        <h2>Linux and other</h2>
        <p class="byline">
          For Linux, BSD, or other operating systems
        </p>
        <div class="downloadbtn">
          <a href="<?php echo @$WARZONE['betaversion']['dl_src'] ?>" class="betaversion"><em>Test the <?php echo $WARZONE['betaversion']['versiontype'] ?></em> <strong>Warzone 2100</strong> <?php echo @$WARZONE['betaversion']['name']; ?> <small>Source code tarball <sub><?php echo @$WARZONE['betaversion']['dl_src_size']; ?></sub></small></a><?php echo @$WARZONE['betaversion']['dl_src_md5'] ?>
        </div>
        <div style="clear:both"></div>
      </div>

<script>
<!--

if (BrowserDetect.OS == 'Win') {
  document.getElementById('betadownload-mac').style.display = 'none';
  document.getElementById('betadownload-src').style.display = 'none';
} else if (BrowserDetect.OS == 'Mac') {
  document.getElementById('betadownload-win').style.display = 'none';
  document.getElementById('betadownload-src').style.display = 'none';
} else if (BrowserDetect.OS == 'iPhone') {
  document.write('<div class="content-box-margin"><p>Warzone 2100 is not available for the iPhone at this time.</p></div>');
} else {
  document.getElementById('betadownload-win').style.display = 'none';
  document.getElementById('betadownload-mac').style.display = 'none';
}
function displayallbeta()
{
  document.getElementById('betadownload-win').style.display = 'block';
  document.getElementById('betadownload-mac').style.display = 'block';
  document.getElementById('betadownload-src').style.display = 'block';
  document.getElementById('betadownload-displayall').style.display = 'none';
}
document.write('<div class="content-box-margin" id="betadownload-displayall"><p><a href="#testing" onclick="displayallbeta()">Display downloads for other OSes</a></p></div>');

//-->
</script>

<?php
}
?>

      <h1>Other versions</h1>
      <div class="content-box-margin">
        <p>Older versions of Warzone 2100 are available at <a href="https://sourceforge.net/projects/warzone2100/files/">our Sourceforge mirror</a>, and even older releases at <a href="http://download.gna.org/warzone/releases/">our GNA mirror</a>.</p>
        <p>Experimental builds are available at <a href="<?php echo $protocol; ?>developer.wz2100.net/wiki/ExperimentalBuilds">the Experimental Builds wiki page.</a></p>
      </div>
      <div class="content-box-margin">
        <h2>Git</h2>
        <p>Our git repository is located at:</p>
        <blockquote><p><code>git://github.com/Warzone2100/warzone2100.git</code></p></blockquote>
        <p><a href="<?php echo $protocol; ?>developer.wz2100.net/browser/?rev=master">Browse repository online</a></p>
        <p>For more development information, see <a href="<?php echo $protocol; ?>developer.wz2100.net/">Development</a></p>
      </div>
    </div> 
<?php print_footer(); ?>
  </div>
</body> 
</html>
