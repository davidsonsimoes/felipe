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
define('DB_NAME', 'feli41171711359');

/** MySQL database username */
define('DB_USER', 'feli41171711359');

/** MySQL database password */
define('DB_PASSWORD', 'u6TC!dIlfR');

/** MySQL hostname */
define('DB_HOST', 'feli41171711359.db.41171711.hostedresource.com:3311');

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
define('AUTH_KEY',         'kYKj@Ag#V_EY<kl71G*qw1<0R*xD}f}U_uBzRZK]XD0j1^`B#gZhyGVf-};rJr)%');
define('SECURE_AUTH_KEY',  '(O5FHOtSMYs81<8;nRp8|CNy%z1LJ|5b!N?zii?Z6-{L,rSlYP p1|^zqx0R7?oi');
define('LOGGED_IN_KEY',    '1=3uwG9wy$K-Thk{ME(L?Ka80BkJ7)^/ !7R/?I!DHq^?%l?1_W)xUQwg+lh#R<E');
define('NONCE_KEY',        'Uv}ci,8l=E0V|/o[IKA7A{23`#-|j0-JBanXHZfari[kKGU(VW`5)X85L,##2]!g');
define('AUTH_SALT',        'g<1-B4*}VZgJFil(J.Q#|FDxa{y|6d/9?MP||29:q|g<.b/J:|/e$t?+|YEW?{6$');
define('SECURE_AUTH_SALT', '^I7WCtx(AKjn@u;z6OruN]k,XLn^8y6h[D&5IvFzbFpetzS{pSW6wyOJamV~GojX');
define('LOGGED_IN_SALT',   'S@.OMye5RG03l{wUM?+>0=0Rmo+W|*5PA`[$96{ =#_v:>q/OOb/K9E^I<$FWU7[');
define('NONCE_SALT',       'A/I>jui=^}FJl7rD2E?%t]Ax6{5}XHyy+=lMrn7fusS#__c6pqp`1~sZ]+W%<_5f');

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
define('FS_METHOD', 'direct');

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
