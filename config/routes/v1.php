<?php

declare(strict_types=1);

use App\Components\Router\StaticRouteGroup as Group;
use App\Http\V1\BattleTokenAction;
use App\Http\V1\CreateBattleAction;
use App\Http\V1\OpenApiAction;
use App\Http\V1\TokenAction;
use Slim\App;
use Slim\Routing\RouteCollectorProxy;

return static function (App $app): void {
    $app->group('/v1', new Group(static function (RouteCollectorProxy $group): void {
        $group->get('', OpenApiAction::class);
        $group->post('/battle', CreateBattleAction::class);
        $group->post('/battle/{id}/token', BattleTokenAction::class);

        $group->group('/identity', new Group(static function (RouteCollectorProxy $group): void {
            $group->post('/token', TokenAction::class);
        }));
    }));
};
