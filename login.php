<?php
$user  = $_POST['uname'];
$pass = $_POST['psw'];
include('Net/SSH2.php');

session_start();

include 'phpsbs_inc.php';

$IP=$rlogin_hostname;
$ssh = new Net_SSH2($IP);

if ($ssh->login($user, $pass)) 
{
	$_SESSION['user'] = $user;
	$_SESSION['pass'] = $pass;
	$_SESSION['IP'] = $IP;
	header("Location: main.php"); // Redirect browser
} 
else 

{
	header("Location: index.html"); // Redirect browser 
	exit();
}

?>