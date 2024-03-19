<?php

declare(strict_types=1);

namespace App\Modules\Identity\Query\FindIdByCredentials;

use App\Modules\Identity\Service\PasswordHasher;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class Fetcher
{
    public function __construct(
        private Connection $connection,
        private PasswordHasher $passwordHasher,
    ) {}

    /** @throws Exception */
    public function fetch(Query $query): ?User
    {
        $result = $this->connection->createQueryBuilder()
            ->select(
                'id',
                'password_hash',
            )
            ->from('users')
            ->where('login = :login')
            ->setParameter('login', mb_strtolower($query->username))
            ->executeQuery();

        /** @var array{id: int, password_hash: ?string}|false */
        $row = $result->fetchAssociative();

        if ($row === false) {
            return null;
        }

        $hash = $row['password_hash'];

        if ($hash === null) {
            return null;
        }

        if (!$this->passwordHasher->validate($query->password, $hash)) {
            return null;
        }

        return new User(
            id: $row['id'],
            isActive: true
        );
    }
}
