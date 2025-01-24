<?php

declare(strict_types=1);

namespace CentralQuality\WpTests\TestCase;

use PHPUnit_Adapter_TestCase;
use WP_Error;
use WP_UnitTestCase;

require_once getenv('WP_PHPUNIT__DIR') . '/includes/phpunit-adapter-testcase.php';
require_once getenv('WP_PHPUNIT__DIR') . '/includes/abstract-testcase.php';
require_once getenv('WP_PHPUNIT__DIR') . '/includes/testcase.php';

abstract class IntegrationTestCase extends WP_UnitTestCase
{
    protected static $port;

    protected $wp_redirect = [];
    protected $wp_http_requests = [];
    protected $wp_http_responses = [];


    public static function setUpBeforeClass(): void
    {
        $token = getenv('TEST_TOKEN') ?: 1;

        if (!defined('ABSPATH')) {
            define('ABSPATH', '/wptests/persistent/plugin/wp/');
            define('WP_DEBUG', true);
            define('DB_HOST', 'mariadb');
            define('DB_CHARSET', 'utf8');
            define('DB_COLLATE', '');
            define('DB_NAME', 'plugin_functional_' . $token);
//            define('DB_NAME', 'plugin_integration');
            define('DB_PASSWORD', 'root');
            define('DB_USER', 'root');

            define('WP_HOME', 'http://localhost');
            define('WP_SITEURL', 'http://localhost');

            define('AUTH_KEY', 'put your unique phrase here');
            define('SECURE_AUTH_KEY', 'put your unique phrase here');
            define('LOGGED_IN_KEY', 'put your unique phrase here');
            define('NONCE_KEY', 'put your unique phrase here');
            define('AUTH_SALT', 'put your unique phrase here');
            define('SECURE_AUTH_SALT', 'put your unique phrase here');
            define('LOGGED_IN_SALT', 'put your unique phrase here');
            define('NONCE_SALT', 'put your unique phrase here');

            $table_prefix = 'wp_';

            define('WP_TESTS_DOMAIN', 'example.org');
            define('WP_TESTS_EMAIL', 'admin@example.org');

            define('WP_HTTP_BLOCK_EXTERNAL', true);
            define('WP_PLUGIN_DIR', '/wptests/persistent/plugin/plugins');

            require_once __DIR__ . '/../../files/bootstrap.php';
        }

        parent::setUpBeforeClass();
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

    public function setUp(): void
    {
        // its inside setUp() but under "WP_RUN_CORE_TESTS" conditional
        $this->reset__SERVER();

        parent::setUp();

        $this->recordRedirect();
        $this->recordHttpRequests();
    }

    public function tearDown(): void
    {
        parent::tearDown();

        $this->wp_redirect = [];
        $this->wp_http_requests = [];
        $this->wp_http_responses = [];

        // clear Woocommerce stuff
        if (is_plugin_active('woocommerce/woocommerce.php')) {
            wc_clear_notices();
        }
    }

    protected function recordRedirect()
    {
        add_filter('wp_redirect', function ($location, $status) {
            $this->wp_redirect = [
                'location' => $location,
                'status' => $status,
            ];

            // disable redirects in PHPUnit
            return false;
        }, 10, 2);
    }

    protected function recordHttpRequests()
    {
        add_filter('pre_http_request', function ($response, array $request, string $url) {
            $this->wp_http_requests[] = [
                'url' => $url,
                'request' => $request
            ];

            $index = count($this->wp_http_requests) - 1;

            $fake_response = $this->wp_http_responses[$index] ?? false;
            if (!$fake_response) {
                throw new \Exception(sprintf('Missing HTTP response for request: %s %s', $request['method'], $url));
            }

            if ($fake_response['asserts']) {
                $fake_response['asserts']($request, $url);
            }

            return $fake_response['response'];
        }, 10, 3);
    }

    /**
     * @param array|WP_Error $response
     * @param callable|null $asserts
     */
    protected function addHttpResponse($response, $asserts = null)
    {
//        if (is_array($response)) {
//            $minimum_keys = ['headers', 'body'];
//        }

        $this->wp_http_responses[] = [
            'asserts' => $asserts,
            'response' => $response
        ];
    }

    protected function assertHttpRequestsCount(int $count)
    {
        $this->assertEquals($count, count($this->wp_http_requests), 'Expected HTTP requests count failed.');
    }

    /**
     * @param array $redirect
     * @return void
     */
    protected function assertRedirect($redirect)
    {
        static::assertEquals($redirect, $this->wp_redirect);
    }

    protected function assertIsRedirected()
    {
        static::assertNotEquals([], $this->wp_redirect);
    }

    protected function assertRedirectLocation(string $location)
    {
        static::assertNotEquals([], $this->wp_redirect);
        static::assertEquals($location, $this->wp_redirect['location']);
    }

    protected function loginAsAdmin(): \WP_User
    {
        return wp_set_current_user(1);
    }

    protected function loginAs(\WP_User $user): \WP_User
    {
        return wp_set_current_user($user->ID);
    }

    protected function logout()
    {
        wp_set_current_user(0);
    }
}
