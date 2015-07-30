<?php
session_start();
if(isset($_SESSION['user'])){
	$user=$_SESSION['user'];
}else{
	header('location:signin.php');
}
?>
<html>
<head>
	<title>Dashboard</title>
</head>
<body>
	<p>Welcome to the Dashboard</p>
</body>
</html>