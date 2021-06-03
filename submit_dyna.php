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

$config['LS-DYNA']['job'] = $jobname; 
$config['LS-DYNA']['folder'] = $target_dir;
//$config['LS-DYNA']['param'] = ""; // for later use!
$config['LS-DYNA']['cpu'] = $ncpu;
$config['LS-DYNA']['mem'] = $memsize;
$config['LS-DYNA']['maxtime'] = $maxtime;

write_ini_file($SBSini, $config);

$sftp->put($filepath,$SBSini,NET_SFTP_LOCAL_FILE);
unlink($SBSini);

$target_file = $local_uploads_path . basename($_FILES["InputFile"]["name"]);
$source_file=$local_uploads_path . basename($_FILES["InputFile"]["name"]);
$tmp_file=$_FILES["InputFile"]["tmp_name"];
ini_set('display_errors',1);
error_reporting(E_ALL);
if(!MoveToUploads($target_file, $tmp_file)){exit;}

if (isset($_FILES['ImportFiles'])) {
	$myFile = $_FILES['ImportFiles'];
	$fileCount = count($myFile["name"]);

	for ($i = 0; $i < $fileCount; $i++) 
	{
		$target_file1 = $local_uploads_path . basename($myFile["name"][$i]);
		$source_file1=$local_uploads_path . basename($myFile["name"][$i]);	
		$tmp_file1=$myFile["tmp_name"][$i];		
		if(!MoveToUploads($target_file1, $tmp_file1)){exit;}	
	}
}

$shell_file=$local_uploads_path . $jobname . ".sh";

$imedat=basename($_FILES["InputFile"]["name"]);
$i=WriteShell($shell_file, $target_dir, $sbatch_stdout, $sbatch_error, $jobname, $ncpu, $memsize, $maxtime, $imedat, $exefile);

$command="mkdir " . $target_dir;
$output=$sftp->exec($command);

$target_file = $target_dir . $imedat;
$i=$sftp->put($target_file,$source_file,NET_SFTP_LOCAL_FILE);
unlink($source_file);

// MOVE import files
if (isset($_FILES['ImportFiles'])) {
	$myFile = $_FILES['ImportFiles'];
	$fileCount = count($myFile["name"]);

	for ($i = 0; $i < $fileCount; $i++) 
	{
		$target_file1 = $target_dir . basename($myFile["name"][$i]);
		$source_file1=$local_uploads_path . basename($myFile["name"][$i]);	
		$m=$sftp->put($target_file1,$source_file1,NET_SFTP_LOCAL_FILE);
		unlink($source_file1);
	}
}

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

function WriteShell($shell_file, $target_dir, $sbatch_stdout, $sbatch_error, $jobname, $ncpu, $memsize, $maxtime, $inputfile, $exefile) {

$myfile = fopen($shell_file, "w") or die("Unable to open file!");
fwrite($myfile, "#!/bin/bash -l\n");

fwrite($myfile, "# SLURM skript for LS-DYNA\n");
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
$pn=$ncpu;
if($pn>64)$pn=64;
fwrite($myfile, "#SBATCH --ntasks-per-node=");
fwrite($myfile,$pn);
fwrite($myfile, "\n");
//fwrite($myfile, "#SBATCH --ntasks-per-socket=32\n");
//fwrite($myfile, "#SBATCH --hint=nomultithread\n");
//fwrite($myfile, "#SBATCH --exclusive\n");
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

fwrite($myfile, "# SLURM JOB DETAILS\n");
fwrite($myfile, "HOSTBUILD=()\n");
fwrite($myfile, "for i in \$( /usr/bin/scontrol show hostnames \$SLURM_JOB_NODELIST | sort -u | sed -e :a -e 'N;s/\\n/ /;ba' ); do\n");
fwrite($myfile, "  for j in \$( seq 1 \$SLURM_HOSTSLOTS ); do\n");
fwrite($myfile, "    HOSTBUILD+=(\"\${i}\")\n");
fwrite($myfile, "  done\n");
fwrite($myfile, "done\n");
fwrite($myfile, "HOSTLIST=\$(printf \",%s\" \"\${HOSTBUILD[@]}\")\n");
fwrite($myfile, "HOSTLIST=\${HOSTLIST:1}\n");
fwrite($myfile, "echo \$HOSTLIST\n");
fwrite($myfile, "\n");
fwrite($myfile, "# Dolocanje podatkov za analizo:\n");
fwrite($myfile, "INPUT=");
fwrite($myfile, $inputfile);
fwrite($myfile, "\n");
fwrite($myfile, "LSTC_MEMORY=auto\n");
fwrite($myfile, "\n");
//fwrite($myfile, "export PATH=/usr/lib64:\${LD_LIBRARY_PATH:+:\$LD_LIBRARY_PATH}\n");

if(strpos($exefile,"platformmpi")>0)
{	
	fwrite($myfile, "export mpiroot=/ceph/grid/software/platform_mpi/\n");
	fwrite($myfile, "export MPI_ROOT=\$mpiroot\n");
	fwrite($myfile, "export PATH=\${mpiroot}bin:\${PATH:+:\$PATH}\n");
	fwrite($myfile, "export MANPATH=\${mpiroot}share/man:\${MANPATH:+:\$MANPATH}\n");
	fwrite($myfile, "\n");
	fwrite($myfile, "/ceph/grid/software/platform_mpi/bin/mpirun -TCP -aff -affcycle=numa -np \$SLURM_NPROCS -hostlist \$HOSTLIST ");
	fwrite($myfile, $exefile);
}
else
{

	/*
	fwrite($myfile, "export mpiroot=/ceph/grid/software/ompi-3.0.4/\n");
	fwrite($myfile, "export MPI_ROOT=\$mpiroot\n");
	fwrite($myfile, "export PATH=\${mpiroot}bin:\${PATH}\n");
	fwrite($myfile, "export MANPATH=\${mpiroot}share/man:\${MANPATH}\n");
	fwrite($myfile, "export LD_LIBRARY_PATH=");
	fwrite($myfile, "/ceph/grid/software/dyna");
	fwrite($myfile, "\${LD_LIBRARY_PATH:+:\$LD_LIBRARY_PATH}\n");
	fwrite($myfile, "\n");
*/
	//fwrite($myfile, "/ceph/grid/software/ompi-3.0.4/bin/mpirun -np \$SLURM_NPROCS --bind-to core ");
	//fwrite($myfile, "module load mpi/openmpi-4.0.3\n");
	fwrite($myfile, "export LD_LIBRARY_PATH=");
	fwrite($myfile, "/ceph/grid/software/dyna");
	fwrite($myfile, "\${LD_LIBRARY_PATH:+:\$LD_LIBRARY_PATH}\n");
	fwrite($myfile, "export LD_LIBRARY_PATH=");
	fwrite($myfile, "/ceph/grid/software/openmpi-4.0.2/lib");
	fwrite($myfile, "\${LD_LIBRARY_PATH:+:\$LD_LIBRARY_PATH}\n");
// IB ?
	//fwrite($myfile, "export OMPI_MCA_orte_base_help_aggregate=0\n");
	//fwrite($myfile, "export OMPI_MCA_opal_common_ucx_opal_mem_hooks=1\n");
	//fwrite($myfile, "export OMPI_MCA_pml_ucx_verbose=100\n");
	//fwrite($myfile, "/ceph/grid/software/openmpi-4.0.2/bin/mpirun -np \$SLURM_NPROCS -host \$HOSTLIST --map-by numa --bind-to core --mca pml ucx --rank-by numa --nooversubscribe ");
// IB
	fwrite($myfile, "/ceph/grid/software/openmpi-4.0.2/bin/mpirun -np \$SLURM_NPROCS -host \$HOSTLIST --map-by numa --bind-to core --rank-by numa --nooversubscribe ");
	//fwrite($myfile, "mpirun -n \$SLURM_NPROCS --bind-to core ");
	fwrite($myfile, $exefile);
}
fwrite($myfile, " i=\$INPUT\n");
fwrite($myfile, "\n");
fwrite($myfile, $exefile);
fwrite($myfile, ".l2a -j *binout*\n");
fclose($myfile);

	return true;
}


function MoveToUploads($target_file, $tmp_file) {

	$uspeh=false;
	// move file to uploads

	if (move_uploaded_file($tmp_file, $target_file)) {
			$uspeh=true;
		} else {
			 //echo "Not uploaded because of error #".$_FILES["InputFile"]["error"];
			//header("Location: index.html"); // Redirect browser 
			$uspeh=false;
		}
	return $uspeh;
}

function write_ini_file($file, $array) {
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