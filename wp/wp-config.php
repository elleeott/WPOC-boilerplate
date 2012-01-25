<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */
//get environment specific info - db creds
require_once('wp-config-local.php');


define('WP_SITEURL', 'http://' . $_SERVER['SERVER_NAME'] . '/wp');
define('WP_HOME', 'http://' . $_SERVER['SERVER_NAME']);
define('COOKIE_DOMAIN', $_SERVER['SERVER_NAME']);	
define( 'WP_CONTENT_DIR', $_SERVER['DOCUMENT_ROOT'] . '/static/wp-content' ); 
define( 'WP_CONTENT_URL', '//static.'.str_replace('www.','',$_SERVER['SERVER_NAME']).'/wp-content');


// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

define('DB_NAME', $db_name);

/** MySQL database username */
define('DB_USER', $db_user);

/** MySQL database password */
define('DB_PASSWORD', $password);

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'KJk_gc>+L}td&!|7|q#;2eKB?1_Q$+JS;(@_wxTgU<!VNh&?)by.V}p()(1R+u$,');
define('SECURE_AUTH_KEY',  '||_}5C>v+y o3vMMYJ9GN)0KS};@+{>+raR~[3]Yvu9%sNib<h.:},IW!ozi7g-o');
define('LOGGED_IN_KEY',    'x}e!8jaqF6 (f79-C)QFf;Qc9Y.)8#a{.C:)-*#K;ZQ>g#X5,!U`oQ9ff@{S3.N7');
define('NONCE_KEY',        'xVI),b%zp~LD)x>gc)q5cle~RVk=bC4Ga>>0bt&5nyG$5Fn>oSY3PJ^uA2I(G2zZ');
define('AUTH_SALT',        '$?u<gJ3C%(C<5xdZz7<8+0+x#4AZml 4G*gtO/9=WX|tceqbI$+R,EYhw3E%&&ED');
define('SECURE_AUTH_SALT', 'QzRtwqyLe:Yc!Hp/E-5`6|6y_f5Lp8U{kbTAQK:r;:HRj@qjNnR[rjTpt|x$Mgv2');
define('LOGGED_IN_SALT',   '^pxf[V]<|AT-Fjcgfc^T#&m]1-`<YR/Ng^${a`:>igq{>iS2hb|i/:BD$?~la!:)');
define('NONCE_SALT',       '-%>|u=%)>4_)(b{/L f#BfY75h0Mzg<-]0jrCTj3Tu-~+L?mZYanUk]M##u6%4%;');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'bm3dWP_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');