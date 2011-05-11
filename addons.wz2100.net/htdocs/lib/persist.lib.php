<?php

/********************************************************
 ** Persistence library                                **
 ** Version 1.2 RC1 - Released 2010 Apr 18             **
 ** By Guangcong Luo <Zarel>                           **
 ** released under public domain / CC0                 **
 ** http://creativecommons.org/licenses/zero/1.0/      **
 ********************************************************
 *
 * Meant for PHP 4.0.4 and up. I've tried to make it work in
 * PHP 3, but I've never tested it, so use it in PHP 3 at
 * your own risk.
 *
 **[ Description ]***************************************
 *
 * This persistence library is used to save and load variables between
 * sessions. @include persist_load('var_name') will set $var_name to
 * its value the last time persist_save('var_name') was called, from
 * any file.
 *
 * Since you'll probably want to store more than one variable, it's
 * usually preferable to use $var_name as an array.
 *
 * Make sure PHP has permissions to edit var_name.inc.php
 * (If all else fails, CHMOD to 777).
 *
 * 'var_name' can be anything.
 *
 * Example code:
 * <?php
 *  include_once 'persist.lib.php';
 *  @include persist_load();
 *
 *  echo 'You are visitor #'.(++$_PERSIST['hitcounter']).'.';
 *  persist_save();
 * ?>
 *
 * More sophisticated example:
 * <?php
 *  include_once 'persist.lib.php';
 *  @include persist_load('includes/hitcounter.inc.php');
 *
 *  echo 'You are visitor #'.(++@$hitcounter[__FILE__]).'.';
 *  persist_save('hitcounter', 'includes/hitcounter.inc.php');
 * ?>
 *
 * If a script does not need to update the value of $_PERSIST, it can
 * simply include persist.inc.php instead of persist.lib.php .
 *
 * The first example is useful if you are using one persist variable
 * in one file, while the second is useful if you are using multiple
 * persist variables in multiple files.
 *
 **[ Function reference ]********************************
 *
 * persist_save([name], [path])
 *   Updates stored $_PERSIST.
 *   Both parameters are optional.
 *   [name] the name of the variable.
 *          Default: '_PERSIST'
 *   [path] the path (RELATIVE TO persist.lib.php) to the file storing
 *          the variable.
 *          Default: '[name].inc.php'
 *                   [name] will be lowercase, and a preceding '_' will
 *                   be removed.
 *
 * persist_load([name or path])
 *   Usage: @include persist_load()
 *   Should be used instead of including a file directly, since it
 *   will output paths relative to persist.lib.php.
 *   Either parameter is optional.
 *   [name] @include persist_load('var_name') will load a variable
 *          saved using persist_save('var_name')
 *   [path] @include persist_load('path.inc.php') will load a variable
 *          saved using persist_save('var_name', 'path.inc.php')
 *   blank  @include persist_load() will load $_PERSIST saved with
 *          persist_save()
 *
 ********************************************************/

// Used to load files saved with persist_save()
// Returns absolute path to file.
// Usage: @include_once persist_load('var_name');
function persist_load($path='')
{
	if (!$path) $path = '_PERSIST';
	if (strpos($path,'.') === FALSE)
		$path = strtolower(substr($path,0,1)==='_'?substr($path,1):$path).'.inc.php';
	if (substr($path,0,1)!='/')
		$path = substr(__FILE__,0,strrpos(__FILE__,'/')+1).$path;
	return $path;
}

// Updates $_PERSIST
// Returns true if successful, false if unsuccessful.
function persist_save($name='', $path='')
{
	if (!$name) $name = '_PERSIST';
	if (!$path) $path = strtolower(substr($name,0,1)==='_'?substr($name,1):$name).'.inc.php';;
	if (substr($path,0,1)!='/') $path = substr(__FILE__,0,strrpos(__FILE__,'/')+1).$path;
	// Open persist.inc.php and start editing it
	if (!is_writable($path)) return false;
	$res = @fopen($path,"w");
	if (!$res) return false;
	fwrite($res,"<?php\n\$".$name." = ".persist_export($GLOBALS[$name]).";\n?>");
	fclose($res);
	return true;
}

// Returns a PHP representation of a variable. Kind of like the opposite of eval().
// More robust form of var_export()
// e.g. persist_export("It's a string!") = "'It\'s a string!'"
//      persist_export(12) = "12";
function persist_export($var, $pre='')
{
	if (is_null($var)) // NULL
		return 'NULL';
	if (is_bool($var)) // Boolean
		return ($var?'TRUE':'FALSE');
	if (is_int($var) || is_float($var)) // Number
		return ''.$var;
	if (is_string($var)) // String
		return "'".persist_phpescape($var)."'";
	if (is_array($var)) //Array
	{
		if (empty($var)) return 'array()';
		$buf = "array(\n";
		$nleft = count($var); $i = -1; reset($var);
		// Recurse (Whee!)
		while (($cur = each($var)) !== FALSE)
		{
			$buf .= $pre."\t";
			if (!is_int($cur[0]))
			{
				$buf .= "'".persist_phpescape($cur[0])."' => ";
				$i = FALSE;
			}
			else if ($i===FALSE || $cur[0] != ++$i) 
			{
				$buf .= $cur[0].' => ';
				$i = ($i!==FALSE&&$cur[0]>$i?$cur[0]:FALSE);
			}
			$buf .= persist_export($cur[1], $pre."\t");
			if (--$nleft) $buf .= ',';
			$buf .= "\n";
		}
		return $buf.$pre.')';
	}
	return "unserialize('".persist_phpescape(serialize($var))."')";
}

function persist_phpescape($str)
{
	return strtr($str,array("\\" => "\\\\", "'" => "\\'"));
}

?>