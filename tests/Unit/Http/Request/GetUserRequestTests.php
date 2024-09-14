<?php

use Reqres\Data\User;
use Reqres\Http\Connector\ReqresConnector;
use Reqres\Http\Request\GetUserRequest;
use Saloon\Exceptions\Request\Statuses\NotFoundException;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('can create an instance', function () {
    $request = new GetUserRequest(1);

    expect($request)
        ->toBeInstanceOf(GetUserRequest::class);
});

test('can resolve endpoint', function () {
    $request = new GetUserRequest(1);

    expect($request->resolveEndpoint())->toBe('/users/1');
});

test('can create DTO from response', function () {
    $id = 1;
    $firstName = 'Aiyub';
    $lastName = 'Bawa';
    $email = 'aiyub@test.test';
    $avatar = 'https://ui-avatars.com/api/?name=Aiyub+Bawa';

    $response = MockResponse::make([
        'data' => [
            'id' => $id,
            'first_name' => $firstName,
            'last_name' => $lastName,
            'email' => $email,
            'avatar' => $avatar,
        ]
    ], 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        GetUserRequest::class => $response,
    ]);
    $connector = new ReqresConnector();
    $connector->withMockClient($client);

    $user = $connector->getUserById(1);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->and($user->id)->toBe(1)
        ->and($user->firstName)->toBe($firstName)
        ->and($user->lastName)->toBe($lastName)
        ->and($user->email)->toBe($email)
        ->and($user->avatar)->toBe($avatar)
        ->and($user->job)->toBe(null)
        ->and($user->createdAt)->toBe(null);
});

it('throws an exception if the response json data is malformed', function () {
    $response = MockResponse::make('{"id": 1', 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        GetUserRequest::class => $response,
    ]);

    $connector = new ReqresConnector();
    $connector->withMockClient($client);

    $connector->getUserById(1);
})->throws(JsonException::class);

it('it handles lookup of non-existent user', function () {
    $response = MockResponse::make([], 404, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        GetUserRequest::class => $response,
    ]);

    $connector = new ReqresConnector();
    $connector->withMockClient($client);

    $connector->getUserById(1000);
})->throws(NotFoundException::class);



