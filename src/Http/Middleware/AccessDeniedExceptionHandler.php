<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use App\Http\Response\JsonErrorResponse;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Finder\Exception\AccessDeniedException;

final class AccessDeniedExceptionHandler implements MiddlewareInterface
{
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        try {
            return $handler->handle($request);
        } catch (AccessDeniedException $exception) {
            return new JsonErrorResponse(
                code: $exception->getCode(),
                message: $exception->getMessage()
            );
        }
    }
}
