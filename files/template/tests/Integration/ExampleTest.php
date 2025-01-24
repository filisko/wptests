<?php

declare(strict_types=1);

namespace YourNamespace\Tests\Integration;

use CentralQuality\WpTests\TestCase\IntegrationTestCase;

class ExampleTest extends IntegrationTestCase
{
    private $sut;

    public function setUp(): void
    {
        parent::setUp();

        $this->assertTrue(is_plugin_active('hello/hello.php'), 'Plugin must be active in Database');

        $this->sut = new \Example();
    }

    public function test_happiest_path(): void
    {
        $user = $this->factory()->user->create_and_get();
        $user->add_cap('edit_posts');
        $this->loginAs($user);
    }
}
