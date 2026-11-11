<?php

namespace Tests;

use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected int $initialObLevel = 1;

    public function createApplication()
    {
        $loader = require __DIR__ . '/../bootstrap/autoload.php';

        $_ENV['APP_BASE_PATH'] = dirname(__DIR__);
        $_SERVER['APP_BASE_PATH'] = dirname(__DIR__);

        $app = require dirname(__DIR__) . '/bootstrap/app.php';

        $app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

        return $app;
    }

    protected function setUp(): void
    {
        $this->initialObLevel = ob_get_level();
        parent::setUp();
    }

    protected function tearDown(): void
    {
        while (ob_get_level() > $this->initialObLevel) {
            @ob_end_clean();
        }
        parent::tearDown();
    }
}
