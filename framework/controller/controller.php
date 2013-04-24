<?php

abstract class Controller {
	public $request;
	public $matches;
	public $framework;
	public $templates;
	public $cache;
	public $methodargs;
	
	function __construct($framework)
	{
		$this->framework = $framework;
		$this->templates = $framework->TemplateCache;
		$this->cache = $framework->Cache;
		if(method_exists($this, 'Init'))
		{
			$this->Init();
		}
		$classinfo = new ReflectionClass($this);
		foreach($classinfo->getMethods(ReflectionMethod::IS_PUBLIC) as $method)
		{
			if(!in_array($method->getName(), array('__construct', 'Init', 'ControllerPrep', 'ControllerClean')))
			{
				$this->methodargs[$method->getName()] = array();
				foreach($method->getParameters() as $parameter)
				{
					$this->methodargs[$method->getName()][] = $parameter->getName();
				}
			}
		}
	}
	
	public function ControllerPrep($request, $matches)
	{
		$this->request = $request;
		$this->matches = $matches;
	}
	
	public function ControllerClean()
	{
		$this->request = null;
		$this->matches = null;
	}
}
