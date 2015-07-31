<?php
session_start();
if(isset($_SESSION['user'])){

	include('functions.php');

	$msg = array();
	
	switch($_POST['func'])
	{
		case 'etl1':
			$msg[]  = "Calling etl1 function...";
			$debug = runETL1();
			$msg[]  = "Done...";
			break;
		case 'etl2':
			break;
		case 'etl3':
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