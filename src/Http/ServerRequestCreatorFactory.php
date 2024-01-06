<?php

declare(strict_types=1);

namespace Denosys\Core\Http;

use Slim\Factory\Psr17\ServerRequestCreator;
use Slim\Factory\ServerRequestCreatorFactory as SlimServerRequestCreatorFactory;

class ServerRequestCreatorFactory extends SlimServerRequestCreatorFactory
{
    const SERVER_REQUEST_CREATOR_CLASS = 'Denosys\Core\Http\ServerRequestFactory';
    const SERVER_REQUEST_CREATOR_METHOD = 'createFromGlobals';

    public static function create(): ServerRequestCreator
    {
        return new ServerRequestCreator(
            self::SERVER_REQUEST_CREATOR_CLASS,
            self::SERVER_REQUEST_CREATOR_METHOD
        );
    }
}
