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
echo "<html><head><title>PHPSBS</title>";
echo "<meta name=\"AUTHOR\" content=\"Miran Ulbin\"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
echo "<meta name=\"KEYWORDS\" content=\"slurm hpc supercomputing batch queue linux miran ulbin\">";
echo "<link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css\" integrity=\"sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l\" crossorigin=\"anonymous\">";
echo "<link rel=\"stylesheet\" href=\"style.css\">";
echo "</head><body>";
echo "<script src=\"https://code.jquery.com/jquery-3.5.1.slim.min.js\" integrity=\"sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj\" crossorigin=\"anonymous\"></script>";
echo "<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js\" integrity=\"sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns\" crossorigin=\"anonymous\"></script>";
	
echo "<nav class=\"navbar navbar-expand-lg navbar-light fixed-top \" style=\"background-color: #e3f2fd;\"><a class=\"navbar-brand\" href=\"#\">PHPSBS</a><div class=\"nav nav-tabs\"><a class=\"nav-link\" href=\"main.php\">Home</a>";
				
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
echo "<a class=\"nav-link active\" href=\"about.php\">About PHPSBS</a></div></nav>";
echo "<div class=\"container\">";
		

echo "<br><br><br><br><br>";
echo "<div align=\"center\">";
echo "<table class=\"table table-bordered\">";
echo "<tr><td align=\"center\">";
echo "<b>PHPSBS</b> is a web interface that allows to connect to the usefull commands of Slurm workload managers. With this interface, you can monitor, submit and cancel your jobs at real time.<br><br>";
echo "<b>AUTHOR</b> Written by Miran Ulbin 2019-2021.<br><br>";

echo "<b>REPORTING BUGS</b> Report bugs to <a href=\"mailto:miran.ulbin@um.si\">miran.ulbin@um.si</a><br><br>";
echo "<b>LICENSE</b> This is free software: you are free to change and redistribute it. GNU General Public License version 3.0 (<a href=http://gnu.org/licenses/gpl.html target=gpl>GPLv3</a>).<br><br>";
echo "<b>Version : 2.1.0 (June 2021)</b><br><br>";
echo "</td></tr>";
echo "</table></div><br></div></body></html>";
?>

