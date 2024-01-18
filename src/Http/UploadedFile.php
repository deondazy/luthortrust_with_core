<?php

declare(strict_types=1);

namespace Denosys\Core\Http;

use Denosys\Core\Application;
use Denosys\Core\Filesystem\FilesystemManager;
use Slim\Psr7\UploadedFile as SlimUploadedFile;

class UploadedFile extends SlimUploadedFile
{
    public function storePublicly(string $path, mixed $name, array $options = []): string|false
    {
        $options = $this->parseOptions($options);

        $options['visibility'] = 'public';

        return $this->storeAs($path, $name, $options);
    }

    public function storeAs(string $path, mixed $name = null, array $options = []): string|false
    {
        $options = $this->parseOptions($options);

        $disk = $this->getDiskFromOptions($options, 'disk');

        return Application::getContainer()->get(FilesystemManager::class)->disk($disk)->writeStream(
            $path,
            $name
        );
        
        // return Application::getContainer()->get(FilesystemManager::class)->disk($disk)->writeStream(
        //     $path,
        //     $this,
        //     $name,
        //     $options
        // );
    }

    /**
     * Parse and format the given options.
     *
     * @param  array|string  $options
     * 
     * @return array
     */
    protected function parseOptions(array|string $options): array
    {
        if (is_string($options)) {
            $options = ['disk' => $options];
        }

        return $options;
    }

    // TODO: Move this to Arr::pull or Arr::get
    private function getDiskFromOptions(array &$options, string|int $key): mixed
    {
        if (isset($options[$key])) {
            $disk = $options[$key];

            unset($options[$key]);

            return $disk;
        }

        return $options;
    }
}
