<?php

declare(strict_types=1);

namespace App\Http\Middleware\Identity;

use DateTimeZone;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Exception;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Signer\Rsa\Sha256;
use Lcobucci\JWT\Token\Plain;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\StrictValidAt;
use Lcobucci\JWT\Validation\RequiredConstraintsViolated;
use League\OAuth2\Server\AuthorizationValidators\AuthorizationValidatorInterface;
use League\OAuth2\Server\CryptKey;
use League\OAuth2\Server\CryptTrait;
use League\OAuth2\Server\Exception\OAuthServerException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Psr\Http\Message\ServerRequestInterface;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
final class BearerTokenValidator implements AuthorizationValidatorInterface
{
    use CryptTrait;

    protected CryptKey $publicKey;

    private Configuration $jwtConfiguration;

    public function __construct(
        private readonly AccessTokenRepositoryInterface $accessTokenRepository,
        /** @var non-empty-string $contents */
        private readonly string $contents = 'z',
    ) {}

    public function setPublicKey(CryptKey $key): void
    {
        $this->publicKey = $key;

        $this->initJwtConfiguration();
    }

    public function validateAuthorization(ServerRequestInterface $request): ServerRequestInterface
    {
        if ($request->hasHeader('authorization') === false) {
            throw OAuthServerException::accessDenied('Missing "Authorization" header');
        }

        $header = $request->getHeader('authorization');
        $jwt = trim((string)preg_replace('/^\s*Bearer\s/', '', $header[0]));

        if (empty($jwt)) {
            throw OAuthServerException::accessDenied('Jwt expects non empty string');
        }

        try {
            /** @var Plain $token */
            $token = $this->jwtConfiguration->parser()->parse($jwt);
        } catch (Exception $exception) {
            throw OAuthServerException::accessDenied($exception->getMessage(), null, $exception);
        }

        try {
            $constraints = $this->jwtConfiguration->validationConstraints();
            $this->jwtConfiguration->validator()->assert($token, ...$constraints);
        } catch (RequiredConstraintsViolated) {
            throw OAuthServerException::accessDenied('Access token could not be verified');
        }

        $claims = $token->claims();

        if ($this->accessTokenRepository->isAccessTokenRevoked((string)$claims->get('jti'))) {
            throw OAuthServerException::accessDenied('Access token has been revoked');
        }

        return $request
            ->withAttribute('oauth_access_token_id', $claims->get('jti'))
            ->withAttribute('oauth_client_id', $this->convertSingleRecordAudToString($claims->get('aud')))
            ->withAttribute('oauth_user_id', $claims->get('sub'))
            ->withAttribute('oauth_user_role', $claims->get('role'))
            ->withAttribute('oauth_scopes', $claims->get('scopes'));
    }

    private function initJwtConfiguration(): void
    {
        $this->jwtConfiguration = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText(
                contents: $this->contents
            )
        );

        /** @var non-empty-string $contents */
        $contents = $this->publicKey->getKeyContents();

        $this->jwtConfiguration->setValidationConstraints(
            new StrictValidAt(new SystemClock(new DateTimeZone(date_default_timezone_get()))),
            new SignedWith(
                new Sha256(),
                InMemory::plainText(
                    contents: $contents,
                    passphrase: $this->publicKey->getPassPhrase() ?? ''
                )
            )
        );
    }

    private function convertSingleRecordAudToString(mixed $aud): mixed
    {
        return \is_array($aud) && \count($aud) === 1 ? $aud[0] : $aud;
    }
}
