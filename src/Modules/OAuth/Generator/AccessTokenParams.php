<?php

declare(strict_types=1);

namespace App\Modules\OAuth\Generator;

use DateTimeImmutable;

final readonly class AccessTokenParams
{
    public function __construct(
        public string $userId,
        public ?int $battleId,
        public DateTimeImmutable $expires,
    ) {}
}
