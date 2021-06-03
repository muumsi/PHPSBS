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
echo "<html><head><title>PHPSBS</title>";
echo "<meta name=\"AUTHOR\" content=\"Miran Ulbin\"><meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">";
echo "<meta name=\"KEYWORDS\" content=\"slurm hpc supercomputing batch queue linux miran ulbin\">";
echo "<link rel=\"stylesheet\" href=\"https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css\" integrity=\"sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l\" crossorigin=\"anonymous\">";
echo "<link rel=\"stylesheet\" href=\"style.css\">";
echo "</head><body>";
echo "<script src=\"https://code.jquery.com/jquery-3.5.1.slim.min.js\" integrity=\"sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj\" crossorigin=\"anonymous\"></script>";
echo "<script src=\"https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.bundle.min.js\" integrity=\"sha384-Piv4xVNRyMGpqkS2by6br4gNJ7DXjqk09RmUpJ8jgGtD7zP9yug3goQfGII0yAns\" crossorigin=\"anonymous\"></script>";

$home=$user_home_fs . $user . "/";
$nas=$home . $fs_nas_folder;

// ini file is written or modified at users home
$filepath=$home . ".PHPSBS";	
$inifile=$sftp->get($filepath);
$SBSini = tempnam(sys_get_temp_dir(), 'SBSini');
if(empty($inifile))
{
	$config=array();
	$config['LS-DYNA']['job'] = ""; // for later use!
	$config['LS-DYNA']['folder'] = $home . "test1/";
	$config['LS-DYNA']['param'] = ""; // for later use!
	$config['LS-DYNA']['cpu'] = '16';
	$config['LS-DYNA']['mem'] = '0';
	$config['LS-DYNA']['maxtime'] = '07';
						
	$config['ANSYS APDL']['job'] = ""; // for later use!
	$config['ANSYS APDL']['folder'] = $home . "test1/";
	$config['ANSYS APDL']['param'] = ""; // for later use!
	$config['ANSYS APDL']['cpu'] = '16';
	$config['ANSYS APDL']['mem'] = '0';
	$config['ANSYS APDL']['maxtime'] = '07';

	$config['ANSYS AEDT']['job'] = ""; // for later use!
	$config['ANSYS AEDT']['folder'] = $home . "test1/";
	$config['ANSYS AEDT']['param'] = ""; // for later use!
	$config['ANSYS AEDT']['cpu'] = '16';
	$config['ANSYS AEDT']['mem'] = '0';
	$config['ANSYS AEDT']['maxtime'] = '07';

	$config['ANSYS CFX']['job'] = ""; // for later use!
	$config['ANSYS CFX']['folder'] = $home . "test1/";
	$config['ANSYS CFX']['param'] = ""; // for later use!
	$config['ANSYS CFX']['cpu'] = '16';
	$config['ANSYS CFX']['mem'] = '0';
	$config['ANSYS CFX']['maxtime'] = '07';
		
	$config['FLUENT']['job'] = ""; 
	$config['FLUENT']['folder'] = $home . "test1/";
	$config['FLUENT']['param'] = "3ddp"; 
	$config['FLUENT']['cpu'] = '4';
	$config['FLUENT']['mem'] = '0';
	$config['FLUENT']['maxtime'] = '07';	
				
	$config['SINGULARITY']['job'] = ""; // for later use!
	$config['SINGULARITY']['folder'] = $home . "test1/";
	$config['SINGULARITY']['param'] = "simpleFoam"; 
	$config['SINGULARITY']['cpu'] = '16';
	$config['SINGULARITY']['mem'] = '0';
	$config['SINGULARITY']['maxtime'] = '07';

	$config['LAMMPS']['job'] = ""; // for later use! 
	$config['LAMMPS']['folder'] = $home . "test1/";
	$config['LAMMPS']['param'] = ""; 
	$config['LAMMPS']['cpu'] = '16';
	$config['LAMMPS']['mem'] = '0';
	$config['LAMMPS']['maxtime'] = '07';
		
	$config['SRUN']['job'] = ""; 
	$config['SRUN']['folder'] = $home . "test1/";
	$config['SRUN']['param'] = ""; 
	$config['SRUN']['cpu'] = '1';
	$config['SRUN']['mem'] = '0';
	$config['SRUN']['maxtime'] = '07';

	write_ini_file($SBSini, $config);
	$sftp->put($filepath,$SBSini);
	unlink($SBSini);
}


$wdi=0;

echo "<nav class=\"navbar navbar-expand-lg navbar-light fixed-top \" style=\"background-color: #e3f2fd;\">
					<a class=\"navbar-brand\" href=\"#\">PHPSBS</a>
					<div class=\"nav nav-tabs\">
						<a class=\"nav-link active\" href=\"main.php\">Home</a>";
				
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
echo "<a class=\"nav-link\" href=\"about.php\">About PHPSBS</a></div></nav>";

echo "<div class=\"container\">";
echo "<br><br><br><br><br><div class=\"table-responsive\">";


// First running or pending jobs are displayed		
$command="squeue -u " . $user . " -o '%i,%P,%j,%u,%T,%R,%M,%l,%D,%N'";
$output = $sftp->exec($command);

$separator = "\n";
$s1 = citaj_vrst($output, $line, $separator);
$i=0;
while (!empty($line)) {

	if($i>0)
	{
		echo "<div class=\"table-responsive\"><table class=\"table table-striped table-bordered\">
					<thead>
					<tr>
					<td style=\"text-align:center;\">Job id</td>
					<td style=\"text-align:center;\">Name</td>
					<td style=\"text-align:center;\">User</td>
					<td style=\"text-align:center;\">State</td>
					<td style=\"text-align:center;\">Time</td>
					<td style=\"text-align:center;\">Time limit</td>
					<td style=\"text-align:center;\">Nodes</td>
					<td style=\"text-align:center;\">Nodelist</td>
					</tr></thead><tbody>";
		$l=citaj($line,$job,$partition,$name,$userown,$state,$reason,$time,$limit,$ncpu,$hostlist);
		echo "<tr>
		      <td style=\"text-align:center;\">$job</td>
		      <td style=\"text-align:center;\">$name</td>
		      <td style=\"text-align:center;\">$userown</td>
		      <td style=\"text-align:center;\">$state</td>
		      <td style=\"text-align:center;\">$time</td>
		      <td style=\"text-align:center;\">$limit</td>
		      <td style=\"text-align:center;\">$ncpu</td>
		      <td style=\"text-align:center;\">$hostlist</td>
		      </tr>";

		echo " </tbody></table></div><div class=\"table-responsive\" style=\"width:100%\">";
		echo "<table class=\"table\" style=\"border-collapse:collapse;border:none;\"><thead><tr>";
		$cmd="squeue -j" . $job . " -o %Z";
		$wd[$wdi]= trim(substr($sftp->exec($cmd),9));
	    echo "<td style=\"text-align:right;border:none;width:60%;\">Working folder:</td><td style=\"text-align:left;border:none;width:40%;color:blue;word-break: break-all;overflow: hidden;\">" .$wd[$wdi] . "</td></tr><tr> ";
		if(strcmp($state,"RUNNING")==0){
			// Special treatment for CFX where .out file is displayed instead of stdout
			$defile=$wd[$wdi] . "*.def";
			$command="ls " . $defile;
			$defile=$sftp->exec($command);
			$defile1=pathinfo($defile, PATHINFO_EXTENSION);
			if(strcmp(trim($defile1),"def")==0)
			{
				// CFX output
				$outfile=$wd[$wdi] . "/" . pathinfo($defile, PATHINFO_FILENAME) . "_001.out";
				$command="tail -c 5000 " . $outfile;
			}
			else
			{
				$filepath=$wd[$wdi] . "/" . $sbatch_stdout; 
				$command="tail -c 5000 " . $filepath;
			}
			$izpis=$sftp->exec($command);
			echo "<td style=\"text-align:center;border:none;width:60%;\">" . $name . " output:</td><td style=\"text-align:center;border:none;width:40%;\">" . $name . " errors:</td></tr></thead><tbody><tr> ";
							
			echo "<td style=\"text-align:center;border:none\"><textarea id=\"izpis" . $job . "\"  rows=\"10\" cols=\"80\" wrap=\"hard\" readonly>" . $izpis . "</textarea></td>";
			$filepath=$wd[$wdi] . "/" . $sbatch_error; 
			$izpis=$sftp->get($filepath);
			$wdi++;
			echo "<td style=\"text-align:center;border:none\"><textarea id=\"errors" . $job . "\"  rows=\"10\"cols=\"40\" wrap=\"hard\" readonly>" . $izpis . "</textarea></td></tr>";
		}		  
		echo "</tbody></table></div>";
			
		echo "<br><div class=\"table-responsive\"><table class=\"table\" style=\"width:100%;margin:auto;border:none;\"><tbody><tr><td style=\"text-align:center;border:none\"><a href=\"cancel.php?job=" . $job . "\"><button type=\"button\" class=\"btn btn-danger\" style=\"width:20%;margin:0 auto;\">scancel " . $job . "</button></a></td></tr></tbody></table><br><br></div>";			
	}
	echo "<script>var textarea". $job . " = document.getElementById('izpis". $job . "');textarea". $job . ".scrollTop = textarea". $job . ".scrollHeight;</script>";
	echo "<script>var textarea1". $job . " = document.getElementById('errors". $job . "');textarea1". $job . ".scrollTop = textarea1". $job . ".scrollHeight;</script>";

	$s1 = citaj_vrst($output,$line, $separator);
	$i++;
}

// List of existing simulation folders
if($i==1)echo "<div class=\"table-responsive\"><table class=\"table\"><tr><td style=\"color:red;text-align:center;border:none;\">There are no active slurm jobs!</td></tr></table></div>";

echo "<div class=\"table-responsive\"><table class=\"table table-bordered table-striped table-hover\"><thead><tr>";
echo "<th style=\"size:30%;min-width:300px;text-align:center;color:blue;\">Analysis folder</td>";
echo "<th style=\"size:40%;text-align:center;color:blue;\">Analysis state</td>";
echo "<th style=\"size:10%;text-align:center;color:blue;\">Download</td>";
echo "<th style=\"size:10%;text-align:center;color:blue;\">Move to ";
echo $fs_nas_name;
echo "</td><th style=\"size:10%;text-align:center;color:blue;\">Remove analysis folder</td></tr></thead><tbody>";


$command="find ~ -name \"" . $sbatch_stdout . "\" | sort";
$output=$sftp->exec($command);
$separator = "\n";
$s1 = citaj_vrst($output, $line, $separator);

	
while (!empty($line)) 
{

	$ok=1;
	$len=strlen($line)-11;
	$curr_folder=trim(substr($line,0,$len));
	
		for($j=0;$j<$wdi;$j++)
		{
			$teststr=$curr_folder . "/";
			if(strcmp($wd[$j],$teststr)==0)$ok=0;
		}
		if($ok)
		{

			$defile=$curr_folder . "/*.def";
			$command="ls " . $defile;
			$defile=$sftp->exec($command);
			$defile1=pathinfo($defile, PATHINFO_EXTENSION);
			// special treatment for CFX
			if(strcmp(trim($defile1),"def")==0)
			{
				// izpis za CFX
				$outfile=$curr_folder . "/" . pathinfo($defile, PATHINFO_FILENAME) . "_001.out";
				$command="tail " . $outfile;
			}
			else
			{
				$filepath=$curr_folder . "/" . $sbatch_stdout; 
				$command="tail " . $filepath;
			}
			$tail=$sftp->exec($command);
			$command="du -sk " . $curr_folder . " | cut -f1";
			$fsize=$sftp->exec($command);
			echo "<tr><td style=\"word-break: break-all;overflow: hidden;\">" . $curr_folder . "</td>";
			echo "<td style=\"word-break: break-all;overflow: hidden;\">" . $tail . "</td>";
			echo "<td><a href=\"download.php?folder=" . $curr_folder . "\"><button type=\"button\" onclick=\"document.body.style.cursor='progress';\" class=\"btn btn-info\" style=\"margin:0 auto;\">Download </button></a></td>";	
			$lenn=strlen($nas);
			if(strlen($curr_folder)>$lenn)
			{
				$tmp=substr($curr_folder,0,$lenn);
				if(strcmp($nas,$tmp)==0)
				{
					echo "<td>OK!</td>";			
				}
				else
				{
					echo "<td><a href=\"movenas.php?folder=" . $curr_folder . "&nas=" . $nas . "\"><button type=\"button\" class=\"btn btn-warning\" style=\"margin:0 auto;\">Move</button></a></td>";
				}
			}	
			else
			{
				echo "<td><a href=\"movenas.php?folder=" . $curr_folder . "\"><button type=\"button\" class=\"btn btn-warning\" style=\"margin:0 auto;\">Move</button></a></td>";
			}			
		
			echo "<td ><a href=\"delete.php?folder=" . $curr_folder . "\"><button type=\"button\" class=\"btn btn-dark\" style=\"margin:0 auto;\">Delete </button></a></td>";
			echo "</tr>";
		}

		$s1 = citaj_vrst($output,$line, $separator);
}

echo "</tbody></table></div></div></body></html>";
		

// ---- func

function write_ini_file($file, $array ) {
	
	// check first argument is string
	if (!is_string($file)) {
		throw new \InvalidArgumentException('Function argument 1 must be a string.');
	}

	// check second argument is array
	if (!is_array($array)) {
		throw new \InvalidArgumentException('Function argument 2 must be an array.');
	}

	// process array
	$data = array();
	foreach ($array as $key => $val) {
		if (is_array($val)) {
			$data[] = "[$key]";
			foreach ($val as $skey => $sval) {
				if (is_array($sval)) {
					foreach ($sval as $_skey => $_sval) {
						if (is_numeric($_skey)) {
							$data[] = $skey.'[] = '.(is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"'.$_sval.'"'));
						} else {
							$data[] = $skey.'['.$_skey.'] = '.(is_numeric($_sval) ? $_sval : (ctype_upper($_sval) ? $_sval : '"'.$_sval.'"'));
						}
					}
				} else {
					$data[] = $skey.' = '.(is_numeric($sval) ? $sval : (ctype_upper($sval) ? $sval : '"'.$sval.'"'));
				}
			}
		} else {
			$data[] = $key.' = '.(is_numeric($val) ? $val : (ctype_upper($val) ? $val : '"'.$val.'"'));
		}
		// empty line
		$data[] = null;
	}

	// open file pointer, init flock options
	$fp = fopen($file, 'w');
	$retries = 0;
	$max_retries = 100;

	if (!$fp) {
		return false;
	}

	// loop until get lock, or reach max retries
	do {
		if ($retries > 0) {
			usleep(rand(1, 5000));
		}
		$retries += 1;
	} while (!flock($fp, LOCK_EX) && $retries <= $max_retries);

	// couldn't get the lock
	if ($retries == $max_retries) {
		return false;
	}

	// got lock, write data
	fwrite($fp, implode(PHP_EOL, $data).PHP_EOL);

	// release lock
	flock($fp, LOCK_UN);
	fclose($fp);

	return true;
}
function citaj($line,&$job,&$partition,&$name,&$user,&$state,&$reason,&$time,&$limit,&$ncpu,&$hostlist)
{
// read from slurm output
	$ostanek=$line;
	$first=strpos($ostanek,",");
	$job=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$first=strpos($ostanek,",");
	$partition=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$first=strpos($ostanek,",");
	$name=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$first=strpos($ostanek,",");
	$user=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$first=strpos($ostanek,",");
	$state=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$first=strpos($ostanek,",");
	$firstoks=strpos($ostanek,"[");
	$firstoke=strpos($ostanek,"]");
	if($firstoks<$first && $firstoke>$first)
	{
		$first=$firstoke+1;
	}
	$reason=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$se=true;
	do
	{
		$first=strpos($ostanek,",");
		$firstoks=strpos($ostanek,"[");
		$firstoke=strpos($ostanek,"]");
		if($firstoks<$first && $firstoke>$first)
		{
			$first=$firstoke+1;
		}
		if(is_numeric(substr($ostanek,0,1)))
		{
			$time=substr($ostanek,0,$first);
			$ostanek=substr($ostanek,$first+1);
			$se=false;
		} else
		{		
			$reason=$reason . substr($ostanek,0,$first);
			$ostanek=substr($ostanek,$first+1);
		}
	} while ( $se);
	$first=strpos($ostanek,",");
	$limit=substr($ostanek,0,$first);
	$ostanek=substr($ostanek,$first+1);
	$first=strpos($ostanek,",");
	$ncpu=substr($ostanek,0,$first);
	$hostlist=substr($ostanek,$first+1); 

	return $line;

} 
function citaj_vrst(&$output, &$vrst, $separator)
{
// read one line
	$ostanek=$output;
	$first=strpos($ostanek,$separator);
	$vrst=substr($ostanek,0,$first);
	$output=substr($ostanek,$first+1);

	return $vrst;

} 

?>
