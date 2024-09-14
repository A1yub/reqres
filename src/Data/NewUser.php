<?php

namespace Reqres\Data;

use JsonSerializable;

class NewUser implements JsonSerializable
{
    public function __construct(
        public string $name,
        public string $job,
    ) {}

    /**
     * Serialise the object to an array.
     *
     * @return array
     */
    public function __serialize(): array
    {
        return [
            'name' => $this->name,
            'job' => $this->job,
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