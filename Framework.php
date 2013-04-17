<?php

define('DS', DIRECTORY_SEPARATOR);
$dirs = array('.' . DS . 'framework');
while (($dir = array_shift($dirs)) !== NULL)
{
	if(($frameworkdir = @opendir($dir)) !== false) {
		while (($file = readdir($frameworkdir)) !== false) 
		{
			if($file == '.' || $file == '..')
			{
				continue;
			}
			if(is_dir($dir . DS . $file))
			{
				array_push($dirs, $dir . DS . $file);
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