<?php
	if(!class_exists('Authorization'))
	{
		class Authorization extends Object implements DatabaseObject
		{	
			public function __construct($IN = array())
			{
				$IN['salt'] = Security::createSalt();//create a salt used for encrypting the authorized key
				$authorized_key = $IN['authorized_key'];
				$IN['authorized_key'] = Security::hashPass($IN['salt'],$authorized_key);
				parent::__construct($IN);
			}
			
			public static function nextRowId()
			{
				$sql = "SELECT * FROM authorizations WHERE row_id = '1';";
				$query = new Query($GLOBALS['gConn'],$sql);
				
				if($query->hasNextRow())
				{
					$sql = "SELECT MIN(t1.row_id + 1) AS nextID FROM authorizations t1 LEFT JOIN authorizations t2 ON t1.row_id + 1 = t2.row_id WHERE t2.row_id IS NULL;";
					$query = new Query($GLOBALS['gConn'],$sql);
					if($query->hasNextRow())
					{
						$query->nextRow();
						$nextId = $query->get("nextID");
						return $nextId;
					}
					else
					{
						$sql = "SELECT MAX(row_id + 1) AS nextID FROM authorizations;";
						$query = new Query($GLOBALS['gConn'],$sql);
						if($query->hasNextRow())//oh shit if this is never not the case lol
						{
							$query->nextRow();
							$nextId = $query->get("nextID");
							return $nextId;
						}
					}
				}
				else
				{
					$nextId = 1;
					return $nextId;
				}
			}
		
			public function databaseWrite()
			{
				//insert empty user into database and assign autoincremented id to row_id
				if(!$this->get("row_id") || !$this->exists(array("row_id" => $this->get("row_id"))))
				{
					$this->set("row_id",self::nextRowId());
					$sql = "INSERT INTO authorizations (row_id,authorization_given,authorization_exp,authorization_type) VALUES ('".$this->get("row_id")."',NOW(),TIMESTAMPADD(DAY, 14, NOW()),'PASSWORD');";
					new Query($GLOBALS['gConn'],$sql);
				}
				$temp = $this->data;
				foreach($temp as $key => $value)
				{
					if(strtolower($key) !== "row_id")
					{
						$sql = "UPDATE authorizations SET ".$key."='".$value."' WHERE row_id='".$this->get("row_id")."';";
						new Query($GLOBALS['gConn'],$sql);
					}
				}
			}
			
			public function databaseDelete()
			{
				$sql = "DELETE FROM authorizations WHERE row_id='".$this->get("row_id")."';";
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
				$sql = "SELECT * FROM authorizations WHERE ".$whereSql;
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
				$sql = "SELECT * FROM authorizations WHERE ".$whereSql;
				$query = new Query($GLOBALS['gConn'],$sql);
				if($query->getNumRows() > 0)
				{
					return new User($query->nextRow());
				}
				return false;
			}
			
			public static function getByRowId($row_id)
			{
				$sql = "SELECT * FROM authorizations WHERE row_id='".$row_id."';";
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
