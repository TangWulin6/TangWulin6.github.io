<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'w' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         '8QV[n>mYW46BX&M/#@}w)tyi>H5&V?0z8*si{<3j^tU2G~b~1bODgH*}7;[|!|;p' );
define( 'SECURE_AUTH_KEY',  'wi_wdW]/JXeAa59bDp7gPY&R]q[B,mnHB2.ooS#O4Gluk~;.Y|V/f~eoQu1}4(NA' );
define( 'LOGGED_IN_KEY',    'nM}zO0(xXBLRJfE8%6n5X%mu Xfu+C,Fo[ &v}TDhkyMj3U*k~Prkh5d_JUT4qoS' );
define( 'NONCE_KEY',        'mW2W6vM8O]U3{nji%T&UH$8ad5!8~0 S`|7L4|Sv3^H5q#L^3(`;f_<0=dM)coO9' );
define( 'AUTH_SALT',        'ZJN>f~gca[sUWk,laC<2FRA+gG5zZ*UfI,.}Ot62MY8pe45i@`E:HU)p~(EJ.L#C' );
define( 'SECURE_AUTH_SALT', 'G,}Bpz-;|4B8ex=2~7MwMiEdxenf=8V+H4Zf$SGn-|NfQ#UC;@qZi@k/Xo_l}v7X' );
define( 'LOGGED_IN_SALT',   'oBU}SR%R|J~Ut?=!KB]~R.Pc]Q=9u{`P<_={QE8uG~?Xl%z3% A6B Bqb>hLCQw!' );
define( 'NONCE_SALT',       'vxx!=0n$e|giU4#`EO5~tZh6]){wuDV5y^}!`l+nQ0TGP-x~QpQ_?Mi^|$1_z#LU' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
