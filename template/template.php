<?php

class Template
{
	private $vars = array();
	private $template = null;
	private $framework;

	function __construct($template, $framework)
	{
		$this->template = $template;
		$this->framework = $framework;
	}

	public function __get($name)
	{
		return $this->vars[$name];
	}

	public function __set($name, $value)
	{
		$this->vars[$name] = $value;
	}

	public function __isset($name)
	{
		return isset($this->vars[$name]);
	}

	public function __unset($name)
	{
		unset($this->vars[$name]);
	}
	
	public function Extract($vars)
	{
		foreach($vars as $key => $value)
		{
			$this->vars[$key] = $value;
		}
	}

	public function Clean()
	{
		$this->vars = array();
	}

	public function Render($output_view_file = true)
	{
		if(!$output_view_file)
		{
			ob_start();
		}
		$this->template->__invoke($this->vars, $this->framework);
		if(!$output_view_file)
		{
			return ob_get_clean();
		}
	}
}