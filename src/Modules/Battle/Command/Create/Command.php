<?php

declare(strict_types=1);

namespace App\Modules\Battle\Command\Create;

use Symfony\Component\Validator\Constraints as Assert;

final class Command
{
    public function __construct(
        #[Assert\NotBlank]
        public readonly int $userId,
        /** @var int[] $userIds */
        public array $userIds,
    ) {}
}
