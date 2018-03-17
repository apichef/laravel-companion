<?php

namespace Sarala;

use Illuminate\Http\Request;
use Illuminate\Foundation\Testing\TestResponse;

class TestCase extends \Orchestra\Testbench\TestCase
{
    protected function setUp()
    {
        parent::setUp();

        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');
        $this->artisan('migrate', ['--database' => 'testbench']);
        $this->withFactories(__DIR__.'/database/factories');
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('sarala.base_url', 'https://sarala-demo.app/api');
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver'   => 'sqlite',
            'database' => ':memory:',
            'prefix'   => '',
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [JsonApiServiceProvider::class];
    }

    protected function getTestJsonApiResponse(JsonApiResponse $response, Request $request): TestResponse
    {
        return $this->createTestResponse($response->toResponse($request));
    }

    protected function getContentFromResponse(TestResponse $response): array
    {
        return json_decode($response->getContent(), true);
    }
}