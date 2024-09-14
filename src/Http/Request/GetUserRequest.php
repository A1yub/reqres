<?php

namespace Reqres\Http\Request;

use Reqres\Data\User;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;

class GetUserRequest extends Request
{
    protected Method $method = Method::GET;

    public function __construct(protected int $userId)
    {
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
        return "/users/$this->userId";
    }

    /**
     * @inheritDoc
     *
     * @throws \JsonException
     */
    public function createDtoFromResponse(Response $response): mixed
    {
        $data = $response->json('data');

        return new User(
            id: (int) $data['id'],
            firstName: $data['first_name'],
            lastName: $data['last_name'],
            email: $data['email'],
            avatar: array_key_exists('avatar', $data) ? $data['avatar'] : null,
        );
    }
}