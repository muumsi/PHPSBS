<?php
include('Net/SFTP.php');
session_start();
$IP = $_SESSION['IP'];
$user=$_SESSION['user'];
$pass=$_SESSION['pass'];

include 'phpsbs_inc.php';

if(strcmp($user,"")!==0)
{
	$sftp = new Net_SFTP($IP);
	if (!$sftp->login($user, $pass)) 
	{
      		header("Location: index.html"); // Redirect browser 
	}

}	


$folder=$_GET["folder"];

echo "<html><head><title>PHPSBS</title>";
echo "<meta name=\"AUTHOR\" content=\"Miran Ulbin\"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
echo "<meta name=\"KEYWORDS\" content=\"slurm hpc supercomputing batch queue linux miran ulbin\">";
echo "<link rel=\"stylesheet\" href=\"bootstrap.min.css\"><link rel=\"stylesheet\" href=\"style.css\">";
echo "</head><body>";
echo "<script src=\"jquery-3.2.1.slim.min.js\"></script><script src=\"jquery.validate.min.js\"></script>";
echo "<script src=\"popper.min.js\"></script><script src=\"bootstrap.min.js\"></script>";
echo "<nav class=\"navbar navbar-expand-lg navbar-light fixed-top \" style=\"background-color: #e3f2fd;\">
					<a class=\"navbar-brand\" href=\"#\">PHPSBS</a>
					<div class=\"nav nav-pills\">
						<a class=\"nav-link\" href=\"main.php\">Home</a>";
				
echo "<a class=\"nav-link active\" href=\"download.php\">Download</a>";
echo "<a class=\"nav-link\" href=\"lsdyna.php\">LS-DYNA</a>";
echo "<a class=\"nav-link\" href=\"aster.php\">CODE ASTER</a>";
echo "<a class=\"nav-link\" href=\"ansys_apdl.php\">ANSYS Mechanical</a>";
echo "<a class=\"nav-link\" href=\"ansys_adyn.php\">ANSYS AutoDyn</a>";
echo "<a class=\"nav-link\" href=\"ansys_cfx.php\">ANSYS CFX</a>";
echo "<a class=\"nav-link\" href=\"fluent.php\">FLUENT</a>";
//echo "<a class=\"nav-link\" href=\"singularity.php\">SINGULARITY</a>";
//echo "<a class=\"nav-link\" href=\"lammps.php\">LAMMPS</a>";
echo "<a class=\"nav-link\" href=\"srun.php\">SRUN</a>";
echo "<a class=\"nav-link\" href=\"blender.php\">BLENDER</a>";
//echo "<a class=\"nav-link\" href=\"calculix.php\">CalculiX</a>";
//echo "<a class=\"nav-link\" href=\"freefem.php\">FreeFEM</a>";
echo "<a class=\"nav-link\" href=\"about.php\">About PHPSBS</a></div></nav>";

echo "<nav class=\"navbar navbar-expand-lg navbar-light fixed-top \" style=\"background-color: #e3f2fd;\">
					<a class=\"navbar-brand\" href=\"#\">PHPSBS</a>
					<div class=\"nav nav-tabs\">
						<a class=\"nav-link\" href=\"main.php\">Home</a>";
	
echo "<a class=\"nav-link active\" href=\"download.php\">Download</a>";
			
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
echo "<a class=\"nav-link\" href=\"about.php\">About PHPSBS</a></div></nav>";


echo "<div class=\"container\">";
echo "<br><br><br><br><br><div class=\"table-responsive\">";
echo "<table class=\"table\" style=\"border-collapse:collapse;border:none;\"><thead><tr>";
echo "<td>Saved folder</td><td>Compressed file for download</td></tr></thead><tbody>";
$last=strrpos($folder,"/")+1;
$filename=substr($folder,$last) . ".zip";
$filepath=$uploads_path . $filename;
$command="/usr/bin/zip -r -q " . $filepath . " " . $folder;
$link="uploads/" . $filename;
$output = $sftp->exec($command);
$command="chmod 755 " . $filepath;
$output = $sftp->exec($command);
echo "<td>" . $folder . "</td><td><a href=" . $link . " download >" . $filename . "</a></td></tr></tbody>";
echo "</table></body></html>";
?>