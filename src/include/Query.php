<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of Query
 *
 * @author Collin
 */
if(!class_exists('Query'))
{
    class Query
    {
        private $result = null;
        private $conn = null;
        private $sql = null;
        private $fields = array();
        private $fieldNames = array();
        private $currRow = null;
        private $index = 0;
        private $rowindex = 0;
        private $numrows = 0;
        private $numcols = 0;
		private $assoc = true;//whether to return just associative array or both (associative and number)

        function __construct($conn, $sql='',$assoc = true)
        {
			$this->assoc = $assoc;
            $this->conn = $conn;
            $this->sql = $sql;
            if($sql !== '' && is_resource($conn))
            {
                $this->result = mysql_query($sql,$conn) or die(mysql_error());
                //if($this->result != 1 || $this->result != 0 || $this->result != null)
				if(is_resource($this->result))
                {
                    $this->numrows = mysql_num_rows($this->result);
                    $this->numcols = mysql_num_fields($this->result);
                    $this->establishFields();
                }
            }
        }

        function add($name, $obj)
        {
            $this->fieldNames[count($this->fieldNames)] = $name;
            $this->fields[count($this->fields)] = $obj;
        }

        public static function getNextUniqId($type)
        {
            $query = new Query($GLOBALS['gConn'], 'INSERT INTO uniqids(type) VALUES(\'' . $type . '\');');
            return mysql_insert_id($GLOBALS['gConn']);
        }
		
		function getType($id)
		{
			$query = new Query($this->conn, 'SELECT type FROM uniqids WHERE uniqid=\''.$id.'\';');
			if($query->hasNextRow())
			{
				$query->nextRow();
				return $query->next();
			}
			else
			{
				return false;
			}
		}

        function executePreparedStatement($action, $post='')
        {
            $sql = $action . '(';

            if(count($this->fieldNames) > 0)
                $sql = $sql . $this->fieldNames[0];

            for($i = 1; $i < count($this->fieldNames); $i++)
            {
                $sql = $sql . ', ' . $this->fieldNames[$i];
            }

            $sql = $sql . ') VALUES(';

            if(count($this->fields) > 0)
                $sql = $sql . '\'' . $this->fields[0] . '\'';

            for($i = 1; $i < count($this->fields); $i++)
            {
                $sql = $sql . ', \'' . $this->fields[$i] . '\'';
            }

            $sql = $sql . ') ' . $post .';';
            return mysql_query($sql, $this->conn);
        }

        function next()
        {
            if($this->currRow != null)
            {
                if($this->index < $this->numcols)
                {
                    return $this->currRow[$this->index++];
                }
                else
                {
                    return null;
                }
            }
            else
            {
                return null;
            }
        }
		
		function get($key)
		{
			if($this->currRow != null)
            {                    
				return $this->currRow[$key];
            }
            else
            {
                return null;
            }
		}

        function hasNext()
        {
            if($this->currRow != null && $this->numcols > $this->index)
            {
                return true;
            }
            return false;
        }

        function nextRow()
        {
            if($this->currRow = ($this->assoc ? mysql_fetch_array($this->result,MYSQL_ASSOC) : mysql_fetch_array($this->result,MYSQL_BOTH)))
            {
                $this->index = 0;
                $this->rowindex++;
                return $this->currRow;
            }
            return null;
        }

        function hasNextRow()
        {
            if($this->rowindex < $this->numrows)
            {
                return true;
            }
            return false;
        }
		
		function getCurrRow()
		{
			if($this->currRow != null)
			{
				return $this->currRow;
			}
		}
		
		function getNumRows()
		{
			return $this->numrows;
		}
		
        function establishFields()
        {
            $i = 0;
            while ($i < mysql_num_fields($this->result))
            {
                $meta = mysql_fetch_field($this->result, $i);
                if (!$meta)
                {

                }
                else
                {
                    $this->fields[$i] = $meta->name;
                }
                $i++;
            }
        }

        /*
         * A debugging function that will print a list of all
         * columns that are available from this query.
         */
        function showFields()
        {
            echo '|---------------------------------------|<br/>';
            for($i = 0; $i < count($this->fields); $i++)
            {
                echo $this->fields[$i].'<br/>';
            }
            echo '|---------------------------------------|<br/>';
        }
    }
}
?>
