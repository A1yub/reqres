<?php

namespace Reqres\Http\Connector;

use InvalidArgumentException;
use Reqres\Data\NewUser;
use Reqres\Data\User;
use Reqres\Data\UserCollection;
use Reqres\Http\Request\CreateUserRequest;
use Reqres\Http\Request\GetUserRequest;
use Reqres\Http\Request\GetUsersRequest;
use Reqres\Interface\Http\Connector\UserApiInterface;
use Saloon\Exceptions\Request\FatalRequestException;
use Saloon\Exceptions\Request\RequestException;
use Saloon\Http\Connector;
use Saloon\Traits\Plugins\AlwaysThrowOnErrors;

class ReqresConnector extends Connector implements UserApiInterface
{
    use AlwaysThrowOnErrors;

    protected string $baseUrl = 'https://reqres.in/api';

    public function __construct(?string $baseUrl = null) {
        if (! empty($baseUrl)) {
            $this->baseUrl = $baseUrl;
        }
    }

    /**
     * @inheritDoc
     */
    public function resolveBaseUrl(): string
    {
        return $this->baseUrl;
    }

    /**
     * @inheritDoc
     */
    protected function defaultConfig(): array
    {
        return [
            'timeout' => 5,
        ];
    }

    /**
     * @inheritDoc
     */
    protected function defaultHeaders(): array
    {
        return [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * @inheritDoc
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function getUserById(int $id): ?User
    {
        $response = $this->send(new GetUserRequest($id));
        return $response->dtoOrFail();
    }

    /**
     * @inheritDoc
     *
     * @throws FatalRequestException
     * @throws RequestException
     * @throws InvalidArgumentException
     */
    public function createUser(NewUser $data): ?User
    {
        if (empty($data->name)) {
            throw new InvalidArgumentException('Name is required.');
        }

        if (empty($data->job)) {
            throw new InvalidArgumentException('Job is required.');
        }

        $response = $this->send(new CreateUserRequest($data));
        return $response->dtoOrFail();
    }

    /**
     * @inheritDoc
     *
     * @throws FatalRequestException
     * @throws RequestException
     */
    public function getUsers(int $page = 1, int $perPage = 1): UserCollection
    {
        $response = $this->send(new GetUsersRequest($page, $perPage));
        return $response->dtoOrFail();
    }
}