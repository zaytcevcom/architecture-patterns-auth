<?php

declare(strict_types=1);

namespace App\Http\Middleware\Identity;

use App\Http\Exception\UnauthorizedHttpException;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\ResourceServer;
use LogicException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;

final class Authenticate implements MiddlewareInterface
{
    private const string ATTRIBUTE = 'identity';

    public function __construct(
        private readonly ResourceServer $server,
    ) {}

    public static function findIdentity(ServerRequestInterface $request): ?Identity
    {
        $identity = $request->getAttribute(self::ATTRIBUTE);

        if ($identity !== null && !$identity instanceof Identity) {
            throw new LogicException('Invalid identity.');
        }

        return $identity;
    }

    public static function getIdentity(ServerRequestInterface $request): Identity
    {
        $identity = self::findIdentity($request);

        if ($identity === null) {
            throw new UnauthorizedHttpException($request);
        }

        return $identity;
    }

    public static function getAccessToken(ServerRequestInterface $request): string
    {
        $token = null;

        if ($request->hasHeader('authorization')) {
            $header = $request->getHeaderLine('authorization');

            $token = trim(str_replace('Bearer', '', $header));

            if (empty($token)) {
                $token = null;
            }
        }

        if ($token === null) {
            throw new UnauthorizedHttpException($request);
        }

        return $token;
    }

    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if (!$request->hasHeader('authorization')) {
            return $handler->handle($request);
        }

        try {
            $request = $this->server->validateAuthenticatedRequest($request);
        } catch (OAuthServerException) {
            throw new UnauthorizedHttpException($request);
        }

        $identity = new Identity(
            id: (int)$request->getAttribute('oauth_user_id'),
        );

        return $handler->handle($request->withAttribute(self::ATTRIBUTE, $identity));
    }
}
