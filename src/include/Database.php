<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Database
 *
 * @author Collin
 */
if(!class_exists('Database'))
{
    class Database
    {
        private $host = 'localhost';
        private $db = 'crm';
        private $user = 'webuser';
        private $pass = 'chancey';
        private $conn = null;
        private $result = null;
		
        function  __construct()
        {
            $this->conn = mysql_connect($this->host, $this->user, $this->pass);
			//(mysql_connect($this->host, $this->user, $this->pass)) or die(mysql_error());
            mysql_select_db($this->db) or die(mysql_error());
        }
		
        function getDBName()
        {
            return $this->db;
        }
		
        function getConnection()
        {
            return $this->conn;
		}
		
        function close()
        {
            if($this->conn != null)
            {
                mysql_close($this->conn);
            }
        }
		
		function __destruct()
		{
			//$this->close();
		}
    }
}
?>
