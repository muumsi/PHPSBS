# PHPSBS
Requrements:
1. HTTP server with PHP support (Apache, NGINX, …)
2. PHP - https://www.php.net/ (php.ini should be modified to enable upload of large files)
3. PEAR, the PHP Ext. and Appl. Repo. - http://pear.php.net/package/PEAR (SSH1, SSH2, SFTP, SCP) (folders CRYPT, FILE, MATH and NET)
4. jstree is included in folder dist (https://github.com/vakata/jstree)
5. jquery-form is included in files (https://github.com/jquery-form/form) 
6. File phpsbs_inc.php should be edited and proper values to variables should be set (hostname, title and filesystem).
7. Softlink with name uploads should be created with link to HPC file system (ln -s /ceph/PHPSBS uploads).
8. File purgeuploads should be edited to periodically purge uploads folder above (once a day).
