<?php

declare(strict_types=1);

namespace App\Http\V1;

use App\Components\Router\Route;
use App\Helpers\OpenApi\ResponseSuccessful;
use App\Helpers\OpenApi\Security;
use App\Http\Middleware\Identity\Authenticate;
use App\Http\Response\JsonResponse;
use App\Modules\Battle\Entity\BattleUser\BattleUserRepository;
use App\Modules\OAuth\Entity\Client\Client;
use App\Modules\OAuth\Generator\AccessTokenGenerator;
use App\Modules\OAuth\Generator\BearerTokenGenerator;
use App\Modules\OAuth\Generator\RefreshTokenGenerator;
use DomainException;
use Exception;
use OpenApi\Attributes as OA;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Post(
    path: '/battle/{id}/token',
    description: 'Получение JWT токена для участия в танковом бое',
    summary: 'Получение JWT токена для участия в танковом бое',
    security: [Security::BEARER_AUTH],
    tags: ['Battle'],
    responses: [new ResponseSuccessful()]
)]
#[OA\Parameter(
    name: 'id',
    description: 'Идентификатор игры',
    in: 'path',
    required: true,
    schema: new OA\Schema(
        type: 'integer',
        format: 'int64'
    ),
    example: 1
)]
final readonly class BattleTokenAction implements RequestHandlerInterface
{
    public function __construct(
        private BattleUserRepository $battleUserRepository,
        private AccessTokenGenerator $accessTokenGenerator,
        private RefreshTokenGenerator $refreshTokenGenerator,
        private ContainerInterface $container
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);
        $battleId = Route::getArgumentToInt($request, 'id');

        $this->checkIsMember($battleId, $identity->id);

        return new JsonResponse([
            $this->getTokens($battleId, $identity->id),
        ]);
    }

    private function checkIsMember(int $battleId, int $userId): void
    {
        $isMember = $this->battleUserRepository->isMember($battleId, $userId);

        if (!$isMember) {
            throw new DomainException('You are not a member');
        }
    }

    /** @throws ContainerExceptionInterface|Exception|NotFoundExceptionInterface */
    private function getTokens(int $battleId, int $userId): array
    {
        /**
         * @psalm-suppress MixedArrayAccess
         * @var array{
         *    encryption_key:string,
         * } $config
         */
        $config = $this->container->get('config')['oauth'];

        $token = new BearerTokenGenerator(
            accessTokenGenerator: $this->accessTokenGenerator,
            refreshTokenGenerator: $this->refreshTokenGenerator,
            encryptionKeyPath: $config['encryption_key']
        );

        return $token->generate(
            client: new Client(
                identifier: 'server',
                name: 'SERVER',
                redirectUri: 'default'
            ),
            userId: (string)$userId,
            battleId: $battleId,
        );
    }
}
