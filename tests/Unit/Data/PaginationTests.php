<?php

namespace Tests\Unit\Data;

use Reqres\Data\Pagination;

beforeEach(function () {
    $this->page = 1;
    $this->perPage = 6;
    $this->total = 12;
    $this->totalPages = 2;

    $this->pagination = new Pagination(
        page: $this->page,
        perPage: $this->perPage,
        total: $this->total,
        totalPages: $this->totalPages,
    );
});

test('can create instance', function () {
    $newUser = new Pagination(
        page: $this->page,
        perPage: $this->perPage,
        total: $this->total,
        totalPages: $this->totalPages,
    );

    expect($newUser)
        ->toBeInstanceOf(Pagination::class);
});

test('it can be serialised', function () {
    $expected = [
        'page' => $this->page,
        'per_page' => $this->perPage,
        'total' => $this->total,
        'total_pages' => $this->totalPages,
    ];

    expect($this->pagination->__serialize())->toBe($expected)
        ->and($this->pagination->jsonSerialize())->toBe($expected);
});

test('it returns the correct values', function () {
    $page = 2;
    $perPage = 10;
    $total = 20;
    $totalPages = 2;

    $pagination = new Pagination(
        page: $page,
        perPage: $perPage,
        total: $total,
        totalPages: $totalPages,
    );

    expect($pagination->page)->toBe($page)
        ->and($pagination->perPage)->toBe($perPage)
        ->and($pagination->total)->toBe($total)
        ->and($pagination->totalPages)->toBe($totalPages);
});