<?php

namespace JeffersonGoncalves\MetricsFathom;

use JeffersonGoncalves\MetricsFathom\Data\CurrentVisitors;
use JeffersonGoncalves\MetricsFathom\Data\Event;
use JeffersonGoncalves\MetricsFathom\Data\Milestone;
use JeffersonGoncalves\MetricsFathom\Data\Site;
use JeffersonGoncalves\MetricsFathom\Enums\Sharing;
use JeffersonGoncalves\MetricsFathom\Queries\AggregationQuery;
use JeffersonGoncalves\MetricsFathom\Settings\FathomSettings;

class Fathom
{
    public function __construct(
        private readonly FathomClient $client,
    ) {}

    // =========================================================================
    // Account
    // =========================================================================

    /**
     * @return array<string, mixed>
     */
    public function account(): array
    {
        return $this->client->get('account');
    }

    // =========================================================================
    // Sites
    // =========================================================================

    /**
     * @return array{data: list<Site>, has_more: bool}
     */
    public function sites(int $limit = 10, ?string $startingAfter = null, ?string $endingBefore = null): array
    {
        $params = ['limit' => $limit];

        if ($startingAfter !== null) {
            $params['starting_after'] = $startingAfter;
        }

        if ($endingBefore !== null) {
            $params['ending_before'] = $endingBefore;
        }

        $response = $this->client->get('sites', $params);

        return [
            'data' => array_values(array_map(fn (array $item) => Site::fromArray($item), $response['data'] ?? [])),
            'has_more' => (bool) ($response['has_more'] ?? false),
        ];
    }

    public function site(?string $siteId = null): Site
    {
        $siteId ??= $this->defaultSiteId();

        return Site::fromArray($this->client->get("sites/{$siteId}"));
    }

    public function createSite(string $name, Sharing $sharing = Sharing::None, ?string $sharePassword = null): Site
    {
        $data = ['name' => $name, 'sharing' => $sharing->value];

        if ($sharePassword !== null) {
            $data['share_password'] = $sharePassword;
        }

        return Site::fromArray($this->client->post('sites', $data));
    }

    public function updateSite(string $siteId, ?string $name = null, ?Sharing $sharing = null, ?string $sharePassword = null): Site
    {
        $data = array_filter([
            'name' => $name,
            'sharing' => $sharing?->value,
            'share_password' => $sharePassword,
        ], fn ($value) => $value !== null);

        return Site::fromArray($this->client->post("sites/{$siteId}", $data));
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteSite(string $siteId): array
    {
        return $this->client->delete("sites/{$siteId}");
    }

    /**
     * @return array<string, mixed>
     */
    public function wipeSite(string $siteId): array
    {
        return $this->client->delete("sites/{$siteId}/data");
    }

    // =========================================================================
    // Events
    // =========================================================================

    /**
     * @return array{data: list<Event>, has_more: bool}
     */
    public function events(?string $siteId = null, int $limit = 10, ?string $startingAfter = null, ?string $endingBefore = null): array
    {
        $siteId ??= $this->defaultSiteId();
        $params = ['limit' => $limit];

        if ($startingAfter !== null) {
            $params['starting_after'] = $startingAfter;
        }

        if ($endingBefore !== null) {
            $params['ending_before'] = $endingBefore;
        }

        $response = $this->client->get("sites/{$siteId}/events", $params);

        return [
            'data' => array_values(array_map(fn (array $item) => Event::fromArray($item), $response['data'] ?? [])),
            'has_more' => (bool) ($response['has_more'] ?? false),
        ];
    }

    public function event(string $eventId, ?string $siteId = null): Event
    {
        $siteId ??= $this->defaultSiteId();

        return Event::fromArray($this->client->get("sites/{$siteId}/events/{$eventId}"));
    }

    public function createEvent(string $name, ?string $siteId = null): Event
    {
        $siteId ??= $this->defaultSiteId();

        return Event::fromArray($this->client->post("sites/{$siteId}/events", ['name' => $name]));
    }

    public function updateEvent(string $eventId, string $name, ?string $siteId = null): Event
    {
        $siteId ??= $this->defaultSiteId();

        return Event::fromArray($this->client->post("sites/{$siteId}/events/{$eventId}", ['name' => $name]));
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteEvent(string $eventId, ?string $siteId = null): array
    {
        $siteId ??= $this->defaultSiteId();

        return $this->client->delete("sites/{$siteId}/events/{$eventId}");
    }

    /**
     * @return array<string, mixed>
     */
    public function wipeEvent(string $eventId, ?string $siteId = null): array
    {
        $siteId ??= $this->defaultSiteId();

        return $this->client->delete("sites/{$siteId}/events/{$eventId}/data");
    }

    // =========================================================================
    // Milestones
    // =========================================================================

    /**
     * @return array{data: list<Milestone>, has_more: bool}
     */
    public function milestones(?string $siteId = null, int $limit = 10, ?string $startingAfter = null, ?string $endingBefore = null): array
    {
        $siteId ??= $this->defaultSiteId();
        $params = ['limit' => $limit];

        if ($startingAfter !== null) {
            $params['starting_after'] = $startingAfter;
        }

        if ($endingBefore !== null) {
            $params['ending_before'] = $endingBefore;
        }

        $response = $this->client->get("sites/{$siteId}/milestones", $params);

        return [
            'data' => array_values(array_map(fn (array $item) => Milestone::fromArray($item), $response['data'] ?? [])),
            'has_more' => (bool) ($response['has_more'] ?? false),
        ];
    }

    public function milestone(string $milestoneId, ?string $siteId = null): Milestone
    {
        $siteId ??= $this->defaultSiteId();

        return Milestone::fromArray($this->client->get("sites/{$siteId}/milestones/{$milestoneId}"));
    }

    public function createMilestone(string $name, ?string $milestoneDate = null, ?string $siteId = null): Milestone
    {
        $siteId ??= $this->defaultSiteId();
        $data = ['name' => $name];

        if ($milestoneDate !== null) {
            $data['milestone_date'] = $milestoneDate;
        }

        return Milestone::fromArray($this->client->post("sites/{$siteId}/milestones", $data));
    }

    public function updateMilestone(string $milestoneId, ?string $name = null, ?string $milestoneDate = null, ?string $siteId = null): Milestone
    {
        $siteId ??= $this->defaultSiteId();
        $data = array_filter([
            'name' => $name,
            'milestone_date' => $milestoneDate,
        ], fn ($value) => $value !== null);

        return Milestone::fromArray($this->client->post("sites/{$siteId}/milestones/{$milestoneId}", $data));
    }

    /**
     * @return array<string, mixed>
     */
    public function deleteMilestone(string $milestoneId, ?string $siteId = null): array
    {
        $siteId ??= $this->defaultSiteId();

        return $this->client->delete("sites/{$siteId}/milestones/{$milestoneId}");
    }

    // =========================================================================
    // Aggregations (Reporting)
    // =========================================================================

    /**
     * @return array<int|string, mixed>
     */
    public function aggregate(AggregationQuery $query): array
    {
        return $this->client->get('aggregations', $query->toQueryParams());
    }

    public function query(): AggregationQuery
    {
        return AggregationQuery::pageviews();
    }

    public function queryEvents(): AggregationQuery
    {
        return AggregationQuery::events();
    }

    // =========================================================================
    // Current Visitors
    // =========================================================================

    public function currentVisitors(?string $siteId = null, bool $detailed = false): CurrentVisitors
    {
        $siteId ??= $this->defaultSiteId();
        $params = ['site_id' => $siteId, 'detailed' => $detailed];

        return CurrentVisitors::fromArray($this->client->get('current_visitors', $params));
    }

    // =========================================================================
    // Helpers
    // =========================================================================

    private function defaultSiteId(): string
    {
        return app(FathomSettings::class)->site_id;
    }
}
