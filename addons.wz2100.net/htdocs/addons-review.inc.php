<?php
include_once 'lib/wzaddons.lib.php';

$fullid = strval(@$_REQUEST['fullid']);
if (!$fullid) $fullid = strval(@$_REQUEST['sub']);

?>
<h1>Review addons</h1>
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
if ($loggedinuserid && $isreviewer)
{
?>
<h2>Introduction for reviewers</h2>
<p>
Welcome, reviewer!
</p>
<p>
First, you should probably get to know the review scale.
</p>
<p>
0.0 = Use only for things that crash Warzone, or aren't anything like what they say they are<br />
0.5 = Use for things that aren't exactly like what they say they are, or due to special circumstances should not be approved (explain in your review). Unless the description says otherwise, a map should be a "normal" map. Specifically, it should:<br />
1. make it possible for any player to win. 2. make it possible for any player to tech up. 3. have at least one oil resource per player.<br />
Any map that is not balanced for free-for-all play MUST mention this in the description (e.g. the description could say "2v2 team map"), or it should be rated 0.5 and rejected. A map or mod that does not match its description should be rejected. If applicable, include in your review how the description could be fixed, and what your rating would be if the description were correct.<br />
<br />
(ONLY give a rating of 0.5 or lower to addons that should NOT be approved. ONLY give a rating of 1.0 or higher to addons that SHOULD be approved.)<br />
<br />
1.0 = A horrible map/mod.<br />
3.0 = An average map/mod.<br />
5.0 = An excellent map/mod.
</p>
<p>
Remember: This is not YouTube. 5.0's are few and far between - if you're giving more maps 5.0's than 4.0's, you're doing it wrong.
</p>
<p>
To calibrate your scale, refer to these bundled maps:
</p>
<p>
4c-Rush is 5.0 - a map small enough that you don't have to spend the game waiting for units to get to their destination, but big enough to maneuver in. While playing it, you feel like you're fighting for every inch of this map; there are no areas that can go ignored.
</p>
<p>
4c-Basingstoke is 3.0 - an interesting concept, but nothing much else going for it.
</p>
<p>
2c-Highground is 4.0 - a good map, but there's a bit too much focus on taking the center area, and once the center area is uncontested, it just becomes a matter of turtling. Perhaps increasing the area occupied by scavengers could make taking the center area more interesting.
</p>
<p>
Please add full reviews as well as ratings - a number isn't very useful unless you can explain why you gave it that number. Please give ratings in multiples of 0.5.
</p>
<p>
Reviewing approved addons can be done by clicking on the addon and scrolling down to the "Review" section. Reviewing unapproved addons can be done below, and should be done first, since we need that to help decide whether or not to approve addons.
</p>
<?php
  $youraddons = getaddonsforrating();
?>
<h2>
Review these unrated addons:
</h2>
<ul>
<?php
  if ($youraddons)
  {
    foreach ($youraddons as $addonid)
    {
      $addon = getaddon($addonid);
        echo '<li><a href="/'.$addon['fullid'].'">'.htmlentities($addon['name'].($addon['version']?' '.$addon['version']:'')).(!@$addon['unfinished']?'':' - <em>Unfinished</em>').'</a>'.(isset($addon['rating'])?' (Rating: <strong>'.$addon['rating'].'</strong>)':'');
        if (getaddon(substr($addonid,strlen('unapproved/')))) echo ' <strong>(Update)</strong>';
        echo '</li>';
    }
  }
  else
  {
    echo '<li>All addons have been rated! Check back later.</li>';
  }
?>
</ul>
<?php

  if (ishelper())
  {
    $youraddons = getaddonsforapproval()
?>
<h2>
Approve these rated addons!
</h2>
<p>Any addon listed below is ready for approval.</p>
<ul>
<?php
    if ($youraddons)
    {
    foreach ($youraddons as $addonid)
    {
      $addon = getaddon($addonid);
        echo '<li><a href="/submit/'.$addon['fullid'].'">'.htmlentities($addon['name'].($addon['version']?' '.$addon['version']:'')).(!@$addon['unfinished']?'':' - <em>Unfinished</em>').'</a>'.(isset($addon['rating'])?' (Rating: <strong>'.$addon['rating'].'</strong>)':'');
        if (getaddon(substr($addonid,strlen('unapproved/')))) echo ' <strong>(Update)</strong>';
        echo '</li>';
    }
    }
    else
    {
      echo '<li>All addons that are ready have been approved! Check back later.</li>';
    }
?>
</ul>
<?php
  }
?>
<?php
}
else if ($loggedinuserid)
{
?>
<p>
You do not have reviewer permissions. :(
</p>
<p>
Speak to Zarel if you wish to review addons.
</p>
<?php
}
else
{
?>
<form action="http://forums.wz2100.net/ucp.php?mode=login" method="post" id="login">
<div class="panel">
  <div class="inner"><span class="corners-top"><span></span></span>
  <div class="content">
    <h2>You must be logged in to review maps or mods.</h2>
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
    <input type="hidden" name="redirect" value="./addons-review" />
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
