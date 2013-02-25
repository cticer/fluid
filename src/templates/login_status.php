<?php
	$userLogin = $GLOBALS['login'];
	if($userLogin->verifyLogin())
	{
		echo "success";
	}
	else
	{
		if(isset($_GET['aid']))
		{
			$authorization = Authorization::getByRowId($_GET['aid']);

			if($authorization)
			{
				$user = User::getByRowId($authorization->get("user_id"));
				if($user)
				{
					$GLOBALS['user']=$user;
					$_SESSION['uid']=$user->get("row_id");
					$_SESSION['type']="EMAIL";
					$_SESSION['authorized_key']=$authorization->get("authorized_key");
					$userLogin->init();
					$GLOBALS['login'] = $userLogin;
					header("Location: ".$_SERVER['PHP_SELF']);
				}
			}
		}
		else
		{
			$account_email = null;
			if(isset($_POST['account_email']))
			{
				$account_email = $_POST['account_email'];
			}
			
			$preauthorized_email = null;
			if(isset($_POST['preauthorized_email']))
			{
				$preauthorized_email = $_POST['preauthorized_email'];
			}
			
			$preauthorized_password = null;
			if(isset($_POST['preauthorized_password']))
			{
				$preauthorized_password = $_POST['preauthorized_password'];
			}

			if($account_email && ($preauthorized_password || $preauthorized_email))
			{
				$user = User::getOnExists(array("email"=>$account_email));
				if($preauthorized_password && $user)
				{
					if($user->verifyAuthorization($user->get("email"),$preauthorized_password,false))
					{
						$GLOBALS['user']=$user;
						$_SESSION['uid']=$user->get("row_id");
						$_SESSION['type']="PASSWORD";
						$_SESSION['authorized_key']=$preauthorized_password;
						$userLogin->init();
						$GLOBALS['login'] = $userLogin;
						header("Location: ".$_SERVER['PHP_SELF']);	
					}
				}
				else if($preauthorized_email)
				{
					$authorization = Authorization::getOnExists(array("email" => ($user->get("email")),"authorization_type" => "EMAIL"));
					if($authorization)
					{
						$to = $user->get("email");
						$subject = "Hi!";
						$body = "Hi,\n\nhttp://codexplode.com/fluid/?aid=".$authorization->get("row_id");
						$headers = "From: admin@codexplode.com\r\n"."X-Mailer: php";
						if(mail($to, $subject, $body, $headers)) 
						{
							echo("<p>Message successfully sent!</p>");
						} 
						else 
						{
							echo("<p>Message delivery failed...</p>");
						}
					}
				}
			}
		}
		//die();
?>
<div id="login-status">
<?php	
		//if the user hasn't entered an email, then prompt for one
		if(!$user->get("email") && !$account_email )
		{
			echo "Welcome. To get started, enter an email address: <form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\"><input type=\"text\" name=\"account_email\" id=\"account_email\"><input type=\"submit\"/></form>";
		}
		else
		{
			/*
				if an email has been entered, check it to see if it corresponds to a user
			*/
			if($account_email)
			{
				$GLOBALS['user'] = User::getOnExists(array("email"=>$account_email));
				$user = $GLOBALS['user'];
				
				/*
					TODO: After email entered, verify user with email exists
					Exists - prompt for password or an email address to approve authorization
					No Exists - insert into database with random password
				*/
				if($GLOBALS['user'])
				{
					$passwordExists = Authorization::exists(array("user_id"=>($user->get("row_id")),"authorization_type"=>"PASSWORD"));
					$emailExists = Authorization::exists(array("user_id"=>$user->get("row_id"),"authorization_type"=>"EMAIL"));
					
					/*
						Enter a password or an email address to login
					*/
					if($passwordExists && $emailExists)
					{
						echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\"><label>Email</label><input type=\"text\" name=\"preauthorized_email\" id=\"preauthorized_email\"><br/><label>Password</label><input type=\"text\" name=\"preauthorized_password\" id=\"preauthorized_password\"><input type=\"hidden\" name=\"account_email\" id=\"account_email\" value=\"".$account_email."\"/><input type=\"submit\"/></form>";
					}
					else if($passwordExists)
					{
						echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\"><label>Password</label><input type=\"text\" name=\"preauthorized_password\" id=\"preauthorized_password\"><input type=\"hidden\" name=\"account_email\" id=\"account_email\" value=\"".$account_email."\"/><input type=\"submit\"/></form>";
					}
					else if($emailExists)
					{
						echo "<form action=\"".$_SERVER['PHP_SELF']."\" method=\"POST\"><label>Email</label><input type=\"text\" name=\"preauthorized_email\" id=\"preauthorized_email\"><input type=\"hidden\" name=\"account_email\" id=\"account_email\" value=\"".$account_email."\"/></form>";
					}
					/*
						They have neither set a password nor verified an email address
					*/
					else
					{
						echo "You have not verified any email address and have not set a password. You must login using the link email during verification to verify your account and login.";
					}
				}
				else
				{
					$GLOBALS['user'] = new User();
					$GLOBALS['user']->set("email",$account_email);
					$GLOBALS['user']->databaseWrite();
					//$_SESSION['uid'] = $GLOBALS['user']->get("row_id");
				}
			}
		}
		//verify that an email is authorized on an account
		if($user->verifyAuthorization($user->get("email"),true))
		{
			echo "Hello ".$user->get("first_name").", the email address you entered is authorized for login. <form action=\"".$_SERVER['PHP_SELF']."\"><input type=\"submit\"/></form> or enter a valid password <form action=\"#\"><input type=\"text\"/><input type=\"submit\"/></form>";
		}
	}
?>