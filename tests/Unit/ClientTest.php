<?php

namespace Iamfredric\EduAdmin\Tests\Unit;

use Iamfredric\EduAdmin\Client;
use Iamfredric\EduAdmin\Exceptions\MissingClientCredentialsException;
use Iamfredric\EduAdmin\HttpFake;

test('a client cannot be instantiated without credentials', function () {
    Client::setCredentials('', '');
    expect(fn () => new Client())
        ->toThrow(MissingClientCredentialsException::class);
});

test('a client can be instantiated with credentials', function () {
    Client::setCredentials('username', 'password');

    expect(new Client())
        ->not->toThrow(MissingClientCredentialsException::class)
        ->toBeInstanceOf(Client::class);
});

it('can put', function () {
    $http = Client::fake();
    $client = new Client();

    $client->put('test', ['attributes' => 'here']);

    $http->assertCalled('https://api.eduadmin.se/v1/test');
});

it('can get', function () {
    $http = Client::fake();
    $client = new Client();

    $client->get('testget', ['attributes' => 'here']);

    $http->assertCalled('https://api.eduadmin.se/v1/testget');
});

it('throws exception if incorrect auth response', function () {
    $http = Client::fake()->when('token', []);
    $client = new Client();
    expect(fn () => $client->get('dsada', []))
        ->toThrow(\Exception::class, 'Unexpected response from EduAdmin');
});