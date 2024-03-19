<?php

declare(strict_types=1);

namespace App\Components\Router\Test;

use App\Components\Router\StaticRouteGroup;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Slim\Routing\RouteCollectorProxy;

/**
 * @internal
 */
final class StaticRouteGroupTest extends TestCase
{
    /** @throws Exception */
    public function testSuccess(): void
    {
        $collector = $this->createStub(RouteCollectorProxy::class);

        $callable = static fn (RouteCollectorProxy $collector): RouteCollectorProxy => $collector;

        $group = new StaticRouteGroup($callable);

        self::assertSame($collector, $group($collector));
    }
}
