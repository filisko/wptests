<?php

declare(strict_types=1);

namespace YourNamespace\Integration;

use Filisko\WpTests\TestCase\IntegrationTestCase;

class ExampleTestCase extends IntegrationTestCase
{
    public function set_up(): void
    {
        parent::set_up();
    }

    public function tear_down(): void
    {
        parent::tear_down();
    }

    public function test_happiest_path(): void
    {
        $this->loginAsAdmin();

        update_user_meta(1, 'first_name', 'Test');

        static::assertEquals('Filis', wp_get_current_user()->first_name);
    }

    public function test_happiest2_path(): void
    {
        $this->loginAsAdmin();

        update_user_meta(1, 'first_name', 'Test');

        static::assertEquals('Filis', wp_get_current_user()->first_name);
    }

    public function test_happiest_path2(): void
    {
        $this->addHttpResponse(['body' => json_encode([
            'data' => [
                'ordenID' => '723b3cfb-f0b3',
                'url' => 'http//bancamiga.com'
            ],
            'mensaje' => 'Orden Creada',
            'status' => 200,
        ])]);

        $this->addHttpResponse(['body' => json_encode([
            'data' => [
                'ordenID' => '723b3cfb-f0b3',
                'url' => 'http//bancamiga.com'
            ],
            'mensaje' => 'Orden Creada',
            'status' => 200,
        ])], function (array $request, string $url) {
//            $this->assertEquals('application/x-www-form-urlencoded', $request['headers']['Content-Type']);
            $this->assertStringContainsString('jsonplaceholder.typicode.com', $url);
        });

        $response = (new \WP_Http())->request('https://jsonplaceholder.typicode.com/posts');
        $response = (new \WP_Http())->request('https://jsonplaceholder.typicode.com/posts');

        $this->assertHttpRequestsCount(2);
    }
}
