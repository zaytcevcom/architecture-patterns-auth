<?php

declare(strict_types=1);

namespace App\Modules\Identity\Entity\User;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use DomainException;

class UserRepository
{
    /** @var EntityRepository<User> */
    private EntityRepository $repo;
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->repo = $em->getRepository(User::class);
        $this->em = $em;
    }

    public function getById(int $id): User
    {
        if (!$user = $this->findById($id)) {
            throw new DomainException(
                message: 'error.user.user_not_found',
                code: 1
            );
        }

        return $user;
    }

    public function findById(int $id): ?User
    {
        return $this->repo->findOneBy(['id' => $id]);
    }

    public function add(User $user): void
    {
        $this->em->persist($user);
    }

    public function save(User $user): void
    {
        $this->em->persist($user);
        $this->em->flush();
    }

    public function remove(User $user): void
    {
        $this->em->remove($user);
    }

    public function clear(): void
    {
        $this->em->clear();
    }
}
