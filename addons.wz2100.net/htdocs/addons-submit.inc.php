<?php
$fullid = strval(@$_REQUEST['fullid']);
if (!$fullid) $fullid = strval(@$_REQUEST['sub']);

?>
<script>
<!--

function fileform_callback_wz_new(curaddon)
{
  // omgomg successful upload
  $('#addonform').html(getform(curaddon));
}
function fileform_errors(errors)
{
  $('#errors').html(errors);
}

-->
</script>
<h1>Submit an addon</h1>
<p>
  <a href="/">&laquo; Back to addon list</a>
</p>
<div id="errors"><?php echo rendererrors();

if ($acted)
{
  echo '<p style="margin:1em 0;padding:2px 4px;border:2px solid #000088;">Submitted!</p>';
  $fullid = '';
}

?></div>
<?php
if ($loggedinuserid)
{
?>
<div id="addonform">
<p>
If you are uploading a map, it must be named: <code>#c-[mapname].wz</code>, where # is the number of players.<br />
[mapname] may not contain any spaces or exceed 35 characters in length.
</p>
<ul>
	<li><strong>Do not</strong> create maps that have the <em>same name</em>.</li>
	<li><strong>Do not</strong> convert 8 or 4 player maps to 7/6/5/3 players <em>and leave the name the same!</em></li>
	<li>All maps should have a version number in them, until the final map is done.</li>
</ul>
<?php
  $youraddons = getyouraddons();
  if ($youraddons)
  {
?>
<h2>
Edit your addons!
</h2>
<ul>
<?php
    foreach ($youraddons as $addonid)
    {
      $addon = getaddon($addonid);
      echo '<li><a href="/submit/'.$addon['fullid'].'">'.htmlentities($addon['name'].($addon['version']?' '.$addon['version']:'')).'</a>'.(isset($addon['rating'])?' (Rating: <strong>'.$addon['rating'].'</strong>)':'').'</li>';
    }
?>
</ul>
<?php
  }
  $youraddons = getyourunapprovedaddons();
  if ($youraddons)
  {
?>
<h2>
Edit your unapproved addons!
</h2>
<?php
  if ($isadmin) echo '<p><strong>Approval instructions:</strong> Any addon that is rated 1.0 or higher should be approved. For rating instructions, see <a href="/review">Review</a>.</p>';
?>
<ul>
<?php
    foreach ($youraddons as $addonid)
    {
      $addon = getaddon($addonid);
      echo '<li><a href="/submit/'.$addon['fullid'].'">'.htmlentities($addon['name'].($addon['version']?' '.$addon['version']:'')).(!@$addon['unfinished']?'':' - <em>Unfinished</em>').'</a>'.(isset($addon['rating'])?' (Rating: <strong>'.$addon['rating'].'</strong>)':'');
      if (getaddon(substr($addonid,strlen('unapproved/')))) echo ' <strong>(Update)</strong>';
      echo '</li>';
    }
?>
</ul>
<?php
  }
?>
<h2>
  Upload a new map or mod:
</h2>
<div class="formrow"><iframe src="/fileform.php?type=wz" style="width:340px;height:32px;"></iframe></div>
<div class="formrow"><p>
  (You will be able to add details after you have selected a map or mod to upload.)
</p></div>
</div>
<?php
  if ($fullid)
  {
    if ($curaddon = getaddon('unapproved/'.$fullid))
      $fullid = 'unapproved/'.$fullid;
    else
      $curaddon = getaddon($fullid);
    if ($curaddon && checkuser($curaddon['submitterid']))
    {
?>
<script>
  $('#addonform').html(getform((<?php echo $curaddon?json_encode($curaddon):'404'; ?>)));
</script>
<?php
    }
  }
}
else
{
?>
<form action="http://forums.wz2100.net/ucp.php?mode=login" method="post" id="login">
<div class="panel">
  <div class="inner"><span class="corners-top"><span></span></span>
  <div class="content">
    <h2>You must be logged in to submit maps or mods.</h2>
    <dl>
      <dt><label for="username">Username:</label></dt>
      <dd><input type="text" tabindex="1" name="username" id="username" size="25" value="" class="inputbox autowidth" /></dd>
    </dl>
    <dl>
      <dt><label for="password">Password:</label></dt>
      <dd><input type="password" tabindex="2" id="password" name="password" size="25" class="inputbox autowidth" /></dd>
      <dd><a href="http://forums.wz2100.net/ucp.php?mode=sendpassword">I forgot my password</a></dd><dd><a href="http://forums.wz2100.net/ucp.php?mode=resend_act">Resend activation e-mail</a></dd>
    </dl>
    <dl>
      <dd><label for="autologin"><input type="checkbox" name="autologin" id="autologin" tabindex="4" /> Log me on automatically each visit</label></dd>
      <dd><label for="viewonline"><input type="checkbox" name="viewonline" id="viewonline" tabindex="5" /> Hide my online status this session</label></dd>
    </dl>
    <dl>
      <dt>&nbsp;</dt>
      <dd><input type="hidden" name="sid" value="<?php echo $user->data['session_id']; ?>" />
<input type="submit" name="login" tabindex="6" value="Login" class="button1" /></dd>
    </dl>
    <input type="hidden" name="redirect" value="./addons-submit" />
  </div>
  <span class="corners-bottom"><span></span></span></div>
</div>

  <div class="panel">
    <div class="inner"><span class="corners-top"><span></span></span>
 
    <div class="content">
      <h3>Register</h3>
      <p>In order to login you must be registered. Registering takes only a few moments but gives you increased capabilities. The board administrator may also grant additional permissions to registered users. Before you register please ensure you are familiar with our terms of use and related policies. Please ensure you read any forum rules as you navigate around the board.</p>
      <p><strong><a href="http://forums.wz2100.net/ucp.php?mode=terms">Terms of use</a> | <a href="http://forums.wz2100.net/ucp.php?mode=privacy">Privacy policy</a></strong></p>
      <hr class="dashed" />
      <p><a href="http://forums.wz2100.net/ucp.php?mode=register" class="button2">Register</a></p>
    </div>

    <span class="corners-bottom"><span></span></span></div>
  </div>


</form>
<?php
}
?>
