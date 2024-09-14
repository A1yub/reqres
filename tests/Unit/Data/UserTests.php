<?php

namespace Tests\Unit\Data;

use DateTime;
use Reqres\Data\User;

beforeEach(function () {
    $this->id = 1;
    $this->firstName = 'John';
    $this->lastName = 'Doe';
    $this->email = 'john@test.test';
    $this->avatar = 'https://test.test/avatar.jpg';
    $this->job = 'Software Engineer';
    $this->createdAt = (new DateTime())->format('c');

    $this->user = new User(
        id: $this->id,
        firstName: $this->firstName,
        lastName: $this->lastName,
        email: $this->email,
        avatar: $this->avatar,
        job: $this->job,
        createdAt: $this->createdAt,
    );
});

test('can create instance', function () {
    $user = new User(
        id: $this->id,
        firstName: $this->firstName,
        lastName: $this->lastName,
        email: $this->email,
        avatar: $this->avatar,
        job: $this->job,
        createdAt: $this->createdAt,
    );

    expect($user)
        ->toBeInstanceOf(User::class);
});

test('can serialize to array', function () {
    $expected = [
        'id' => $this->id,
        'first_name' => $this->firstName,
        'last_name' => $this->lastName,
        'email' => $this->email,
        'avatar' => $this->avatar,
        'job' => $this->job,
        'created_at' => $this->createdAt,
    ];

    expect($this->user->__serialize())->toBe($expected)
        ->and($this->user->jsonSerialize())->toBe($expected);
});

test('it returns the correct values', function () {
    $id = 2;
    $firstName = 'Aiyub';
    $lastName = 'Bawa';
    $email = 'aiyub@test.test';
    $avatar = 'https://test.test/avatar.jpg';
    $job = 'Full Stack Developer';
    $createdAt = (new DateTime())->format('c');

    $newUser = new User(
        id: $id,
        firstName: $firstName,
        lastName: $lastName,
        email: $email,
        avatar: $avatar,
        job: $job,
        createdAt: $createdAt,
    );

    expect($newUser->id)->toBe($id)
        ->and($newUser->firstName)->toBe($firstName)
        ->and($newUser->lastName)->toBe($lastName)
        ->and($newUser->email)->toBe($email)
        ->and($newUser->avatar)->toBe($avatar)
        ->and($newUser->job)->toBe($job)
        ->and($newUser->createdAt)->toBe($createdAt);
});

test('it can get the name', function () {
    expect($this->user->getName())->toBe($this->firstName.' '.$this->lastName);
});

test('it formats name correctly if last name is missing', function () {
    expect((new User(
        id: 1,
        firstName: 'Aiyub',
    ))->getName())->toBe('Aiyub');
});

test('it trims trailing and leading spaces from name', function (string $firstName, ?string $lastName) {
    $expected = 'Aiyub Bawa';
    if ($firstName && !$lastName) {
        $expected = 'Aiyub';
    }

    expect((new User(
        id: 1,
        firstName: $firstName,
        lastName: $lastName,
    ))->getName())->toBe($expected);
})->with([
    ['Aiyub', 'Bawa'],
    [' Aiyub', 'Bawa'],
    ['Aiyub', 'Bawa '],
    [' Aiyub', 'Bawa '],
    [' Aiyub ', ''],
    [' Aiyub', ''],
    ['Aiyub ', ''],
]);