<?php

declare(strict_types=1);

namespace App\Modules\Identity\Entity\User;

use Doctrine\ORM\Mapping as ORM;
use DomainException;

#[ORM\Entity]
#[ORM\Table(name: 'users')]
class User
{
    #[ORM\Id]
    #[ORM\Column(type: 'integer', unique: true, options: ['unsigned' => true])]
    #[ORM\GeneratedValue(strategy: 'AUTO')]
    private ?int $id = null;

    #[ORM\Column(type: 'string')]
    private string $login;

    #[ORM\Column(type: 'string', length: 255)]
    private string $passwordHash;

    private function __construct(
        string $login,
        string $passwordHash
    ) {
        $this->login = $login;
        $this->passwordHash = $passwordHash;
    }

    public static function signup(
        string $login,
        string $passwordHash
    ): self {
        return new self(
            login: $login,
            passwordHash: $passwordHash
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

    public function getLogin(): string
    {
        return $this->login;
    }

    public function setLogin(string $login): void
    {
        $this->login = $login;
    }

    public function getPasswordHash(): string
    {
        return $this->passwordHash;
    }

    public function setPasswordHash(string $passwordHash): void
    {
        $this->passwordHash = $passwordHash;
    }
}
