<?php 
$db_name = 'frictio';
$db_user = 'elleeott_frictio';
$password = '';

$protocol='http:';
if(!empty($_SERVER['HTTPS'])) {
    $protocol='https:';
}
$static_subdomain = $protocol.'//static.'.str_replace('www.','',$_SERVER['SERVER_NAME']);

define('STATIC_SUBDIR',$static_subdomain);
define('WP_DEBUG', true);
define('FORCE_SSL_ADMIN', true);