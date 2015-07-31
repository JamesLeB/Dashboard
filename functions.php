<?php
function test(){ return "Functions loaded"; }
function runETL1()
{
	$f = file_get_contents('SYSTEM/Appointments/test1.txt');
	return "$f";
}
?>