<?php

declare(strict_types=1);

namespace App\Modules\Battle\Entity\BattleUser;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

class BattleUserRepository
{
    /** @var EntityRepository<BattleUser> */
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(BattleUser::class);
        $this->em = $em;
    }

    public function getById(int $id): BattleUser
    {
        if (!$battleUser = $this->findById($id)) {
            throw new DomainException(
                message: 'error.battle.battle_user_not_found',
                code: 1
            );
        }

        return $battleUser;
    }

    public function findById(int $id): ?BattleUser
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    public function isMember(int $battleId, int $userId): bool
    {
        $battle = $this->repo->findOneBy(['battleId' => $battleId, 'userId' => $userId]);

        return null !== $battle;
    }

    public function add(BattleUser $battleUser): void
    {
        $this->em->persist($battleUser);
    }

    public function save(BattleUser $battleUser): void
    {
        $this->em->persist($battleUser);
        $this->em->flush();
    }

    public function remove(BattleUser $battleUser): void
    {
        $this->em->remove($battleUser);
    }
}
