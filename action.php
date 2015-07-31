<?php
session_start();
if(isset($_SESSION['user'])){

	include('functions.php');

	$msg = array();
	$debug = '';
	
	switch($_POST['func'])
	{
		case 'etl1':
			$msg[]  = "Calling etl1 function...";
			$debug = runETL1();
			$msg[]  = "Done...";
			break;
		case 'etl2':
			$msg[]  = "Create file...";
			$debug = "lets do something";
			break;
		case 'etl3':
			$msg[]  = "Clearing the line...";
			$debug = "It's cleared";
			break;
		default:
	}

	$e = array(implode('</br/>',$msg),$debug);
	echo json_encode($e);

}
else
{
	header('location:signin.php');
}

?>