<?php
session_start();
if(isset($_SESSION['user'])){

	include('functions.php');
	
	switch($_POST['func'])
	{
		case 'etl1':
			$msg = "groot";
			$debug = "rocks";
			break;
		case 'etl2':
			break;
		case 'etl3':
			break;
		default:
	}

	$e = array($msg,$debug);
	echo json_encode($e);

}
else
{
	header('location:signin.php');
}

?>