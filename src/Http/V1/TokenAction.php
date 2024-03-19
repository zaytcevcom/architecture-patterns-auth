<?php

declare(strict_types=1);

namespace App\Http\V1;

use App\Helpers\OpenApi\ResponseSuccessful;
use App\Http\Response\JsonDataResponse;
use DomainException;
use Exception;
use League\OAuth2\Server\AuthorizationServer;
use OpenApi\Attributes as OA;
use Psr\Http\Message\ResponseFactoryInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;

#[OA\Post(
    path: '/identity/token',
    description: '**Grant — Password** (Получения токенов доступа по логину и паролю).<br><br>
        **Обязательные параметры:**<br>
        - grant_type = **password**<br>
        - client_id<br>
        - username<br>
        - password<br>
        <br><br>
        **Grant — Refresh Token** (Обновление токенов доступа)<br><br>
        **Обязательные параметры:**<br>
        - grant_type = **refresh_token**<br>
        - client_id<br>
        - refresh_token<br>
        <br><br>
        **Grant — Authorization Code** (Обмен кода авторизации на токены доступа)<br><br>
        **Обязательные параметры:**<br>
        - grant_type = **authorization_code**<br>
        - client_id<br>
        - redirect_uri<br>
        - code<br>
        - code_verifier<br>
    ',
    summary: 'Авторизация',
    requestBody: new OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    property: 'grant_type',
                    type: 'string',
                    example: 'password'
                ),
                new OA\Property(
                    property: 'client_id',
                    type: 'string',
                    example: '1'
                ),
                new OA\Property(
                    property: 'username',
                    type: 'string',
                    example: 'Zaytcev'
                ),
                new OA\Property(
                    property: 'password',
                    type: 'string',
                    example: '1234567890'
                ),
                new OA\Property(
                    property: 'ipAddress',
                    type: 'string',
                    example: '108.108.0.0'
                ),
                new OA\Property(
                    property: 'userAgent',
                    type: 'string',
                    example: 'Safari'
                ),
            ]
        )
    ),
    tags: ['Identity'],
    responses: [new ResponseSuccessful()]
)]
final readonly class TokenAction implements RequestHandlerInterface
{
    public function __construct(
        private AuthorizationServer $server,
        private ResponseFactoryInterface $response,
    ) {}

    public function handle(ServerRequestInterface $request): ResponseInterface
    {
        try {
            $response = $this->server->respondToAccessTokenRequest($request, $this->response->createResponse());

            /** @var array{access_token: string, refresh_token:string} $data */
            $data = json_decode((string)$response->getBody(), true);
        } catch (Exception $exception) {
            throw new DomainException(
                message: $exception->getMessage(),
                code: (int)$exception->getCode()
            );
        }

        return new JsonDataResponse($data);
    }
}
