<?php

class Cache implements CoreInit
{
	private $framework;
	private $cache = array();
	private $cacheexpire = array();
	
	function __construct($framework)
	{
		$this->framework = $framework;
	}
	
	public function Init()
	{
	}
	
	public function Get($key, $item, callable $generate, $lifetime = 60)
	{
		if(!array_key_exists($key, $this->cache))
		{
			$this->cache[$key] = array();
			$this->cacheexpire[$key] = array();
		}
		if(array_key_exists($item, $this->cache[$key]))
		{
			if($this->cacheexpire[$key][$item] > time())
			{
				return $this->cache[$key][$item];
			}
		}
		$this->cache[$key][$item] = $generate();
		$this->cacheexpire[$key][$item] = time() + $lifetime;
		return $this->cache[$key][$item];
	}
	
	public function Set($key, $item, $value, $lifetime = 60)
	{
		if(!array_key_exists($key, $this->cache))
		{
			$this->cache[$key] = array();
			$this->cacheexpire[$key] = array();
		}
		$this->cache[$key][$item] = $value;
		$this->cacheexpire[$key][$item] = time() + $lifetime;
	}
	
	public function Clear($key, $item)
	{
		if(!array_key_exists($key, $this->cache))
		{
			$this->cache[$key] = array();
			$this->cacheexpire[$key] = array();
		}
		unset($this->cache[$key][$item]);
		unset($this->cacheexpire[$key][$item]);
	}
}
