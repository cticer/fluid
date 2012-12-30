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
	
	$salt = Security::createSalt();
	$password = Security::hashPass($salt,"deb24ian!");
	echo $salt."<br/>";
	echo $password."<br/>";
?>
	</body>
</html>