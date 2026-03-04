## Laravel Metrics Fathom

### Overview
Laravel package for Fathom Analytics API. Provides a fluent API client with query builders, DTOs, and enums. Uses `spatie/laravel-settings` for configuration storage. Namespace: `JeffersonGoncalves\MetricsFathom`.

### Key Concepts
- **Facade**: `Fathom` facade (`JeffersonGoncalves\MetricsFathom\Facades\Fathom`) bound to `'fathom'`
- **Settings**: `FathomSettings` (spatie/laravel-settings) with group `metrics-fathom`
- **Client**: `FathomClient` uses Laravel HTTP client with Bearer token auth
- **Query Builder**: `AggregationQuery` for pageview/event aggregation reports

### API Client
The `Fathom` class provides methods for Sites, Events, Milestones, Aggregations, and Current Visitors.

@verbatim
<code-snippet name="facade-usage" lang="php">
use JeffersonGoncalves\MetricsFathom\Facades\Fathom;

// Sites CRUD
$sites = Fathom::sites(limit: 10);        // returns ['data' => Site[], 'has_more' => bool]
$site = Fathom::site('SITE_ID');           // returns Site DTO
$site = Fathom::createSite('My Site', Sharing::Public);

// Events CRUD (uses default site_id from settings if omitted)
$events = Fathom::events();
$event = Fathom::createEvent('signup');

// Milestones
$milestones = Fathom::milestones();
$milestone = Fathom::createMilestone('Launch', '2024-01-01');

// Current visitors
$visitors = Fathom::currentVisitors(detailed: true);  // returns CurrentVisitors DTO
</code-snippet>
@endverbatim

### DTOs
- `Site` - id, name, sharing, sharePassword
- `Event` - id, name, siteId
- `Milestone` - id, name, siteId, milestoneDate
- `CurrentVisitors` - total, content[], referrers[]

All DTOs implement `fromArray(array $data)` and `toArray()`.

### Enums

@verbatim
<code-snippet name="enums" lang="php">
use JeffersonGoncalves\MetricsFathom\Enums\{Entity, Aggregate, DateGrouping, FieldGrouping, FilterOperator, Sharing};

Entity::Pageview, Entity::Event
Aggregate::Visits, Aggregate::Uniques, Aggregate::Pageviews, Aggregate::AvgDuration, Aggregate::BounceRate
Aggregate::Conversions, Aggregate::UniqueConversions, Aggregate::Value  // Event-only
DateGrouping::Hour, DateGrouping::Day, DateGrouping::Month, DateGrouping::Year
FieldGrouping::Hostname, FieldGrouping::Pathname, FieldGrouping::Browser, FieldGrouping::CountryCode, etc.
FilterOperator::Is, FilterOperator::IsNot, FilterOperator::IsLike, FilterOperator::IsNotLike, FilterOperator::Matching, FilterOperator::NotMatching
Sharing::None, Sharing::Private, Sharing::Public
</code-snippet>
@endverbatim

### AggregationQuery Builder

@verbatim
<code-snippet name="aggregation-query" lang="php">
use JeffersonGoncalves\MetricsFathom\Facades\Fathom;
use JeffersonGoncalves\MetricsFathom\Enums\{Aggregate, DateGrouping, FieldGrouping, FilterOperator};

// Pageview aggregation
$query = Fathom::query()
    ->aggregate(Aggregate::Pageviews, Aggregate::Visits)
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
$query = Fathom::queryEvents()
    ->aggregate(Aggregate::Conversions)
    ->forEvent('signup')
    ->groupByDate(DateGrouping::Month);
$results = Fathom::aggregate($query);
</code-snippet>
@endverbatim

### Configuration
Settings stored via `FathomSettings` (spatie/laravel-settings, group: `metrics-fathom`):
- `api_token` (string) - Fathom API token
- `site_id` (string) - Default site ID
- `base_url` (string) - API base URL
- `timezone` (string) - Default timezone for queries

Publish migrations: `php artisan vendor:publish --tag=metrics-fathom-settings-migrations`

### Conventions
- All list methods return `['data' => DTO[], 'has_more' => bool]` for pagination
- Methods accepting `?string $siteId = null` fall back to `FathomSettings::site_id`
- Exceptions: `AuthenticationException` (401), `RateLimitException` (429), `FathomException` (generic)
- Service provider registers `FathomClient` and `Fathom` as singletons
