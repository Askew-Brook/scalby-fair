<?php

namespace Tests\Feature;

use Tests\TestCase;

class HomepageTest extends TestCase
{
    public function test_the_homepage_is_available(): void
    {
        $this->get('/')->assertOk();
    }
}
