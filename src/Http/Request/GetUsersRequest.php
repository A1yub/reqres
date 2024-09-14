<?php

namespace Reqres\Http\Request;

use Reqres\Data\Pagination;
use Reqres\Data\User;
use Reqres\Data\UserCollection;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\RequestProperties\HasQuery;

class GetUsersRequest extends Request
{
    use HasQuery;

    protected Method $method = Method::GET;

    public function __construct(public int $page = 1, public int $perPage = 10)
    {
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
        return "/users";
    }

    /**
     * @inheritDoc
     */
    public function defaultQuery(): array
    {
        return [
            'page' => strval($this->page),
            'per_page' => strval($this->perPage),
        ];
    }

    /**
     * @inheritDoc
     *
     * @throws \JsonException
     */
    public function createDtoFromResponse(Response $response): mixed
    {
        $userData = $response->json('data');

        $pagination = new Pagination(
            page: $response->json('page'),
            perPage: $response->json('per_page'),
            total: $response->json('total'),
            totalPages: $response->json('total_pages'),
        );

        $users = array_map(
            fn ($user) => new User(
                id: $user['id'],
                firstName: $user['first_name'],
                lastName: $user['last_name'],
                email: $user['email'],
                avatar: array_key_exists('avatar', $user) ? $user['avatar'] : null,
            ),
            $userData
        );

        return new UserCollection(
            users: $users,
            pagination: $pagination,
        );
    }
}