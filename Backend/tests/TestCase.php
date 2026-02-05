<?php

namespace Tests;

use App\Services\ReCaptchaService;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Model::unguard();

        $this->mock(ReCaptchaService::class, function ($mock) {
            $mock->shouldReceive('verify')->andReturnNull();
        });
    }
}
