<?php

declare(strict_types=1);

namespace App\Modules\Battle\Command\Create;

use App\Components\Flusher;
use App\Modules\Battle\Entity\Battle\Battle;
use App\Modules\Battle\Entity\Battle\BattleRepository;
use App\Modules\Battle\Entity\BattleUser\BattleUser;
use App\Modules\Battle\Entity\BattleUser\BattleUserRepository;

final readonly class Handler
{
    public function __construct(
        private BattleRepository $battleRepository,
        private BattleUserRepository $battleUserRepository,
        private Flusher $flusher
    ) {}

    public function handle(Command $command): int
    {
        $battle = $this->createBattle($command->userId);
        $this->addUsers($battle->getId(), $command->userIds);

        return $battle->getId();
    }

    private function createBattle(int $userId): Battle
    {
        $battle = Battle::create(
            userId: $userId,
            createdAt: time()
        );

        $this->battleRepository->add($battle);
        $this->flusher->flush();

        return $battle;
    }

    /** @param int[] $userIds */
    private function addUsers(int $battleId, array $userIds): void
    {
        foreach ($userIds as $userId) {
            $battleUser = BattleUser::create(
                battleId: $battleId,
                userId: $userId,
            );

            $this->battleUserRepository->add($battleUser);
        }

        $this->flusher->flush();
    }
}
