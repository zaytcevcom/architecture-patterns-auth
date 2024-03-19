<?php

declare(strict_types=1);

use App\Http\Middleware\AccessDeniedExceptionHandler;
use App\Http\Middleware\ClearEmptyInput;
use App\Http\Middleware\DenormalizationExceptionHandler;
use App\Http\Middleware\DomainExceptionHandler;
use App\Http\Middleware\Identity\Authenticate;
use App\Http\Middleware\InvalidArgumentExceptionHandler;
use App\Http\Middleware\MethodNotAllowedExceptionHandler;
use App\Http\Middleware\ThrowableHandler;
use App\Http\Middleware\ValidationExceptionHandler;
use Slim\App;
use Slim\Middleware\ErrorMiddleware;

return static function (App $app): void {
    $app->add(Authenticate::class);
    $app->add(AccessDeniedExceptionHandler::class);
    $app->add(MethodNotAllowedExceptionHandler::class);
    $app->add(DomainExceptionHandler::class);
    $app->add(DenormalizationExceptionHandler::class);
    $app->add(ValidationExceptionHandler::class);
    $app->add(InvalidArgumentExceptionHandler::class);
    $app->add(ClearEmptyInput::class);
    $app->add(ThrowableHandler::class);
    $app->addBodyParsingMiddleware();
    $app->add(ErrorMiddleware::class);
};
