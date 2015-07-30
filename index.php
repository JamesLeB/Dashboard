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
	<table>
		<tr>
			<td>Create EDI 270 request</td>
			<td><button>ETL-1</button></td>
		</tr>
		<tr>
			<td>Create final report</td>
			<td><button>ETL-2</button></td>
		</tr>
		<tr>
			<td>Clear the line</td>
			<td><button>ETL-3</button></td>
		</tr>
	</table>
</body>
</html>