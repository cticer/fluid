<?php
	if(!class_exists('SessionObject'))
	{
		abstract class SessionObject
		{	
			public function __construct($IN = array())
			{
				//$this->data = $IN;
			}
			
			public function set($key, $value)
			{
				$_SESSION[$key] = $value;
			}
			
			public function get($key)
			{
				if(isset($_SESSION[$key]))
				{
					return $_SESSION[$key];
				}
				return null;
			}
		}
	}
?>