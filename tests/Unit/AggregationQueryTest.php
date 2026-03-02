<?php

use JeffersonGoncalves\MetricsFathom\Enums\Aggregate;
use JeffersonGoncalves\MetricsFathom\Enums\DateGrouping;
use JeffersonGoncalves\MetricsFathom\Enums\FieldGrouping;
use JeffersonGoncalves\MetricsFathom\Enums\FilterOperator;
use JeffersonGoncalves\MetricsFathom\Queries\AggregationQuery;

it('creates pageview query with default site id from settings', function () {
    $params = AggregationQuery::pageviews()
        ->aggregate(Aggregate::Visits, Aggregate::Pageviews)
        ->toQueryParams();

    expect($params)
        ->toHaveKey('entity', 'pageview')
        ->toHaveKey('entity_id', 'TESTSITE')
        ->toHaveKey('aggregates', 'visits,pageviews')
        ->toHaveKey('timezone', 'UTC');
});

it('creates event query', function () {
    $params = AggregationQuery::events()
        ->aggregate(Aggregate::Conversions)
        ->forSite('MYSITE')
        ->forEvent('signup')
        ->toQueryParams();

    expect($params)
        ->toHaveKey('entity', 'event')
        ->toHaveKey('site_id', 'MYSITE')
        ->toHaveKey('entity_name', 'signup')
        ->toHaveKey('aggregates', 'conversions');
});

it('supports date grouping', function () {
    $params = AggregationQuery::pageviews()
        ->aggregate(Aggregate::Visits)
        ->groupByDate(DateGrouping::Day)
        ->toQueryParams();

    expect($params)->toHaveKey('date_grouping', 'day');
});

it('supports field grouping', function () {
    $params = AggregationQuery::pageviews()
        ->aggregate(Aggregate::Pageviews)
        ->groupByField(FieldGrouping::Browser)
        ->toQueryParams();

    expect($params)->toHaveKey('field_grouping', 'browser');
});

it('supports date range', function () {
    $params = AggregationQuery::pageviews()
        ->aggregate(Aggregate::Visits)
        ->between('2026-01-01 00:00:00', '2026-01-31 23:59:59')
        ->toQueryParams();

    expect($params)
        ->toHaveKey('date_from', '2026-01-01 00:00:00')
        ->toHaveKey('date_to', '2026-01-31 23:59:59');
});

it('supports datetime objects', function () {
    $from = new DateTime('2026-03-01');
    $to = new DateTime('2026-03-02');

    $params = AggregationQuery::pageviews()
        ->aggregate(Aggregate::Visits)
        ->from($from)
        ->to($to)
        ->toQueryParams();

    expect($params)
        ->toHaveKey('date_from', '2026-03-01 00:00:00')
        ->toHaveKey('date_to', '2026-03-02 00:00:00');
});

it('supports sorting', function () {
    $params = AggregationQuery::pageviews()
        ->aggregate(Aggregate::Visits)
        ->sortBy('visits', 'desc')
        ->toQueryParams();

    expect($params)->toHaveKey('sort_by', 'visits:desc');
});

it('supports limit', function () {
    $params = AggregationQuery::pageviews()
        ->aggregate(Aggregate::Visits)
        ->limit(50)
        ->toQueryParams();

    expect($params)->toHaveKey('limit', 50);
});

it('supports filters', function () {
    $params = AggregationQuery::pageviews()
        ->aggregate(Aggregate::Visits)
        ->where(FieldGrouping::Pathname, FilterOperator::Is, '/blog')
        ->where('browser', FilterOperator::IsNot, 'Chrome')
        ->toQueryParams();

    $filters = json_decode($params['filters'], true);

    expect($filters)->toHaveCount(2)
        ->and($filters[0])->toBe([
            'property' => 'pathname',
            'operator' => 'is',
            'value' => '/blog',
        ])
        ->and($filters[1])->toBe([
            'property' => 'browser',
            'operator' => 'is not',
            'value' => 'Chrome',
        ]);
});

it('supports custom timezone', function () {
    $params = AggregationQuery::pageviews()
        ->aggregate(Aggregate::Visits)
        ->timezone('America/Sao_Paulo')
        ->toQueryParams();

    expect($params)->toHaveKey('timezone', 'America/Sao_Paulo');
});

it('allows overriding site id for pageview queries', function () {
    $params = AggregationQuery::pageviews()
        ->aggregate(Aggregate::Visits)
        ->forSite('CUSTOM')
        ->toQueryParams();

    expect($params)->toHaveKey('entity_id', 'CUSTOM');
});
