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
		function loopback()
		{
			$('#debug').css('background','lightgreen');
			var p = {func: 'etl2_run'};
			$.post('action.php',p,function(data){
				var o = $.parseJSON(data);
				$('#output').html(o[0]);
				$('#debug').html(o[1]);
				if(o[2] == 1)
				{
					loopback();
				}
				else
				{
					$('#debug').css('background','white');
				}
			});
		}
		$(document).ready(function(){
			$('#etl1').click(function(){
				var p = {func: 'etl1'};
				$.post('action.php',p,function(data){
					var o = $.parseJSON(data);
					$('#output').html(o[0]);
					$('#debug').html(o[1]);
				});
			});
	/*
			$('#etl2').click(function(){
				var p = {func: 'etl2_start'};
				$.post('action.php',p,function(data){
					var o = $.parseJSON(data);
					$('#output').html(o[0]);
					$('#debug').html(o[1]);
					if(o[2] == 1) { loopback(); }
				});
			});
	 */

			$('#etl2').click(function(){
				var p = {func: 'etl2_merge'};
				$.post('action.php',p,function(data){
					var o = $.parseJSON(data);
					$('#output').html(o[0]);
					$('#debug').html(o[1]);
				});
			});

			$('#etl3').click(function(){
				var p = {func: 'etl3'};
				$.post('action.php',p,function(data){
					var o = $.parseJSON(data);
					$('#output').html(o[0]);
					$('#debug').html(o[1]);
				});
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
			<td><button id='etl2'>ETL-2</button></td>
		</tr>
		<tr>
			<td>Clear the line</td>
			<td><button id='etl3'>ETL-3</button></td>
		</tr>
	</table>
	<div id='output'></div>
	<div id='debug'></div>
</body>
</html>