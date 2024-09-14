<?php

namespace Reqres\Service;

use InvalidArgumentException;
use Reqres\Data\NewUser;
use Reqres\Data\User;
use Reqres\Data\UserCollection;
use Reqres\Http\Connector\ReqresConnector;
use Reqres\Interface\Http\Connector\UserApiInterface;
use Saloon\Exceptions\Request\Statuses\NotFoundException;
use Saloon\Exceptions\SaloonException;

class UserService
{
    protected UserApiInterface $client;

    public function __construct(?UserApiInterface $client = null)
    {
        if (! $client) {
            $client = new ReqresConnector();
        }

        $this->client = $client;
    }

    /**
     * Retrieves a user by their ID.
     *
     * @param int $id
     *
     * @return User|null
     *
     * @throws SaloonException
     */
    public function getUserById(int $id): ?User
    {
        try {
            return $this->client->getUserById($id);
        } catch (NotFoundException $ex) {
            return null;
        }
    }

    /**
     * Creates a new user record.
     *
     * @param NewUser $data
     *
     * @return int
     *
     * @throws SaloonException
     * @throws InvalidArgumentException
     */
    public function createUser(NewUser $data): int
    {
        return $this->client->createUser($data)->id;
    }

    /**
     * Retrieves a paginated list of users.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return UserCollection
     *
     * @throws SaloonException
     */
    public function getUsers(int $page = 1, int $perPage = 10): UserCollection
    {
        return $this->client->getUsers(
            page: $page,
            perPage: $perPage,
        );
    }
}