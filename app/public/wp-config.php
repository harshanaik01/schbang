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
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

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
define( 'AUTH_KEY',          's(%P~NNc1h~>8SQZnTr1jx1!$T8ZlF%t=[77vG~ <F~=A%#>02r~X|Hanuj]MHk8' );
define( 'SECURE_AUTH_KEY',   'Mf727(T3?PmL0a}~q!5#{t+U?b.__0CPo|1xM$hKI1>G2S0FSlLY; v2aMqOVap)' );
define( 'LOGGED_IN_KEY',     'rOa5Ia`&A!Q9V33)).lQAKO40p9{wS*GMBMr.go%J3(3ERpOF5k}coSekO]&pW+:' );
define( 'NONCE_KEY',         '=Mp =L>YnOc@VdTVk7+6B!uI#P&3R;I_Wt@8KH^|wJZb&.A|oFW`=HiZ*n~g,F&d' );
define( 'AUTH_SALT',         'iN[6V;zbG6!D~`Kk+AI4^fZ!-o!!jhxw,eP4%uwW|T{a?SLS4=r;f4YZBHRW>i}w' );
define( 'SECURE_AUTH_SALT',  'twsd5.I;x+ysR:w.Hw9y7$q@ B]7wMSEK)-4U*cDSk2CI2dd?Rg JN~$C.L7D0~x' );
define( 'LOGGED_IN_SALT',    'i#oU)qcLD}Vs/.2g@-P5(s^xL0{Aov&:-3zB<WM#O7rHUwXeS-g2/YA~c#}Fas/d' );
define( 'NONCE_SALT',        'pctne@b>kBfb_lcjwxpd61eyiyc.z2~[Q_3b8F^wWO+VQa0`2=-]Joqq%cavFS12' );
define( 'WP_CACHE_KEY_SALT', '[<H.g{AE89Z*|jl^Osjzxfl:<EJ>x{]]WM&^cHgEPy+<tr8&E9&tv_+Y|w|jmt-1' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
