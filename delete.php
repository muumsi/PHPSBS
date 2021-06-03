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
echo "<html>";
echo "<head>";
echo "<title>PHPSBS</title>";
echo "<meta name=\"AUTHOR\" content=\"Miran Ulbin \">";
echo "<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
echo "<meta name=\"KEYWORDS\" content=\"slurm hpc supercomputing batch queue linux miran ulbin\">";
echo "<link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css\" integrity=\"sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l\" crossorigin=\"anonymous\">";
echo "<link rel=\"stylesheet\" href=\"style.css\">";
echo "</head>";

echo "<body>";
echo "<script src=\"https://code.jquery.com/jquery-3.5.1.slim.min.js\" integrity=\"sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj\" crossorigin=\"anonymous\"></script>";
echo "<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js\" integrity=\"sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns\" crossorigin=\"anonymous\"></script>";

echo "<nav class=\"navbar navbar-expand-lg navbar-light fixed-top \" style=\"background-color: #e3f2fd;\">
					<a class=\"navbar-brand\" href=\"#\">PHPSBS</a>
					<div class=\"nav nav-tabs\">
						<a class=\"nav-link\" href=\"main.php\">Home</a>";
				
echo "<a class=\"nav-link\" href=\"lsdyna.php\">LS-DYNA</a>";
//echo "<a class=\"nav-link\" href=\"aster.php\">CODE ASTER</a>";
echo "<a class=\"nav-link\" href=\"ansys_apdl.php\">ANSYS Mehanical</a>";
echo "<a class=\"nav-link\" href=\"ansys_apdl_gpu.php\">ANSYS Mechanical GPU</a>";
echo "<a class=\"nav-link\" href=\"ansys_cfx.php\">ANSYS CFX</a>";
echo "<a class=\"nav-link\" href=\"ansys_aedt.php\">ANSYS AEDT</a>";
//echo "<a class=\"nav-link\" href=\"fluent.php\">FLUENT</a>";
//echo "<a class=\"nav-link\" href=\"singularity.php\">SINGULARITY</a>";
//echo "<a class=\"nav-link\" href=\"lammps.php\">LAMMPS</a>";
echo "<a class=\"nav-link\" href=\"srun.php\">SRUN</a>";
echo "<a class=\"nav-link\" href=\"blender.php\">BLENDER</a>";
echo "<a class=\"nav-link active\" href=\"#\">DELETE</a>";
echo "<a class=\"nav-link\" href=\"about.php\">About PHPSBS</a></div></nav>";

echo "<div class=\"container\">";
echo "<br><br><br><br><br><br><br><br><br><br><br><br><table class=\"table table-striped\"><thead><tr><td colspan=\"3\" style=\"font-size:large;text-align:center;\">Are you sure to delete all files in folder: " . $folder . "?</td></tr></thead><tbody><tr>";

echo "<td  style=\"size:20%\"><a href=\"main.php\"><button type=\"button\" class=\"btn btn-info\" style=\"margin:0 auto;\">Cancel </button></a></td>";
echo "<td style=\"size:60%\">&nbsp;</td>";
echo "<td  style=\"size:20%\"><a href=\"deletec.php?folder=" . $folder . "\"><button type=\"button\" class=\"btn btn-dark\" style=\"margin:0 auto;\">Delete </button></a></td></tr>";
		
echo "</div>";
echo "</body>";
echo "</html>";

?>