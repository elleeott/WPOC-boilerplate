#WordPress & OpenCart Boilerplate

html5, performance optimized Boilerplate for Wordpress and opencart sites.
current working install of WP 3.3.1 and OC 1.5.1.3


## install instructions

1. download and put it in webroot
2. create a mysql db and user per WP and OC documentation.
3. rename wp-config-sample.php to wp-config.php and enter db credentials
4. run WP install - this will set up the required db tables for WP.
5. in webroot, create /static directory, and map this to a subdomain  (static.mywebsite.com)

## notes
root .htaccess is assume unchanged:
git update-index --assume-unchanged <file>
git update-index --no-assume-unchanged <file>