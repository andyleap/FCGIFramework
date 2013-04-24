<?php

class TemplateCache implements ArrayAccess, CoreInit
{
	public $templates = array();
	private $framework;

	function __construct($framework)
	{
		$this->framework = $framework;
		$templateDir = $this->framework->Options['MainDir'] . DS . $this->framework->Options['TemplateDir'];
		$dirs = array($templateDir);
		$templates = array();
		$compiletemplates = array();
		while(($dir = array_shift($dirs)) !== NULL)
		{
			if(($dirHandle = @opendir($dir)) !== false)
			{
				while(($file = readdir($dirHandle)) !== false)
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
							$templates[] = substr($dir . DS . $file, strlen($templateDir) + 1, -4);
						}
						if(preg_match('/^.+templ$/', $file))
						{
							$compiletemplates[] = substr($dir . DS . $file, strlen($templateDir) + 1, -6);
						}
					}
				}
				closedir($dirHandle);
			}
		}
		foreach($templates as $template)
		{
			$this->templates[$template] = function($vars, $framework) use ($template, $templateDir)
					{
						extract($vars);
						include $templateDir . DS . $template . '.php';
					};
		}
		
		foreach($compiletemplates as $template)
		{
			$this->CompileTemplate($templateDir, $template);
			$this->templates[$template] = (include $this->framework->Options['MainDir'] . DS . $this->framework->Options['CompiledTemplateDir'] . DS . $template . '.templ.php');
		}
	}
	
	private function CompileTemplate($templateDir, $template)
	{
		$output = '<?php return function($vars, $framework) { extract($vars); ?>';
		$output .= $this->ProcessTemplate($templateDir . DS . $template . '.templ', 'var');
		$output .= '<?php } ?>';
		if(!file_exists(dirname($this->framework->Options['MainDir'] . DS . $this->framework->Options['CompiledTemplateDir'] . DS . $template)))
		{
			mkdir(dirname($this->framework->Options['MainDir'] . DS . $this->framework->Options['CompiledTemplateDir'] . DS . $template), 0755, true);
		}
		file_put_contents($this->framework->Options['MainDir'] . DS . $this->framework->Options['CompiledTemplateDir'] . DS . $template . '.templ.php', $output);
	}
	
	private function ProcessTemplate($template)
	{
		$data = file_get_contents($template);
		$output = '';
		while(strlen($data) > 0)
		{
			$matches = array();
			preg_match('#^([^{]*)(?:{([^}]*)}(.*))?$#s', $data, $matches);
			$output .= $matches[1];
			if(count($matches) > 2 && strlen($matches[2]) > 0)
			{
				preg_match('#^([^\s]+)(?:\s(.*))?$#s', $matches[2], $commandMatches);
				switch(strtolower($commandMatches[1]))
				{
					case 'if':
						$output .= '<?php if(' . $commandMatches[2] . ') { ?>';
						break;
					case 'else':
						$output .= '<?php } else { ?>';
						break;
					case '/if':
						$output .= '<?php } ?>';
						break;
					case 'include':
						$args = self::ArgParser($commandMatches[2]);
						if($this->FilePather(dirname($template) . DS . $args['file'] . '.templ'))
						{
							if(isset($args['values']))
							{
								$output .= '<?php $framework->TemplateCache->templates[\'' . substr($this->FilePather(dirname($template) . DS . $args['file'] . '.templ'), 0, -6) . '\']($' . $args['values'] . ', $framework); ?>';
							}
							else
							{
								$output .= $this->ProcessTemplate(dirname($template) . DS . $args['file'] . '.templ');
							}
						}
						break;
					case 'literal':
						preg_match('#^((?:[^{]|{(?!/literal}))*){(/literal)}(.*)$#s', $matches[3], $matches);
						$output .= $matches[1];
						break;
					case 'foreach':
						$args = self::ArgParser($commandMatches[2]);
						$output .= '<?php foreach($' . $args['from'] . ' as $' . $args['item'] . ') { ?>';
						break;
					case '/foreach':
						$output .= '<?php } ?>';
					default:
						if(substr($commandMatches[0], 0, 1) === '=')
						{
							$output .= '<?= $' . substr($commandMatches[0], 1) . ' ?>';
						}
						break;
				}
			}
			if(count($matches) > 3)
			{
				$data = $matches[3];
			}
			else
			{
				$data = '';
			}
		}
		return $output;
	}
	
	private static function ArgParser($text)
	{
		$args = array();
		preg_match_all('#(?<name>[^\s=]+)=(?:(["\'])|)(?<value>(?(2)(?:[^\2]|(?<=\\\\)\2)|\S)+)(?(2)(?<!\\\\)\2|)#', $text, $argMatches, PREG_SET_ORDER);
		array_walk($argMatches, function($arg) use(&$args)
		{
			$args[$arg['name']] = $arg['value'];
		});
		return $args;
	}
	
	private function FilePather($file)
	{
		$realfile = realpath($file);
		$realbase = realpath($this->framework->Options['MainDir'] . DS . $this->framework->Options['TemplateDir']);
		if(substr($realfile, 0, strlen($realbase)) === $realbase)
		{
			return substr($realfile, strlen($realbase) + 1);
		}
		return false;
	}
	
	public function Init()
	{
		
	}

	public function offsetExists($offset)
	{
		return isset($this->templates[$offset]);
	}

	public function offsetGet($offset)
	{
		if(isset($this->templates[$offset]))
		{
			return new Template($this->templates[$offset], $this->framework);
		}
		else
		{
			throw new Exception('No such template');
		}
	}

	public function offsetSet($offset, $value)
	{
		
	}

	public function offsetUnset($offset)
	{
		
	}

}