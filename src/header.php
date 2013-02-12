<?php
	session_start();
	
	if(!defined('INC_PATH'))
		define('INC_PATH','/home/content/52/10278052/html/uua/');
	
	//$_SESSION['uid'] = 2;
	//unset($_SESSION['uid']);
	
	/*
		If this is set to true, a user wasn't found to be logged in.
		
		Prompt them for their email address and do one of three things:
		
		(email exists) = retrieve user from database (prompt for account password + allow them to try different email address)
		(email doesn't exist) = confirm email and create user account (prompt for option of trying differnt email address)
		(no email entered) = allow them to continue with site as if logged in ... create temp user account. prompt them to complete email address entry to keep login
	*/
	$GLOBALS['email_prompt'] = true;
	$GLOBALS['pw_prompt'] = false;
	
	foreach( glob("include/pre/*.php") as $filename)
	{
		include $filename;
	}

	foreach( glob("include/*.php") as $filename)
	{
		include $filename;
	}
	
	if(!isset($GLOBALS['gConn']))
	{
		$database = new Database();
		$GLOBALS['database'] = $database;
		$GLOBALS['gConn'] = $database->getConnection();
	}
	
	$_SESSION['uid'] = 3;
	$GLOBALS['user'] = new User();
	if(isset($_SESSION['uid']))
	{
		$GLOBALS['user'] = User::getByRowId($_SESSION['uid']);
		$GLOBALS['email_prompt'] = false;
		$GLOBALS['pw_promt'] = false;
	}
	$GLOBALS['user']->databaseWrite();
	$_SESSION['uid'] = $GLOBALS['user']->get("row_id");
	
	$_GET = Security::escapeAllInput($_GET);
	$_POST = Security::escapeAllInput($_POST);
?>