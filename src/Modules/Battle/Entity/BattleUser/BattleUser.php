<?php

declare(strict_types=1);

namespace App\Modules\Battle\Entity\BattleUser;

use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'battle_user')]
class BattleUser
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true, options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'integer')]
    private int $battleId;

    #[ORM\Column(type: 'integer')]
    private int $userId;

    private function __construct(
        int $battleId,
        int $userId,
    ) {
        $this->battleId = $battleId;
        $this->userId = $userId;
    }

    public static function create(
        int $battleId,
        int $userId,
    ): self {
        return new self(
            battleId: $battleId,
            userId: $userId
        );
    }

    public function getId(): int
    {
        if (null === $this->id) {
            throw new DomainException('Id not set');
        }
        return $this->id;
    }

    public function setId(?int $id): void
    {
        $this->id = $id;
    }

    public function getBattleId(): int
    {
        return $this->battleId;
    }

    public function setBattleId(int $battleId): void
    {
        $this->battleId = $battleId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function setUserId(int $userId): void
    {
        $this->userId = $userId;
    }
}
