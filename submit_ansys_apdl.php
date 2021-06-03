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
$target_dir = $_POST["workf"];
if(strcmp(substr($target_dir, -1),"/") !==0)
{
	$target_dir=$target_dir . "/";
}
$jobname = $_POST["jobname"];
$ncpu = $_POST["ncpu"];
$memsize = $_POST["memsize"];
$maxtime = $_POST["maxtime"];
if(strpos($maxtime,":")<=0)$maxtime=$maxtime . "-00:00:00";
$ansysargs = $_POST["ansysargs"];
$exefile = $_POST["nexe"];
$nodelist="";
if(isset($_POST['hosts'])){
        $hosts = $_POST['hosts'];

		$j=0;
		for($i=0;$i<count($hosts)-1;$i++){  
			$nodelist=$nodelist . $hosts[$j] . ",";
			$j++;
		}
		$nodelist=$nodelist . $hosts[$j] . " ";	
}
$home=$user_home_fs . $user . "/";
$filepath=$home . ".PHPSBS";	
$inifile=$sftp->get($filepath);
$SBSini = tempnam(sys_get_temp_dir(), 'SBSini');
$config=array();
$config=parse_ini_string($inifile,true);
$config['ANSYS APDL']['job'] = $jobname; 
$config['ANSYS APDL']['folder'] = $target_dir;
$config['ANSYS APDL']['param'] = $abargs; 
$config['ANSYS APDL']['cpu'] = $ncpu;
$config['ANSYS APDL']['mem'] = $memsize;
$config['ANSYS APDL']['maxtime'] = $maxtime;

write_ini_file($SBSini, $config);

$sftp->put($filepath,$SBSini,NET_SFTP_LOCAL_FILE);
unlink($SBSini);

$target_file = $local_uploads_path . basename($_FILES["InputFile"]["name"]);
$source_file=$local_uploads_path . basename($_FILES["InputFile"]["name"]);
$tmp_file=$_FILES["InputFile"]["tmp_name"];
ini_set('display_errors',1);
error_reporting(E_ALL);

if(!MoveToUploads($target_file, $tmp_file)){exit;}

$shell_file=$local_uploads_path . $jobname . ".sh";

$imedat=basename($_FILES["InputFile"]["name"]);
$i=WriteShell($shell_file, $target_dir, $sbatch_stdout, $sbatch_error, $jobname, $ncpu, $memsize, $maxtime, $imedat, $exefile, $ansysargs);

$command="mkdir " . $target_dir;
$output=$sftp->exec($command);

$target_file = $target_dir . $imedat;

$i=$sftp->put($target_file,$source_file,NET_SFTP_LOCAL_FILE);
unlink($source_file);

$source_file=$local_uploads_path . $jobname . ".sh";
$target_file=$target_dir . $jobname . ".sh";
$i=$sftp->put($target_file,$source_file,NET_SFTP_LOCAL_FILE);
unlink($source_file);

$command="cd " .  $target_dir;

if(strcmp($nodelist,"")==0){
	$command=$command . ";sbatch " .  $jobname . ".sh &";
}
else
{
	$command=$command . ";sbatch " . "--nodelist=" . $nodelist .  $jobname . ".sh &";		
}
$output=$sftp->exec($command);

header("Refresh:0; url=main.php"); // Refresh

exit();


function MoveToUploads($target_file, $tmp_file) {

	$uspeh=false;
	// move file to uploads

	if (move_uploaded_file($tmp_file, $target_file)) {
			$uspeh=true;
		} else {
			 echo "Not uploaded because of error #".$_FILES["InputFile"]["error"];
			//header("Location: index.html"); // Redirect browser 
			$uspeh=false;
		}
	return $uspeh;
}

function WriteShell($shell_file, $target_dir, $sbatch_stdout, $sbatch_error, $jobname, $ncpu, $memsize, $maxtime, $inputfile, $exefile, $ansysargs) {

$myfile = fopen($shell_file, "w") or die("Unable to open file!");
fwrite($myfile, "#!/bin/bash -l\n");

fwrite($myfile, "# SLURM skript for ANSYS-APDL\n");
fwrite($myfile, "\n");
fwrite($myfile, "# Job name\n");
fwrite($myfile, "#SBATCH -J ");
fwrite($myfile, $jobname);
fwrite($myfile, "\n");

fwrite($myfile, "\n");
fwrite($myfile, "# Files\n");
fwrite($myfile, "#SBATCH -o ");
fwrite($myfile, $sbatch_stdout);
fwrite($myfile, "\n");
fwrite($myfile, "#SBATCH -e ");
fwrite($myfile, $sbatch_error);
fwrite($myfile, "\n");
fwrite($myfile, "#SBATCH --export=ALL\n");
fwrite($myfile, "\n");
fwrite($myfile, "# Tasks per core\n");

fwrite($myfile, "#SBATCH --ntasks-per-core=1\n");
fwrite($myfile, "#SBATCH --ntasks-per-node=64\n");
fwrite($myfile, "\n");

fwrite($myfile, "# Working dir\n");
fwrite($myfile, "#SBATCH -D ");
fwrite($myfile, $target_dir);
fwrite($myfile, "\n");
fwrite($myfile, "\n");
fwrite($myfile, "# Number of CPU\n");
fwrite($myfile, "#SBATCH -n ");
fwrite($myfile, $ncpu);
fwrite($myfile, "\n");
fwrite($myfile, "\n");
fwrite($myfile, "# Memory size\n");
fwrite($myfile, "#SBATCH --mem=");
if($memsize==0){
	fwrite($myfile, $memsize);
	fwrite($myfile, "\n");
}
else
{
	fwrite($myfile, $memsize);
	fwrite($myfile, "G\n");	
}

fwrite($myfile, "\n");
fwrite($myfile, "# Max. time\n");
fwrite($myfile, "#SBATCH --time=");
fwrite($myfile, $maxtime);
fwrite($myfile, "\n");

fwrite($myfile, "# GATHER SLURM JOB DETAILS\n");
fwrite($myfile, "unset SLURM_GTIDS\n");
fwrite($myfile, "\n");
fwrite($myfile,"export MPI_WORKDIR=");
fwrite($myfile, $target_dir);
fwrite($myfile, "\n");
fwrite($myfile,"srun hostname -s > /tmp//hosts.\$SLURM_JOB_ID\n");
fwrite($myfile,"if [ \"x\$SLURM_NPROCS\" = \"x\" ]; then\n");
fwrite($myfile,"  if [ \"x\$SLURM_NTASKS_PER_NODE\" = \"x\" ];then\n");
fwrite($myfile,"    SLURM_NTASKS_PER_NODE=1\n");
fwrite($myfile,"  fi\n");
fwrite($myfile,"  SLURM_NPROCS=`expr \$SLURM_JOB_NUM_NODES \\* \$SLURM_NTASKS_PER_NODE`\n");
fwrite($myfile,"fi\n");
fwrite($myfile,"# format the host list for mechanical\n");
fwrite($myfile,"mech_hosts=\"\"\n");
fwrite($myfile,"for host in `sort -u /tmp//hosts.\$SLURM_JOB_ID`; do \n");
fwrite($myfile,"  n=`grep -c \$host /tmp//hosts.\$SLURM_JOB_ID`\n");
fwrite($myfile,"  mech_hosts=\$(printf \"%s%s:%d:\" \"\$mech_hosts\" \"\$host\" \"\$n\")\n");
fwrite($myfile,"done\n");

fwrite($myfile, "# Simulation parameters:\n");
fwrite($myfile, "INPUT=");
fwrite($myfile, $inputfile);
fwrite($myfile, "\n");
fwrite($myfile, "export CFX5RSH=ssh\n");
fwrite($myfile, "export LD_LIBRARY_PATH=/ceph/grid/software/ansys_inc/lib64\n");

fwrite($myfile, "\n");

// exe
fwrite($myfile, $exefile);
fwrite($myfile, " -p aa_r -dis -mpi ibmmpi -machines \${mech_hosts%%:} -lch -dir \"$target_dir\"");
fwrite($myfile, " -j ");
fwrite($myfile, $jobname);
if(!empty($ansysargs))
{
	fwrite($myfile, " ");
	fwrite($myfile, $ansysargs);
	fwrite($myfile, " ");
}
fwrite($myfile, " -b < \$INPUT > _izpis.txt");
fwrite($myfile, " ");
fwrite($myfile, "\n");
fwrite($myfile, "\n");
# cleanup
fwrite($myfile, "rm /tmp/hosts.\$SLURM_JOB_ID\n");

fclose($myfile);

	return true;
}

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
?>