<?php

namespace Reqres\Interface\Http\Connector;

use Reqres\Data\NewUser;
use Reqres\Data\User;
use Reqres\Data\UserCollection;

interface UserApiInterface
{
    /**
     * Retrieves a user by their ID.
     *
     * @param int $id
     *
     * @return User|null
     */
    public function getUserById(int $id): ?User;

    /**
     * Creates a new user record.
     *
     * @param NewUser $data
     *
     * @return User|null
     */
    public function createUser(NewUser $data): ?User;

    /**
     * Retrieves a paginated list of users.
     *
     * @param int $page
     * @param int $perPage
     *
     * @return UserCollection
     */
    public function getUsers(int $page = 1, int $perPage = 10): UserCollection;
}