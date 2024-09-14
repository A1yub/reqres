<?php

namespace Reqres\Data;

use JsonSerializable;

class User implements JsonSerializable
{
    public function __construct(
        public int $id,
        public string $firstName,
        public ?string $lastName = null,
        public ?string $email = null,
        public ?string $avatar = null,
        public ?string $job = null,
        public ?string $createdAt = null,
    ) {}

    /**
     * Get the name, a combination of first and last name.
     *
     * @return string
     */
    public function getName(): string
    {
        return trim($this->firstName.' '.$this->lastName);
    }

    /**
     * Serialise the object to an array.
     *
     * @return array
     */
    public function __serialize(): array
    {
        return [
            'id' => $this->id,
            'first_name' => $this->firstName,
            'last_name' => $this->lastName,
            'email' => $this->email,
            'avatar' => $this->avatar,
            'job' => $this->job,
            'created_at' => $this->createdAt,
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