<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'fluxandlux' );

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
define( 'AUTH_KEY',         'j~vYN9ZyDM@WGQ3#sY@:a~;v/{R?S-}DOl1{Fd6_I-/eYpi|blotOgo] 7&9:O^J' );
define( 'SECURE_AUTH_KEY',  'pA+[4=3[{&Su1Kdkd#wJ8y2.PEO<g9`M$mYY4sk@julGcL`v8HQm jKb^VoEX92D' );
define( 'LOGGED_IN_KEY',    'H.-_]ZbA6TG*FXbh&+]JG,=b:/(r8,&D;h _Lh*#{C5>=zqfMF`|AI|^nygO^&D!' );
define( 'NONCE_KEY',        '10:0L}I&hh4@W{XKNgkR<$n$1/$u#/$r*JigdXC<=m+!1jAP?^Y~9T.M8j2>KUZm' );
define( 'AUTH_SALT',        '_D,6%_He!J>qDpPlNg!pX4Hd6aAvr~u @y74GGA%cLrbLFg%GKC]Q|ynN[!I>/)M' );
define( 'SECURE_AUTH_SALT', '.H#uuYiPzxAR<f~I{z+GG2A(3f>sShS7w()fDft?xNlJ>UQ|`cS~F]5qFXlT&]92' );
define( 'LOGGED_IN_SALT',   'EOU~LB8vx4},d>iZ$cjJe;qCQZ^2DaJc/3ZPi~[*F}%fi5lO}M+AKGXK=nU9Jf?[' );
define( 'NONCE_SALT',       '(M!SCXfM[,|%#Rooml{GV?V}F+1vY#5 A~#}`#/y%da0>D_3hFl`)6rYu^td}O7G' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
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
