<?php

declare(strict_types=1);

namespace YourNamespace\EndToEnd;

use Filisko\WpTests\TestCase\OldFunctionalTestCase;
use Facebook\WebDriver\WebDriverBy;

class ExampleTestCaseOld extends OldFunctionalTestCase
{
    public function test_admin_login()
    {
        $this->gotoLoginPage();
        $this->fillField('log', 'root');
        $this->fillField('pwd', '123123123')->submit();

        $this->assertCssElementContainsText('.display-name', 'root');

        // change display name!
        wp_update_user(
            ['ID' => 1, 'display_name' => 'WP Tests']
        );

        // refresh the browser
        $this->driver->navigate()->refresh();

        // assert again
        $this->assertCssElementContainsText('.display-name', 'WP Tests');

        // reset back :)
        wp_update_user(
            ['ID' => 1, 'display_name' => 'root']
        );

        // this is powerful !!!
    }
}
