<?php

namespace Reqres\Http\Request;

use Reqres\Data\NewUser;
use Reqres\Data\User;
use Saloon\Contracts\Body\HasBody;
use Saloon\Enums\Method;
use Saloon\Http\Request;
use Saloon\Http\Response;
use Saloon\Traits\Body\HasJsonBody;

class CreateUserRequest extends Request implements HasBody
{
    use HasJsonBody;

    protected Method $method = Method::POST;

    public function __construct(protected NewUser $data)
    {
    }

    /**
     * @inheritDoc
     */
    public function resolveEndpoint(): string
    {
        return '/users';
    }

    /**
     * @inheritDoc
     */
    public function defaultBody(): array
    {
        return [
            'name' => $this->data->name,
            'job' => $this->data->job,
        ];
    }

    /**
     * @inheritDoc
     *
     * @throws \JsonException
     */
    public function createDtoFromResponse(Response $response): mixed
    {
        $data = $response->json();

        return new User(
            id: (int) $data['id'],
            firstName: $data['name'],
            job: $data['job'],
            createdAt: $data['createdAt'],
        );
    }
}