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
	<script src='jquery-1.11.1.js'></script>
	<link href='style.css' rel='stylesheet'/>
	<script>
		$(document).ready(function(){
			$('#output').html('');
			$('#etl1').click(function(){
				$('#output').html('etl one');
			});
		});
	</script>
</head>
<body>
	<p>Welcome to the Dashboard</p>
	<table>
		<tr>
			<td>Create EDI 270 request</td>
			<td><button id='etl1'>ETL-1</button></td>
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
	<div id='output'>output</div>
</body>
</html>