<?php

declare(strict_types=1);

namespace App\Http\Middleware\Identity;

final readonly class Identity
{
    public function __construct(
        public int $id,
    ) {}
}
