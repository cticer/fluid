<?php
	include "header.php";
?>
<html> 
	<head>
	
	</head>
	<link href="stylesheet.css" rel="stylesheet" type="text/css"/>
	<body>
<?php
	$dirPrefix = "/home/content/c/o/d/code7128/html/fluid";
	include $dirPrefix."/templates/login_status.php";
	$ver = Verification::getOnExists(array("verification_name"=>"birth month"));
	$randPassword = Security::createRandomPassword(40);
	echo "Randomly Generated Password: ".$randPassword."</br>";
	$auth = new Authorization(array("user_id"=>"3","authorized_key"=>$randPassword,"authorization_type"=>"EMAIL","email"=>"cticer2491@gmail.com","verification_id"=>$ver->get("row_id"),"verification_answer"=>1));
	$auth->databaseWrite();
	//die();
	if($user->verifyAuthorization("cticer2491@gmail.com",$randPassword,true))
	{
		echo 'Authorized';
	}
?>
	</body>
</html>