<?php

declare(strict_types=1);

namespace App\Modules\Battle\Entity\Battle;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

class BattleRepository
{
    /** @var EntityRepository<Battle> */
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(Battle::class);
        $this->em = $em;
    }

    public function getById(int $id): Battle
    {
        if (!$battle = $this->findById($id)) {
            throw new DomainException(
                message: 'error.battle.battle_not_found',
                code: 1
            );
        }

        return $battle;
    }

    public function findById(int $id): ?Battle
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    public function add(Battle $battle): void
    {
        $this->em->persist($battle);
    }

    public function save(Battle $battle): void
    {
        $this->em->persist($battle);
        $this->em->flush();
    }

    public function remove(Battle $battle): void
    {
        $this->em->remove($battle);
    }
}
