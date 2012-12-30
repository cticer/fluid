<?php
	if(!interface_exists('DatabaseObject'))
	{
		interface DatabaseObject
		{	
			public static function nextRowId();
			public function databaseWrite();
			public function databaseDelete();
			public static function exists($IN = array());
			public static function getByRowId($rowid);
		}
	}
?>