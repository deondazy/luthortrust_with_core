<?php

declare(strict_types=1);

namespace Denosys\Core\Filesystem\Drivers;

use Aws\S3\S3Client;
use League\Flysystem\Filesystem;
use League\Flysystem\Visibility;
use League\Flysystem\AwsS3V3\AwsS3V3Adapter;
use Denosys\Core\Filesystem\FilesystemFactoryInterface;
use League\Flysystem\AwsS3V3\PortableVisibilityConverter;

class AwsS3V3FilesystemDriverFactory implements FilesystemFactoryInterface
{
    public function make(array $config): Filesystem
    {
        $s3Config = $this->formatS3Config($config);

        $root = (string) ($s3Config['root'] ?? '');

        $visibility = new PortableVisibilityConverter(
            $config['visibility'] ?? Visibility::PUBLIC
        );

        $streamReads = $s3Config['stream_reads'] ?? false;

        $client = new S3Client($s3Config);
        $adapter = new AwsS3V3Adapter($client, $s3Config['bucket'], $root, $visibility, null, $config['options'] ?? [], $streamReads);
        return new Filesystem($adapter);
    }

    protected function formatS3Config(array $config): array
    {
        $config += ['version' => 'latest'];

        if (!empty($config['key']) && !empty($config['secret'])) {
            // TODO: Add support for array interactions using an Arr class.
            $config['credentials'] = array_intersect_key($config, array_flip(['key', 'secret']));
        }

        if (!empty($config['token'])) {
            $config['credentials']['token'] = $config['token'];
        }

        return $this->removeFromConfig($config, ['token']);
    }

    // TODO: Add support for array interactions using an Arr class.
    private function removeFromConfig(array &$config, array $keys): array
    {
        $original = &$config;

        $keys = (array) $keys;

        if (count($keys) === 0) {
            return $config;
        }

        foreach ($keys as $key) {
            // if the exact key exists in the top-level, remove it
            if (array_key_exists($key, $config)) {
                unset($config[$key]);

                continue;
            }

            $parts = explode('.', $key);

            // clean up before each pass
            $config = &$original;

            while (count($parts) > 1) {
                $part = array_shift($parts);

                if (isset($config[$part]) && is_array($config[$part])) {
                    $config = &$config[$part];
                } else {
                    continue 2;
                }
            }

            unset($config[array_shift($parts)]);
        }
    }
}
