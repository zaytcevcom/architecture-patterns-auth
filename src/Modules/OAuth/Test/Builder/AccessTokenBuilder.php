<?php

declare(strict_types=1);

namespace App\Modules\OAuth\Test\Builder;

use App\Modules\OAuth\Entity\AccessToken\AccessToken;
use App\Modules\OAuth\Entity\Client\Client;
use App\Modules\OAuth\Entity\Scope\Scope;

final class AccessTokenBuilder
{
    /**
     * @var Scope[]
     */
    private array $scopes;
    private ?string $userIdentifier = null;

    public function __construct()
    {
        $this->scopes = [new Scope('common')];
    }

    public function withUserIdentifier(string $identifier): self
    {
        $clone = clone $this;
        $clone->userIdentifier = $identifier;
        return $clone;
    }

    public function build(Client $client): AccessToken
    {
        $token = new AccessToken($client, $this->scopes);

        if ($this->userIdentifier !== null) {
            $token->setUserIdentifier($this->userIdentifier);
        }

        return $token;
    }
}
