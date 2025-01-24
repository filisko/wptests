<?php

declare(strict_types=1);

namespace CentralQuality\WpTests\TestCase;

use Facebook\WebDriver\Exception\WebDriverException;
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverElement;
use Facebook\WebDriver\WebDriverSelect;
use PHPUnit_Adapter_TestCase;
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
    protected static $client;

    protected static $port;

    public static function setUpBeforeClass(): void
    {
//        static::$stopServerOnTeardown = false;

        $token = getenv('TEST_TOKEN') ?: 1;
//        update_option('siteurl', 'http://localhost:'.static::$port);
//        update_option('home', 'http://localhost:'.static::$port);
        static::$port = '808'.$token;

        if (!defined('ABSPATH')) {
            define('ABSPATH', '/wptests/persistent/plugin/wp/');
            define('WP_DEBUG', true);
            define('DB_HOST', 'mariadb');
            define('DB_CHARSET', 'utf8');
            define('DB_COLLATE', '');
            define('DB_NAME', 'plugin_functional_' . $token);
            define('DB_PASSWORD', 'root');
            define('DB_USER', 'root');
            define('WP_HOME', 'http://php:' . static::$port . '/');
            define('WP_SITEURL', 'http://php:' . static::$port . '/');

            define('WP_CACHE', false);

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
            ]
        ]);

        self::$client = Client::createSeleniumClient('http://selenium:4444/wd/hub', null, 'http://php:'.static::$port.'/');
    }

    public static function tearDownAfterClass(): void
    {
        // we do this cleanup for EVERY single test. see tearDown()
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

    /**
     * Removed _delete_all_data() from original method.
     * Otherwise we cant test against plugins (it will delete everything).
     *
     * @see \WP_UnitTestCase_Base::tear_down_after_class
	 */
	public static function tear_down_after_class()
    {
		self::flush_cache();

		PHPUnit_Adapter_TestCase::tear_down_after_class();
	}

    public function fillField(string $name, string $value)
    {
        return static::$client->getWebDriver()->findElement(WebDriverBy::id($name))->sendKeys($value);
    }

    public function select(string $name, string $value)
    {
        $selectElement = static::$client->getWebDriver()->findElement(WebDriverBy::id($name));
        $select = new WebDriverSelect($selectElement);

        $select->selectByValue($value);
    }

    public function tryClickUntilPossible(WebDriverElement $element)
    {
        static::$client->getWebDriver()->wait()->until(
            function () use ($element) {
                try {
                    $element->click();
                } catch (WebDriverException $e) {
                    return false;
                }
                return true;
            }
        );
    }

    public function assertCssElementContainsText(string $cssSelector, string $text): void
    {
        $actual = static::$client->getWebDriver()->findElement(WebDriverBy::cssSelector($cssSelector))
            ->getText();

        static::assertEquals($text, $actual);
    }

//    public function waitForPageToLoadBasedOnElements($bys){
//        $driver = $this->GetSeleniumDriver();
//        $driver->wait()->until(
//            function () use ($driver, $bys){
//                $foundAll = true;
//                foreach ($bys as $by){
//                    if ($driver->findElement($by) != true)
//                    {
//                        $foundAll = false;
//                        break;
//                    }
//                }
//                return $foundAll;
//            }
//
//        );
//    }
//
//    public function findElementWithWait($elementPath, $parent=null, $shouldBeInteractable=true, $timeoutInSecond = 30, $intervalInMillisecond = 250){
//        $driver = $this->driver;
//
//        $this->driver->wait($timeoutInSecond, $intervalInMillisecond)->until(
//            function () use ($driver, $elementPath, $parent, $shouldBeInteractable){
//                try{
//                    $element =  $parent != null ?  $parent->findElement($elementPath) : $driver->findElement($elementPath);
//                    if (!$shouldBeInteractable){
//                        return $element;
//                    }
//                    $this->driver->executeScript("arguments[0].scrollIntoView(false)", [$element]);
//                    return $element->isDisplayed() ? $element : null;
//
//                }
//                catch (NoSuchElementException $e){
//                    return null;
//                }
//            }
//        );
//        return  $parent != null ?  $parent->findElement($elementPath) : $driver->findElement($elementPath);
//    }


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
