---
name: metrics-fathom-development
description: Development patterns for the laravel-metrics-fathom package - Fathom Analytics API client with query builders, DTOs, and enums
---

## When to use this skill

- Adding new API endpoints or methods to the Fathom client
- Creating new DTOs for API responses
- Adding new enums for API parameters
- Modifying the AggregationQuery builder
- Writing tests for the Fathom API integration

## Setup

Package: `jeffersongoncalves/laravel-metrics-fathom`
Namespace: `JeffersonGoncalves\MetricsFathom`
Requires: PHP ^8.2, Laravel ^11.0|^12.0, spatie/laravel-settings ^3.0

```bash
composer require jeffersongoncalves/laravel-metrics-fathom
php artisan vendor:publish --tag=metrics-fathom-settings-migrations
php artisan migrate
```

### Settings (spatie/laravel-settings)

`FathomSettings` class (group: `metrics-fathom`): `api_token` (string), `site_id` (string), `base_url` (string), `timezone` (string). Auto-registered by the service provider.

## API Client usage

`FathomClient` - Low-level HTTP client with Bearer token auth. Methods: `get()`, `post()`, `delete()`. Registered as singleton.

Error handling: 401 -> `AuthenticationException`, 429 -> `RateLimitException`, empty token -> `AuthenticationException::missingToken()`, other -> `FathomException::fromResponse()`.

### Fathom Facade

```php
use JeffersonGoncalves\MetricsFathom\Facades\Fathom;

// Account
$account = Fathom::account();

// Sites CRUD - returns Site DTO (id, name, sharing, sharePassword)
$sites = Fathom::sites(limit: 10);  // ['data' => Site[], 'has_more' => bool]
$site = Fathom::site('SITE_ID');
$site = Fathom::createSite('My Site', Sharing::Public);
$site = Fathom::updateSite('SITE_ID', name: 'New Name');
Fathom::deleteSite('SITE_ID');
Fathom::wipeSite('SITE_ID');

// Events CRUD - returns Event DTO (id, name, siteId)
$events = Fathom::events(siteId: 'SITE_ID', limit: 20);
$event = Fathom::createEvent('signup');
Fathom::deleteEvent('EVENT_ID');

// Milestones CRUD - returns Milestone DTO (id, name, siteId, milestoneDate)
$milestones = Fathom::milestones(limit: 5);
$milestone = Fathom::createMilestone('Launch', '2024-01-01');

// Current Visitors - returns CurrentVisitors DTO (total, content[], referrers[])
$visitors = Fathom::currentVisitors(detailed: true);
```

Methods accepting `?string $siteId = null` fall back to `FathomSettings::site_id`.

## Query builders

### AggregationQuery

Fluent builder for `/aggregations`. Static constructors: `pageviews()` / `events()`, or via `Fathom::query()` / `Fathom::queryEvents()`.

```php
$query = Fathom::query()
    ->aggregate(Aggregate::Pageviews, Aggregate::Visits, Aggregate::Uniques)
    ->forSite('SITE_ID')
    ->groupByDate(DateGrouping::Day)
    ->groupByField(FieldGrouping::Pathname)
    ->between('2024-01-01', '2024-01-31')
    ->timezone('America/Sao_Paulo')
    ->where(FieldGrouping::Pathname, FilterOperator::IsLike, '/blog%')
    ->sortBy('pageviews', 'desc')
    ->limit(100);
$results = Fathom::aggregate($query);

// Event aggregation
$eventQuery = Fathom::queryEvents()
    ->aggregate(Aggregate::Conversions, Aggregate::UniqueConversions)
    ->forEvent('signup')
    ->groupByDate(DateGrouping::Month);
$results = Fathom::aggregate($eventQuery);
```

Key: `forSite()` sets `entity_id` for pageviews, `site_id` for events. `from()`/`to()` accept `DateTimeInterface|string`. `where()` accepts `string|FieldGrouping`.

## DTOs

All DTOs use readonly constructor properties with `fromArray()` / `toArray()`:

- **Site** - `id`, `name`, `sharing`, `sharePassword`
- **Event** - `id`, `name`, `siteId`
- **Milestone** - `id`, `name`, `siteId`, `milestoneDate`
- **CurrentVisitors** - `total`, `content[]`, `referrers[]`

## Enums

```php
Entity::Pageview, Entity::Event
Aggregate::Visits, Aggregate::Uniques, Aggregate::Pageviews, Aggregate::AvgDuration, Aggregate::BounceRate
Aggregate::Conversions, Aggregate::UniqueConversions, Aggregate::Value  // events only
DateGrouping::Hour, DateGrouping::Day, DateGrouping::Month, DateGrouping::Year
FieldGrouping::Hostname, FieldGrouping::Pathname, FieldGrouping::ReferrerHostname, FieldGrouping::Referrer
FieldGrouping::Browser, FieldGrouping::BrowserVersion, FieldGrouping::CountryCode, FieldGrouping::City
FieldGrouping::DeviceType, FieldGrouping::OperatingSystem, FieldGrouping::OperatingSystemVersion
FieldGrouping::UtmSource, FieldGrouping::UtmMedium, FieldGrouping::UtmCampaign, FieldGrouping::UtmContent, FieldGrouping::UtmTerm
FilterOperator::Is, FilterOperator::IsNot, FilterOperator::IsLike, FilterOperator::IsNotLike
FilterOperator::Matching, FilterOperator::NotMatching
Sharing::None, Sharing::Private, Sharing::Public
```

## Configuration

`FathomServiceProvider` (extends `PackageServiceProvider`): package name `metrics-fathom`. Registers `FathomSettings` in settings config, `FathomClient` and `Fathom` as singletons. Publishes migrations tag: `metrics-fathom-settings-migrations`. Auto-discovered via composer.json.

## Testing patterns

```php
// Mock facade
Fathom::shouldReceive('site')->with('SITE_ID')
    ->andReturn(Site::fromArray(['id' => 'SITE_ID', 'name' => 'Test', 'sharing' => 'none']));

// Mock HTTP
Http::fake([
    'api.usefathom.com/v1/sites' => Http::response([
        'data' => [['id' => 'SITE_1', 'object' => 'Test', 'sharing' => 'none']],
        'has_more' => false,
    ]),
]);

// Test query builder
$query = AggregationQuery::pageviews()->aggregate(Aggregate::Pageviews)->groupByDate(DateGrouping::Day);
$params = $query->toQueryParams();
expect($params['entity'])->toBe('pageview');
expect($params['aggregates'])->toBe('pageviews');
```

```bash
vendor/bin/pest
vendor/bin/phpstan analyse
vendor/bin/pint
```
