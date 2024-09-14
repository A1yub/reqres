<?php

namespace Tests\Unit\Data;

use Reqres\Data\NewUser;

beforeEach(function () {
    $this->name = 'John Doe';
    $this->job = 'Software Engineer';

    $this->newUser = new NewUser(
        name: $this->name,
        job: $this->job,
    );
});

test('can create instance', function () {
    $newUser = new NewUser(
        name: $this->name,
        job: $this->job,
    );

    expect($newUser)
        ->toBeInstanceOf(NewUser::class);
});

test('it can be serialised', function () {
    $expected = [
        'name' => $this->name,
        'job' => $this->job,
    ];

    expect($this->newUser->__serialize())->toBe($expected)
        ->and($this->newUser->jsonSerialize())->toBe($expected);
});

test('it returns the correct values', function () {
    $name = 'Aiyub Bawa';
    $job = 'Full Stack Developer';

    $newUser = new NewUser(
        name: $name,
        job: $job,
    );

    expect($newUser->name)->toBe($name)
        ->and($newUser->job)->toBe($job);
});