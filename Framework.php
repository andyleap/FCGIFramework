<?php

define('DS', DIRECTORY_SEPARATOR);
$dirs = array('.' . DS . 'framework');
$high_pri = array();
$high_pri[] = '.' . DS . 'framework' . DS . 'model' . DS . 'phpAR' . DS . 'Singleton.php';
$high_pri[] = '.' . DS . 'framework' . DS . 'model' . DS . 'phpAR';
$dirs = array_merge($high_pri, $dirs);
$scanned = array();
while (($dir = array_shift($dirs)) !== NULL)
{
	if(!is_dir($dir))
	{
		if(preg_match('/^.+php$/', $dir))
		{
			include_once $dir;
		}
	}
	if(($frameworkdir = @opendir($dir)) !== false) {
		while (($file = readdir($frameworkdir)) !== false) 
		{
			if($file == '.' || $file == '..')
			{
				continue;
			}
			if(is_dir($dir . DS . $file) && !in_array($dir . DS . $file, $scanned))
			{
				array_push($dirs, $dir . DS . $file);
				$scanned[] = $dir . DS . $file;
			}
			else
			{
				if(preg_match('/^.+php$/', $file))
				{
					include_once $dir . DS . $file;
				}
			}
		}
		closedir($frameworkdir);	
	}
}