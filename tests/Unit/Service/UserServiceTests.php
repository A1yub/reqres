<?php

use Reqres\Data\NewUser;
use Reqres\Data\Pagination;
use Reqres\Data\User;
use Reqres\Data\UserCollection;
use Reqres\Http\Connector\ReqresConnector;
use Reqres\Http\Request\CreateUserRequest;
use Reqres\Http\Request\GetUserRequest;
use Reqres\Http\Request\GetUsersRequest;
use Reqres\Interface\Http\Connector\UserApiInterface;
use Reqres\Service\UserService;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('can create instance', function () {
    $service = new UserService();

    expect($service)
        ->toBeInstanceOf(UserService::class);
});

test('can create instance with a custom client', function () {
    $client = new ReqresConnector();
    $service = new UserService($client);

    expect($service)
        ->toBeInstanceOf(UserService::class);

    $customClient = new class implements UserApiInterface {
        /**
         * @inheritDoc
         */
        public function getUserById(int $id): ?User
        {
            return new User(
                id: 1,
                firstName: 'John',
            );
        }

        /**
         * @inheritDoc
         */
        public function createUser(NewUser $data): ?User
        {
            return new User(
                id: 1,
                firstName: 'John',
                job: 'job',
            );
        }

        /**
         * @inheritDoc
         */
        public function getUsers(int $page = 1, int $perPage = 10): UserCollection
        {
            return new UserCollection(
                users: [],
                pagination: new Pagination(
                    page: 0,
                    perPage: 0,
                    total: 0,
                    totalPages: 0,
                )
            );
        }
    };

    $service = new UserService($customClient);

    expect($service)
        ->toBeInstanceOf(UserService::class);
});

test('can get user by ID', function () {
    $user = new User(
        id: 1,
        firstName: 'John',
        lastName: 'Doe',
        email: 'test@test.test',
        avatar: 'www.test.test',
        createdAt: (new DateTime())->format('c'),
    );

    $mockClient = new MockClient([
        GetUserRequest::class => MockResponse::make([
            'data' => [
                'id' => 1,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'email' => 'test@test.test',
            ],
        ], 200, [
            'Content-Type' => 'application/json',
        ]),
    ]);

    $service = new UserService(
        client: (new ReqresConnector())
            ->withMockClient($mockClient)
    );

    $record = $service->getUserById($user->id);

    expect($record)->toBeInstanceOf(User::class)
        ->and($record->id)->toBe($user->id)
        ->and($record->firstName)->toBe($user->firstName);
});

test('it can handle user not found', function () {
    $mockClient = new MockClient([
        GetUserRequest::class => MockResponse::make([
            'error' => 'Resource not found',
        ], 404, [
            'Content-Type' => 'application/json',
        ]),
    ]);

    $service = new UserService(
        client: (new ReqresConnector())
            ->withMockClient($mockClient)
    );

    $record = $service->getUserById(1);

    expect($record)->toBeNull();
});

test('can create a user', function () {
    $user = new NewUser(
        name: 'John Doe',
        job: 'Software Engineer',
    );

    $mockClient = new MockClient([
        CreateUserRequest::class => MockResponse::make([
            'id' => 23,
            'name' => $user->name,
            'job' => $user->job,
        ], 201, [
            'Content-Type' => 'application/json',
        ]),
    ]);

    $service = new UserService(
        client: (new ReqresConnector())
            ->withMockClient($mockClient)
    );

    $record = $service->createUser($user);

    expect($record)->toBeInt()
        ->and($record)->toBe(23);
});

test('it throws an exception when creating a user with missing data', function (string $name, string $job) {
    $mockClient = new MockClient([
        CreateUserRequest::class => MockResponse::make([
            'error' => 'Name is required.',
        ], 400, [
            'Content-Type' => 'application/json',
        ]),
    ]);

    $service = new UserService(
        client: (new ReqresConnector())
            ->withMockClient($mockClient)
    );

    $service->createUser(new NewUser(
        name: $name,
        job: $job,
    ));
})
    ->throws(Exception::class)
    ->with([
        ['', ''],
        ['John Doe', ''],
        ['', 'Software Engineer'],
    ]);

test('can get users', function () {
    $mockClient = new MockClient([
        GetUsersRequest::class => MockResponse::make([
            'data' => [
                [
                    'id' => 1,
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'test@test.test',
                ],
                [
                    'id' => 2,
                    'first_name' => 'Jane',
                    'last_name' => 'Doe',
                    'email' => 'jane@test.test',
                ],
            ],
            'page' => 1,
            'per_page' => 2,
            'total' => 2,
            'total_pages' => 1,
        ], 200, [
            'Content-Type' => 'application/json',
        ]),
    ]);

    $service = new UserService(
        client: (new ReqresConnector())
            ->withMockClient($mockClient)
    );

    $collection = $service->getUsers();

    expect($collection)->toBeInstanceOf(UserCollection::class)
        ->and($collection->data)->toHaveCount(2)
        ->and($collection->data[0])->toBeInstanceOf(User::class)
        ->and($collection->data[1])->toBeInstanceOf(User::class)
        ->and($collection->pagination->page)->toBe(1)
        ->and($collection->pagination->perPage)->toBe(2)
        ->and($collection->pagination->total)->toBe(2)
        ->and($collection->pagination->totalPages)->toBe(1);
});

test('can get users with pagination', function () {
    $mockClient = new MockClient([
        GetUsersRequest::class => MockResponse::make([
            'data' => [
                [
                    'id' => 1,
                    'first_name' => 'John',
                    'last_name' => 'Doe',
                    'email' => 'test@test.test',
                ],
            ],
            'page' => 2,
            'per_page' => 10,
            'total' => 10,
            'total_pages' => 10,
        ], 200, [
            'Content-Type' => 'application/json',
        ])
    ]);

    $service = new UserService(
        client: (new ReqresConnector())
            ->withMockClient($mockClient)
    );

    $collection = $service->getUsers(2, 2);

    expect($collection)->toBeInstanceOf(UserCollection::class)
        ->and($collection->data)->toHaveCount(1)
        ->and($collection->data[0])->toBeInstanceOf(User::class)
        ->and($collection->pagination->page)->toBe(2)
        ->and($collection->pagination->perPage)->toBe(10)
        ->and($collection->pagination->total)->toBe(10)
        ->and($collection->pagination->totalPages)->toBe(10);
});

