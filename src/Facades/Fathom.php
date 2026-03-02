<?php

namespace JeffersonGoncalves\MetricsFathom\Facades;

use Illuminate\Support\Facades\Facade;
use JeffersonGoncalves\MetricsFathom\Data\CurrentVisitors;
use JeffersonGoncalves\MetricsFathom\Data\Event;
use JeffersonGoncalves\MetricsFathom\Data\Milestone;
use JeffersonGoncalves\MetricsFathom\Data\Site;
use JeffersonGoncalves\MetricsFathom\Enums\Sharing;
use JeffersonGoncalves\MetricsFathom\Queries\AggregationQuery;

/**
 * @method static array<string, mixed> account()
 * @method static array{data: list<Site>, has_more: bool} sites(int $limit = 10, ?string $startingAfter = null, ?string $endingBefore = null)
 * @method static Site site(?string $siteId = null)
 * @method static Site createSite(string $name, Sharing $sharing = Sharing::None, ?string $sharePassword = null)
 * @method static Site updateSite(string $siteId, ?string $name = null, ?Sharing $sharing = null, ?string $sharePassword = null)
 * @method static array<string, mixed> deleteSite(string $siteId)
 * @method static array<string, mixed> wipeSite(string $siteId)
 * @method static array{data: list<Event>, has_more: bool} events(?string $siteId = null, int $limit = 10, ?string $startingAfter = null, ?string $endingBefore = null)
 * @method static Event event(string $eventId, ?string $siteId = null)
 * @method static Event createEvent(string $name, ?string $siteId = null)
 * @method static Event updateEvent(string $eventId, string $name, ?string $siteId = null)
 * @method static array<string, mixed> deleteEvent(string $eventId, ?string $siteId = null)
 * @method static array<string, mixed> wipeEvent(string $eventId, ?string $siteId = null)
 * @method static array{data: list<Milestone>, has_more: bool} milestones(?string $siteId = null, int $limit = 10, ?string $startingAfter = null, ?string $endingBefore = null)
 * @method static Milestone milestone(string $milestoneId, ?string $siteId = null)
 * @method static Milestone createMilestone(string $name, ?string $milestoneDate = null, ?string $siteId = null)
 * @method static Milestone updateMilestone(string $milestoneId, ?string $name = null, ?string $milestoneDate = null, ?string $siteId = null)
 * @method static array<string, mixed> deleteMilestone(string $milestoneId, ?string $siteId = null)
 * @method static array<int|string, mixed> aggregate(AggregationQuery $query)
 * @method static AggregationQuery query()
 * @method static AggregationQuery queryEvents()
 * @method static CurrentVisitors currentVisitors(?string $siteId = null, bool $detailed = false)
 *
 * @see \JeffersonGoncalves\MetricsFathom\Fathom
 */
class Fathom extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'fathom';
    }
}
