<?php

declare(strict_types=1);

namespace App\Tests\Integration;

use CentralQuality\WpTests\TestCase\IntegrationTestCase;

class ExampleTest extends IntegrationTestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    public function test_user_with_id_one_is_admin(): void
    {
        $admin = get_user_by('id', 1);

        static::assertEquals(['administrator'], $admin->roles);
    }

    public function test_factory_example(): void
    {
        $user = $this->factory()->user->create_and_get();
        $user->add_cap('edit_posts');
        $this->loginAs($user);

        $postId = $this->factory()->post->create();
        $ownerId = get_post_field( 'post_author', $postId );

        // owner is the previusly logged in user:
        static::assertEquals($ownerId, $ownerId);
    }
}
