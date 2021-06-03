# PHPSBS
Requrements:
HTTP server with PHP support (Apache, NGINX, …)
PHP - https://www.php.net/
PEAR, the PHP Ext. and Appl. Repo. - http://pear.php.net/package/PEAR (SSH1, SSH2, SFTP, SCP) (folders CRYPT, FILE, MATH and NET should be copied in PHPQstat folder)
File phpsbs_inc.php should be edited and proper values to variables should be set (hostname, title and filesystem).
Softlink with name uploads should be created with link to HPC file system (ln -s /ceph/PHPSBS uploads).
File purgeuploads should be edited to periodically purge uploads folder above (once a day).
