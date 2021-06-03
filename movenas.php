<?php
include('Net/SFTP.php');
session_start();
$IP = $_SESSION['IP'];
$user=$_SESSION['user'];
$pass=$_SESSION['pass'];

if(strcmp($user,"")!==0)
{
	$sftp = new Net_SFTP($IP);
	if (!$sftp->login($user, $pass)) 
	{
      		header("Location: index.html"); // Redirect browser 
	}

}	


$folder=$_GET["folder"];
$nas=$_GET["nas"];
$filepath="~/" . $fs_nas_folder . $filename;
$command="mv " . $folder . " " . $nas;
$output = $sftp->exec($command);
header("Location: main.php"); // Redirect browser 
exit();
?>