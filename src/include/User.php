<?php
	if(!class_exists('User'))
	{
		class User extends Object implements DatabaseObject
		{	
			public function __construct($IN = array())
			{
				parent::__construct($IN);
			}
			
			public static function nextRowId()
			{
				$sql = "SELECT MIN(t1.row_id + 1) AS nextID FROM users t1 LEFT JOIN users t2 ON t1.row_id + 1 = t2.row_id WHERE t2.row_id IS NULL;";
				$query = new Query($GLOBALS['gConn'],$sql);
				if($query->hasNextRow())
				{
					$query->nextRow();
					$nextId = $query->get("nextID");
					return $nextId;
				}
				else
				{
					$sql = "SELECT MAX(row_id + 1) AS nextID FROM users;";
					$query = new Query($GLOBALS['gConn'],$sql);
					if($query->hasNextRow())//oh shit if this is never not the case lol
					{
						$query->nextRow();
						$nextId = $query->get("nextID");
						return $nextId;
					}
				}
			}
		
			public function databaseWrite()
			{
				//insert empty user into database and assign autoincremented id to row_id
				if(!$this->get("row_id") || !$this->exists(array("row_id" => $this->get("row_id"))))
				{
					$this->set("row_id",self::nextRowId());
					$sql = "INSERT INTO users (row_id) VALUES ('".$this->get("row_id")."');";
					new Query($GLOBALS['gConn'],$sql);
					//$this->set("row_id",mysql_insert_id());
				}
				$temp = $this->data;
				foreach($temp as $key => $value)
				{
					if(strtolower($key) !== "row_id")
					{
						$sql = "UPDATE users SET ".$key."='".$value."' WHERE row_id='".$this->get("row_id")."';";
						new Query($GLOBALS['gConn'],$sql);
					}
				}
			}
			
			public function databaseDelete()
			{
				$sql = "DELETE FROM users WHERE row_id='".$this->get("row_id")."';";
				echo $sql;
				new Query($GLOBALS['gConn'],$sql);
			}
			
			/*
				Searches for specified user in database
				
				IN is an array of keys => values
				keys being one of the mysql columns
				values being an exact search string (case sensitive)
			*/
			public static function exists($IN = array())
			{
				if(count($IN) < 1)
				{
					return false;
				}
				$whereSql = "";
				$firstRun = true;
				foreach($IN as $key => $value)
				{
					if(!$firstRun)
					{
						$whereSql = $whereSql." AND ";
					}
					else
					{
						$firstRun = false;
					}
					$whereSql = $whereSql.$key."='".$value."'";
				}
				$sql = "SELECT * FROM users WHERE ".$whereSql;
				$query = new Query($GLOBALS['gConn'],$sql);
				if($query->getNumRows() > 0)
				{
					return true;
				}
				return false;
			}
			
			/*
				same as above but returns the user if found (first result only)
			*/
			public static function getOnExists($IN = array())
			{
				if(count($IN) < 1)
				{
					return false;
				}
				$whereSql = "";
				$firstRun = true;
				foreach($IN as $key => $value)
				{
					if(!$firstRun)
					{
						$whereSql = $whereSql." AND ";
					}
					else
					{
						$firstRun = false;
					}
					$whereSql = $whereSql.$key."='".$value."'";
				}
				$sql = "SELECT * FROM users WHERE ".$whereSql;
				$query = new Query($GLOBALS['gConn'],$sql);
				if($query->getNumRows() > 0)
				{
					return new User($query->nextRow());
				}
				return false;
			}
			
			public static function getByRowId($row_id)
			{
				$sql = "SELECT * FROM users WHERE row_id='".$row_id."';";
				$query = new Query($GLOBALS['gConn'],$sql);
				if($query->hasNextRow())
				{
					return new User($query->nextRow());
				}
				return false;
			}
		}
	}
?>
