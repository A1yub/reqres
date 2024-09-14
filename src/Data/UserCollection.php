<?php

namespace Reqres\Data;

use JsonSerializable;

class UserCollection implements JsonSerializable
{
    public array $data = [];
    public Pagination $pagination;

    public function __construct(
        array $users,
        Pagination $pagination,
    ) {
        foreach ($users as $user) {
            if (! $user instanceof User) {
                throw new \InvalidArgumentException(
                    'The collection data must be an array of User instances.'
                );
            }
        }

        $this->data = $users;
        $this->pagination = $pagination;
    }

    /**
     * Get a user by their ID.
     *
     * @param int $id
     *
     * @return User|null
     */
    public function getUserById(int $id): ?User
    {
        foreach ($this->data as $user) {
            if ($user->id === $id) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Get a user by their email address.
     *
     * @param string $email
     *
     * @return User|null
     */
    public function getUserByEmail(string $email): ?User
    {
        foreach ($this->data as $user) {
            if ($user->email === $email) {
                return $user;
            }
        }

        return null;
    }

    /**
     * Get a user by their name (first and last).
     *
     * @param string $firstName
     * @param string|null $lastName
     *
     * @return array
     */
    public function getUsersByName(string $firstName, ?string $lastName = null): array
    {
        $users = [];

        foreach ($this->data as $user) {
            if ($lastName === null) {
                if ($user->firstName === $firstName) {
                    $users[] = $user;
                }
            } else {
                if ($user->firstName === $firstName && $user->lastName === $lastName) {
                    $users[] = $user;
                }
            }
        }

        return $users;
    }

    /**
     * Get a user by their job.
     *
     * @param string $job
     *
     * @return array
     */
    public function getUsersByJob(string $job): array
    {
        $users = [];

        foreach ($this->data as $user) {
            if ($user->job === $job) {
                $users[] = $user;
            }
        }

        return $users;
    }

    /**
     * Serialise the object to an array.
     *
     * @return array
     */
    public function __serialize(): array
    {
        $serialisedUsers = array_map(
            fn (User $user) => $user->jsonSerialize(),
            $this->data
        );

        return [
            'data' => $serialisedUsers,
            'pagination' => $this->pagination->jsonSerialize(),
        ];
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): mixed
    {
        return $this->__serialize();
    }
}