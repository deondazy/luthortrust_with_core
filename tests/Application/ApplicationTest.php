<?php

use Denosys\Core\Application;
use Denosys\Core\Environment\EnvironmentLoaderInterface;

beforeEach(function () {
    $this->basePath = __DIR__ . '/../../';
});

it('should instantiate the application with a base path', function () {
    $application = new Application($this->basePath);

    expect($application)->toBeInstanceOf(Application::class);
    expect($application->basePath())->toBe($this->basePath);
});

it('should throw an exception if the base path is invalid', function () {
    $basePath = '/invalid/path';

    expect(function () use ($basePath) {
        new Application($basePath);
    })->toThrow(Dotenv\Exception\InvalidPathException::class);
});
