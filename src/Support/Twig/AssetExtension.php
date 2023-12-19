<?php

declare(strict_types=1);

namespace Denosys\Core\Support\Twig;

use Slim\App;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AssetExtension extends AbstractExtension
{
    /**
     * AssetExtension constructor.
     *
     * @param ?string $baseAssetUrl Base URL for assets, e.g., '/public/'
     */
    public function __construct(private readonly ?string $baseAssetUrl = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('asset', [$this, 'getAssetUrl'])
        ];
    }

    /**
     * Get the full URL for the given asset path.
     *
     * @param string $path The relative path to the asset.
     *
     * @return string The full URL to the asset.
     */
    public function getAssetUrl(string $path): string
    {
        if ($this->isValidUrl($path)) {
            return $path;
        }

        $assetRoot = !is_null($this->baseAssetUrl) ? rtrim($this->baseAssetUrl, '/') : '';

        return $assetRoot . '/' . ltrim($path, '/');
    }

    /**
     * Determine if the given path is a valid URL.
     *
     * @param  string  $path
     * @return bool
     */
    public function isValidUrl($path)
    {
        if (! preg_match('~^(#|//|https?://|(mailto|tel|sms):)~', $path)) {
            return filter_var($path, FILTER_VALIDATE_URL) !== false;
        }

        return true;
    }
}
