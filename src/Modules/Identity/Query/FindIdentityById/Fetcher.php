<?php

declare(strict_types=1);

namespace App\Modules\Identity\Query\FindIdentityById;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;

final readonly class Fetcher
{
    public function __construct(
        private Connection $connection
    ) {}

    /** @throws Exception */
    public function fetch(string $id): ?Identity
    {
        $result = $this->connection->createQueryBuilder()
            ->select('id')
            ->from('users')
            ->where('id = :id')
            ->setParameter('id', $id)
            ->executeQuery();

        /** @var array{id: int}|false $row */
        $row = $result->fetchAssociative();

        if ($row === false) {
            return null;
        }

        return new Identity(
            id: (string)$row['id'],
        );
    }
}
