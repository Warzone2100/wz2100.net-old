<form action="converter.php" method="post"><textarea name="something" id="something" rows="20" cols="120"></textarea><input type="submit" value="OK" /></form>

<?php

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

function process_title($matches)
{
	$matches[1] = str_replace('   ','_', $matches[1]);
	$matches[1] = str_replace(' ','', $matches[1]);
	$matches[1] = str_replace('_',' ', $matches[1]);
	$matches[1] = ucwords(strtolower($matches[1]));
	$matches[1] = str_replace(' The ',' the ', $matches[1]);
	$matches[1] = str_replace(' A ',' a ', $matches[1]);
	$matches[1] = str_replace(' An ',' an ', $matches[1]);
	$matches[1] = str_replace(' To ',' to ', $matches[1]);
	$matches[1] = str_replace(' On ',' on ', $matches[1]);
	$matches[1] = str_replace(' From ',' from ', $matches[1]);
	$matches[1] = str_replace('Lz','LZ', $matches[1]);
	return '@N@<h4 id="">'.$matches[1].'</h4>@N@<p>@N@';
}

function process($str)
{
	$str = str_replace("\r\n", "@N@", $str); // makes regexes easier @_@
	$str = '@N@<p>@N@'.preg_replace('/@N@@N@/', '@N@</p>@N@<p>@N@', $str).'@N@</p>';
	$str = preg_replace_callback('/<p>@N@([A-Z! ]+)@N@/', process_title, $str);
	$str = str_replace("@N@", "\r\n", $str); // makes regexes easier @_@
	return $str;
}

$result = process($_REQUEST['something']);

?>

<textarea rows="20" cols="120"><?php echo htmlentities($result); ?></textarea>