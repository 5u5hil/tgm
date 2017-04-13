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
define('DB_NAME', 'tgm');

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
define('AUTH_KEY',         '5i!4/vYZ,v5C}?[{y~3mTnvX@mic?h9$%4;;&~Cuoc/`Uh*E.$4B]Xz+!7h,/X?y');
define('SECURE_AUTH_KEY',  'bCiN#K%K+$!7A<$4>wd7|dU^.c%G-)[3<u^iJcb}1S# %~%-eh,t1{D/`zZBQX#o');
define('LOGGED_IN_KEY',    'n=nVY+yiQi|ou*NgK+(gp,]f5l@/^62iB]72L=B?~-/eG[J^B^4jV:Kt2L.]${36');
define('NONCE_KEY',        'rK|uX}(RA:US:bQcFf?#iJv2/Inf;8}i-<a?}]]yhvzp -[KUek;hGV8.1n=c`#C');
define('AUTH_SALT',        '=Yzw-kEL3X TMUp^bIx5|.:{V@>uxl{I;^vY84[`{s]WTj*ZLDb@,@`}xDj00{>h');
define('SECURE_AUTH_SALT', '}0- VP3?a7 %o{ R?^/PRA_|UZ3oHyrLUkv+2yeQ3;,;SX6&1Q/>U&6ob{qW0c5R');
define('LOGGED_IN_SALT',   'lRr!T@a$Cte2r|b..3*UB/xUHF`fg;,gsX*UJ(Wbld^f`*ii. C9S4!D2O:07=*w');
define('NONCE_SALT',       ' !a5ER.-aQ^j,eH%3iCGFP}Gr!I=L=*+Y^e..)CufsWhed3m5LwMj*6|V%u40M1T');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'tgm_';

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
