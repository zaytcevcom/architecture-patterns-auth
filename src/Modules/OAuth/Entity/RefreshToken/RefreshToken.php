<?php

declare(strict_types=1);

namespace App\Modules\OAuth\Entity\RefreshToken;

use Doctrine\ORM\Mapping as ORM;
use DomainException;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\RefreshTokenTrait;

#[ORM\Entity]
#[ORM\Table(name: 'oauth_refresh_tokens')]
#[ORM\Index(name: 'IDX_SEARCH', fields: ['identifier'])]
#[ORM\Index(name: 'IDX_USER_ID', fields: ['userIdentifier'])]
class RefreshToken implements RefreshTokenEntityInterface
{
    use EntityTrait;
    use RefreshTokenTrait;

    /** @psalm-suppress MissingPropertyType */
    #[ORM\Column(type: 'string', length: 80)]
    #[ORM\Id]
    protected $identifier;

    /** @psalm-suppress MissingPropertyType */
    #[ORM\Column(type: 'datetime_immutable')]
    protected $expiryDateTime;

    #[ORM\Column(type: 'integer', nullable: false)]
    private ?string $userIdentifier = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $locale = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $ipAddress = null;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $userAgent = null;

    #[ORM\Column(type: 'integer', options: ['default' => 0])]
    private int $createdAt = 0;

    public function setAccessToken(AccessTokenEntityInterface $accessToken): void
    {
        $this->accessToken = $accessToken;
        $this->userIdentifier = (string)$accessToken->getUserIdentifier();
    }

    public function getUserIdentifier(): ?string
    {
        if (null === $this->userIdentifier) {
            throw new DomainException('Id not set');
        }

        return $this->userIdentifier;
    }

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function setLocale(?string $locale): void
    {
        $this->locale = $locale;
    }

    public function getIpAddress(): ?string
    {
        return $this->ipAddress;
    }

    public function setIpAddress(?string $ipAddress): void
    {
        $this->ipAddress = $ipAddress;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function setUserAgent(?string $userAgent): void
    {
        $this->userAgent = $userAgent;
    }

    public function getCreatedAt(): int
    {
        return $this->createdAt;
    }

    public function setCreatedAt(int $createdAt): void
    {
        $this->createdAt = $createdAt;
    }
}
