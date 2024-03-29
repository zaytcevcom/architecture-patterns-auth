<?php

declare(strict_types=1);

namespace App\Modules\OAuth\Test\Unit\Entity;

use App\Modules\OAuth\Entity\Scope\Scope;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class ScopeTest extends TestCase
{
    public function testCreate(): void
    {
        $scope = new Scope($identifier = 'common');

        self::assertSame($identifier, $scope->getIdentifier());
        self::assertSame($identifier, $scope->jsonSerialize());
    }
}
