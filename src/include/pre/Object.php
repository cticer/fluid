<?php
	if(!class_exists('Object'))
	{
		abstract class Object
		{	
			protected $data = null;//array containing necessary data passed to object in constructor
		
			public function __construct($IN = array())
			{
				$this->data = $IN;
			}
			
			public function set($key, $value)
			{
				if(!$this->data)
				{
					$this->data = array();
				}
				$temp = array_replace($this->data, array($key => $value));
				$this->data = $temp;
			}
			
			public function get($key)
			{
				if($this->data)
				{
					if(array_key_exists($key,$this->data))
					{
						$temparray = $this->data;
						return $temparray[$key];
					}
					else
					{
						return null;
					}
				}
			}
		}
	}
?>