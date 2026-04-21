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
define( 'DB_NAME', 'clinic_db' );

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
define( 'AUTH_KEY',         '42_,VFNaxxO~db0wjOd[*n~H/m3*u<^^P9~O4co`VrP@EP2$j=(>[u7LCrf;*itl' );
define( 'SECURE_AUTH_KEY',  'hZ)dp6+2`4#%z|=Ji4d*|DHs.|@Os2H9jOYP ~z|yL#rS-tzpPP1Q&&HHTCjIW6]' );
define( 'LOGGED_IN_KEY',    'Gn${o4Ktlr$S^`!PzEN!G=u,$gfB>@{i&t%cD^g+PFGCp~*wCl;3[bBQ8d]HiT~B' );
define( 'NONCE_KEY',        'oaF75g>K=]d|<~rx+p~V-`V69g@|-E,tR1|}>au8=crBKQ6V/PEQxds wK=&%3X4' );
define( 'AUTH_SALT',        '53BB9A}d1 yXU*rwa(SEQEtKVm;tLUMf6mru-@TJD=e$/+/,|:ifztcn+DpqUKoC' );
define( 'SECURE_AUTH_SALT', '+.xxf| sDJl7&0Ga@`*Hl#w[h]z^H}1d[(fN[koW<Ix P1F*4UaCjWzDc&){rbq(' );
define( 'LOGGED_IN_SALT',   'S7TAXZNNBCFS0TJs(+F7fi@ .+-EW*wXk[jM;:U;mYnV<[r7ULMT.m+U]YgC%[8c' );
define( 'NONCE_SALT',       'fg5Ni`kReA83HeWC,)9kQVhdAhm;}sPk6Rxb=y6dm{Lx+7`Kg45i+8X2WWQ.ob57' );

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
