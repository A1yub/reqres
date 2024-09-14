<?php

namespace Reqres\Data;

use JsonSerializable;

class Pagination implements JsonSerializable
{
    public function __construct(
        public int $page,
        public int $perPage,
        public int $total,
        public int $totalPages,
    ) {}

    /**
     * Serialise the object to an array.
     *
     * @return array
     */
    public function __serialize(): array
    {
        return [
            'page' => $this->page,
            'per_page' => $this->perPage,
            'total' => $this->total,
            'total_pages' => $this->totalPages,
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