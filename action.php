<?php
session_start();
if(isset($_SESSION['user'])){
	$user=$_SESSION['user'];

	$a = $_POST['func'];
	echo "this is the trigger scripts $a";

}else{
	header('location:signin.php');
}
?>