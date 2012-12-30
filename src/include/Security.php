<?php
if(!class_exists('Security'))
{
	//require_once '/Header.php';
	class Security
	{
		public static function escapeAllInput($IN)
		{
			$out = array();
			foreach($IN as $key => $var)
			{
				if(is_array($var))
				{
					$temp = array();
					foreach($var as $key2 => $var2)
					{
						if(get_magic_quotes_gpc())
						{
							$var2 = stripslashes($var2);
						}
						$temp[$key2] = mysql_real_escape_string($var2, $GLOBALS['gConn']);
					}
					$out[$key] = $temp;
				}
				else
				{
					if(get_magic_quotes_gpc())
					{
						$var = stripslashes($var);
					}
					$out[$key] = mysql_real_escape_string($var, $GLOBALS['gConn']);
				}
			}
			return $out;
		}

		/*
		 * use this to create a 3 char random string
		 *
		 * this will be stored in the users table
		 * in the salt column
		 */
		public static function createSalt()
		{
			$string = md5(uniqid(rand(), true));
			return substr($string, 0, 3);
		}

		/*
		 * pass salt and the password that the user
		 * input to convert it to a hash sequence.
		 *
		 * this will be stored in the users table
		 * in the password column.
		 *
		 * now when the user inputs a password to
		 * login, you would also call this method
		 * with the value stored in salt. and if the
		 * return value is equal to the hash sequence
		 * stored in the password column of users, then
		 * the user successfully logged in
		 */
		public static function hashPass($salt, $password)
		{
			$hash = sha1($password);
			$hash = sha1($salt.$hash);
			return $hash;
		}
		
		public static function verificationCode()
		{
			return sha1(uniqid(rand(), true));
		}
		
		/*
			returns an array with key and secret
		*/
		public static function consumerKeySecret()
		{
			$fp = fopen('/dev/urandom','rb');
			$entropy_key = fread($fp,32);
			$entropy_secret = fread($fp,32);
			fclose($fp);
			$entropy_key .= uniqid(mt_rand(),true);
			$entropy_secret .= uniqid(mt_rand(),true);
			$key = sha1($entropy_key);
			$secret = sha1($entropy_secret);
			$returnarray = array('consumerkey'=>$key,'consumersecret'=>$secret);
			return $returnarray;
		}
		
		public static function createRandomPassword($length) 
		{ 
			$chars = "abcdefghijkmnopqrstuvwxyz023456789"; 
			srand((double)microtime()*1000000); 
			$i = 0; 
			$pass = '' ; 
			
			while($i < $length) 
			{ 
				$num = rand() % 33; 
				$tmp = substr($chars, $num, 1); 
				$pass = $pass . $tmp; 
				$i++; 
			} 
			return $pass; 
		} 
		
		/*
			A test function to see if a password validates using rules we define such as length
		*/
		public static function passwordTest($pass)
		{
			if(strlen($pass) >= MIN_PW_LENGTH)
			{
				return true;
			}
			return false;
		}
	}
}
?>
