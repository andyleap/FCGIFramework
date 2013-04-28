<?php

class CGI_Request
{
	private $ob_started = false;
	private $open = true;
	public $STDIN = "";
	public $COOKIE = array();
	public $GET = array();
	public $POST = array();
	public $SESSION = array();

	function __construct()
	{
		$this->GET = &$_GET;
		$this->POST = &$_POST;
		$this->SERVER = &$_SERVER;
		$this->SESSION = &$_SESSION;
		$this->COOKIE = &$_COOKIE;
	}

	function Header($name, $value, $replace = true)
	{
		header($name . ': ' . $value, $replace);
	}

	function Session_Start()
	{
		session_start();
	}

	function Session_ID()
	{
		return session_id();
	}

	function SID()
	{
		return SID;
	}

	function Session_Destroy()
	{
		session_destroy();
	}

	function Session_Write_Close()
	{
		session_write_close();
	}

	function Header_Remove($name)
	{
		
	}

	function SetRawCookie($name, $value, $expire = 0, $path = '/', $domain = null, $secure = false, $httponly = false)
	{
		setrawcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}

	function SetCookie($name, $value, $expire = 0, $path = '/', $domain = null, $secure = false, $httponly = false)
	{
		setcookie($name, $value, $expire, $path, $domain, $secure, $httponly);
	}

	function Write($data)
	{
		echo $data;
	}

	public function Start_OB()
	{
		if(!$this->ob_started && $this->open)
		{
			ob_start(array($this, 'Write'), 4096);
			$this->ob_started = true;
		}
	}

	public function End_OB()
	{
		if($this->ob_started)
		{
			ob_end_flush();
			$this->ob_started = false;
		}
	}

	public function Close()
	{
		if($this->open)
		{
			$this->End_OB();
		}
	}

}