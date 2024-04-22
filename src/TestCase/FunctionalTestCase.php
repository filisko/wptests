<?php

declare(strict_types=1);

namespace Filisko\WpTests\TestCase;

use Symfony\Component\Panther\Client;
use Symfony\Component\Panther\PantherTestCaseTrait;
use WP_UnitTestCase;

require_once getenv('WP_PHPUNIT__DIR') . '/includes/phpunit-adapter-testcase.php';
require_once getenv('WP_PHPUNIT__DIR') . '/includes/abstract-testcase.php';
require_once getenv('WP_PHPUNIT__DIR') . '/includes/testcase.php';

abstract class FunctionalTestCase extends WP_UnitTestCase
{
    use PantherTestCaseTrait;

    /**
     * @var Client
     */
    protected $client;

    protected static $port;

    protected $wp_redirect = [];
    protected $wp_http_requests = [];
    protected $wp_http_responses = [];

    public static function setUpBeforeClass(): void
    {
//        static::$stopServerOnTeardown = false;

        $token = getenv('TEST_TOKEN') ?: 1;

        // cleanup!
//        parent::setUpBeforeClass();

//        static::$port = 2222;
//        update_option('siteurl', 'http://localhost:'.static::$port);
//        update_option('home', 'http://localhost:'.static::$port);
//        static::$port = '8080';
//        static::$port = '8080';
//        static::$port = '8080';
//        static::$port = rand(65000, 65555);
        static::$port = '808'.$token;

        if (!defined('ABSPATH')) {
            define('ABSPATH', '/wptests/persistent/plugin/wp/');
            define('WP_DEBUG', true);
            define('DB_HOST', 'mariadb');
            define('DB_CHARSET', 'utf8');
            define('DB_COLLATE', '');
            //        define( 'DB_NAME', 'plugin' );
            //        define( 'DB_NAME', 'plugin_'.$token );
            define('DB_NAME', 'plugin_functional_' . $token);
            define('DB_PASSWORD', 'root');
            define('DB_USER', 'root');
            define('WP_HOME', 'http://php:' . static::$port . '/');
            define('WP_SITEURL', 'http://php:' . static::$port . '/');

            define('AUTH_KEY', 'put your unique phrase here');
            define('SECURE_AUTH_KEY', 'put your unique phrase here');
            define('LOGGED_IN_KEY', 'put your unique phrase here');
            define('NONCE_KEY', 'put your unique phrase here');
            define('AUTH_SALT', 'put your unique phrase here');
            define('SECURE_AUTH_SALT', 'put your unique phrase here');
            define('LOGGED_IN_SALT', 'put your unique phrase here');
            define('NONCE_SALT', 'put your unique phrase here');

            $table_prefix = 'wp_';

            define('WP_TESTS_DOMAIN', 'php');
            //        define( 'WP_TESTS_DOMAIN', 'example.org' );
            define('WP_TESTS_EMAIL', 'admin@example.org');

            define('WP_HTTP_BLOCK_EXTERNAL', true);
            define('WP_PLUGIN_DIR', '/wptests/persistent/plugin/plugins');

            // deeper cleanup per test method, see setUp() of parent
            define('WP_RUN_CORE_TESTS', true);

            require_once __DIR__ . '/../../files/bootstrap.php';
        }

        static::startWebServer([
            'hostname' => 'php',
            'port' => static::$port,
            'webServerDir' => ABSPATH,
            'env' => [
                'FUNCTIONAL_TEST' => 1,
                'TEST_TOKEN' => $token
//                'TEST_TOKEN' => getenv('TEST_TOKEN')
            ]
        ]);
    }

    public static function tearDownAfterClass(): void
    {
        // we do this cleanup for EVERY single test
        // this allows parallel tests
    }

    public function setUp(): void
    {
        // Disable Transaction mode.
        // Otherwise function calls that affect the database won't be reachable by the browser.
    }

    public function tearDown(): void
    {
        // Disable Transaction mode.
        // Otherwise function calls that affect the database won't be reachable by the browser.

        // we apply the "whole cleanup process" to every bit
        parent::tearDownAfterClass();
    }

//    public function fillField(string $name, string $value)
//    {
//        return $this->driver->findElement(WebDriverBy::name($name))->sendKeys($value);
//    }
//
//    public function assertCssElementContainsText(string $cssSelector, string $text): void
//    {
//        $actual = $this->driver->findElement(WebDriverBy::cssSelector($cssSelector))
//            ->getText();
//
//        static::assertEquals($text, $actual);
//    }
}
