<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use CentralQuality\WpTests\TestCase\FunctionalTestCase;

class ExampleTest extends FunctionalTestCase
{
    public function testMyApp(): void
    {
        $user = wp_create_user('test', '123');
        $user = get_user_by('id', $user);
        $user->add_role('administrator');

        static::$client->get('/wp-login.php');

        static::$client->submitForm('Acceder', [
            'log' => 'test',
            'pwd' => '123'
        ]);

        static::assertEquals('test', static::$client->getCrawler()->filter('.display-name')->text());
    }
}
