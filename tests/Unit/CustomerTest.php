<?php

namespace Iamfredric\EduAdmin\Tests\Unit;

use Iamfredric\EduAdmin\Builder;
use Iamfredric\EduAdmin\Client;
use Iamfredric\EduAdmin\HttpFake;
use Iamfredric\EduAdmin\Resources\Customer;

it('can fetch all customers', function () {
    $client = Client::fake();

    $client->when(
        'https://api.eduadmin.se/v1/odata/Customers',
        ['value' => [[
            'CustomerId' => 1,
            'CustomerNumber' => '001',
            'CustomerName' => 'Acme INC',
        ], [
            'CustomerId' => 2,
            'CustomerNumber' => '002',
            'CustomerName' => 'Example INC',
        ]]]
    );

    $customers = Customer::all();

    expect($customers->first())->toBeInstanceOf(Customer::class);

    $client->assertCalled('https://api.eduadmin.se/v1/odata/Customers');
});

test('a customer can be updated', function () {
    $client = Client::fake();
    $customer = new Customer([
        'CustomerId' => 1,
        'CustomerNumber' => '001',
        'CustomerName' => 'Acme INC'
    ]);

    $customer->update(['CustomerName' => '123']);

    $client->assertCalled('https://api.eduadmin.se/v1/odata/Customers/1');
});

test('a customer can be updated via id', function () {
    $client = Client::fake();

    Customer::updateWhereId(1, [
        'CustomerName' => 'Test'
    ]);

    $client->assertCalled('https://api.eduadmin.se/v1/odata/Customers/1');
});

test('a single customer can be fetched', function () {
    $client = Client::fake(function () {
        return [
            'v1/odata/Customers/1' => [
                'CustomerId' => 1,
                'CustomerNumber' => "001",
                'CustomerName' => "Acme INC",
                'Address' => "Test street 1"
            ]
        ];
    });

    $customer = Customer::find(1);

    expect($customer)->toBeInstanceOf(Customer::class);

    $client->assertCalled('v1/odata/Customers/1');
});

it('can retrieve the query builder', function () {
    expect(Customer::query())->toBeInstanceOf(Builder::class);
});

it('can be casted to an array', function () {
    expect((new Customer([
        'CustomerId' => 1,
        'CustomerNumber' => "001",
        'CustomerName' => "Acme INC",
        'Address' => "Test street 1"
    ]))->toArray())
        ->toBe([
            'CustomerId' => 1,
            'CustomerNumber' => "001",
            'CustomerName' => "Acme INC",
            'Address' => "Test street 1"
        ]);
});

test('static calls resolves the query builder', function () {
    expect(Customer::__callStatic('where', ['thing', 'thing']))->toBeInstanceOf(Builder::class);
});

it('can access resource attributes', function () {
    $customer = new Customer([
        'CustomerId' => 1,
        'CustomerNumber' => '001',
        'CustomerName' => 'Acme INC'
    ]);

    expect($customer->getKey())
        ->toBe(1)
        ->and($customer->CustomerNumber)
        ->toBe('001')
        ->and($customer->getAttribute('CustomerName'))
        ->toBe('Acme INC');
});

test('a resource can be saved', function () {
    $client = Client::fake();

    $customer = new Customer([
        'CustomerId' => 1,
        'CustomerNumber' => '001',
        'CustomerName' => 'Acme INC'
    ]);

    $customer->setAttribute('CustomerName', 'Another customer name');
    $customer->CustomerNumber = '2';

    expect($customer->getAttribute('CustomerName'))
        ->toBe('Another customer name')
        ->and($customer->getAttribute('CustomerNumber'))
        ->toBe('2');

    $customer->save();

    $client->assertCalled('v1/odata/Customers/1');
});
