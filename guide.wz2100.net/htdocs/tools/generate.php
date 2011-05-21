<?php
//error_reporting(E_ALL);

chdir('..');

include_once 'tools/persist.lib.php';
include_once 'tools/wzguide.lib.php';

$included = isset($step);
if (!$included && !$isadmin) die('Access denied');

$wz_tls = array(
	'Level All' => '-',
	'Level One' => 'T1',
	'Level Two' => 'T2',
	'Level Three' => 'T3',
	'Level One-Two' => 'T1-2',
	'Level Two-Three' => 'T2-3'
);
function wz_tls($tl)
{	return ($r=$GLOBALS['wz_tls'][$tl])?$r:'N/A'; }
$wz_funcs = array('' => '');

$steps = array(
	'Start',
	'Cache IDs',
	'Cache names',
	'Cache research',
	'Cache weapons',
	'Cache functions',
	'Cache research-functions links',
	'Cache research prereqs',
	'Cache research descs',
	'Cache bodies',
	'Cache propulsions',
	'Cache structures',
	'Cache research-weapons links',
	'Cache structure weapons',
	'Cache research-structure links',
	'Cache templates and research-template links',
	'Cache weapon table'
);
$psteps = array(
	'Generate w/',
	'Generate r.php',
	'Generate r/',
	'Generate w.php',
	'Generate c/',
	'Generate c.php',
	'Generate bp/',
	'Generate techtree',
	'Generate b/',
	'Generate d/',
	'js',
	'annotate graph',
	're-autogen'
);

if (!$included) $step = (isset($_GET['s'])?intval($_GET['s']):0);
$error = false;

if ($included) ob_start();

?>
<html>
<head>
<title>Generate</title>
</head>
<body>
<?php

foreach ($steps as $i => $cstep)
echo '<div>'.$i.'. '.($i<$step?'<s>':($i==$step?'<strong>':'')).'<a href="generate.php?s='.$i.'" style="text-decoration:none">'.$steps[$i].'</a>'.($i<$step?'</s>':($i==$step?'</strong>':'')).'</div>';
echo '<div>--</div>';
foreach ($psteps as $i => $cpstep)
echo '<div>'.($i+1).'. '.($i+101<$step?'<s>':($i+101==$step?'<strong>':'')).'<a href="generate.php?s='.($i+101).'" style="text-decoration:none">'.$psteps[$i].'</a>'.($i+101<$step?'</s>':($i+101==$step?'</strong>':'')).'</div>';

?>
<p><a href="generate.php?s=<?=$step+1?>&autonext">Next</a> (<a href="generate.php?s=<?=$step+1?>">Next one step</a>)</p>
<?php
echo '<pre>';

switch ($step)
{
case 0:
	if (!is_dir('cache')) mkdir('cache');
	echo 'Click next.';
	break;
case 1:
	echo "Generating ids.inc.php (unused)\r\n";
	$tnames = file('data/mp/messages/strings/names.txt');
	echo "--------------------------------\r\n";
	$names = array();
	$comment = false;
	$pri = 0; $c = count($tnames);
	foreach ($tnames as $i => $tname)
	{
		$tname = trim($tname);
		if ($comment && strpos($tname,'*/')!==false && (($comment = false) || true) || $comment || strlen($tname)==0 || substr($tname,0,2)=='//' || strpos($tname,'/*')!==false && (strpos($tname,'*/')!==false || $comment = true))
			continue; // Optimized but unreadable :/ Skips all comments
		$loc = array();
		if (strpos($tname,"\t")) $loc[] = strpos($tname,"\t");
		if (strpos($tname," ")) $loc[] = strpos($tname," ");
		$loc = min($loc);
		$wzid = substr($tname,0,$loc);
		$name = ltrim(substr($tname,$loc));
		if (substr($name,0,2)=='_(') $name = trim(substr($name,2,-1));
		if (substr($name,0,1)=='"') $name = substr($name,1,-1);
		if ($pri<32*$i/$c) { $pri++; echo '.'; }
		$names[$wzid] = simplify($name,$names);
	}
	$fp = fopen('cache/ids.inc.php', 'w') or die('Error');
	fwrite($fp, '<'.'?php $wz_ids=') or fclose($fp);
	fwrite($fp, persist_tophp($names)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	break;
case 2:
	echo "Generating names.inc.php\r\n";
	$tnames = file('data/mp/messages/strings/names.txt');
	$names = array();
	$comment = false;
	foreach ($tnames as $tname)
	{
		$tname = trim($tname);
		if ($comment && strpos($tname,'*/')!==false && (($comment = false) || true) || $comment || strlen($tname)==0 || substr($tname,0,2)=='//' || strpos($tname,'/*')!==false && (strpos($tname,'*/')!==false || $comment = true))
			continue; // Optimized but unreadable :/ Skips all comments
		$loc = array();
		if (strpos($tname,"\t")) $loc[] = strpos($tname,"\t");
		if (strpos($tname," ")) $loc[] = strpos($tname," ");
		$loc = min($loc);
		$wzid = substr($tname,0,$loc);
		$name = ltrim(substr($tname,$loc));
		if (strpos($name,'//')!==FALSE && (strpos($name,'"')===FALSE || strpos($name,'"',strpos($name,'"')+1)<strpos($name,'//')))
			$name = rtrim(substr($name,0,strpos($name,'//')));
		if (substr($name,0,2)=='_(') $name = trim(substr($name,2,-1));
		if (substr($name,0,1)=='"') $name = substr($name,1,-1);
		$names[$wzid] = $name;
	}
	$fp = fopen('cache/names.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_names=') or fclose($fp);
	fwrite($fp, persist_tophp($names)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 3:
	echo "Populating research.inc.php\r\n";
	include 'cache/names.inc.php';
	$tresearch = file('data/mp/stats/research/multiplayer/research.txt');
	$research = array();
	$rids = array();
	$rdids = array();
	foreach ($tresearch as $trsubject)
	{
		$rdata = explode(',',$trsubject);
		$rids[$rdata[0]] = simplify($wz_names[$rdata[0]],$rids);
		$research[$rids[$rdata[0]]] = array(
			'rid' => $rids[$rdata[0]],
			'name' => $wz_names[$rdata[0]],
			'tl' => wz_tls($rdata[1]),
			'price' => intval($rdata[11]),
			'major' => (intval($rdata[3])?FALSE:TRUE),
			'prereqs' => array(),
			'allows' => array(),
			'result' => array(),
			'desc' => array()
		);
		$rdids[$rdata[7]][] = $rids[$rdata[0]];
	}
	$fp = fopen('cache/research.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_research=') or fclose($fp);
	fwrite($fp, persist_tophp($research)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rids=') or fclose($fp);
	fwrite($fp, persist_tophp($rids)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rdids=') or fclose($fp);
	fwrite($fp, persist_tophp($rdids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 4:
	echo "Generating weapons.inc.php\r\n";
	include 'cache/names.inc.php';
	$tweapons = file('data/mp/stats/weapons.txt');
	$weapons = array();
	$wids = array();
	$wsubclasses = array();
	$wz_upgrades = array();
	foreach ($tweapons as $tweapon)
	{
		$wdata = explode(',',$tweapon);
		if ($wdata[0] === 'TUTMG') continue;
		$wids[$wdata[0]] = simplify($wz_names[$wdata[0]],$wids);
		$fullcooldown = (intval($wdata[23])+(intval($wdata[22])?intval($wdata[22]):1)*intval($wdata[20]));
		if (intval($wdata[50])) // air weapon
			$fullcooldown += (intval($wdata[4]/10)+30);
		$rof = ($fullcooldown?(intval($wdata[22])?intval($wdata[22]):1)*600/$fullcooldown:0);
		$row = array(
			'wid' => $wids[$wdata[0]],
			'name' => $wz_names[$wdata[0]],
			'turret' => 'weapon',
			'tl' => wz_tls($wdata[1]),
			'price' => intval($wdata[2]),
			'bp' => intval($wdata[3]),
			'weight' => intval($wdata[4]),
			'hp' => intval($wdata[7]),
			'srange' => intval($wdata[16]),
			'lrange' => intval($wdata[17]),
			'sacc' => intval($wdata[18]),
			'lacc' => intval($wdata[19]),
			'fcooldown' => intval($wdata[20]),
			'nexplosions' => intval($wdata[21]),
			'nrounds' => (intval($wdata[22])?intval($wdata[22]):1),
			'cooldown' => intval($wdata[23]),
			'damage' => intval($wdata[24]),
			'splash' => intval($wdata[25]),
			'splashacc' => intval($wdata[26]),
			'splashdamage' => intval($wdata[27]),
			'burntime' => intval($wdata[28]),
			'burndamage' => intval($wdata[29]),
			'burn' => intval($wdata[30]),
			'lifetime' => intval($wdata[31]),
			'life' => intval($wdata[32]),
			'v' => intval($wdata[33]),
			'iheight' => intval($wdata[34]),
			'movefire' => $wdata[35],
			'class' => simplify($wdata[36]),
			'subclass' => simplify($wdata[37]),
			'direction' => simplify($wdata[38]),
			'type' => simplify($wdata[39]),
			'mrange' => intval($wdata[46]),
			'targetground' => (intval($wdata[49])!=1),
			'targetair' => (intval($wdata[49])!=0),
			'numattackruns' => intval($wdata[50]),
			'designable' => (intval($wdata[51])!=0),
			'penetrate' => (intval($wdata[52])!=0),
			'rof' => round($rof,1),
			'dps' => calcdps($wdata[24],$rof,$wdata[19]),
			'prereq' => ''
		);
		/* if ($row['fcooldown']+$row['cooldown'])
		{
			$row['rof'] = round($row['nrounds']*600/($row['cooldown']+$row['nrounds']*$row['fcooldown']),1);
			$row['dps'] = round($row['damage']*$row['nrounds']*10/($row['cooldown']+$row['nrounds']*$row['fcooldown'])*$row['lacc']/100,1);
		} */
		$weapons[$wids[$wdata[0]]] = $row;
		if (!@$wsubclasses[simplify($wdata[37])]) $wsubclasses[simplify($wdata[37])] = array('scid' => simplify($wdata[37]), 'name' => $subclasses[simplify($wdata[37])], 'upgrades' => array(), 'maxdam' => 0, 'maxacc' => 0, 'maxrof' => 0);
	}
	$wsubclasses['repair'] = array('scid' => 'repair', 'name' => 'Repair', upgrades => array(), 'maxbuild' => 0);
	$wsubclasses['construct'] = array('scid' => 'construct', 'name' => 'Construction', upgrades => array(), 'maxbuild' => 0);
	$wsubclasses['sensor'] = array('scid' => 'sensor', 'name' => 'Sensor', upgrades => array(), 'maxrange' => 0);
	$tweapons = file('data/mp/stats/repair.txt');
	foreach ($tweapons as $tweapon)
	{
		$wdata = explode(',',$tweapon);
		if ($wdata[8]!='TURRET') continue;
		$wids[$wdata[0]] = simplify($wz_names[$wdata[0]],$wids);
		$row = array(
			'wid' => $wids[$wdata[0]],
			'name' => $wz_names[$wdata[0]],
			'turret' => 'repair',
			'tl' => wz_tls($wdata[1]),
			'price' => intval($wdata[2]),
			'bp' => intval($wdata[3]),
			'weight' => intval($wdata[4]),
			'hp' => intval($wdata[7]),
			'build' => intval($wdata[11]),
			'subclass' => 'repair',
			'designable' => intval($wdata[13])?TRUE:FALSE,
			'prereq' => ''
		);
		$weapons[$wids[$wdata[0]]] = $row;
	}
	$tweapons = file('data/mp/stats/construction.txt');
	foreach ($tweapons as $tweapon)
	{
		$wdata = explode(',',$tweapon);
		$wids[$wdata[0]] = simplify($wz_names[$wdata[0]],$wids);
		$row = array(
			'wid' => $wids[$wdata[0]],
			'name' => $wz_names[$wdata[0]],
			'turret' => 'construct',
			'tl' => wz_tls($wdata[1]),
			'price' => intval($wdata[2]),
			'bp' => intval($wdata[3]),
			'weight' => intval($wdata[4]),
			'hp' => intval($wdata[7]),
			'build' => intval($wdata[10]),
			'subclass' => 'construct',
			'designable' => intval($wdata[11])?TRUE:FALSE,
			'prereq' => ''
		);
		$weapons[$wids[$wdata[0]]] = $row;
	}
	$tweapons = file('data/mp/stats/sensor.txt');
	foreach ($tweapons as $tweapon)
	{
		$wdata = explode(',',$tweapon);
		if ($wdata[11]!='TURRET') continue;
		$wids[$wdata[0]] = simplify($wz_names[$wdata[0]],$wids);
		$row = array(
			'wid' => $wids[$wdata[0]],
			'name' => $wz_names[$wdata[0]],
			'turret' => 'sensor',
			'tl' => wz_tls($wdata[1]),
			'price' => intval($wdata[2]),
			'bp' => intval($wdata[3]),
			'weight' => intval($wdata[4]),
			'hp' => intval($wdata[7]),
			'lrange' => intval($wdata[10]),
			'sensor' => simplify($wdata[12]),
			'subclass' => 'sensor',
			'designable' => intval($wdata[15])?TRUE:FALSE,
			'prereq' => ''
		);
		if ($row['wid']=='uplinksensor' && $row['sensor']=='super') $row['sensor']='uplink';
		$weapons[$wids[$wdata[0]]] = $row;
	}
	$fp = fopen('cache/weapons.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_weapons=') or fclose($fp);
	fwrite($fp, persist_tophp($weapons)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_wids=') or fclose($fp);
	fwrite($fp, persist_tophp($wids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	$fp = fopen('cache/wsubclasses.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_wsubclasses=') or fclose($fp);
	fwrite($fp, persist_tophp($wsubclasses)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_upgrades=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_upgrades)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 5:
	echo "Generating functions.inc.php\r\n";
	include_once 'cache/names.inc.php';
	include_once 'cache/research.inc.php';
	$strings = file('data/mp/stats/functions.txt');
	$wz_functions = array();
	$wz_fids = array();
	foreach ($strings as $string)
	{
		$data = explode(',',$string);
		$wz_fids[$data[1]] = simplify($data[1],$wz_fids);
		$row = array(
			'fid' => $wz_fids[$data[1]],
			'funcs' => array(array('type'=>simplify($data[0]))),
			'prereq' => ''
		);
		if ($row['funcs'][0]['type'] == 'weaponupgrade')
		{
			$row['funcs'] = array();
			if (intval($data[3]))
				$row['funcs'][] =
				array('type'=>'weaponrof','subclass'=>simplify($data[2]),
				'amt'=>intval($data[3]));
			if (intval($data[5])) $row['funcs'][] =
				array('type'=>'weaponacc','subclass'=>simplify($data[2]),
				'amt'=>intval($data[5]));
			if (intval($data[6])) $row['funcs'][] =
				array('type'=>'weapondam','subclass'=>simplify($data[2]),
				'amt'=>intval($data[6]));
			if (intval($data[7])) $row['funcs'][] =
				array('type'=>'weaponsdam','subclass'=>simplify($data[2]),
				'amt'=>intval($data[7]));
			if (intval($data[8])) $row['funcs'][] =
				array('type'=>'weaponfdam','subclass'=>simplify($data[2]),
				'amt'=>intval($data[8]));
		}
		if ($row['funcs'][0]['type'] == 'vehiclebodyupgrade')
		{
			$row['funcs'] = array();
			if (intval($data[2])) $row['funcs'][] =
				array('type'=>(intval($data[6])?'vehicle':'cyborg').'engine','amt'=>intval($data[2]));
			if (intval($data[3])) $row['funcs'][] =
				array('type'=>(intval($data[6])?'vehicle':'cyborg').'hp','amt'=>intval($data[3]));
			if (intval($data[4])) $row['funcs'][] =
				array('type'=>(intval($data[6])?'vehicle':'cyborg').'armor','amt'=>intval($data[4]));
			if (intval($data[5])) $row['funcs'][] =
				array('type'=>(intval($data[6])?'vehicle':'cyborg').'thermal','amt'=>intval($data[5]));
		}
		if ($row['funcs'][0]['type'] == 'productionupgrade')
		{
			$row['funcs'] = array();
			if (intval($data[2])) $row['funcs'][] =
				array('type'=>'factoryupgrade','amt'=>intval($data[5]));
			if (intval($data[3])) $row['funcs'][] =
				array('type'=>'cyborgfactoryupgrade','amt'=>intval($data[5]));
			if (intval($data[4])) $row['funcs'][] =
				array('type'=>'vtolfactoryupgrade','amt'=>intval($data[5]));
		}
		if ($row['funcs'][0]['type'] == 'walldefenceupgrade')
		{
			$row['funcs'] = array();
			if (intval($data[2])) $row['funcs'][] =
				array('type'=>'wallarmor','amt'=>intval($data[2]));
			if (intval($data[3])) $row['funcs'][] =
				array('type'=>'wallhp','amt'=>intval($data[3]));
		}
		if ($row['funcs'][0]['type'] == 'structureupgrade')
		{
			$row['funcs'] = array();
			if (intval($data[2])) $row['funcs'][] =
				array('type'=>'structurearmor','amt'=>intval($data[2]));
			if (intval($data[3])) $row['funcs'][] =
				array('type'=>'structurehp','amt'=>intval($data[3]));
			if (intval($data[4])) $row['funcs'][] =
				array('type'=>'structureresist','amt'=>intval($data[4]));
		}
		if ($row['funcs'][0]['type'] == 'researchupgrade'
		|| $row['funcs'][0]['type'] == 'repairupgrade'
		|| $row['funcs'][0]['type'] == 'powerupgrade'
		|| $row['funcs'][0]['type'] == 'rearmupgrade'
		|| $row['funcs'][0]['type'] == 'vehicleecmupgrade'
		|| $row['funcs'][0]['type'] == 'vehicleconstupgrade')
			$row['funcs'][0]['amt'] = intval($data[2]);
		if ($row['funcs'][0]['type'] == 'vehiclesensorupgrade')
			$row['funcs'][0]['amt'] = intval($data[3]);
		$wz_functions[$wz_fids[$data[1]]] = $row;
	}
	$fp = fopen('cache/functions.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_functions=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_functions)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_fids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_fids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 6:
	echo "Caching research-function links\r\n";
	include_once 'cache/research.inc.php';
	include_once 'cache/functions.inc.php';
	include_once 'cache/wsubclasses.inc.php';
	$strings = file('data/mp/stats/research/multiplayer/researchfunctions.txt');
	$wz_upgrades = array();
	foreach ($strings as $string)
	{
		$data = explode(',',$string);
		$wz_functions[simplify($data[1])]['prereq'] = $wz_rids[$data[0]];
		$wz_research[$wz_rids[$data[0]]]['result'][] = array('function',simplify($data[1]));
		foreach ($wz_functions[simplify($data[1])]['funcs'] as $func)
		{
			if (substr($func['type'],0,6) == 'weapon')
			{
				if ($func['type'] == 'weapondam')
				{
					$wz_wsubclasses[$func['subclass']]['upgrades'][] = $wz_rids[$data[0]];
					$wz_wsubclasses[$func['subclass']]['maxdam']
					= max($wz_wsubclasses[$func['subclass']]['maxdam'], $func['amt']);
				}
				if ($func['type'] == 'weaponacc')
				{
					$wz_wsubclasses[$func['subclass']]['upgrades'][] = $wz_rids[$data[0]];
					$wz_wsubclasses[$func['subclass']]['maxacc']
					= max($wz_wsubclasses[$func['subclass']]['maxacc'], $func['amt']);
				}
				if ($func['type'] == 'weaponrof')
				{
					$wz_wsubclasses[$func['subclass']]['upgrades'][] = $wz_rids[$data[0]];
					$wz_wsubclasses[$func['subclass']]['maxrof']
					= max($wz_wsubclasses[$func['subclass']]['maxrof'], $func['amt']);
				}
			}
			else if ($func['type'] == 'vehicleconstupgrade')
			{
				$wz_wsubclasses['construct']['upgrades'][] = $wz_rids[$data[0]];
				$wz_wsubclasses['construct']['maxbuild']
					= max($wz_wsubclasses['construct']['maxbuild'], $func['amt']);
			}
			else if ($func['type'] == 'vehiclesensorupgrade')
			{
				$wz_wsubclasses['sensor']['upgrades'][] = $wz_rids[$data[0]];
				$wz_wsubclasses['sensor']['maxrange']
					= max($wz_wsubclasses['sensor']['maxrange'], $func['amt']);
			}
			else
			{
				$wz_upgrades[$func['type']]['upgrades'][] = $wz_rids[$data[0]];
				$wz_upgrades[$func['type']]['max']
					= max(@$wz_upgrades[$func['type']]['max'], $func['amt']);
			}
		}
	}
	$fp = fopen('cache/research.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_research=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_research)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rids)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rdids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rdids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	$fp = fopen('cache/wsubclasses.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_wsubclasses=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_wsubclasses)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_upgrades=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_upgrades)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 7:
	echo "Populating research.inc.php prereqs\r\n";
	include 'cache/research.inc.php';
	$tprereqs = file('data/mp/stats/research/multiplayer/prresearch.txt');
	foreach ($tprereqs as $tprereq)
	{
		$rdata = explode(',',$tprereq);
		$wz_research[$wz_rids[$rdata[0]]]['prereqs'][] = $wz_rids[$rdata[1]];
		$wz_research[$wz_rids[$rdata[1]]]['allows'][] = $wz_rids[$rdata[0]];
	}
	/* $wz_researchnew = array();
	$wz_researchnew['machinegun'] = $wz_research['machinegun'];
	$wz_researchnew['engineering'] = $wz_research['engineering'];
	$wz_researchnew['sensorturret'] = $wz_research['sensorturret'];
	while (1)
	{
		$done = true;
		foreach ($wz_research as $k => $research)
		{
			if (!$wz_researchnew[$k])
			{
				$allprereqs = true;
				foreach ($research['prereqs'] as $prereq)
				{
					if (!$wz_researchnew[$prereq])
					{
						$allprereqs = false;
						break;
					}
				}
				if ($allprereqs)
				{
					$wz_researchnew[$k] = $research;
					$done = false;
				}
			}
		}
		if ($done)
		{
			break;
		}
	} */
	$fp = fopen('cache/research.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_research=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_research)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rids)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rdids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rdids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 8:
	echo "Populating research.inc.php descriptions\r\n";
	include 'cache/research.inc.php';
	$strings = file('data/mp/messages/resmessagesall.rmsg');
	$strings = array_merge($strings, file('data/mp/messages/resmessages1.rmsg'));
	$strings = array_merge($strings, file('data/mp/messages/resmessages12.rmsg'));
	$strings = array_merge($strings, file('data/mp/messages/resmessages2.rmsg'));
	$strings = array_merge($strings, file('data/mp/messages/resmessages23.rmsg'));
	$strings = array_merge($strings, file('data/mp/messages/resmessages3.rmsg'));
	$current = '';
	$depth = 0;
	$comment = false;
	foreach ($strings as $string)
	{
		$string = trim($string);
		if ($comment && strpos($string,'*/')!==false && (($comment = false) || true) || $comment || strlen($string)==0 || substr($string,0,2)=='//' || strpos($string,'/*')!==false && (strpos($string,'*/')!==false || $comment = true))
			continue; // Optimized but unreadable :/ Skips all comments
		if (strpos($string,'{')!==false)
		{
			$depth++;
			continue;
		}
		if (strpos($string,'}')!==false)
		{
			$depth--;
			continue;
		}
		if ($depth==0 && $string) $current = $string;
		if (substr($string,-1)==',') $string = trim(substr($string,0,-1));
		if (substr($string,0,2)=='_(') $string = trim(substr($string,2,-1));
		if (substr($string,0,1)=='"') $string = substr($string,1,-1);
		if ($depth==2)
		{
			if (@$wz_rdids[$current]) foreach ($wz_rdids[$current] as $rdid)
			{
				$wz_research[$rdid]['desc'][] = $string;
			}
		}
	}
	$fp = fopen('cache/research.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_research=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_research)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rids)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rdids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rdids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 8:
	echo "Populating research.inc.php descriptions\r\n";
	include 'cache/research.inc.php';
	$strings = file('data/mp/messages/strings/resstrings.txt');
	$comment = false;
	foreach ($strings as $string)
	{
		$string = trim($string);
		if ($comment && strpos($string,'*/')!==false && (($comment = false) || true) || $comment || strlen($string)==0 || substr($string,0,2)=='//' || strpos($string,'/*')!==false && (strpos($string,'*/')!==false || $comment = true))
			continue; // Optimized but unreadable :/ Skips all comments
		$loc = array();
		if (strpos($string,"\t")) $loc[] = strpos($string,"\t");
		if (strpos($string," ")) $loc[] = strpos($string," ");
		$loc = min($loc);
		$wzid = substr($string,0,$loc);
		$desc = ltrim(substr($string,$loc));
		if (substr($desc,0,2)=='_(') $desc = trim(substr($desc,2,-1));
		if (substr($desc,0,1)=='"') $desc = substr($desc,1,-1);
		if (substr($wzid,-5,4)=='_MSG') $wzid = substr($wzid,0,-5);
		$wz_research[$wz_rdids[$wzid]]['desc'][] = $desc;
	}
	$fp = fopen('cache/research.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_research=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_research)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 9:
	echo "Generating bodies.inc.php\r\n";
	include_once 'cache/names.inc.php';
	$strings = file('data/mp/stats/body.txt');
	$wz_bodies = array();
	$wz_bids = array();
	foreach ($strings as $string)
	{
		$data = explode(',',$string);
		$wz_bids[$data[0]] = simplify($wz_names[$data[0]],$wz_bids);
		$row = array(
			'bid' => $wz_bids[$data[0]],
			'name' => $wz_names[$data[0]],
			'tl' => wz_tls($data[1]),
			'size' => simplify($data[2]),
			'price' => intval($data[3]),
			'bp' => intval($data[4]),
			'weight' => intval($data[5]),
			'hp' => intval($data[6]),
			'weapons' => intval($data[9]),
			'engine' => intval($data[10]),
			'armor' => intval($data[11]),
			'thermal' => intval($data[12]),
			'designable' => intval($data[24])?true:false,
			'prereq' => ''
		);
		$wz_bodies[$wz_bids[$data[0]]] = $row;
	}
	$fp = fopen('cache/bodies.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_bodies=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_bodies)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_bids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_bids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 10:
	echo "Generating propulsions.inc.php\r\n";
	include_once 'cache/names.inc.php';
	$strings = file('data/mp/stats/propulsiontype.txt');
	$wz_proptypes = array();
	foreach ($strings as $string)
	{
		$data = explode(',',$string);
		$ptid = simplify($data[0]);
		$row = array(
			'ptid' => $ptid,
			'type' => simplify($data[1]),
			'speed' => intval($data[2])
		);
		$wz_proptypes[$ptid] = $row;
	}
	$strings = file('data/mp/stats/propulsion.txt');
	$wz_propulsions = array();
	$wz_pids = array();
	foreach ($strings as $string)
	{
		$data = explode(',',$string);
		$wz_pids[$data[0]] = simplify($wz_names[$data[0]],$wz_pids);
		$row = array(
			'pid' => $wz_pids[$data[0]],
			'name' => $wz_names[$data[0]],
			'tl' => wz_tls($data[1]),
			'price' => simplify($data[2]),
			'bp' => intval($data[3]),
			'weight' => intval($data[4]),
			'hp' => intval($data[7]),
			'prop' => simplify($data[9]),
			'maxspeed' => intval($data[10]),
			'speed' => $wz_proptypes[simplify($data[9])]['speed'],
			'type' => $wz_proptypes[simplify($data[9])]['type'],
			'designable' => intval($data[11])?true:false,
			'primary' => 
				($lc=substr($data[0],-1)=='1'||!ctype_digit($lc))?true:false,
			'prereq' => ''
		);
		$wz_propulsions[$wz_pids[$data[0]]] = $row;
	}
	$fp = fopen('cache/propulsions.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_propulsions=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_propulsions)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_proptypes=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_proptypes)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_pids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_pids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 11:
	echo "Generating structures.inc.php\r\n";
	include_once 'cache/names.inc.php';
	$strings = file('data/mp/stats/structures.txt');
	$wz_structures = array();
	$wz_sids = array();
	foreach ($strings as $string)
	{
		$data = explode(',',$string);
		$wz_sids[$data[0]] = simplify($wz_names[$data[0]],$wz_sids);
		$row = array(
			'sid' => $wz_sids[$data[0]],
			'name' => $wz_names[$data[0]],
			'type' => simplify($data[1]),
			'tl' => wz_tls($data[2]),
			'strength' => simplify($data[3]),
			'h' => intval($data[5]),
			'w' => intval($data[6]),
			'material' => simplify($data[7]),
			'bp' => intval($data[8]),
			'z' => intval($data[9]),
			'armor' => intval($data[10]),
			'hp' => intval($data[11]),
			'price' => intval($data[13]),
			'resistance' => intval($data[15]),
			'ecm_wzid' => $data[18],
			'sensor_wzid' => $data[19],
			'turrets' => array(),
			'prereq' => ''
		);
		if (@$wz_wids[$row['sensor_wzid']])
		{
			$row['turrets'][] = $wz_wids[$row['sensor_wzid']];
		}
		$wz_structures[$wz_sids[$data[0]]] = $row;
	}
	$fp = fopen('cache/structures.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_structures=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_structures)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_sids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_sids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 12:
	echo "Caching research-weapon-body-propulsion links\r\n";
	include_once 'cache/research.inc.php';
	include_once 'cache/weapons.inc.php';
	include_once 'cache/structures.inc.php';
	include_once 'cache/bodies.inc.php';
	include_once 'cache/propulsions.inc.php';
	$strings = file('data/mp/stats/research/multiplayer/resultcomponent.txt');
	foreach ($strings as $string)
	{
		$rdata = explode(',',$string);
		if ($rdata[2] == 'REPAIR' && $rdata[1] == 'AutoRepair')
		{
			$wz_research[$wz_rids[$rdata[0]]]['result'][] = array('weapon','autorepair');
		}
		else if (in_array($rdata[2],array('WEAPON','CONSTRUCT','REPAIR','SENSOR')) && $rdata[1] != '0')
		{
			$wz_weapons[$wz_wids[$rdata[1]]]['prereq'] = $wz_rids[$rdata[0]];
			$wz_research[$wz_rids[$rdata[0]]]['result'] = array_merge(array(array('weapon',$wz_wids[$rdata[1]])),$wz_research[$wz_rids[$rdata[0]]]['result']);
		}
		if ($rdata[4] == 'WEAPON' && $rdata[3] != '0')
		{
			$wz_weapons[$wz_wids[$rdata[3]]]['prereq'] = $wz_rids[$rdata[0]];
			$wz_research[$wz_rids[$rdata[0]]]['result'][] = array('rplcweapon',$wz_wids[$rdata[3]]);
		}
		if ($rdata[2] == 'BODY' && $rdata[1] != '0')
		{
			$wz_bodies[$wz_bids[$rdata[1]]]['prereq'] = $wz_rids[$rdata[0]];
			$wz_research[$wz_rids[$rdata[0]]]['result'][] = array('body',$wz_bids[$rdata[1]]);
		}
		if ($rdata[2] == 'PROPULSION' && $rdata[1] != '0')
		{
			$wz_propulsions[$wz_pids[$rdata[1]]]['prereq'] = $wz_rids[$rdata[0]];
			$wz_research[$wz_rids[$rdata[0]]]['result'][] = array('propulsion',$wz_pids[$rdata[1]]);
		}
	}
	$strings = file('data/mp/stats/research/multiplayer/redcomponents.txt');
	foreach ($strings as $string)
	{
		$rdata = explode(',',$string);
		if ($rdata[2] == 'WEAPON' && $rdata[1] != '0')
		{
			$wz_weapons[$wz_wids[$rdata[1]]]['redprereq'] = $wz_rids[$rdata[0]];
			$wz_research[$wz_rids[$rdata[0]]]['result'][] = array('redweapon',$wz_wids[$rdata[1]]);
		}
	}
	$strings = file('data/mp/stats/research/multiplayer/redstructure.txt');
	foreach ($strings as $string)
	{
		$rdata = explode(',',$string);
		$wz_structures[$wz_sids[$rdata[1]]]['redprereq'] = $wz_rids[$rdata[0]];
		$wz_research[$wz_rids[$rdata[0]]]['result'][] = array('redstructure',$wz_sids[$rdata[1]]);
	}
	$fp = fopen('cache/research.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_research=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_research)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rids)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rdids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rdids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	$fp = fopen('cache/weapons.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_weapons=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_weapons)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_wids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_wids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	$fp = fopen('cache/bodies.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_bodies=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_bodies)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_bids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_bids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	$fp = fopen('cache/propulsions.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_propulsions=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_propulsions)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_proptypes=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_proptypes)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_pids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_pids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	$fp = fopen('cache/structures.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_structures=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_structures)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_sids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_sids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 13:
	echo "Generating structures.inc.php weapons\r\n";
	include_once 'cache/names.inc.php';
	include_once 'cache/structures.inc.php';
	include_once 'cache/weapons.inc.php';
	$strings = file('data/mp/stats/structureweapons.txt');
	foreach ($strings as $string)
	{
		$data = explode(',',$string);
		if (@$wz_wids[$data[1]])
		{
			$wz_structures[$wz_sids[$data[0]]]['turrets'][] = $wz_wids[$data[1]];
			$wz_structures[$wz_sids[$data[0]]]['hp'] += $wz_weapons[$wz_wids[$data[1]]]['hp'];
		}
		if (@$wz_wids[$data[2]])
		{
			$wz_structures[$wz_sids[$data[0]]]['turrets'][] = $wz_wids[$data[2]];
			$wz_structures[$wz_sids[$data[0]]]['hp'] += $wz_weapons[$wz_wids[$data[2]]]['hp'];
		}
		if (@$wz_wids[$data[3]])
		{
			$wz_structures[$wz_sids[$data[0]]]['turrets'][] = $wz_wids[$data[3]];
			$wz_structures[$wz_sids[$data[0]]]['hp'] += $wz_weapons[$wz_wids[$data[3]]]['hp'];
		}
		if (@$wz_wids[$data[4]])
		{
			$wz_structures[$wz_sids[$data[0]]]['turrets'][] = $wz_wids[$data[4]];
			$wz_structures[$wz_sids[$data[0]]]['hp'] += $wz_weapons[$wz_wids[$data[4]]]['hp'];
		}
	}
	$fp = fopen('cache/structures.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_structures=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_structures)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_sids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_sids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 14:
	echo "Caching research-structure links\r\n";
	include_once 'cache/research.inc.php';
	include_once 'cache/structures.inc.php';
	$strings = file('data/mp/stats/research/multiplayer/resultstructure.txt');
	foreach ($strings as $string)
	{
		$rdata = explode(',',$string);
		$wz_structures[$wz_sids[$rdata[1]]]['prereq'] = $wz_rids[$rdata[0]];
		$wz_research[$wz_rids[$rdata[0]]]['result'][] = array('struct',$wz_sids[$rdata[1]]);
	}
	$strings = file('data/mp/stats/research/multiplayer/researchstruct.txt');
	foreach ($strings as $string)
	{
		$data = explode(',',$string);
		$wz_research[$wz_rids[$data[0]]]['prereqs'][] = '../s/'.$wz_sids[$data[1]];
	}
	$fp = fopen('cache/research.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_research=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_research)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rids)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rdids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rdids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	$fp = fopen('cache/structures.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_structures=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_structures)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_sids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_sids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 15:
	echo "Generating templates.inc.php\r\n";
	include_once 'cache/names.inc.php';
	include_once 'cache/bodies.inc.php';
	include_once 'cache/propulsions.inc.php';
	include_once 'cache/weapons.inc.php';
	include_once 'cache/research.inc.php';
	$strings = file('data/mp/stats/templates.txt');
	$wz_templates = array();
	$wz_cyborgs = array();
	$wz_tids = array();
	foreach ($strings as $string)
	{
		$data = explode(',',$string);
		$aionly = ($data[6]!='5'&&$data[6]!='YES')?TRUE:FALSE;
		$wz_tids[$data[0]] = simplify(($aionly?'ai':'').$wz_names[$data[0]],$wz_tids);
		$row = array(
			'tid' => $wz_tids[$data[0]],
			'name' => $wz_names[$data[0]],
			'turrets' => array(),
			'body' => $wz_bids[$data[2]],
			'propulsion' => $wz_pids[$data[7]],
			'brain' => simplify($data[3]),
			'construct' => $wz_wids[$data[4]],
			'ecm' => simplify($data[5]),
			'repair' => @$wz_wids[$data[8]],
			'sensor' => @$wz_wids[$data[10]],
			'prereq' => '',
			'hp' => 0,
			'armor' => 0,
			'thermal' => 0,
			'engine' => 0,
			'maxspeed' => 0,
			'engine' => 0,
			'weight' => 0,
			'bp' => 0,
			'price' => 0,
			'aionly' => $aionly
		);
		$row['armor'] = $wz_bodies[$row['body']]['armor'];
		$row['thermal'] = $wz_bodies[$row['body']]['thermal'];
		$row['engine'] = $wz_bodies[$row['body']]['engine'];
		$row['maxspeed'] = $wz_propulsions[$row['propulsion']]['maxspeed'];
		$row['bp'] = iupg($wz_bodies[$row['body']]['bp'],
			$wz_propulsions[$row['propulsion']]['bp']);
		$row['hp'] = iupg($wz_bodies[$row['body']]['hp'],$wz_propulsions[$row['propulsion']]['hp']);
		$row['price'] = iupg(@$wz_bodies[$row['bodies']]['price'],
			@$wz_propulsions[$row['propulsion']]['price']);
		$row['weight'] = iupg($wz_bodies[$row['body']]['weight'],$wz_propulsions[$row['propulsion']]['weight']);
		if ($row['construct']&&$row['construct']!='znullconstruct') $row['turrets'][] = $row['construct'];
		if ($row['repair']&&$row['construct']!='znullrepair') $row['turrets'][] = $row['repair'];
		if ($row['sensor']&&$row['construct']!='znullconstruct') $row['turrets'][] = $row['sensor'];
		$wz_templates[$wz_tids[$data[0]]] = $row;
	}
	$strings = file('data/mp/stats/assignweapons.txt');
	foreach ($strings as $string)
	{
		$data = explode(',',$string);
		if (!isset($wz_tids[$data[0]])) continue;
		$row = &$wz_templates[$wz_tids[$data[0]]];
		if ($data[1] != 'NULL' && $wz_wids[$data[1]])
			$row['turrets'][] = $wz_wids[$data[1]];
		if ($data[2] != 'NULL' && $wz_wids[$data[2]])
			$row['turrets'][] = $wz_wids[$data[2]];
		if ($data[3] != 'NULL' && $wz_wids[$data[3]])
			$row['turrets'][] = $wz_wids[$data[3]];
	}
	foreach ($wz_templates as $row)
	{
		foreach ($row['turrets'] as $turret) if ($wz_weapons[$turret])
		{
			$wz_templates[$row['tid']]['bp'] += $wz_weapons[$turret]['bp'];
			$wz_templates[$row['tid']]['hp'] += $wz_weapons[$turret]['hp'];
			$wz_templates[$row['tid']]['price'] += $wz_weapons[$turret]['price'];
			$wz_templates[$row['tid']]['weight'] += $wz_weapons[$turret]['weight'];
		}
		echo $row['name'].': '.$row['weight'].' = '.$wz_bodies[$row['body']]['weight'].'\\'.$wz_propulsions[$row['propulsion']]['weight'].' + '.$wz_weapons[$row['turrets'][0]]['weight']."\n"; /////
	}
	foreach ($wz_templates as &$row)
	{
		if (!$row['aionly'])
		{
			$row['prereq'] = $wz_bodies[$row['body']]['prereq'];
		}
		if ($wz_propulsions[$row['propulsion']]['prop']=='legged')
		{
			$wz_cyborgs[$row['tid']] = $row;
			$wz_templates[$row['tid']]['prereq'] = $row['prereq'];
			if ($wz_research[$row['prereq']]['result'][0][0] == 'body')
				$wz_research[$row['prereq']]['result'][0] = array('cyborg',$row['tid']);
			else if ($wz_research[$row['prereq']]['result'][1][0] == 'body')
				$wz_research[$row['prereq']]['result'][1] = array('cyborg',$row['tid']);
			else if ($wz_research[$row['prereq']]['result'][2][0] == 'body')
				$wz_research[$row['prereq']]['result'][2] = array('cyborg',$row['tid']);
		}
	}
	$fp = fopen('cache/templates.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_templates=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_templates)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_cyborgs=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_cyborgs)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_tids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_tids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	$fp = fopen('cache/research.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_research=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_research)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rids)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rdids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rdids)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 16:
	echo "Making weapon table\r\n";
	include_once 'cache/research.inc.php';
	include_once 'cache/structures.inc.php';
	include_once 'cache/wsubclasses.inc.php';
	include_once 'cache/functions.inc.php';
	$wz_weapontable = array();
	$strings = file('data/mp/stats/weaponmodifier.txt');
	foreach ($strings as $string)
	{
		$data = explode(',',$string);
		$wz_weapontable[simplify($data[0])][simplify($data[1])] = intval($data[2]);
	}
	$strings = file('data/mp/stats/structuremodifier.txt');
	foreach ($strings as $string)
	{
		$data = explode(',',$string);
		$wz_weapontable[simplify($data[0])][simplify($data[1])] = intval($data[2]);
	}
	$fp = fopen('cache/weapontable.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_weapontable=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_weapontable)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo "Done.\r\n";
	echo "Sorting research.\r\n";
	$sort = array();
	foreach ($wz_research as $key => $row) {
		$sort[$key]  = $row['price'];
	}
	array_multisort($sort, SORT_ASC, $wz_research);
	$wz_research['machinegun']['enabled'] = 1;
	$wz_research['engineering']['enabled'] = 1;
	$wz_research['sensorturret']['enabled'] = 1;
	$wz_researchorder = array();
	while (true)
	{
		$somethingleft = false;
		foreach ($wz_research as $i => $research)
		{
			if (@$research['enabled'] == 1)
			{
				$somethingleft = true;
				foreach ($research['allows'] as $curallow)
				{
					if (!@$wz_research[$curallow]['enabled'])
					{
						$canenable = true;
						foreach ($wz_research[$curallow]['prereqs'] as $prereq)
						{
							if (substr($prereq,0,2) !== '..' && !@$wz_research[$prereq]['enabled'])
							{
								$canenable = false;
								break;
							}
						}
						if ($canenable)
							$wz_research[$curallow]['enabled'] = 1;
					}
				}
				$wz_research[$i]['enabled'] = 2;
				$wz_researchorder[] = $i;
				break;
			}
		}
		if (!$somethingleft)
			break;
	}
	foreach ($wz_researchorder as $k)
	{
		$wz_research[$k]['totalbp'] = 0;
		foreach ($wz_research[$k]['prereqs'] as $prereq)
		{
			if ($wz_research[$k]['totalbp'] < @$wz_research[$prereq]['totalbp'])
			{
				$wz_research[$k]['totalbp'] = $wz_research[$prereq]['totalbp'];
			}
		}
		$wz_research[$k]['totalbp'] += $wz_research[$k]['price'];
	}
	foreach ($wz_researchorder as $k)
	{
		//echo $k.' ';
		$totaltime = 0;
		$totalbp = 0;
		$researchmultiplier = 1;
		if ($wz_research[$k]['totalbp'] > $wz_research['researchmodule']['totalbp'])
		{
			$totaltime = $wz_research['researchmodule']['totalbp'];
			$totalbp = $wz_research['researchmodule']['totalbp'];
			$researchmultiplier = 1.857;
		}
		foreach ($wz_upgrades['researchupgrade']['upgrades'] as $upgrade)
		{
			//echo $upgrade.':'.$wz_research[$upgrade]['totalbp'].' '.$k.':'.$wz_research[$k]['totalbp'];
			if ($wz_research[$upgrade]['totalbp'] > $wz_research[$k]['totalbp'])
			{
				break;
			}
			$totaltime += ($wz_research[$upgrade]['totalbp']-$totalbp)/$researchmultiplier;
			$totalbp = $wz_research[$upgrade]['totalbp'];
			$researchmultiplier = 1.857 + $wz_functions[$wz_research[$upgrade]['result'][0][1]]['funcs'][0]['amt']/100;
		}
		$totaltime += ($wz_research[$k]['totalbp']-$totalbp)/$researchmultiplier;
		$wz_research[$k]['totaltime'] = $totaltime;
		//echo '<br />';
//$out .= '<li>'.($wz_functions[$wz_research[$upgrade]['result'][0][1]]['funcs'][0]['amt']+100).'% - <a href="'.$root.'r/'.$upgrade.'">'.$wz_research[$upgrade]['name']."</a></li>\r\n";

	}
	$fp = fopen('cache/research.inc.php', 'w');
	fwrite($fp, '<'.'?php $wz_research=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_research)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rids)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_rdids=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_rdids)) or fclose($fp);
	fwrite($fp, '; ') or fclose($fp);
	fwrite($fp, '$wz_researchorder=') or fclose($fp);
	fwrite($fp, persist_tophp($wz_researchorder)) or fclose($fp);
	fwrite($fp, '; ?'.'>') or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 112:
	echo "Annotating graph\r\n";
	include_once 'cache/research.inc.php';
	
	$data = file_get_contents('cache/researchtree.svg');
	preg_match_all('/\<polygon[^>]* points="([-0-9\.]+),([-0-9\.]+) [-0-9\.]+,[-0-9\.]+ ([-0-9\.]+),([-0-9\.]+) [-0-9\., ]*"[^>]*>\s*<text[^>]*>([^<]*)<\/text>/', $data, $matches);
	
	$gen = '<!DOCTYPE html PUBLIC>
<html><head><title>Warzone 2100 Tech Tree</title><link rel="stylesheet" href="treestyle.css" type="text/css" /></head><body><img src="../images/researchtree.png" />'."\r\n";
	$gen .= '<div style="display:none;"><a href="./">&laquo; Back to Research</a> <a href="tech-tree-linear">Linear tech tree</a></div>'."\r\n";
	foreach ($matches[5] as $i => $match)
	{
		if ($match=='Automatic') continue;
		$match = html_entity_decode($match);
		$x = min(abs(intval($matches[1][$i])),abs(intval($matches[3][$i])));
		$y = max(abs(intval($matches[2][$i])),abs(intval($matches[4][$i])));
		$w = abs(intval($matches[1][$i])-intval($matches[3][$i]));
		$h = abs(intval($matches[2][$i])-intval($matches[4][$i]));
		$x += 145;
		$y = 3421-$y;
		$x *= 0.320285 / 0.75;
		$y *= 0.320285 / 0.75;
		$w *= 0.320285 / 0.75;
		$h *= 0.320285 / 0.75;
		
		$x -= 1;
		$y -= 1;
		$w += 4;
		$h += 2;
		
		if ($match == 'Sensor Turret') { $x -= 1; $w -= 1; }
		
		$class='none';
		if ($wz_research[simplify($match)]['result'] && $wz_research[simplify($match)]['result'][0][0] == 'struct')
			$class='struct';
		if ($wz_research[simplify($match)]['result'] && $wz_research[simplify($match)]['result'][0][0] == 'weapon')
			$class='weapon';
		if ($wz_research[simplify($match)]['result'] && $wz_research[simplify($match)]['result'][0][0] == 'body')
			$class='component';
		if ($wz_research[simplify($match)]['result'] && $wz_research[simplify($match)]['result'][0][0] == 'cyborg')
			$class='weapon';
		if ($wz_research[simplify($match)]['result'] && $wz_research[simplify($match)]['result'][0][0] == 'propulsion')
			$class='component';
		
		$gen .= '<div><a href="'.simplify($match).'" style="display:block;position:absolute;left:'.intval($x).'px;top:'.intval($y).'px;width:'.intval($w).'px;height:'.intval($h).'px;" title="'.$match.' | $'.min(intval($wz_research[simplify($match)]['price']/32),450).'" class="'.$class.'"><span>'.$match.'</span></a></div>'."\r\n";
	}
	$gen .= '<div style="display:block;position:absolute;border:1px solid #808080;background:#F0F0F0;left:65px;top:445px;width:81px;text-align:center;">START</div>';
	
	$gen .= '<script>window.scrollTo(0,400);</script>';
	$gen .= '<div id="linkbar"><a href="./">&laquo; Back to Research</a> <a href="tech-tree-linear">Linear tech tree</a></div>'."\r\n";
	
	$gen .= "<script type=\"text/javascript\">

  var _gaq = _gaq || [];
  _gaq.push(['_setAccount', 'UA-8534680-2']);
  _gaq.push(['_trackPageview']);

  (function() {
    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
    (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(ga);
  })();

</script>
";
	$gen .= '</body></html>';
	
	$fp = fopen('r/graphicaltechtree.html', 'w');
	fwrite($fp, $gen) or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 101:
	echo "Generating w/\r\n";
	include 'cache/weapons.inc.php';
	function weaponrow($weapon)
	{
		global $sensors;
		if ($weapon['wid']=='nexuslink2') return FALSE;
		if ($weapon['wid']=='commandturret') $weapon['prereq'] = 'commandturret';
		if ($weapon['turret'] == 'construct' || $weapon['turret'] == 'repair')
			return "<tr><td class=\"l\" valign=\"middle\"><a href=\"{$weapon['wid']}\" class=\"iconlink\">".iconimg($weapon['wid']).'<span>'.trim($weapon['name'],'*')."</span></a></td><td class=\"c\">{$weapon['hp']}</td><td class=\"c\">{$weapon['build']}</td><td class=\"c\">".($weapon['turret'] == 'repair'?'Repair units':'Build/repair structures')."</td><td class=\"c\">2</td><td class=\"c rwb\">".($weapon['prereq']?"<a href=\"../r/{$weapon['prereq']}\" class=\"small\">Prereqs</a>":"&nbsp;")."</td></tr>\r\n";
		if ($weapon['turret'] == 'sensor')
			return "<tr><td class=\"l\" valign=\"middle\"><a href=\"{$weapon['wid']}\" class=\"iconlink\">".iconimg($weapon['wid']).'<span>'.trim($weapon['name'],'*')."</span></a></td><td class=\"c\">{$weapon['hp']}</td><td class=\"c\">".$sensors[$weapon['sensor']]."</td><td class=\"c\">".round($weapon['lrange']/128,1)."</td><td class=\"c rwb\">".($weapon['prereq']?"<a href=\"../r/{$weapon['prereq']}\" class=\"small\">Prereqs</a>":"&nbsp;")."</td></tr>\r\n";
		$structweaps = array(
			'lasersatellitecommandpost' => '../d/lasersatellitecommandpost',
			'empmortar' => '../d/empmortarpit',
			'cannonfortress' => '../d/cannonfortress',
			'heavyrocketbastion' => '../d/heavyrocketbastion',
			'missilefortress' => '../d/missilefortress',
			'massdriver' => '../d/massdriverfortress'
		);
		if (@$structweaps[$weapon['wid']]) $weapon['wid'] = $structweaps[$weapon['wid']];
		return "<tr><td class=\"l\" valign=\"middle\"><a href=\"{$weapon['wid']}\" class=\"iconlink\">".iconimg($weapon['wid']).'<span>'.trim($weapon['name'],'*')."</span></a></td><td class=\"c\">{$weapon['hp']}</td><td class=\"c\" align=\"char\" char=\"(\">".weapondam($weapon).($weapon['subclass']=='emp'?'':" <span class=\"smallgrey\">(".round($weapon['dps'],1).")</span>")."</td><td class=\"c\">".weaponrof($weapon)."</td><td class=\"c\"><span class=\"smallgrey\">{$weapon['sacc']}-</span><span class=\"fwrap\"> </span>{$weapon['lacc']}%</td><td class=\"c\"><span class=\"smallgrey\">".round($weapon['srange']/128,1)."&ndash;</span><span class=\"fwrap\"> </span>".round($weapon['lrange']/128,1)."</td><td class=\"c rwb\">".($weapon['prereq']?"<a href=\"../r/{$weapon['prereq']}\" class=\"small\">Prereqs</a>":"&nbsp;")."</td></tr>\r\n";
	}
	$weaponheadrow = "<th class=\"l nb\" width=\"35%\">&nbsp;</th><th class=\"c\" width=\"6%\">HP</th><th class=\"c\" width=\"14%\">Damage<br /><span class=\"smallgrey\">(<acronym title=\"Damage per second at Armor 10\">DPS/A10</acronym>)</span></th><th class=\"c\" width=\"8%\"><acronym title=\"Rate of fire - shots per minute\">ROF</acronym></th><th class=\"c\" width=\"14%\">Accuracy<br /><span class=\"smallgrey\">(close-<span class=\"fwrap\"> </span>long)</span></th><th class=\"c\" width=\"14%\">Range<br /><span class=\"smallgrey\">(close-<span class=\"fwrap\"> </span>long)</span></th><th class=\"c rnb\" width=\"9%\">&nbsp;</th></tr>\r\n";
	$weaponhead = "<div class=\"guidetable\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" class=\"wt\">\r\n<tr valign=\"top\">".$weaponheadrow;
	$weaponheadrow = "<tr valign=\"top\" class=\"nf\">".$weaponheadrow;
	$weaponfoot = "</table></div>\r\n";
	$content = '<p>Damage key: <span class="kinetic">kinetic</span>, <span class="thermal">thermal</span>, <strong>s</strong>plash, <strong>f</strong>lame</p>'."\r\n";
	//$content .= $weaponhead.$weaponfoot;
	$content .= "<h3 id=\"systems\">Systems</h3>\r\n";
	// Obtain a list of columns
	foreach ($wz_weapons as $key => $row) {
    $sort[$key]  = $row['price']*100+$row['dps'];
	}
	array_multisort($sort, SORT_ASC, $wz_weapons);
	$content .= "<div class=\"guidetable\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" class=\"wt\">\r\n<tr valign=\"top\"><th class=\"l nb\" width=\"35%\">&nbsp;</th><th class=\"c\" width=\"6%\">HP</th><th class=\"c\" width=\"14%\">Build points</th><th class=\"c\" width=\"22%\">Ability</th><th class=\"c\" width=\"14%\">Range</th><th class=\"c rnb\" width=\"9%\">&nbsp;</th></tr>";
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && $weapon['turret']=='construct') $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"repair\">Repair Turrets</h4>\r\n";
	$content .= "<div class=\"guidetable\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" class=\"wt\">\r\n<tr valign=\"top\"><th class=\"l nb\" width=\"35%\">&nbsp;</th><th class=\"c\" width=\"6%\">HP</th><th class=\"c\" width=\"14%\">Build points</th><th class=\"c\" width=\"22%\">Ability</th><th class=\"c\" width=\"14%\">Range</th><th class=\"c rnb\" width=\"9%\">&nbsp;</th></tr>";
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && $weapon['turret']=='repair') $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"sensor\">Sensors</h4>\r\n";
	$content .= "<p><em>See also:</em> <a href=\"../sensors\">How to use sensors</a></p>\r\n";
	$content .= "<div class=\"guidetable\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" class=\"wt\">\r\n<tr valign=\"top\"><th class=\"l nb\" width=\"35%\">&nbsp;</th><th class=\"c\" width=\"6%\">HP</th><th class=\"c\" width=\"36%\">Sensor</th><th class=\"c\" width=\"14%\">Range</th><th class=\"c rnb\" width=\"9%\">&nbsp;</th></tr>";
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && $weapon['turret']=='sensor') $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"command\">Command turrets</h4>\r\n";
	$content .= "<p><em>See also:</em> <a href=\"../commanders\">How to use commanders</a></p>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['subclass'] == 'command') $content .= weaponrow($weapon);
	$content .= $weaponfoot;

	// Weapon Table!
	function weapontabrow($prop)
	{
		global $props, $wz_weapontable;
		if ($wz_weapontable['allrounder'][$prop] && !$wz_weapontable['antiaircraft'][$prop])
			$wz_weapontable['antiaircraft'][$prop] = $wz_weapontable['allrounder'][$prop];
		return "<tr><th class=\"l\">{$props[$prop]}</th><td class=\"c\">{$wz_weapontable['antipersonnel'][$prop]}%</td><td class=\"c\">{$wz_weapontable['antitank'][$prop]}%</td><td class=\"c\">{$wz_weapontable['bunkerbuster'][$prop]}%</td><td class=\"c\">{$wz_weapontable['artilleryround'][$prop]}%</td><td class=\"c\">{$wz_weapontable['flamer'][$prop]}%</td><td class=\"c\">{$wz_weapontable['antiaircraft'][$prop]}%</td></tr>\r\n";
	}
	$content .= "<h3 id=\"weapontable\">Weapon Damage Table</h3>\r\n";
	$content .= "<div class=\"guidetable\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" class=\"mpad\">\r\n<tr valign=\"top\"><th class=\"r nb\" width=\"22%\">&nbsp;</th><th class=\"c\" width=\"13%\"><acronym title=\"Anti-Personnel\">AP</acronym</th><th class=\"c\" width=\"13%\">Anti-Tank</th><th class=\"c\" width=\"13%\">Anti-Struct</th><th class=\"c\" width=\"13%\">Artillery</th><th class=\"c\" width=\"13%\">Flamer</th><th class=\"c\" width=\"13%\">".($rebalance?'All-Rounder':'Anti-Air')."</th></tr>\r\n";
	foreach (array_keys($props) as $prop)
	{
		if ($prop == 'soft')
			$content .= "<tr valign=\"top\" class=\"nf\"><th class=\"r nb\" width=\"22%\">&nbsp;</th><th class=\"c\" width=\"13%\"><acronym title=\"Anti-Personnel\">AP</acronym</th><th class=\"c\" width=\"13%\">Anti-Tank</th><th class=\"c\" width=\"13%\">Anti-Struct</th><th class=\"c\" width=\"13%\">Artillery</th><th class=\"c\" width=\"13%\">Flamer</th><th class=\"c\" width=\"13%\">".($rebalance?'All-Rounder':'Anti-Air')."</th></tr>\r\n";
		$content .= weapontabrow($prop);
	}
	$content .= "</table></div>\r\n";

	$content .= "<h3 id=\"weapons\">Weapons</h3>\r\n";
	// Obtain a list of columns
	$content .= "<h4 id=\"machinegun\">Machineguns</h4>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && !$weapon['numattackruns'] && $weapon['subclass'] == 'machinegun') $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"cannon\">Cannons</h4>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && !$weapon['numattackruns'] && $weapon['subclass'] == 'cannon' && $weapon['targetground']) $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"flamer\">Flamers</h4>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && !$weapon['numattackruns'] && $weapon['subclass'] == 'flame') $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"mortar\">Mortars and Howitzers</h4>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && !$weapon['numattackruns'] && $weapon['subclass'] == 'mortars') $content .= weaponrow($weapon);
	$content .= $weaponheadrow;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && !$weapon['numattackruns'] && $weapon['subclass'] == 'howitzers') $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"rocket\">Rockets and Missiles</h4>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && !$weapon['numattackruns'] && ($weapon['subclass'] == 'rocket' && $weapon['targetground'])) $content .= weaponrow($weapon);
	$content .= $weaponheadrow;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && !$weapon['numattackruns'] && ($weapon['subclass'] == 'missile' && $weapon['targetground'])) $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"gauss\">Rail Guns</h4>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && !$weapon['numattackruns'] && $weapon['subclass'] == 'gauss') $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"laser\">Lasers</h4>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && !$weapon['numattackruns'] && $weapon['subclass'] == 'energy' && $weapon['targetground'] && $weapon['wid']!='alexssuperweapon') $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"electronic\">Electronic</h4>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && !$weapon['numattackruns'] && ($weapon['subclass'] == 'electronic' || $weapon['subclass'] == 'command' || $weapon['subclass'] == 'emp') && $weapon['prereq']) $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"aa\">Anti-air</h4>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && !$weapon['numattackruns'] && (!$weapon['targetground'] && $weapon['targetair'])) $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h3 id=\"vtolweapons\">VTOL Weapons</h3>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && $weapon['numattackruns'] && $weapon['subclass'] != 'bomb' && $weapon['targetground']) $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"vtolbombs\">Bombs</h4>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && $weapon['numattackruns'] && $weapon['subclass'] == 'bomb') $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h4 id=\"airtoair\">Air-to-air</h4>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if ($weapon['designable'] && $weapon['numattackruns'] && !$weapon['targetground']) $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	$content .= "<h3 id=\"struct\">Structure-only Weapons</h3>\r\n";
	$content .= $weaponhead;
	foreach ($wz_weapons as $weapon)
		if (in_array($weapon['wid'],array('lasersatellitecommandpost','cannonfortress','heavyrocketbastion','missilefortress','massdriver','lassat','fortresscannonweapon','rocketbastionweapon','missilefortressweapon','massdriverweapon','empmortar'))) $content .= weaponrow($weapon);
	$content .= $weaponfoot;
	
	$wdata = file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','Turrets',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVw}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#systems">Systems</a><ul><li><a href="#repair">Repair turrets</a></li><li><a href="#sensor">Sensor turrets</a></li><li><a href="#command">Command turrets</a></li></ul></li><li><a href="#weapontable">Weapon Table</a></li><li><a href="#weapons">Weapons</a><ul><li><a href="#machinegun">Machineguns</a></li><li><a href="#cannon">Cannons</a></li><li><a href="#flamer">Flamers</a></li><li><a href="#mortar">Mortars/Howitzers</a></li><li><a href="#rocket">Rockets/Missiles</a></li><li><a href="#gauss">Rail Guns</a></li><li><a href="#laser">Lasers</a></li><li><a href="#electronic">Electronic</a></li><li><a href="#aa">Anti-air</a></li></ul></li><li><a href="#vtolweapons">VTOL Weapons</a><ul><li><a href="#vtolbombs">Bombs</a></li><li><a href="#airtoair">Air-to-air</a></li></ul></li><li><a href="#struct">Structure Weapons</a></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<strong>Turrets</strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('w')) mkdir('w');
	$fp = fopen('w/index.html', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 102:
	echo "Generating r.php/\r\n";
	include_once 'cache/research.inc.php';
	$content = '<'.'?php echo showdesc($id,\'../\'); ?'.'>';
	$content .= '<h3 id="prereqs">Prerequisites</h3>';
	$content .= '<p class="expcolall"><a href="#" class="aexp" onclick="return expandall(document.getElementById(\'prereqtree\'));">Expand all</a> | <a href="#" class="acol" onclick="return collapseall(document.getElementById(\'prereqtree\'));">Collapse all</a></p>';
	$content .= '<div class="b"><ul id="prereqtree" class="tree"><'.'?php echo showprereqs($id,$ar,\'../\'); ?'.'></ul></div>';
	$content .= '<h3 id="allows">Required for</h3>';
	$content .= '<'.'?php echo showallows($id,\'../\'); ?'.'>';
	$wdata = '<'.'?php $run=true; include_once \'../tools/wzguide.lib.php\'; ?'.'>'.file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','<?'.'php echo $wz_research[$id][\'name\']; ?'.'> - Research',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVr}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#prereqs">Prereqs</a></li><li><a href="#allows">Required for</a></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<span class="arrow">&raquo;</span> <strong><?'.'php echo $wz_research[$id][\'name\']; ?'.'></strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('r')) mkdir('r');
	$fp = fopen('r/r.php', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 104:
	echo "Generating w.php/\r\n";
	include_once 'cache/weapons.inc.php';
	include_once 'cache/wsubclasses.inc.php';
	$content = '<'.'?php echo weapondesc($id,\'../\'); ?'.'>';
	$content .= '<h3 id="upgrades">Upgrades</h3>';
	$content .= '<'.'?php echo weaponupgrades($id,\'../\'); comments(\'w/\'.$id,\'../\'); ?'.'>';
	$wdata = '<'.'?php $run=true; include_once \'../tools/wzguide.lib.php\'; ?'.'>'.file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','<?'.'php echo $wz_weapons[$id][\'name\']; ?'.'> - Turrets',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVw}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#upgrades">Upgrades</a></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<span class="arrow">&raquo;</span> <strong><?'.'php echo $wz_weapons[$id][\'name\']; ?'.'></strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('w')) mkdir('w');
	$fp = fopen('w/w.php', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 103:
	echo "Generating r/\r\n";
	include_once 'cache/research.inc.php';
	$content = '<p><em>See also:</em> <a href="tech-tree">Tech Tree</a></p>';
	if (file_exists('r/graphicaltechtree.html'))
		$content = '<p><em>See also:</em> <a href="tech-tree">Tech Tree</a>, <a href="tech-tree-linear">Linear Tech Tree</a></p>';
	$content .= '<h3 id="machinegun">Machinegun</h3>';
	$content .= showdesc('machinegun','../');
	$content .= '<h4 id="machinegunallows">Required for</h4>';
	$content .= showallows('machinegun','../');
	$content .= '<h3 id="engineering">Engineering</h3>';
	$content .= showdesc('engineering','../');
	$content .= '<h4 id="engineeringallows">Required for</a></h4>';
	$content .= showallows('engineering','../');
	$content .= '<h3 id="sensor">Sensor Turret</h3>';
	$content .= showdesc('sensorturret','../');
	$content .= '<h4 id="sensorallows">Required for</a></h4>';
	$content .= showallows('sensorturret','../');
	$wdata = file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','Research',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVr}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#machinegun">Machinegun</a><ul><li><a href="#machinegunallows">Required for</a></li></ul></li><li><a href="#engineering">Engineering</a><ul><li><a href="#engineeringallows">Required for</a></li></ul></li><li><a href="#sensor">Sensor Turret</a><ul><li><a href="#sensorallows">Required for</a></li></ul></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<strong>Research</strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('r')) mkdir('r');
	$fp = fopen('r/index.html', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 105:
	echo "Generating c/\r\n";
	include 'cache/weapons.inc.php';
	include 'cache/templates.inc.php';
	function cyborgrow($cyborg)
	{
		global $wz_weapons;
		$weapon = $wz_weapons[$cyborg['turrets'][0]];
		if ($weapon['turret'] == 'construct' || $weapon['turret'] == 'repair')
			return "<tr><td class=\"l\"><a href=\"{$cyborg['tid']}\" class=\"iconlink\">".iconimg($cyborg['tid'],'c').'<span>'.trim($weapon['name'],'*')."</span></a></td><td class=\"c\">".templatehp($cyborg)."</td><td class=\"c\">{$weapon['build']}</td><td class=\"c\">".($weapon['turret'] == 'repair'?'Repair units':'Build/repair structures')."</td><td class=\"c\">2</td><td class=\"c rwb\">".($cyborg['prereq']?"<a href=\"../r/{$cyborg['prereq']}\" class=\"small\">Prereqs</a>":"&nbsp;")."</td></tr>\r\n";
		if ($weapon['turret'] == 'sensor')
			return "<tr><td class=\"l\"><a href=\"{$cyborg['tid']}\" class=\"iconlink\">".iconimg($cyborg['tid'],'c').'<span>'.trim($weapon['name'],'*')."</span></a></td><td class=\"c\">".templatehp($cyborg)."</td><td class=\"c\">".$sensors[$weapon['sensor']]."</td><td class=\"c\">{$weapon['lrange']}</td><td class=\"c rwb\">".($cyborg['prereq']?"<a href=\"../r/{$cyborg['prereq']}\" class=\"small\">Prereqs</a>":"&nbsp;")."</td></tr>\r\n";
		return "<tr><td class=\"l\"><a href=\"{$cyborg['tid']}\" class=\"iconlink\">".iconimg($cyborg['tid'],'c')."<span>{$cyborg['name']}</span></a></td><td class=\"c\">".templatehp($cyborg)."</td><td class=\"c\" align=\"char\" char=\"(\">".weapondam($weapon)." <span class=\"smallgrey\">(".round($weapon['dps'],1).")</span></td><td class=\"c\">".weaponrof($weapon)."</td><td class=\"c\"><span class=\"smallgrey\">{$weapon['sacc']}-</span><span class=\"fwrap\"> </span>{$weapon['lacc']}%</td><td class=\"c\"><span class=\"smallgrey\">".round($weapon['srange']/128,1)."&ndash;</span><span class=\"fwrap\"> </span>".round($weapon['lrange']/128,1)."</td><td class=\"c rwb\">".($cyborg['prereq']?"<a href=\"../r/{$cyborg['prereq']}\" class=\"small\">Prereqs</a>":"&nbsp;")."</td></tr>\r\n";
	}
	$cyborgheadrow = "<th class=\"l nb\" width=\"35%\">&nbsp;</th><th class=\"c\" width=\"6%\">HP</th><th class=\"c\" width=\"14%\">Damage<br /><span class=\"smallgrey\">(<acronym title=\"Damage per second at Armor 10\">DPS/A10</acronym>)</span></th><th class=\"c\" width=\"8%\"><acronym title=\"Rate of fire - shots per minute\">ROF</acronym></th><th class=\"c\" width=\"14%\">Accuracy<br /><span class=\"smallgrey\">(close-<span class=\"fwrap\"> </span>long)</span></th><th class=\"c\" width=\"14%\">Range<br /><span class=\"smallgrey\">(close-<span class=\"fwrap\"> </span>long)</span></th><th class=\"c rnb\" width=\"9%\">&nbsp;</th></tr>\r\n";
	$cyborghead = "<div class=\"guidetable\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" class=\"wt\">\r\n<tr valign=\"top\">".$cyborgheadrow;
	$cyborgheadrow = "<tr valign=\"top\" class=\"nf\">".$cyborgheadrow;
	$cyborgfoot = "</table></div>\r\n";
	$content = '<p>Damage key: <span class="kinetic">kinetic</span>, <span class="thermal">thermal</span>, <strong>s</strong>plash, <strong>f</strong>lame</p>'."\r\n";
	//$content .= "<h3 id=\"cyborgs\">Cyborgs</h3>\r\n";
	// Obtain a list of columns // $wz_weapons[$cyborg['turrets'][0]]
	foreach ($wz_cyborgs as $key => $row)
		$sort[$key]  = $row['price'];
	array_multisort($sort, SORT_ASC, $wz_cyborgs);
	$content .= '<h3 id="system">Systems</h3>';
	$content .= "<div class=\"guidetable\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" class=\"wt\">\r\n<tr valign=\"top\"><th class=\"l nb\" width=\"35%\">&nbsp;</th><th class=\"c\" width=\"6%\">HP</th><th class=\"c\" width=\"14%\">Build points</th><th class=\"c\" width=\"22%\">Ability</th><th class=\"c\" width=\"14%\">Range</th><th class=\"c rnb\" width=\"9%\">&nbsp;</th></tr>";
	foreach ($wz_cyborgs as $cyborg)
		if ($cyborg['prereq'] && $wz_weapons[$cyborg['turrets'][0]]['turret'] != 'weapon') $content .= cyborgrow($cyborg);
	$content .= $cyborgfoot;
	$content .= '<h3 id="weapon">Weapons</h3>';
	$content .= $cyborghead;
	foreach ($wz_cyborgs as $cyborg)
		if ($cyborg['prereq'] && $wz_weapons[$cyborg['turrets'][0]]['turret'] == 'weapon' && $cyborg['armor'] <= 13) $content .= cyborgrow($cyborg);
	$content .= $cyborgheadrow;
	foreach ($wz_cyborgs as $cyborg)
		if ($cyborg['prereq'] && $wz_weapons[$cyborg['turrets'][0]]['turret'] == 'weapon' && $cyborg['armor'] > 13) $content .= cyborgrow($cyborg);
	$content .= $cyborgfoot;
	$content .= '<h3 id="transport">Transport</h3>';
	$content .= $cyborghead;
	if (isset($wz_templates['transport']))
	{
		$wz_templates['transport']['prereq'] = 'cyborgtransport';
		$content .= cyborgrow($wz_templates['transport']);
	}
	else
	{
		$wz_templates['cyborgtransport']['prereq'] = 'cyborgtransport';
		$content .= cyborgrow($wz_templates['cyborgtransport']);
	}
	$content .= $cyborgfoot;
	$wdata = file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','Cyborgs',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVc}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#system">Systems</a></li><li><a href="#weapon">Weapons</a></li><li><a href="#transport">Transport</a></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<strong>Cyborgs</strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('c')) mkdir('c');
	$fp = fopen('c/index.html', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 106:
	echo "Generating c.php, t.php, bp.php, b.php, d.php/\r\n";
	include_once 'cache/weapons.inc.php';
	include_once 'cache/wsubclasses.inc.php';
	$content = '<'.'?php echo templatedesc($id,\'../\'); ?'.'>';
	$content .= '<h3 id="upgrades">Upgrades</h3>';
	$content .= '<'.'?php if ($wz_templates[$id][\'aionly\']) echo \'<p>This template is AI only.</p>\'; echo weaponupgrades($wz_templates[$id][\'turrets\'][0],\'../\'), bodyupgrades($wz_propulsions[$wz_templates[$id][\'propulsion\']][\'prop\']==\'legged\',\'../\'); comments(\'w/\'.$wz_templates[$id][\'turrets\'][0],\'../\'); ?'.'>';
	$wdata = '<'.'?php $run=true; include_once \'../tools/wzguide.lib.php\'; ?'.'>'.file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','<?'.'php echo $wz_templates[$id][\'name\']; ?'.'> - Cyborgs',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVc}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#upgrades">Upgrades</a></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<span class="arrow">&raquo;</span> <strong><?'.'php echo $wz_templates[$id][\'name\']; ?'.'></strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('c')) mkdir('c');
	$fp = fopen('c/c.php', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	$content = '<'.'?php echo templatedesc($template,\'../\',$wz_propulsions[$template[\'propulsion\']][\'prop\']==\'legged\'); ?'.'>';
	$content .= '<h3 id="upgrades">Upgrades</h3>';
	$content .= '<'.'?php echo weaponupgrades($template[\'turrets\'][0],\'../\'), bodyupgrades($wz_propulsions[$template[\'propulsion\']][\'prop\']==\'legged\',\'../\'); ?'.'>';
	$wdata = '<'.'?php $run=true; include_once \'../tools/wzguide.lib.php\'; ?'.'>'.file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','<?'.'php  $template = mktemplate($_REQUEST[\'w\'],$_REQUEST[\'b\'],$_REQUEST[\'p\']); $cyborg = isset($_REQUEST[\'cyborg\']); echo $template[\'name\']; ?'.'> - Templates',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVc}','<?'.'php if ($cyborg) echo \'class="c"\' ?'.'>',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#upgrades">Upgrades</a></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<span class="arrow">&raquo;</span> <strong><?'.'php echo $template[\'name\']; ?'.'></strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('c')) mkdir('c');
	$fp = fopen('c/t.php', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	$content = '<'.'?php echo bodydesc($id,\'../\',false); ?'.'>';
	$content .= '<h3 id="upgrades">Upgrades</h3>';
	$content .= '<'.'?php echo bodyupgrades(false,\'../\'); comments(\'bp/\'.$id,\'../\'); ?'.'>';
	$wdata = '<'.'?php $run=true; include_once \'../tools/wzguide.lib.php\'; ?'.'>'.file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','<?'.'php echo $wz_bodies[$id][\'name\']; ?'.'> - Bodies',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVbp}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#upgrades">Upgrades</a></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<span class="arrow">&raquo;</span> <strong><?'.'php echo $wz_bodies[$id][\'name\']; ?'.'></strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('bp')) mkdir('bp');
	$fp = fopen('bp/bp.php', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	$content = '<'.'?php echo structuredesc($id,\'../\',false); ?'.'>';
	$content .= '<h3 id="upgrades">Upgrades</h3>';
	$content .= '<'.'?php if ($wz_structures[$id][\'turrets\']) echo weaponupgrades($wz_structures[$id][\'turrets\'][0],\'../\'); echo structureupgrades($id,\'../\',false); comments(\'d/\'.$id,\'../\'); ?'.'>';
	$wdata = '<'.'?php $run=true; include_once \'../tools/wzguide.lib.php\'; ?'.'>'.file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','<?'.'php echo $wz_structures[$id][\'name\']; ?'.'> - Defensive structures',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVd}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#upgrades">Upgrades</a></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<span class="arrow">&raquo;</span> <strong><?'.'php echo $wz_structures[$id][\'name\']; ?'.'></strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('d')) mkdir('d');
	$fp = fopen('d/d.php', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	$content = '<'.'?php echo structuredesc($id,\'../\',true); ?'.'>';
	$content .= '<h3 id="upgrades">Upgrades</h3>';
	$content .= '<'.'?php if ($wz_structures[$id][\'turrets\']) echo weaponupgrades($wz_structures[$id][\'turrets\'][0],\'../\'); echo structureupgrades($id,\'../\',true); comments(\'d/\'.$id,\'../\'); ?'.'>';
	$wdata = '<'.'?php $run=true; include_once \'../tools/wzguide.lib.php\'; ?'.'>'.file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','<?'.'php echo $wz_structures[$id][\'name\']; ?'.'> - Base structures',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVb}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#upgrades">Upgrades</a></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<span class="arrow">&raquo;</span> <strong><?'.'php echo $wz_structures[$id][\'name\']; ?'.'></strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('b')) mkdir('b');
	$fp = fopen('b/b.php', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 107:
	echo "Generating bp/\r\n";
	function bodyrow($body)
	{
		$realbody = false;
		if ($body['bid'] == 'cyborgchaingun1ground') list($body['name'],$body['size'],$body['prereq']) = array('<em>Cyborg body</em>','&mdash;','');
		else if ($body['bid'] == 'superheavygunner') list($body['name'],$body['size'],$body['prereq']) = array('<em>Super heavy cyborg body</em>','&mdash;','');
		else $realbody = true;
		return "<tr><td class=\"l\"><".($realbody?'a href="'.$body['bid'].'"':'span')." class=\"iconlink\">".iconimg($body['bid'],'bp')."<span>{$body['name']}</span></".($realbody?'a':'span')."></td><td class=\"c\">{$body['hp']}</td><td class=\"c\">".bodyarmor($body)."</td><td class=\"c\">{$body['engine']}</td><td class=\"c\">{$body['size']}</td><td class=\"c\">{$body['weight']}</td><td class=\"c price\">\${$body['price']}</td><td class=\"c rwb\">".($body['prereq']?"<a href=\"../r/{$body['prereq']}\" class=\"small\">Prereqs</a>":"&nbsp;")."</td></tr>\r\n";
	}
	$bodyheadrow = "<th class=\"l nb\" width=\"35%\">&nbsp;</th><th class=\"c\" width=\"6%\">HP</th><th class=\"c\" width=\"11%\">Armor</th><th class=\"c\" width=\"10%\">Engine</th><th class=\"c\" width=\"11%\">Size</th><th class=\"c\" width=\"10%\">Weight</th><th class=\"c\" width=\"8%\">Price</th><th class=\"c rnb\" width=\"9%\">&nbsp;</th></tr>\r\n";
	$bodyhead = "<div class=\"guidetable\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" class=\"wt\">\r\n<tr valign=\"top\">".$bodyheadrow;
	$bodyheadrow = "<tr valign=\"top\" class=\"nf\">".$bodyheadrow;
	$bodyfoot = "</table></div>\r\n";
	$content = '<p>Armor key: <span class="kinetic">kinetic</span>, <span class="thermal">thermal</span></p>'."\r\n";
	//$content .= "<h3 id=\"cyborgs\">Cyborgs</h3>\r\n";
	// Obtain a list of columns
	foreach ($wz_bodies as $key => $row)
		$sort[$key]  = $row['price'];
	array_multisort($sort, SORT_ASC, $wz_bodies);
	$content .= '<h3 id="bodies">Bodies</h3>';
	$content .= $bodyhead;
	foreach ($wz_bodies as $body)
		if (in_array($body['bid'],array('viper','cobra','python'))) $content .= bodyrow($body);
	$content .= $bodyheadrow;
	foreach ($wz_bodies as $body)
		if (in_array($body['bid'],array('bug','scorpion','mantis'))) $content .= bodyrow($body);
	$content .= $bodyheadrow;
	foreach ($wz_bodies as $body)
		if (in_array($body['bid'],array('leopard','panther','tiger'))) $content .= bodyrow($body);
	$content .= $bodyheadrow;
	foreach ($wz_bodies as $body)
		if (in_array($body['bid'],array('retaliation','retribution','vengeance'))) $content .= bodyrow($body);
	$content .= $bodyheadrow;
	foreach ($wz_bodies as $body)
		if (in_array($body['bid'],array('wyvern','dragon'))) $content .= bodyrow($body);
	$content .= $bodyfoot;
	$content .= '<h4 id="miscbodies">Miscellaneous bodies</a></h4>';
	$content .= $bodyhead;
	foreach ($wz_bodies as $body)
		if (in_array($body['bid'],array('cyborgchaingun1ground','superheavygunner','transportbody'))) $content .= bodyrow($body);
	$content .= $bodyfoot;
	function proprow($prop)
	{
		global $wz_proptypes;
		$types = array('air' => 'Air', 'ground' => 'Ground');
		return "<tr><td class=\"l\"><span class=\"iconlink\">".iconimg($prop['pid'],'bp')."<span>{$prop['name']}</span></span></td><td class=\"c\">+".($prop['hp'])."%</td><td class=\"c\">".$wz_proptypes[$prop['prop']]['speed']."%</td><td class=\"c\">{$prop['maxspeed']}</td><td class=\"c\">".$types[$wz_proptypes[$prop['prop']]['type']]."</td><td class=\"c\">+{$prop['weight']}%</td><td class=\"c price\">+{$prop['price']}%</td><td class=\"c rwb\">".($prop['prereq']&&$prop['prereq']!='cyborgpropulsion'?"<a href=\"../r/{$prop['prereq']}\" class=\"small\">Prereqs</a>":"&nbsp;")."</td></tr>\r\n";
	}
	$propheadrow = "<th class=\"l nb\" width=\"35%\">&nbsp;</th><th class=\"c\" width=\"9%\">HP</th><th class=\"c\" width=\"9%\">Speed</th><th class=\"c\" width=\"11%\">Max speed</th><th class=\"c\" width=\"10%\">Type</th><th class=\"c\" width=\"9%\">Weight</th><th class=\"c\" width=\"8%\">Price</th><th class=\"c rnb\" width=\"9%\">&nbsp;</th></tr>\r\n";
	$prophead = "<div class=\"guidetable\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" class=\"wt\">\r\n<tr valign=\"top\">".$propheadrow;
	$propheadrow = "<tr valign=\"top\" class=\"nf\">".$propheadrow;
	$propfoot = "</table></div>\r\n";
	$sort = array();
	foreach ($wz_propulsions as $key => $row)
		$sort[$key]  = $row['price'];
	array_multisort($sort, SORT_ASC, $wz_propulsions);
	$content .= '<h3 id="props">Propulsions</h3>';
	$content .= $prophead;
	foreach ($wz_propulsions as $prop)
		if ($prop['prereq'] && $prop['pid'] != 'cyborgpropulsion') $content .= proprow($prop);
	$content .= $propheadrow;
	foreach ($wz_propulsions as $prop)
		if ($prop['pid'] == 'cyborgpropulsion') $content .= proprow($prop);
	$content .= $propfoot;
	$content .= '<'.'?php @include \'../tools/wzguide.lib.php\'; @comments(\'bp\'); ?'.'>';
	$wdata = file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','Bodies and Propulsions',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVbp}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#bodies">Bodies</a><ul><li><a href="#miscbodies">Misc. bodies</a></li></ul></li><li><a href="#props">Propulsions</a></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<strong>Bodies and Propulsions</strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('bp')) mkdir('bp');
	$fp = fopen('bp/index.php', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 108:
	echo "Generating r/techtree.html\r\n";
	include_once 'cache/research.inc.php';
	$ar = array();
	$deepest = 0;
	$deepests = array();
	$content .= '<div class="b"><ul id="prereqtree" class="tree">';
	$content .= '<li class="expcolall"><div class="ti" style="height: 50px"><a href="#" class="aexp" onclick="return expandall(document.getElementById(\'prereqtree\'));">Expand all</a> | <a href="#" class="acol" onclick="return collapseall(document.getElementById(\'prereqtree\'));">Collapse all</a></div></li>';
	$content .= gentechtree('machinegun',$ar,'../');
	$content .= gentechtree('engineering',$ar,'../');
	$content .= gentechtree('sensorturret',$ar,'../');
	$content .='</ul></div>';
	$content = guide('r/techtree',108).$content;
	//$content = '<p>Size: '.count($ar).' entries. Deepest: '.$GLOBALS['deepest'].' - '.$wz_research[$GLOBALS['deepests'][0]]['name'].'</p>'.$content;
	$wdata = file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','Tech Tree',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVr}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#machinegun">Machinegun</a></li><li><a href="#engineering">Engineering</a></li><li><a href="#sensorturret">Sensor Turret</a></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<span class="arrow">&raquo;</span> <strong>Full Tech Tree</strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('r')) mkdir('r');
	$fp = fopen('r/techtree.html', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 109:
	echo "Generating b/\r\n";
	include 'cache/structures.inc.php';
	include 'cache/templates.inc.php';
	function baserow($struct)
	{
		global $strengths;
		return "<tr><td class=\"l\"><a href=\"{$struct['sid']}\" class=\"iconlink\">".iconimg($struct['sid'],'b')."<span>{$struct['name']}</span></a></td><td class=\"c\">{$struct['hp']}</td><td class=\"c\"><span class=\"kinetic\">{$struct['armor']}</span>/<span class=\"thermal\">{$struct['armor']}</span></td><td class=\"c\">{$struct['w']}&times;{$struct['h']}</td><td class=\"c\">".($strengths[$struct['strength']])."</td><td class=\"c\">{$struct['z']}</td><td class=\"c price\">\${$struct['price']}</td><td class=\"c rwb\">".($struct['prereq']?"<a href=\"../r/{$struct['prereq']}\" class=\"small\">Prereqs</a>":"&nbsp;")."</td></tr>\r\n";
	}
	$baseheadrow = "<th class=\"l nb\" width=\"35%\">&nbsp;</th><th class=\"c\" width=\"6%\">HP</th><th class=\"c\" width=\"11%\">Armor</th><th class=\"c\" width=\"11%\">Size</th><th class=\"c\" width=\"12%\">Strength</th><th class=\"c\" width=\"8%\">Height</th><th class=\"c\" width=\"8%\">Price</th><th class=\"c rnb\" width=\"9%\">&nbsp;</th></tr>\r\n";
	$basehead = "<div class=\"guidetable\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" class=\"wt\">\r\n<tr valign=\"top\">".$baseheadrow;
	$baseheadrow = "<tr valign=\"top\" class=\"nf\">".$baseheadrow;
	$basefoot = "</table></div>\r\n";
	$content = '<p>Damage key: <span class="kinetic">kinetic</span>, <span class="thermal">thermal</span>'."\r\n";
	$content .= '<h3 id="cc">Command Center</h3>';
	$content .= $basehead.baserow($wz_structures['commandcenter']).$basefoot;
	$content .= guide('b/commandcenter',109);
	$content .= '<h3 id="powergen">Power Generator</h3>';
	$content .= $basehead.baserow($wz_structures['powergenerator']).baserow($wz_structures['powermodule']).$basefoot;
	$content .= guide('b/powergenerator',109);
	$content .= '<h3 id="research">Research Facility</h3>';
	$content .= $basehead.baserow($wz_structures['researchfacility']).baserow($wz_structures['researchmodule']).$basefoot;
	$content .= guide('b/researchfacility',109);
	$content .= '<h3 id="factory">Factories</h3>';
	$content .= $basehead.baserow($wz_structures['factory']).baserow($wz_structures['factorymodule']).$baseheadrow.baserow($wz_structures['cyborgfactory']).$baseheadrow.baserow($wz_structures['vtolfactory']).$basefoot;
	$content .= guide('b/factory',109);
	$content .= '<h3 id="commandrelay">Command Relay Center</h3>';
	$content .= $basehead.baserow($wz_structures['commandrelaycenter']).$basefoot;
	$content .= guide('b/commandrelaycenter',109);
	$content .= '<h3 id="other">Other</h3>';
	$content .= $basehead.baserow($wz_structures['vtolrearmingpad']).baserow($wz_structures['repairfacility']).baserow($wz_structures['oilderrick']).$basefoot;
	$wdata = file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','Base Structures',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVb}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#cc">Command Center</a></li><li><a href="#powergen">Power Generator</a></li><li><a href="#research">Research Facility</a></li><li><a href="#factory">Factories</a></li><li><a href="#commandrelay">Command Relay</a></li><li><a href="#other">Other</a></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<strong>Base Structures</strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('b')) mkdir('b');
	$fp = fopen('b/index.html', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 110:
	echo "Generating d/\r\n";
	include 'cache/structures.inc.php';
	include 'cache/templates.inc.php';
	function structrow($struct)
	{
		global $wz_weapons, $strengths;
		if ($struct['turrets'] && $wz_weapons[$struct['turrets'][0]]['turret']=='weapon')
			return "<tr><td class=\"l\"><a href=\"{$struct['sid']}\" class=\"iconlink\">".iconimg($struct['sid'],'d')."<span>{$struct['name']}</span></a></td><td class=\"c\">".structurehp($struct)."</td><td class=\"c armor\"><span class=\"kinetic\">{$struct['armor']}</span>/<span class=\"thermal\">{$struct['armor']}</span></td><td class=\"c\">{$struct['w']}&times;{$struct['h']}</td><td class=\"c\">".($strengths[$struct['strength']])."</td><td class=\"c\">{$struct['z']}</td><td class=\"c price\">\${$struct['price']}</td><td class=\"c rwb\">".($struct['prereq']?"<a href=\"../r/{$struct['prereq']}\" class=\"small\">Prereqs</a>":"&nbsp;")."</td></tr>\r\n";
		else
			return "<tr><td class=\"l\"><a href=\"{$struct['sid']}\" class=\"iconlink\">".iconimg($struct['sid'],'d')."<span>{$struct['name']}</span></a></td><td class=\"c\">".structurehp($struct)."</td><td class=\"c armor\"><span class=\"kinetic\">{$struct['armor']}</span>/<span class=\"thermal\">{$struct['armor']}</span></td><td class=\"c\">{$struct['w']}&times;{$struct['h']}</td><td class=\"c\">".($strengths[$struct['strength']])."</td><td class=\"c\">{$struct['z']}</td><td class=\"c price\">\${$struct['price']}</td><td class=\"c rwb\">".($struct['prereq']?"<a href=\"../r/{$struct['prereq']}\" class=\"small\">Prereqs</a>":"&nbsp;")."</td></tr>\r\n";
	}
	$structheadrow = "<th class=\"l nb\" width=\"40%\">&nbsp;</th><th class=\"c\" width=\"6%\">HP</th><th class=\"c\" width=\"11%\">Armor</th><th class=\"c\" width=\"9%\">Size</th><th class=\"c\" width=\"10%\">Strength</th><th class=\"c\" width=\"7%\">Height</th><th class=\"c\" width=\"8%\">Price</th><th class=\"c rnb\" width=\"9%\">&nbsp;</th></tr>\r\n";
	$structhead = "<div class=\"guidetable\"><table width=\"100%\" border=\"0\" cellspacing=\"0\" class=\"wt\">\r\n<tr valign=\"top\">".$structheadrow;
	$structheadrow = "<tr valign=\"top\" class=\"nf\">".$structheadrow;
	$structfoot = "</table></div>\r\n";
	$content = '<p>Damage key: <span class="kinetic">kinetic</span>, <span class="thermal">thermal</span>'."\r\n";
	$sort = array();
	foreach ($wz_structures as $key => $row)
		$sort[$key]  = $row['price'] + $row['armor']*1000 + ($row['strength']=='hard'?10000:0);
	array_multisort($sort, SORT_ASC, $wz_structures);
	$content .= '<h3 id="wall">Walls</h3>';
	$content .= $structhead;
	foreach ($wz_structures as $struct)
		if ($struct['sid'] == 'tanktraps' || $struct['sid'] == 'hardcretewall') $content .= structrow($struct);
	$content .= $structfoot;
	$content .= '<h3 id="sensor">Sensor structures</h3>'."\r\n";
	$content .= "<p><em>See also:</em> <a href=\"../sensors#sensors\">How to use sensors</a></p>\r\n";
	$content .= $structhead;
	foreach ($wz_structures as $struct)
		if ($struct['type'] == 'defense' && $struct['prereq'] && !($struct['turrets'] && $wz_weapons[$struct['turrets'][0]]['turret'] == 'weapon')) $content .= structrow($struct);
	$content .= $structheadrow;
	$content .= structrow($wz_structures['satelliteuplinkcenter']);
	$content .= $structfoot;
	$content .= '<h3 id="weapon">Weapon structures</h3>';
	$content .= $structhead;
	foreach ($wz_structures as $struct)
		if ($struct['type'] == 'defense' && $struct['prereq'] && $struct['turrets'] && !in_array($wz_weapons[$struct['turrets'][0]]['direction'],array('indirect','indirecthoming')) && $wz_weapons[$struct['turrets'][0]]['turret'] == 'weapon' && $wz_weapons[$struct['turrets'][0]]['targetground']&& $struct['strength'] != 'hard' && $struct['strength'] != 'bunker') $content .= structrow($struct);
	$content .= $structheadrow;
	foreach ($wz_structures as $struct)
		if ($struct['type'] == 'defense' && $struct['prereq'] && $struct['turrets'] && !in_array($wz_weapons[$struct['turrets'][0]]['direction'],array('indirect','indirecthoming')) && $wz_weapons[$struct['turrets'][0]]['turret'] == 'weapon' && $wz_weapons[$struct['turrets'][0]]['targetground'] && $struct['strength'] == 'hard' && $struct['sid'] != 'lasersatellitecommandpost') $content .= structrow($struct);
	$content .= $structheadrow;
	foreach ($wz_structures as $struct)
		if ($struct['type'] == 'defense' && $struct['prereq'] && $struct['turrets'] && !in_array($wz_weapons[$struct['turrets'][0]]['direction'],array('indirect','indirecthoming')) && $wz_weapons[$struct['turrets'][0]]['turret'] == 'weapon' && $wz_weapons[$struct['turrets'][0]]['targetground'] && $struct['strength'] == 'bunker') $content .= structrow($struct);
	$content .= $structfoot;
	$content .= '<h4 id="artillery">Artillery</a></h4>';
	$content .= $structhead;
	foreach ($wz_structures as $struct)
		if ($struct['type'] == 'defense' && $struct['prereq'] && $struct['turrets'] && in_array($wz_weapons[$struct['turrets'][0]]['direction'],array('indirect','indirecthoming')) && $wz_weapons[$struct['turrets'][0]]['subclass'] == 'mortars') $content .= structrow($struct);
	$content .= $structheadrow;
	foreach ($wz_structures as $struct)
		if ($struct['type'] == 'defense' && $struct['prereq'] && $struct['turrets'] && in_array($wz_weapons[$struct['turrets'][0]]['direction'],array('indirect','indirecthoming')) && $wz_weapons[$struct['turrets'][0]]['subclass'] == 'howitzers') $content .= structrow($struct);
	$content .= $structheadrow;
	foreach ($wz_structures as $struct)
		if ($struct['type'] == 'defense' && $struct['prereq'] && $struct['turrets'] && in_array($wz_weapons[$struct['turrets'][0]]['direction'],array('indirect','indirecthoming')) && $wz_weapons[$struct['turrets'][0]]['subclass'] != 'mortars' && $wz_weapons[$struct['turrets'][0]]['subclass'] != 'howitzers') $content .= structrow($struct);
	$content .= $structfoot;
	$content .= '<h4 id="aa">Anti-air</a></h4>';
	$content .= $structhead;
	foreach ($wz_structures as $struct)
		if ($struct['type'] == 'defense' && $struct['prereq'] && $struct['turrets'] && $wz_weapons[$struct['turrets'][0]]['turret'] == 'weapon' && !$wz_weapons[$struct['turrets'][0]]['targetground']) $content .= structrow($struct);
	$content .= $structfoot;
	$content .= '<h4 id="superweapon">Superweapons</a></h4>';
	$content .= '<p><em>See also:</em> <a href="../w/#struct">Structure weapons</a>';
	$content .= $structhead;
	$content .= structrow($wz_structures['lasersatellitecommandpost']);
	$content .= $structheadrow;
	foreach ($wz_structures as $struct)
		if ($struct['type'] == 'door' && $struct['sid'] != 'lasersatellitecommandpost') $content .= structrow($struct);
	$content .= $structfoot;
	$wdata = file_get_contents('tools/template.html');
	$wdata = str_replace('{TITLE}','Defenses',$wdata);
	$wdata = str_replace('{ROOT}','../',$wdata);
	$wdata = str_replace('{NAVd}',' class="c"',$wdata);
	$wdata = str_replace('{LEFTNAV}','<li><a href="#wall">Walls</a></li><li><a href="#sensor">Sensors</a></li><li><a href="#weapon">Weapons</a><ul><li><a href="#artillery">Artillery</a></li><li><a href="#superweapon">Superweapons</a></li></ul></li>',$wdata);
	$wdata = str_replace('{TITLEBAR}','<strong>Defenses</strong>',$wdata);
	$wdata = str_replace('{CONTENT}',$content,$wdata);
	$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
	if (!is_dir('d')) mkdir('d');
	$fp = fopen('d/index.html', 'w');
	fwrite($fp, $wdata) or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 111:
	echo "Gen js/\r\n";
	include 'cache/weapons.inc.php';
	include 'cache/research.inc.php';
	$content = "wz_weapons = new array();\r\n";
	ksort($wz_weapons);
	foreach ($wz_weapons as $weapon)
	{
		if ($weapon['name'] && substr($weapon['name'],0,1)!='*')
			$content .= "wz_weapons['".$weapon['wid']."'] = ['".$weapon['wid']."','".strtr($weapon['name'],array('\\'=>'\\\\','\''=>'\\\''))."'];\r\n";
	}
	$fp = fopen('data.js', 'w');
	fwrite($fp, $content) or fclose($fp);
	fclose($fp);
	echo 'Done.';
	break;
case 112:
	break;
case 113:
	include_once 'tools/guide.inc.php';

	foreach ($guide as $id => $msg)
	{
		if (@$msg['autogen'])
		{
			echo 'Updating page: '.$id.' '."\r\n";
			$wdata = '<'.'?php include_once \'tools/wzguide.lib.php\'; $msg = $guide[\''.$id.'\']; ?'.'>'.file_get_contents('tools/template.html');
			$wdata = str_replace('{TITLE}',$msg['title'],$wdata);
			$wdata = str_replace('"{ROOT}"','"./"',$wdata);
			$wdata = str_replace('{ROOT}','',$wdata);
			$wdata = str_replace('{NAV}',' class="c"',$wdata);
			$wdata = str_replace('{LEFTNAV}',leftnav($id),$wdata);
			$wdata = str_replace('{TITLEBAR}',$msg['titlebar'],$wdata);
			$wdata = str_replace('{CONTENT}',guide($id,'php').'<'.'?php echo comments(\''.$id.'\'); ?'.'>',$wdata);
			$wdata = preg_replace('/\{[A-Za-z0-9]+\}/','',$wdata);
			$fp = fopen($id.'.php', 'w');
			fwrite($fp, $wdata) or fclose($fp);
			fclose($fp);
		}
	}
	echo 'Done.';
	break;
}

echo '</pre>';

if (isset($_GET['autonext']) && $step<count($steps) && !$error)
	echo '<script type="text/javascript">
<!--
document.location.href = "generate.php?s='.($step+1).'&autonext";
-->
</script>';
?>
</body>
</html>
<?php

if ($included) ob_end_clean();

?>