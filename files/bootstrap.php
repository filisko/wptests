<?php

if (!getenv('WP_PHPUNIT__DIR')) {
    echo "wwhererere" . PHP_EOL;
    exit(0);
}

define('INCLUDES', getenv('WP_PHPUNIT__DIR') . '/includes');

/*
 * Globalize some WordPress variables, because PHPUnit loads this file inside a function.
 * See: https://github.com/sebastianbergmann/phpunit/issues/325
 */
global $wpdb, $current_site, $current_blog, $wp_rewrite, $shortcode_tags, $wp, $phpmailer, $wp_theme_directories;

// if (defined('WP_TESTS_CONFIG_FILE_PATH')) {
//     $config_file_path = WP_TESTS_CONFIG_FILE_PATH;
//
//     if ( ! is_readable( $config_file_path ) ) {
//         echo 'Error: wp-tests-config.php is missing! Please use wp-tests-config-sample.php to create a config file.' . PHP_EOL;
//         exit( 1 );
//     }
//
//     require_once $config_file_path;
// }

require_once INCLUDES . '/functions.php';

$phpunit_version = tests_get_phpunit_version();

if ( version_compare( $phpunit_version, '5.7.21', '<' ) ) {
	printf(
		"Error: Looks like you're using PHPUnit %s. WordPress requires at least PHPUnit 5.7.21." . PHP_EOL,
		$phpunit_version
	);
	echo 'Please use the latest PHPUnit version supported for the PHP version you are running the tests on.' . PHP_EOL;
	exit( 1 );
}

$required_constants = array(
	'WP_TESTS_DOMAIN',
	'WP_TESTS_EMAIL',
);
$missing_constants  = array();

foreach ( $required_constants as $constant ) {
	if ( ! defined( $constant ) ) {
		$missing_constants[] = $constant;
	}
}

if ( $missing_constants ) {
	printf(
		'Error: The following required constants are not defined: %s.' . PHP_EOL,
		implode( ', ', $missing_constants )
	);
	echo 'Please check out `wp-tests-config-sample.php` for an example.' . PHP_EOL,
	exit( 1 );
}

tests_reset__SERVER();

/*
 * Cron tries to make an HTTP request to the site, which always fails,
 * because tests are run in CLI mode only.
 */
define( 'DISABLE_WP_CRON', true );

define( 'WP_MEMORY_LIMIT', -1 );
define( 'WP_MAX_MEMORY_LIMIT', -1 );

define( 'REST_TESTS_IMPOSSIBLY_HIGH_NUMBER', 99999999 );

$PHP_SELF            = '/index.php';
$GLOBALS['PHP_SELF'] = '/index.php';
$_SERVER['PHP_SELF'] = '/index.php';

// Override the PHPMailer.
require_once INCLUDES . '/mock-mailer.php';
$phpmailer = new MockPHPMailer( true );

$wp_theme_directories = array();

//if ( file_exists( DIR_TESTDATA . '/theme' ) ) {
//	$wp_theme_directories[] = DIR_TESTDATA . '/theme';
//}

$GLOBALS['_wp_die_disabled'] = false;
// Allow tests to override wp_die().
tests_add_filter( 'wp_die_handler', '_wp_die_handler_filter' );
// Use the Spy REST Server instead of default.
tests_add_filter( 'wp_rest_server_class', '_wp_rest_server_class_filter' );
// Prevent updating translations asynchronously.
tests_add_filter( 'async_update_translation', '__return_false' );
// Disable background updates.
tests_add_filter( 'automatic_updater_disabled', '__return_true' );

// Preset WordPress options defined in bootstrap file.
// Used to activate themes, plugins, as well as other settings.
if ( isset( $GLOBALS['wp_tests_options'] ) ) {
	function wp_tests_options( $value ) {
		$key = substr( current_filter(), strlen( 'pre_option_' ) );
		return $GLOBALS['wp_tests_options'][ $key ];
	}

	foreach ( array_keys( $GLOBALS['wp_tests_options'] ) as $key ) {
		tests_add_filter( 'pre_option_' . $key, 'wp_tests_options' );
	}
}

// Load WordPress.
require_once ABSPATH . 'wp-settings.php';

// Load class aliases for compatibility with PHPUnit 6+.
if ( version_compare( tests_get_phpunit_version(), '6.0', '>=' ) ) {
	require INCLUDES.'/phpunit6/compat.php';
}

require_once INCLUDES.'/phpunit-adapter-testcase.php';
require_once INCLUDES.'/abstract-testcase.php';
require_once INCLUDES.'/testcase.php';
require_once INCLUDES.'/testcase-rest-api.php';
require_once INCLUDES.'/testcase-rest-controller.php';
require_once INCLUDES.'/testcase-rest-post-type-controller.php';
require_once INCLUDES.'/testcase-xmlrpc.php';
require_once INCLUDES.'/testcase-ajax.php';
require_once INCLUDES.'/testcase-canonical.php';
require_once INCLUDES.'/testcase-xml.php';
require_once INCLUDES.'/exceptions.php';
require_once INCLUDES.'/utils.php';
require_once INCLUDES.'/spy-rest-server.php';
require_once INCLUDES.'/class-wp-rest-test-search-handler.php';
require_once INCLUDES.'/class-wp-rest-test-configurable-controller.php';
require_once INCLUDES.'/class-wp-fake-block-type.php';
require_once INCLUDES.'/class-wp-sitemaps-test-provider.php';
require_once INCLUDES.'/class-wp-sitemaps-empty-test-provider.php';
require_once INCLUDES.'/class-wp-sitemaps-large-test-provider.php';
