<?php

class TestController extends Controller
{
	public $helloTemplate;
	function Init()
	{
		$this->helloTemplate = $this->templates['hello'];
	}
	
	function Test($name = null)
	{
		$this->helloTemplate->name = $name;
		$this->helloTemplate->Render();
	}
}