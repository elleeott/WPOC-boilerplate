<?php
// HTTP
define('HTTP_SERVER', 'http://www.frictio.dev/store/');
//define('HTTP_IMAGE', 'http://frictio.dev/store/image/');
define('HTTP_IMAGE', '/static/img/');
define('HTTP_ADMIN', 'http://www.frictio.dev/store/admin/');

// HTTPS
define('HTTPS_SERVER', 'https://www.frictio.dev/store/');
//define('HTTPS_IMAGE', 'http://frictio.dev/store/image/');
define('HTTPS_IMAGE', '/static/img/');

// DIR
define('DIR_APPLICATION', '/Volumes/drive2/localhost/frictious/store/catalog/');
define('DIR_SYSTEM', '/Volumes/drive2/localhost/frictious/store/system/');
define('DIR_DATABASE', '/Volumes/drive2/localhost/frictious/store/system/database/');
define('DIR_LANGUAGE', '/Volumes/drive2/localhost/frictious/store/catalog/language/');
define('DIR_TEMPLATE', '/Volumes/drive2/localhost/frictious/store/catalog/view/theme/');
define('DIR_CONFIG', '/Volumes/drive2/localhost/frictious/store/system/config/');
//define('DIR_IMAGE', '/Volumes/drive2/localhost/frictious/store/image/');
define('DIR_IMAGE', '/Volumes/drive2/localhost/frictious/static/img/');
define('DIR_CACHE', '/Volumes/drive2/localhost/frictious/store/system/cache/');
define('DIR_DOWNLOAD', '/Volumes/drive2/localhost/frictious/store/download/');
define('DIR_LOGS', '/Volumes/drive2/localhost/frictious/store/system/logs/');

// DB
define('DB_DRIVER', 'mysql');
define('DB_HOSTNAME', 'localhost');
define('DB_USERNAME', 'elleeott_frictio');
if(!defined('DB_PASSWORD')) {
	define('DB_PASSWORD', '');
}
define('DB_DATABASE', 'frictio');
define('DB_PREFIX', 'oc_');
?>