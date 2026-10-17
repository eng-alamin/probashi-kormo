<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    /**
     * Tests must not depend on the developer's .env. Start every test with the public
     * contact details empty, like a fresh install. A test that needs a value sets it itself.
     */
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'site.phone' => null,
            'site.email' => null,
            'site.address' => null,
            'site.whatsapp' => null,
            'site.license_no' => null,
            'site.hours' => null,
            'site.map_embed' => null,
        ]);
    }
    //
}
