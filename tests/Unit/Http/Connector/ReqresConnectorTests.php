<?php

use Reqres\Data\NewUser;
use Reqres\Data\User;
use Reqres\Data\UserCollection;
use Reqres\Http\Connector\ReqresConnector;
use Reqres\Http\Request\CreateUserRequest;
use Reqres\Http\Request\GetUserRequest;
use Reqres\Http\Request\GetUsersRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('can create instance with no base url', function () {
    $defaultBaseUrl = 'https://reqres.in/api';
    $connector = new ReqresConnector();

    expect($connector->resolveBaseUrl())
        ->not()->toBe(null)
        ->toBeString($defaultBaseUrl)
        ->toBeUrl($connector->resolveBaseUrl());
});

test('can create instance with custom base url', function () {
    $customBaseUrl = 'https://example.com/api';
    $connector = new ReqresConnector($customBaseUrl);

    expect($connector->resolveBaseUrl())
        ->not()->toBe(null)
        ->toBeString($customBaseUrl)
        ->toBeUrl($connector->resolveBaseUrl());
});

test('new method can create instance with no base url', function () {
    $defaultBaseUrl = 'https://reqres.in/api';
    $connector = new ReqresConnector();

    expect($connector->resolveBaseUrl())
        ->not()->toBe(null)
        ->toBeString($defaultBaseUrl)
        ->toBeUrl($connector->resolveBaseUrl());
});

test('new method can create instance with custom base url', function () {
    $customBaseUrl = 'https://example.com/api';
    $connector = new ReqresConnector($customBaseUrl);

    expect($connector->resolveBaseUrl())
        ->not()->toBe(null)
        ->toBeString($customBaseUrl)
        ->toBeUrl($connector->resolveBaseUrl());
});

test('default headers are set', function () {
    $connector = new ReqresConnector();

    expect($connector->headers()->all())
        ->not()->toBeEmpty()
        ->toHaveKey('Content-Type', 'application/json')
        ->toHaveKey('Accept', 'application/json');
});

test('can get user by Id', function () {
    $user = new User(
        id: 1,
        firstName: 'Aiyub',
        lastName: 'Bawa',
        email: 'aiyub@test.test',
        avatar: 'https://ui-avatars.com/api/?name=Aiyub+Bawa',
    );

    $response = MockResponse::make([
        'data' => [
            'id' => $user->id,
            'first_name' => $user->firstName,
            'last_name' => $user->lastName,
            'email' => $user->email,
            'avatar' => $user->avatar,
        ]
    ], 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        GetUserRequest::class => $response,
    ]);

    $connector = new ReqresConnector();
    $connector->withMockClient($client);

    $record = (new ReqresConnector())
        ->withMockClient($client)
        ->getUserById($user->id);

    expect($record)
        ->toBeInstanceOf(User::class)
        ->and($record->id)->toBe($user->id);
});

it('throws an exception if getting a non-existent user', function () {
    $response = MockResponse::make([], 404, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        GetUserRequest::class => $response,
    ]);

    (new ReqresConnector())
        ->withMockClient($client)
        ->getUserById(1000);
})->throws(\Saloon\Exceptions\Request\Statuses\NotFoundException::class);

test('can create user', function () {
    $user = new User(
        id: 1,
        firstName: 'Aiyub Bawa',
        job: 'Software Engineer',
        createdAt: (new DateTime())->format('c'),
    );

    $response = MockResponse::make([
        'id' => $user->id,
        'name' => $user->firstName,
        'job' => $user->job,
        'createdAt' => $user->createdAt,
    ], 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        CreateUserRequest::class => $response,
    ]);

    $record = (new ReqresConnector())
        ->withMockClient($client)
        ->createUser(new NewUser(
            name: $user->firstName,
            job: $user->job,
        ));

    expect($record)
        ->toBeInstanceOf(User::class)
        ->and($record->id)->not()->toBeNull()
        ->and($record->firstName)->toBe($user->firstName)
        ->and($record->job)->toBe($user->job)
        ->and($record->createdAt)->toBe($user->createdAt)
        ->and($record->lastName)->toBeNull()
        ->and($record->email)->toBeNull()
        ->and($record->avatar)->toBeNull();
});

it('throws an exception if creating a user with missing required data', function (string $name, string $job) {
    $response = MockResponse::make([], 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        CreateUserRequest::class => $response,
    ]);

    (new ReqresConnector())
        ->withMockClient($client)
        ->createUser(new NewUser(
            name: $name,
            job: $job,
        ));
})->with([
    ['', 'Software Engineer'],
    ['John Doe', ''],
    ['', ''],
])
    ->throws(\InvalidArgumentException::class);

test('can get paginated users list with default pagination parameters', function () {
    $response = MockResponse::make([
        'data' => [
            [
                'id' => 1,
                'first_name' => 'Aiyub',
                'last_name' => 'Bawa',
                'email' => 'aiyub@test.test',
                'avatar' => 'https://ui-avatars.com/api/?name=Aiyub+Bawa',
            ],
        ],
        'page' => 1,
        'per_page' => 1,
        'total' => 1,
        'total_pages' => 1,
    ], 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        GetUsersRequest::class => $response,
    ]);

    $users = (new ReqresConnector())
        ->withMockClient($client)
        ->getUsers();

    expect($users)
        ->toBeInstanceOf(UserCollection::class)
        ->and($users->data)->toHaveCount(1)
        ->and($users->pagination->page)->toBe(1)
        ->and($users->pagination->perPage)->toBe(1)
        ->and($users->pagination->total)->toBe(1)
        ->and($users->pagination->totalPages)->toBe(1);
});

test('can get paginated users list with custom pagination parameters', function () {
    $response = MockResponse::make([
        'data' => [
            [
                'id' => 1,
                'first_name' => 'Aiyub',
                'last_name' => 'Bawa',
                'email' => 'aiyub@test.test',
                'avatar' => 'https://ui-avatars.com/api/?name=Aiyub+Bawa',
            ],
            [
                'id' => 2,
                'first_name' => 'Other',
                'last_name' => 'User',
                'email' => 'otheruser@test.test',
                'avatar' => 'https://ui-avatars.com/api/?name=Other+User',
            ],
        ],
        'page' => 2,
        'per_page' => 2,
        'total' => 1,
        'total_pages' => 1,
    ], 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        GetUsersRequest::class => $response,
    ]);

    $users = (new ReqresConnector())
        ->withMockClient($client)
        ->getUsers(
            page: 2,
            perPage: 2
        );

    expect($users)
        ->toBeInstanceOf(UserCollection::class)
        ->and($users->data)->toHaveCount(2)
        ->and($users->pagination->page)->toBe(2)
        ->and($users->pagination->perPage)->toBe(2)
        ->and($users->pagination->total)->toBe(1)
        ->and($users->pagination->totalPages)->toBe(1);
});

test('it returns an empty user collection when specifying a page outside of valid range', function () {
    $response = MockResponse::make([
        'data' => [],
        'page' => 1000,
        'per_page' => 1,
        'total' => 1,
        'total_pages' => 1,
    ], 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        GetUsersRequest::class => $response,
    ]);

    $users = (new ReqresConnector())
        ->withMockClient($client)
        ->getUsers(
            page: 1000,
        );

    expect($users)
        ->toBeInstanceOf(UserCollection::class)
        ->and($users->data)->toHaveCount(0)
        ->and($users->pagination->page)->toBe(1000)
        ->and($users->pagination->perPage)->toBe(1)
        ->and($users->pagination->total)->toBe(1)
        ->and($users->pagination->totalPages)->toBe(1);
});

test('it does not fail when specifying a perPage outside of valid range', function () {
    $response = MockResponse::make([
        'data' => [],
        'page' => 1,
        'per_page' => 1000,
        'total' => 0,
        'total_pages' => 1,
    ], 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        GetUsersRequest::class => $response,
    ]);

    $users = (new ReqresConnector())
        ->withMockClient($client)
        ->getUsers(
            perPage: 1000,
        );

    expect($users)
        ->toBeInstanceOf(UserCollection::class)
        ->and($users->data)->toHaveCount(0)
        ->and($users->pagination->page)->toBe(1)
        ->and($users->pagination->perPage)->toBe(1000)
        ->and($users->pagination->total)->toBe(0)
        ->and($users->pagination->totalPages)->toBe(1);
});

test('it returns an empty user collection when specifying a page and perPage outside of valid range', function () {
    $response = MockResponse::make([
        'data' => [],
        'page' => 1000,
        'per_page' => 1000,
        'total' => 0,
        'total_pages' => 1,
    ], 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        GetUsersRequest::class => $response,
    ]);

    $users = (new ReqresConnector())
        ->withMockClient($client)
        ->getUsers(
            page: 1000,
            perPage: 1000,
        );

    expect($users)
        ->toBeInstanceOf(UserCollection::class)
        ->and($users->data)->toHaveCount(0)
        ->and($users->pagination->page)->toBe(1000)
        ->and($users->pagination->perPage)->toBe(1000)
        ->and($users->pagination->total)->toBe(0)
        ->and($users->pagination->totalPages)->toBe(1);
});