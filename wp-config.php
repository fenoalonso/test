<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

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
define('AUTH_KEY',         'ng+KE)q~K8q[Lk$InEe;*Jn0(JLJ$12m1C6jNOab$Yn#2+&xi]MDC]<_JW)CcT_v');
define('SECURE_AUTH_KEY',  'PZ9JN)7v>*$Ni)PFN[;PN)nTjJaU|!#r[[SK?1~@?<xX%>`e[Ju4p)mC-/#r[4F0');
define('LOGGED_IN_KEY',    'KZTuwZjXL-=U;WS?80<cX;d-P>7@9+tg@tg^$r9Nf,`TZLbxO}-TAh-:$<{t=*Rw');
define('NONCE_KEY',        '*6F|`-Jk _:>UL4$]Jf3k$i$`->z%c:fr(n@g|5<o#(-::H&hZ?]6l&vqmnq+LB%');
define('AUTH_SALT',        'G1Pw!1Dc$o]n*2F07AH28%}9f0roG0`cU;^`D5>2t53 #vrc+`)gg^ %xW>2d`0D');
define('SECURE_AUTH_SALT', '8Mu[a[Xu)pZ*2FVDKL%+s7hlW&ee`cH|Bl)ZNWjr{czc*Xnn%Fn!k_V!~6G?y#9c');
define('LOGGED_IN_SALT',   'h~a?)m?2Ur~`0p](PBhm_7jHgd/bjITFg368WK@Q&hjc^jT:w G3|Z4l=@U|;lZC');
define('NONCE_SALT',       'z RDHKm! 1vC_<xBq9QFB*K^HKu>o1+~b7QMgI5D_$|BPTX3:@a|MTCe*zfi9;`D');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
