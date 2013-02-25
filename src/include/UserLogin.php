<?php
	if(!class_exists('UserLogin'))
	{
		class UserLogin extends SessionObject
		{	
			public function __construct($IN = array())
			{
				parent::__construct($IN);
			}
			
			public function init()
			{
				$this->set("expires",$this->expirationTimestamp());
			}
			
			public function verifyLogin()
			{
				/*
					If the login hasn't yet expired, then renew the expiration date and continue
				*/
				if(time() < $this->get("expires"))
				{
					if($this->get("authorized_key") && $GLOBALS['user'])
					{
						$user = $GLOBALS['user'];
						$email = false;
						if($this->get("type") == "EMAIL")
						{
							$email = true;
						}
						if($user->verifyAuthorization($user->get("email"),$this->get("authorized_key"),$email,$email))
						{
							$this->set("expires",$this->expirationTimestamp());
							return true;
						}
						else
						{
							//echo 'false';
						}
					}
				}
				/*
					If it has expired, look for $_SESSION['authorized_key'] and $_SESSION['uid'] and then attempt reauthorization
				*/
				else
				{
					if($this->get("authorized_key") && $this->get("uid"))
					{
						$user = User::getByRowId($this->get("uid"));
						$email = false;
						if($this->get("type") == "EMAIL")
						{
							$email = true;
						}
						if($user->verifyAuthorization($user->get("email"),$this->get("authorized_key"),$email,$email))
						{
							$this->set("expires",$this->expirationTimestamp());
							return true;
						}
					}
				}
				return false;
			}
			
			public function expirationTimestamp()
			{
				return time() + (60 * 5);//one hour expirations
			}
		}
	}
?>
