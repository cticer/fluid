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
	
	$auth = new Authorization(array("user_id"=>"3","authorized_key"=>"test1"));
	$auth->databaseWrite();
	echo $user->verifyAuthorization("deb24IAN!");
?>
	</body>
</html>