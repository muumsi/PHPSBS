<?php
include('Net/SSH2.php');
session_start();
$IP = $_SESSION['IP'];
$user=$_SESSION['user'];
$pass=$_SESSION['pass'];
$folder=$_GET["folder"];

if(strcmp($user,"")!==0)
{
	$ssh = new Net_SSH2($IP);
	if (!$ssh->login($user, $pass)) 
	{
      		header("Location: index.html"); // Redirect browser 
	}

}	


$command="rm -rf " . $folder;

	
$output=$ssh->exec($command);

header("Location: main.php"); // Redirect browser 
      
	
?>