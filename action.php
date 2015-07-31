<?php
session_start();
if(isset($_SESSION['user'])){

	include('functions.php');

	$msg = array();
	$debug = '';
	$flag = 0;
	
	switch($_POST['func'])
	{
		case 'etl1':
			$msg[]  = "Calling etl1 function...";
			$debug = runETL1();
			$msg[]  = "Done...";
			break;
#		case 'etl2':
#			$msg[]  = "Get response file...";
#			$debug = readResponse();
#			break;
		case 'etl2_start':
			$a = array();
			for($i=0;$i<1000;$i++){ $a[] = 1; }
			$_SESSION['segments'] = $a;
			$_SESSION['segCount'] = sizeof($_SESSION['segments']);
			$msg[]  = 'Processing '.sizeof($_SESSION['segments']).' of '.$_SESSION['segCount'];
			$debug = 'starting process';
			$flag = 1;
			break;
		case 'etl2_run':
			$seg = array_shift($_SESSION['segments']);
			if(sizeof($_SESSION['segments'])>0)
			{
				$msg[]  = 'Processing '.sizeof($_SESSION['segments']).' of '.$_SESSION['segCount'];
				$debug = 'Running process';
				$flag = 1;
			}
			else
			{
				$msg[] = 'Response file converted to Json...';
				$debug = 'Process complete';
			}
			break;
		case 'etl3':
			$msg[]  = "Clearing the line...";
			$debug = "It's cleared";
			break;
		default:
			$msg[]  = "Hmm...";
			$debug = "Unknown comand";
	}

	$e = array(implode('</br/>',$msg),$debug,$flag);
	echo json_encode($e);

}
else
{
	header('location:signin.php');
}

?>