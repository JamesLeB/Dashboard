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
		case 'etl2_start':
			$debug = setupETL2();
			if(sizeof($_SESSION['segs'])>0)
			{
				$_SESSION['segCount'] = sizeof($_SESSION['segs']);
				$msg[]  = 'Processing '.sizeof($_SESSION['segs']).' of '.$_SESSION['segCount'];
				$flag = 1;
			}
			else
			{
				$msg[] = "Problem starting ETL2...";
			}
			break;
		case 'etl2_run':
			runETL2();
			if(sizeof($_SESSION['segs'])>0)
			{
				$msg[]  = 'Processing '.sizeof($_SESSION['segs']).' of '.$_SESSION['segCount'];
				$debug = 'Running process';
				$flag = 1;
			}
			else
			{
				$msg[] = 'Response file converted to Json...';
				$debug = 'Process complete';
			}
			break;
		case 'etl2_merge':
			$msg[] = 'Merging files now...';
			$debug = mergeETL2();
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