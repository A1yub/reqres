<?php

namespace Tests\Unit\Data;

use DateTime;
use Reqres\Data\Pagination;
use Reqres\Data\User;
use Reqres\Data\UserCollection;

beforeEach(function () {
    $this->user = new User(
        id: 1,
        firstName: 'John',
        lastName: 'Doe',
        email: 'john@test.test',
        avatar: 'https://test.test/avatar.jpg',
        job: 'Software Engineer',
        createdAt: (new DateTime())->format('c'),
    );

    $this->pagination = new Pagination(
        page: 1,
        perPage: 10,
        total: 1,
        totalPages: 1,
    );
});

test('can create instance', function () {
    $collection = new UserCollection(
        users: [$this->user],
        pagination: $this->pagination,
    );

    expect($collection)
        ->toBeInstanceOf(UserCollection::class)
        ->and($collection->pagination)->toBeInstanceOf(Pagination::class)
        ->and($collection->data[0])->toBeInstanceOf(User::class)
        ->and($collection->data[0])->toBe($this->user)
        ->and(count($collection->data))->toBe(1)
        ->and($collection->pagination->page)->toBe(1)
        ->and($collection->pagination->perPage)->toBe(10)
        ->and($collection->pagination->total)->toBe(1)
        ->and($collection->pagination->totalPages)->toBe(1);
});

test('it throws exception when instantiated with invalid data', function () {
    new UserCollection(
        users: ['id' => 1],
        pagination: $this->pagination,
    );
})->throws(\InvalidArgumentException::class);

test('it can be serialised', function () {
    $expected = [
        'data' => [$this->user->jsonSerialize()],
        'pagination' => $this->pagination->jsonSerialize(),
    ];

    $collection = new UserCollection(
        users: [$this->user],
        pagination: $this->pagination,
    );

    $serialised = $collection->jsonSerialize();

    expect($serialised)->toBe($expected)
        ->and(count($serialised['data']))->toBe(1)
        ->and($serialised['data'][0]['first_name'])->toBe($this->user->firstName)
        ->and($serialised['data'][0]['last_name'])->toBe($this->user->lastName)
        ->and($serialised['data'][0]['email'])->toBe($this->user->email)
        ->and($serialised['data'][0]['avatar'])->toBe($this->user->avatar)
        ->and($serialised['data'][0]['job'])->toBe($this->user->job)
        ->and($serialised['data'][0]['created_at'])->toBe($this->user->createdAt)
        ->and($serialised['pagination']['page'])->toBe($this->pagination->page)
        ->and($serialised['pagination']['per_page'])->toBe($this->pagination->perPage)
        ->and($serialised['pagination']['total'])->toBe($this->pagination->total)
        ->and($serialised['pagination']['total_pages'])->toBe($this->pagination->totalPages);
});

test('it can get a user by their ID', function () {
    $collection = new UserCollection(
        users: [$this->user],
        pagination: $this->pagination,
    );

    expect($collection->getUserById(1))->toBe($this->user)
        ->and($collection->getUserById(2))->toBeNull();
});

test('it can get a user by their email address', function () {
    $jane = new User(
        id: 2,
        firstName: 'Jane',
        email: 'jane@test.test',
    );

    $collection = new UserCollection(
        users: [$this->user, $jane],
        pagination: $this->pagination,
    );

    expect($collection->getUserByEmail('test1@test.test'))->toBeNull()
        ->and($collection->getUserByEmail('john@test.test'))->toBe($this->user)
        ->and($collection->getUserByEmail('jane@test.test'))->toBe($jane);
});

test('it can get users by their name', function () {
    $jane = new User(
        id: 2,
        firstName: 'Jane',
        email: 'jane@test.test',
    );

    $jannet = new User(
        id: 2,
        firstName: 'Jannet',
        lastName: 'Doe',
        email: 'jane@test.test',
    );

    $collection = new UserCollection(
        users: [$this->user, $jane, $jannet],
        pagination: $this->pagination,
    );

    expect(count($collection->getUsersByName('John')))->toBe(1)
        ->and($collection->getUsersByName('John')[0])->toBe($this->user)
        ->and(count($collection->getUsersByName('Jane')))->toBe(1)
        ->and($collection->getUsersByName('Jane')[0])->toBe($jane)
        ->and(count($collection->getUsersByName('Jannet', 'Doe')))->toBe(1)
        ->and($collection->getUsersByName('Jannet', 'Doe')[0])->toBe($jannet)
        ->and(count($collection->getUsersByName('Jane', 'Doe')))->toBe(0);

});

test('it can get users by their job', function () {
    $jane = new User(
        id: 2,
        firstName: 'Jane',
        job: 'Software Engineer',
    );

    $jannet = new User(
        id: 2,
        firstName: 'Jannet',
        lastName: 'Doe',
        job: 'Designer',
    );

    $collection = new UserCollection(
        users: [$this->user, $jane, $jannet],
        pagination: $this->pagination,
    );

    expect(count($collection->getUsersByJob('Software Engineer')))->toBe(2)
        ->and($collection->getUsersByJob('Software Engineer'))->toContain($this->user)
        ->and($collection->getUsersByJob('Software Engineer'))->toContain($jane)
        ->and(count($collection->getUsersByJob('Designer')))->toBe(1)
        ->and($collection->getUsersByJob('Designer')[0])->toBe($jannet)
        ->and(count($collection->getUsersByJob('Software Developer')))->toBe(0);
});