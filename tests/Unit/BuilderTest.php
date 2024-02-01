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

it('can build with query from dot notations', function () {
    $builder = new Builder('test');

    $builder->with('ProgrammeStarts.Bookings.Events');

    expect($builder->getParams()['$expand'])
        ->toBe('ProgrammeStarts($expand=Bookings($expand=Events))');

    $builder = new Builder('test');

    $builder->with('ProgrammeStarts.Bookings');

    expect($builder->getParams()['$expand'])
        ->toBe('ProgrammeStarts($expand=Bookings)');

    $builder = new Builder('test');

    $builder->with('ProgrammeStarts');

    expect($builder->getParams()['$expand'])
        ->toBe('ProgrammeStarts');
});

it('can build where clause on dates', function () {
    $builder = new Builder('test');

    $builder->whereDate('DateField', '=', $now = now());

    expect($builder->getParams('$filter'))
        ->toBe("DateField Eq {$now->toISOString()}");
});

it('can build where not in', function () {
    $builder = new Builder('test');

    $builder->where('Thing', 'NOT IN', [1, 2]);

    expect($builder->getParams('$filter'))
        ->toBe("not(Thing in (1,2))");
});

it('can build raw queries', function () {
    $builder = new Builder('test');

    $builder->whereRaw('Thing eq something');

    expect($builder->getParams('$filter'))
        ->toBe("Thing eq something");
});

it('can filter relationship', function () {
    $builder = new Builder('test');

    $builder->with(function ($query) {
        $query->whereNull('CustomerId');

        return 'Events';
    });

    expect($builder->getParams('$expand'))
        ->toBe("Events(\$filter=CustomerId Eq null)");
});

it('can filter where has on a relationship', function () {
    $builder = new Builder('test');

    $builder->whereHas('Events', function ($query) {
        $query->where('CustomerId', '=', 1);
    });

    expect($builder->getParams('$filter'))
        ->toBe("Events/any(d:d/CustomerId Eq 1)");
});

it('can filter where has on a relationship without a callable', function () {
    $builder = new Builder('test');

    $builder->whereHas('Events');

    expect($builder->getParams('$filter'))
        ->toBe("Events/any");
});

it('can load related data with keyed callable', function () {
    $oldBuilder = new Builder('test');

    $oldBuilder->where('CategoryId', '=', 123) // Todo
        ->select('One', 'Two', 'Three')
        ->with(function (Builder $query) {
            $query->where('Thing', '=', 'hi');

            return 'Events';
        });

    $builder = new Builder('test');

    $builder->where('CategoryId', '=', 123) // Todo
        ->select('One', 'Two', 'Three')
        ->with(['Events' => function (Builder $query) {
            $query->where('Thing', '=', 'hi');
        }]);

    expect($builder->getQueryString())
        ->toBe('$count=true$select=One,Two,Three$filter=CategoryId Eq 123$expand=Events($filter=Thing Eq \'hi\')');

    expect($oldBuilder->getQueryString())->toBe($builder->getQueryString());
});

it('can load related data from a relation', function () {
    $builder = new Builder('test');
    $builder->with(['Events' => function (Builder $query) {
        $query
            ->where('thing', 'ahoy')
            ->select('Ahoy', 'Matey');
    }]);

    expect($builder->getQueryString())
        ->toBe('$count=true$expand=Events($select=Ahoy,Matey;$filter=thing Eq \'ahoy\')');
});
