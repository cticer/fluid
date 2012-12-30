<div id="login-status">
<?php
	$user = $GLOBALS['user'];
	
	if(!$user->get("email"))
	{
		if(isset($_POST['email']))
		{
			$userWithEmail = User::getOnExists(array("email"=>$_POST['email']));
			if($userWithEmail)//if a user exists with the email provided
			{
				$delUser = $GLOBALS['user'];
				echo $delUser->get("row_id");
				$GLOBALS['user'] = $userWithEmail;
				if(isset($_POST['password']) && Security::hashPass($GLOBALS['user']->get("salt"),$_POST['password']) == $GLOBALS['user']->get("password"))
				{
					$delUser->databaseDelete();
					$_SESSION['uid'] = $GLOBALS['user']->get("row_id");
					//$GLOBALS['user'] now contains a logged in user
				}
				else
				{
	?>
	<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST"><label for="password">Password</label><input type="password" name="password" id="password"/><input type="hidden" name="email" id="email" value="<?php echo $_POST['email'];?>"/><input type="submit"/></form>
	<?php
				}
				echo $GLOBALS['user']->get("first_name");
				
			}
			else
			{
				//they just supplied an email.
				$GLOBALS['user']->set("email",$_POST['email']);
				
				/*
					GENERATE A RANDOM PASSWORD, SALT AND HASH
					STORE IN DATABASE ACCORDINGLY AND THEN EMAIL
					THE DETAILS TO THE USER
				*/
			}
			$GLOBALS['user']->databaseWrite();
			$GLOBALS['email_prompt'] = false;
		}
		else
		{
?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="POST"><label for="email">Email</label><input type="text" name="email" id="email"/><input type="submit"/></form>
<?php		
		}
	}
	else
	{
		//user already logged in
?>
<h2>Hello, <?php echo $user->get("first_name");?></h2>
<?php
	}
?>
</div>