<?php
session_start();
if(isset($_SESSION['user'])){
	$user=$_SESSION['user'];

	$a = $_POST['func'];

	switch($a)
	{
		case 'etl1':
			echo "Creating request file...";
			break;
		case 'etl2':
			echo "Creating final report";
			break;
		case 'etl3':
			echo "Clearing the line";
			break;
		default:
			echo "Error";
	}

}else{
	header('location:signin.php');
}
?>