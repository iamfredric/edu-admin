<?php

namespace Iamfredric\EduAdmin\Tests\Unit;

use Iamfredric\EduAdmin\Builder;
use Iamfredric\EduAdmin\Client;
use Illuminate\Support\Facades\Http;

it('can add where clauses', function () {
    $builder = new Builder('test');
    $builder->where('thing', 'thing');
    expect($builder->getParams())
        ->toBe([
            '$count' => 'true',
            '$filter' => "thing Eq 'thing'"
        ]);
});

it('can add multiple where clauses', function () {
    $builder = new Builder('test');
    $builder->where('thing', 'thing')
        ->where('another', '!=', 'thing');

    expect($builder->getParams())
        ->toBe([
            '$count' => 'true',
            '$filter' => "thing Eq 'thing' AND another Ne 'thing'"
        ]);
});

it('can group where where clauses', function () {
    $builder = new Builder('test');
    $builder->where(function (Builder $query) {
        $query->where('item', '>', 4)
            ->orWhere('item', '<', 6);
    })->where('id', 'five');

    expect($builder->getParams())
        ->toBe([
            '$count' => 'true',
            '$filter' => "(item Gt 4 OR item Lt 6) AND id Eq 'five'"
        ]);
});

it('can load related data', function () {
    $builder = new Builder('test');
    $builder->with('CustomFields', 'OtherFields');

    expect($builder->getParams())
        ->toBe([
            '$count' => 'true',
            '$expand' => 'CustomFields,OtherFields'
        ]);
});

test('it can call get', function () {
    $http = Client::fake();

    Client::setCredentials('u', 'p');

    $builder = new Builder('test');
    $builder->get(['me' => 'attribute']);

    $http->assertCalled('https://api.eduadmin.se/token');
    $http->assertCalled('https://api.eduadmin.se/v1/test');
});

test('it can call put', function () {
    $http = Client::fake();

    Client::setCredentials('u', 'p');

    $builder = new Builder('test');
    $builder->put(['me' => 'attribute']);

    $http->assertCalled('https://api.eduadmin.se/token');
    $http->assertCalled('https://api.eduadmin.se/v1/test');
});

it('can add conditional tags', function () {
    $builder = new Builder('test');
    $builder->when('thing', fn (Builder $query, $thing) => $query->where('thing', $thing));

    $builder->when('', fn (Builder $query, $thing) => $query->where('other-thing', $thing));

    expect($builder->getParams())
        ->toBe([
            '$count' => 'true',
            '$filter' => "thing Eq 'thing'"
        ]);
});
