<?php
	include "header.php";
?>
<html> 
	<head>
	
	</head>
	<link href="stylesheet.css" rel="stylesheet" type="text/css"/>
	<body>
<?php
	include "templates/login_status.php";
	
	$randPassword = Security::createRandomPassword(40);
	echo "Randomly Generated Password: ".$randPassword."</br>";
	$auth = new Authorization(array("user_id"=>"3","authorized_key"=>$randPassword,"authorization_type"=>"EMAIL","email"=>"cticer2491@gmail.com"));
	$auth->databaseWrite();
	//die();
	if($user->verifyAuthorization("cticer2491@gmail.com",$randPassword,true))
	{
		echo 'Authorized';
	}
?>
	</body>
</html>