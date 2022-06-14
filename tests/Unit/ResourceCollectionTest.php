<?php

namespace Iamfredric\EduAdmin\Tests\Unit;

use Iamfredric\EduAdmin\Client;
use Iamfredric\EduAdmin\ResourceCollection;
use Iamfredric\EduAdmin\Resources\Customer;

it('can iterate over items', function () {
    $collection = new ResourceCollection(
        [['CustomerName' => 'One'], ['CustomerName' => 'Two']],
        2,
        10,
        0,
        null,
        'asc',
        Customer::class
    );

    $count = 0;
    foreach ($collection as $item) {
        $count++;
    }

    expect($count)->toBe(2);
});

it('can get an item by key', function () {
    $collection = new ResourceCollection(
        [['CustomerName' => 'One'], ['CustomerName' => 'Two']],
        2,
        10,
        0,
        null,
        'asc',
        Customer::class
    );

    expect($collection[0]->CustomerName)->toBe('One');
    expect($collection[1]->CustomerName)->toBe('Two');
});

it('can tell if there are any more records', function () {
    $collection = new ResourceCollection(
        [['CustomerName' => 'One'], ['CustomerName' => 'Two']],
        2,
        10,
        0,
        null,
        'asc',
        Customer::class
    );

    expect($collection->hasMore())->toBeFalse();


    $collection = new ResourceCollection(
        [['CustomerName' => 'One'], ['CustomerName' => 'Two']],
        3,
        2,
        0,
        null,
        'asc',
        Customer::class
    );

    expect($collection->hasMore())->toBeTrue();
});

it('can get the total number of records', function () {
    $collection = new ResourceCollection(
        [['CustomerName' => 'One'], ['CustomerName' => 'Two']],
        2,
        10,
        0,
        null,
        'asc',
        Customer::class
    );

    expect($collection->total())->toBe(2);
});

it('can get the next batch of records', function () {
    $http = Client::fake();
    Client::setCredentials('u', 'p');

    $collection = new ResourceCollection(
        [['CustomerName' => 'One'], ['CustomerName' => 'Two']],
        20,
        10,
        0,
        null,
        'asc',
        Customer::class
    );

    $collection->next();

    $http->assertCalled('https://api.eduadmin.se/v1/odata/Customers', [
        "params" => [
            '$count' => "true",
            '$top' => 10,
            '$skip' => 10
        ]
    ]);
});

it('can get the previous batch of records', function () {
    $http = Client::fake();
    Client::setCredentials('u', 'p');

    $collection = new ResourceCollection(
        [['CustomerName' => 'One'], ['CustomerName' => 'Two']],
        100,
        10,
        30,
        null,
        'asc',
        Customer::class
    );

    $collection->prev();

    $http->assertCalled('https://api.eduadmin.se/v1/odata/Customers', [
        "params" => [
            '$count' => "true",
            '$top' => 10,
            '$skip' => 20
        ]
    ]);
});
