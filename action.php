<?php
session_start();
if(isset($_SESSION['user'])){

	include('functions.php');
	
	switch($_POST['func'])
	{
		case 'etl1':
			echo "Creating request file...".test();
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