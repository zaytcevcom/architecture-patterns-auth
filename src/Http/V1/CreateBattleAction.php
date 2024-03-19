<?php

declare(strict_types=1);

namespace App\Http\V1;

use App\Components\Serializer\Denormalizer;
use App\Components\Validator\Validator;
use App\Helpers\OpenApi\ResponseSuccessful;
use App\Helpers\OpenApi\Security;
use App\Http\Middleware\Identity\Authenticate;
use App\Http\Response\JsonResponse;
use App\Modules\Battle\Command\Create\Command;
use App\Modules\Battle\Command\Create\Handler;
use Exception;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Symfony\Component\Serializer\Exception\ExceptionInterface;

#[OA\Post(
    path: '/battle',
    description: 'Создание танкового боя',
    summary: 'Создание танкового боя',
    security: [Security::BEARER_AUTH],
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'userIds',
                    type: 'array',
                    items: new OA\Items(),
                    example: [],
                ),
            ]
        )
    ),
    tags: ['Battle'],
    responses: [new ResponseSuccessful()]
)]
final readonly class CreateBattleAction implements RequestHandlerInterface
{
    public function __construct(
        private Handler $handler,
        private Validator $validator,
        private Denormalizer $denormalizer,
    ) {}

    /** @throws Exception|ExceptionInterface */
    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        $identity = Authenticate::getIdentity($request);

        $command = $this->denormalizer->denormalize(
            data: array_merge((array)$request->getParsedBody(), [
                'userId' => $identity->id,
            ]),
            type: Command::class
        );

        $this->validator->validate($command);

        $battleId = $this->handler->handle($command);

        return new JsonResponse([
            'id' => $battleId,
        ]);
    }
}
