<?php

use Reqres\Data\NewUser;
use Reqres\Data\User;
use Reqres\Http\Connector\ReqresConnector;
use Reqres\Http\Request\CreateUserRequest;
use Saloon\Http\Faking\MockClient;
use Saloon\Http\Faking\MockResponse;

test('can create an instance', function () {
    $name = 'John Doe';
    $job = 'Software Engineer';

    $request = new CreateUserRequest(
        new NewUser(
            name: $name,
            job: $job,
        )
    );

    expect($request)
        ->toBeInstanceOf(CreateUserRequest::class)
        ->and($request->defaultBody())->toBe([
            'name' => $name,
            'job' => $job,
        ]);
});

test('can resolve endpoint', function () {
    $request = new CreateUserRequest(
        new NewUser(
            name: 'John Doe',
            job: 'Software Engineer',
        )
    );

    expect($request->resolveEndpoint())->toBe('/users');
});

test('can create DTO from response', function () {
    $name = 'Aiyub Bawa';
    $job = 'Full Stack Developer';
    $createdAt = '2021-10-01T00:00:00Z';

    $response = MockResponse::make([
        'id' => 1,
        'name' => $name,
        'job' => $job,
        'createdAt' => $createdAt,
    ], 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        CreateUserRequest::class => $response,
    ]);
    $connector = new ReqresConnector();
    $connector->withMockClient($client);

    $user = $connector->createUser(new NewUser(
        name: $name,
        job: $job,
    ));

    expect($user)
        ->toBeInstanceOf(User::class)
        ->and($user->id)->toBe(1)
        ->and($user->firstName)->toBe($name)
        ->and($user->job)->toBe($job)
        ->and($user->createdAt)->toBe($createdAt);
});

it('throws an exception if the response json data is malformed', function () {
    $response = MockResponse::make('{"id": 1', 200, [
        'Content-Type' => 'application/json',
    ]);

    $client = new MockClient([
        CreateUserRequest::class => $response,
    ]);

    $connector = new ReqresConnector();
    $connector->withMockClient($client);

    $connector->createUser(new NewUser(
        name: 'John Doe',
        job: 'Software Engineer',
    ));
})->throws(JsonException::class);



