<div id="login-status">
<?php
	$email = null;
	//$email = $_POST['email'];
	//$authorized_key = $_POST['authorized_key'];

	
	$user = $GLOBALS['user'];
	
	//if the user hasn't entered an email, then prompt for one
	if(!$user->get("email"))
	{
		echo "no email set";
	}
	else
	{
		//verify that an email is authorized on an account
		if($user->verifyAuthorization($user->get("email"),true))
		{
			echo "Hello ".$user->get("first_name").", the email address you entered is authorized for login. <form action=\"#\"><input type=\"submit\"/></form> or enter a valid password <form action=\"#\"><input type=\"text\"/><input type=\"submit\"/></form>";
		}
	}
?>