<?php

if (@$showmaster)
{
?>
<h2>3.1 Addons List</h2>
<p class="error">
	<strong>Warning:</strong> Addons shown here are for the <em>3.1</em> version, and may be incompatible with older versions of Warzone. Unless you are using the 3.1 version, please consult the <a href="/old">Old Addons List</a> instead.
</p>
<?php
}

if ($loggedinuserid && $isreviewer)
{
?>
<div style="float:right;padding:18px"><a href="/review">Review Addons</a></div>
<?php
}
?>
<script>
<!--
document.write('<div id="subnav"><ul class="large"><li><a href="#maps" onclick="return usetab(\'maps\')" id="mapslink" class="c">Maps</a></li> <li><a href="#mods" onclick="return usetab(\'mods\')" id="modslink">Mods</a></li></ul></div>');
function usetab(that)
{
	if (that == 'maps')
	{
		document.getElementById('mapssection').style.display = 'block';
		document.getElementById('modssection').style.display = 'none';
		document.getElementById('mapslink').className = 'c';
		document.getElementById('modslink').className = 'nc';
	}
	if (that == 'mods')
	{
		document.getElementById('mapssection').style.display = 'none';
		document.getElementById('modssection').style.display = 'block';
		document.getElementById('mapslink').className = 'nc';
		document.getElementById('modslink').className = 'c';
	}
	return false;
}
-->
</script>
<div id="mapssection">
<h3 id="maps">
	Maps
</h3>
<?php

function selected($name, $value)
{
	if (@$_REQUEST[$name] == $value)
	{
		echo ' selected="selected"';
	}
}

?>
<form>
Minimum rating: <select name="minrating"><option value="1"<?php selected('minrating', 1); ?>>Any</option><option value="2"<?php selected('minrating', 2); ?>>2</option><option value="3"<?php selected('minrating', 3); ?>>3</option><option value="4"<?php selected('minrating', 4); ?>>4</option></select>
Players: <select name="players"><option value=""<?php selected('players', ''); ?>>Any</option><option value="2"<?php selected('players', 2); ?>>2</option><option value="3"<?php selected('players', 3); ?>>3</option><option value="4"<?php selected('players', 4); ?>>4</option><option value="5"<?php selected('players', 5); ?>>5</option><option value="6"<?php selected('players', 6); ?>>6</option><option value="7"<?php selected('players', 7); ?>>7</option><option value="8"<?php selected('players', 8); ?>>8</option><option value="9"<?php selected('players', 9); ?>>9</option><option value="10"<?php selected('players', 10); ?>>10</option></select>
<button type="submit">Filter</button>
</form>
<?php

function rating_cmp($a, $b)
{
    if (@$a['rating'] == @$b['rating']) {
        return 0;
    }
    return (@$a['rating'] < @$b['rating']) ? 1 : -1;
}
usort($_ADDONS['map'], 'rating_cmp');
usort($_ADDONS['mod'], 'rating_cmp');

$atleastone = false;
foreach ($_ADDONS['map'] as $addon)
{
	if ($addon['players'] != 2 && $addon['players'] != 4 && $addon['players'] != 8 && !@$showmaster)
	{
		continue;
	}
	if (@$_REQUEST['players'] && $addon['players'] != $_REQUEST['players'])
	{
		continue;
	}
	if (@$_REQUEST['minrating'] && @$addon['rating'] < $_REQUEST['minrating'])
	{
		continue;
	}
	if (@$_REQUEST['maxrating'] && @$addon['rating'] > $_REQUEST['maxrating'])
	{
		continue;
	}
	$atleastone = true;
?>
<div class="addon">
	<?php if (checkuser($addon['submitterid'])) echo '<div style="float:right"><a href="/submit/'.$addon['fullid'].'">Edit</a></div>'; ?>
	<a href="/<?php echo $addon['fullid']; ?>" class="addonlink"><?php if ($addon['pic']) echo '<img src="/'.$addon['dir'].'_thumb_80_pic.gif"  alt="" class="pic" />'; else echo '<span class="nopic">No picture available</span>'; ?>
	<span class="p"><strong><?php echo htmlentities($addon['name']); ?></strong> <?php echo htmlentities($addon['version']); ?> <em class="addontype"><?php echo addontype($addon); ?></em> <em class="byline">by <?php echo htmlentities($addon['author']); ?></em></span>
<?php if (@$addon['rating']) { ?>
	<div class="rating">
		<em>Rating:</em>
		<span style="float:left;width:80px;height:16px;background:transparent url(/images/star-empty.png)"><span style="display:block;width:<?php echo intval($addon['rating']*16); ?>px;height:16px;background:transparent url(/images/star.png)"></span></span>
		<strong><?php $strrating = strval(round($addon['rating'],1)); if (strlen($strrating) == 1) $strrating .= '.'; if (strlen($strrating) == 2) $strrating .= '0'; echo $strrating; ?></strong>
	</div>
<?php } ?>
	<span class="p"><?php
	if (strlen($addon['htmldesc']) < 500)
	{
		echo $addon['htmldesc'];
		if ($addon['morepics']) {
			echo ' <span class="a">[Screenshots<span class="more">, more</span>]</span>';
		}
		else
		{
			echo '<span class="more"> [More]</a>';
		}
	}
	else
	{
		echo substr($addon['htmldesc'],0,500);
		if ($addon['morepics']) {
			echo '<span class="a">... [Read More, Screenshots]</span>';
		}
		else
		{
			echo '<span class="a">... [Read More]</a>';
		}
	}
?></span></a>
	<div><em>Download:</em> <a href="/<?php echo htmlentities('download'.substr($addon['dir'],5).$addon['filename']); ?>"><code><?php echo htmlentities($addon['filename']); ?></code></a></div>
</div>
<?php
}

if (!$atleastone) echo '<p><em>There are no maps matching your criteria.</em></p>';

?>
</div>
<div id="modssection"><h3 id="mods">
	Mods
</h3>
<?php

foreach ($_ADDONS['mod'] as $addon)
{
?>
<div class="addon">
	<?php if (checkuser($addon['submitterid'])) echo '<div style="float:right"><a href="/submit/'.$addon['fullid'].'">Edit</a></div>'; ?>
	<a href="/<?php echo $addon['fullid']; ?>" class="addonlink"><?php if ($addon['pic']) echo '<img src="/'.$addon['dir'].'_thumb_80_pic.gif"  alt="" class="pic" />'; else echo '<span class="nopic">No picture available</span>'; ?>
	<span class="p"><strong><?php echo htmlentities($addon['name']); ?></strong> <?php echo htmlentities($addon['version']); ?> <em class="addontype"><?php echo addontype($addon); ?></em> <em class="byline">by <?php echo htmlentities($addon['author']); ?></em></span>
<?php if (@$addon['rating']) { ?>
	<div class="rating">
		<em>Rating:</em>
		<span style="float:left;width:80px;height:16px;background:transparent url(/images/star-empty.png)"><span style="display:block;width:<?php echo intval($addon['rating']*16); ?>px;height:16px;background:transparent url(/images/star.png)"></span></span>
		<strong><?php $strrating = strval(round($addon['rating'],1)); if (strlen($strrating) == 1) $strrating .= '.'; if (strlen($strrating) == 2) $strrating .= '0'; echo $strrating; ?></strong>
	</div>
<?php } ?>
	<span class="p"><?php
	if (strlen($addon['htmldesc']) < 500)
	{
		echo $addon['htmldesc'];
		if ($addon['morepics']) {
			echo ' <span class="a">[Screenshots<span class="more">, more</span>]</span>';
		}
		else
		{
			echo '<span class="more"> [More]</a>';
		}
	}
	else
	{
		echo substr($addon['htmldesc'],0,500);
		if ($addon['morepics']) {
			echo '<span class="a">... [Read More, Screenshots]</span>';
		}
		else
		{
			echo '<span class="a">... [Read More]</a>';
		}
	}
?></span></a>
	<div><em>Download:</em> <a href="/<?php echo htmlentities('download'.substr($addon['dir'],5).$addon['filename']); ?>"><code><?php echo htmlentities($addon['filename']); ?></code></a></div>
</div>
<?php
}
?>
</div>
<script>
<!--
usetab('maps');
//-->
</script>
<h3>Installing</h3>
<p>
<em>See:</em> <a href="http://wz2100.net/faq#HowdoIinstallamap">How do I install a map or mod?</a>
</p>
<h3>Submit</h3>
<p>
	<a href="/submit">Submit your own map/mod!</a>
</p>
