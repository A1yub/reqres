<?php

use Reqres\Data\Pagination;
use Reqres\Data\User;
use Reqres\Data\UserCollection;
use Reqres\Http\Connector\ReqresConnector;
use Reqres\Http\Request\GetUsersRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('can create an instance', function () {
    $request = new GetUsersRequest();

    expect($request)
        ->toBeInstanceOf(GetUsersRequest::class);
});

test('can create instance with page number and/or per page', function (int $page, int $perPage) {
    $request = new GetUsersRequest($page, $perPage);

    expect($request)
        ->toBeInstanceOf(GetUsersRequest::class)
        ->and($request->page)->toBe($page)
        ->and($request->perPage)->toBe($perPage);
})->with([
    [1, 1],
    [1, 10],
    [2, 20],
    [3, 30],
    [4, 40],
]);

test('can resolve endpoint', function () {
    $request = new GetUsersRequest();

    expect($request->resolveEndpoint())->toBe('/users');
});

test('default query contains correct attributes', function ($page, $perPage) {
    $request = new GetUsersRequest($page, $perPage);

    expect($request->defaultQuery())->toHaveCount(2)
        ->and($request->defaultQuery())->toHaveKey('page')
        ->and($request->defaultQuery())->toHaveKey('per_page')
        ->and($request->defaultQuery()['page'])->toBe(strval($page))
        ->and($request->defaultQuery()['per_page'])->toBe(strval($perPage));
})->with([
    [1, 1],
    [1, 10],
    [2, 20],
    [3, 30],
    [4, 40],
]);

test('can create DTO from response', function () {
    $user1 = [
        'id' => 1,
        'first_name' => 'Aiyub',
        'last_name' => 'Bawa',
        'email' => 'aiyub@test.test',
        'avatar' => 'https://ui-avatars.com/api/?name=Aiyub+Bawa',
    ];

    $users = [
        $user1,
    ];

    $response = MockResponse::make([
        'data' => $users,
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

    $connector = new ReqresConnector();
    $connector->withMockClient($client);

    $data = $connector->getUsers(1, 1);

    expect($data)
        ->toBeInstanceOf(UserCollection::class)
        ->and($data->pagination)->toBeInstanceOf(Pagination::class)
        ->and($data->pagination->page)->toBe(1)
        ->and($data->pagination->perPage)->toBe(1)
        ->and($data->pagination->total)->toBe(1)
        ->and($data->pagination->totalPages)->toBe(1)
        ->and($data->data)->toHaveCount(1)
        ->and($data->data[0])->toBeInstanceOf(User::class)
        ->and($data->data[0]->id)->toBe($user1['id'])
        ->and($data->data[0]->firstName)->toBe($user1['first_name'])
        ->and($data->data[0]->lastName)->toBe($user1['last_name'])
        ->and($data->data[0]->email)->toBe($user1['email'])
        ->and($data->data[0]->avatar)->toBe($user1['avatar']);
});

it('throws an exception if the response json data is malformed', function () {
    $response = MockResponse::make('{"data": [{"id": 1', 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        GetUsersRequest::class => $response,
    ]);

    $connector = new ReqresConnector();
    $connector->withMockClient($client);

    $connector->getUsers();
})->throws(JsonException::class);



