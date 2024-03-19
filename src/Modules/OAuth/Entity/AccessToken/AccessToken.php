<?php

declare(strict_types=1);

namespace App\Modules\OAuth\Entity\AccessToken;

use DateTimeImmutable;
use Lcobucci\JWT\UnencryptedToken;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Entities\Traits\AccessTokenTrait;
use League\OAuth2\Server\Entities\Traits\EntityTrait;
use League\OAuth2\Server\Entities\Traits\TokenEntityTrait;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class AccessToken implements AccessTokenEntityInterface
{
    use AccessTokenTrait;
    use EntityTrait;
    use TokenEntityTrait;

    private ?int $battleId = null;

    /**
     * @param ScopeEntityInterface[] $scopes
     */
    public function __construct(ClientEntityInterface $client, array $scopes)
    {
        $this->setClient($client);

        foreach ($scopes as $scope) {
            $this->addScope($scope);
        }
    }

    public function __toString(): string
    {
        return $this->convertToJWT()->toString();
    }

    public function getBattleId(): ?int
    {
        return $this->battleId;
    }

    public function setBattleId(?int $battleId): void
    {
        $this->battleId = $battleId;
    }

    public function convertToJWT(): UnencryptedToken
    {
        $this->initJwtConfiguration();

        /** @var non-empty-string $userIdentifier */
        $userIdentifier = (string)$this->getUserIdentifier();

        /** @var non-empty-string $identifier */
        $identifier = (string)$this->getIdentifier();

        /** @var non-empty-string $clientIdentifier */
        $clientIdentifier = $this->getClient()->getIdentifier();

        return $this->jwtConfiguration->builder()
            ->permittedFor($clientIdentifier)
            ->identifiedBy($identifier)
            ->issuedAt(new DateTimeImmutable())
            ->canOnlyBeUsedAfter(new DateTimeImmutable())
            ->expiresAt($this->getExpiryDateTime())
            ->relatedTo($userIdentifier)
            ->withClaim('scopes', $this->getScopes())
            ->withClaim('battle', $this->getBattleId())
            ->getToken($this->jwtConfiguration->signer(), $this->jwtConfiguration->signingKey());
    }
}
