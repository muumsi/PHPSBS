<?php
include('Net/SSH2.php');
session_start();
$IP = $_SESSION['IP'];
$user=$_SESSION['user'];
$pass=$_SESSION['pass'];
$job=$_GET["job"];

if(strcmp($user,"")!==0)
{
	$ssh = new Net_SSH2($IP);
	if (!$ssh->login($user, $pass)) 
	{
      		header("Location: index.html"); // Redirect browser 
	}

}	


$command="scancel " . $job;
	
$output=$ssh->exec($command);
	
header("Refresh:0; url=main.php"); // Refresh
	
?>