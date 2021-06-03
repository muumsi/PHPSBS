<?php
// SLURM login hostname
$rlogin_hostname='rmaister.hpc-rivr.um.si'; 
// Name of the HPC
$phpq_title='Maister.hpc-rivr.um.si SLURM Queue';
// HPC distributed file system root
$fs_root='/ceph';
// FS for archive (nas) - realized as link in home directory and should be change in main.php if not so
$fs_nas_folder="CEPH2/";
// NAS FS name
$fs_nas_name="CEPH2";
// user home root
$user_home_fs="/ceph/grid/home/";
// PHPSBS folder have to include soft link to HPC FS path where large files can be stored 
// and is accessible by HPC and web server computer "ln -s /ceph/grid/data/PHPSBS/ uploads
// in next line path to this folder is stored (chmod 777 PHPSBS)!
// crontab for purging path periodically (dailly!) should be added (purgeuploads.sh)
$uploads_path="/ceph/grid/home/PHPSBS/";
// uploads path from perspective of web server computer, this is soft link folder described above
$local_uploads_path="/var/www/html/PHPSBS/uploads/";
// sbatch stdout file
$sbatch_stdout="_izpis.txt";
// sbatch error file
$sbatch_error="_napake.txt";


?>