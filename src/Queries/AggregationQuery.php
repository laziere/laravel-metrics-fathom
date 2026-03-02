<?php

namespace JeffersonGoncalves\MetricsFathom\Queries;

use DateTimeInterface;
use JeffersonGoncalves\MetricsFathom\Enums\Aggregate;
use JeffersonGoncalves\MetricsFathom\Enums\DateGrouping;
use JeffersonGoncalves\MetricsFathom\Enums\Entity;
use JeffersonGoncalves\MetricsFathom\Enums\FieldGrouping;
use JeffersonGoncalves\MetricsFathom\Enums\FilterOperator;
use JeffersonGoncalves\MetricsFathom\Settings\FathomSettings;

class AggregationQuery
{
    private Entity $entity;

    /** @var array<int, Aggregate> */
    private array $aggregates = [];

    private ?string $entityId = null;

    private ?string $siteId = null;

    private ?string $entityName = null;

    private ?DateGrouping $dateGrouping = null;

    private ?FieldGrouping $fieldGrouping = null;

    private ?string $sortBy = null;

    private ?string $timezone = null;

    private ?string $dateFrom = null;

    private ?string $dateTo = null;

    private ?int $limit = null;

    /** @var list<array{property: string, operator: string, value: string}> */
    private array $filters = [];

    public function __construct(Entity $entity)
    {
        $this->entity = $entity;
    }

    public static function pageviews(): self
    {
        return new self(Entity::Pageview);
    }

    public static function events(): self
    {
        return new self(Entity::Event);
    }

    public function aggregate(Aggregate ...$aggregates): self
    {
        array_push($this->aggregates, ...$aggregates);

        return $this;
    }

    public function forSite(string $siteId): self
    {
        if ($this->entity === Entity::Pageview) {
            $this->entityId = $siteId;
        } else {
            $this->siteId = $siteId;
        }

        return $this;
    }

    public function forEvent(string $eventName): self
    {
        $this->entityName = $eventName;

        return $this;
    }

    public function groupByDate(DateGrouping $grouping): self
    {
        $this->dateGrouping = $grouping;

        return $this;
    }

    public function groupByField(FieldGrouping $grouping): self
    {
        $this->fieldGrouping = $grouping;

        return $this;
    }

    public function sortBy(string $field, string $direction = 'desc'): self
    {
        $this->sortBy = "{$field}:{$direction}";

        return $this;
    }

    public function timezone(string $timezone): self
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function from(DateTimeInterface|string $date): self
    {
        $this->dateFrom = $date instanceof DateTimeInterface
            ? $date->format('Y-m-d H:i:s')
            : $date;

        return $this;
    }

    public function to(DateTimeInterface|string $date): self
    {
        $this->dateTo = $date instanceof DateTimeInterface
            ? $date->format('Y-m-d H:i:s')
            : $date;

        return $this;
    }

    public function between(DateTimeInterface|string $from, DateTimeInterface|string $to): self
    {
        return $this->from($from)->to($to);
    }

    public function limit(int $limit): self
    {
        $this->limit = $limit;

        return $this;
    }

    public function where(string|FieldGrouping $property, FilterOperator $operator, string $value): self
    {
        $this->filters[] = [
            'property' => $property instanceof FieldGrouping ? $property->value : $property,
            'operator' => $operator->value,
            'value' => $value,
        ];

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    public function toQueryParams(): array
    {
        $settings = app(FathomSettings::class);

        $params = [
            'entity' => $this->entity->value,
            'entity_id' => $this->entityId ?? $settings->site_id,
        ];

        if ($this->aggregates !== []) {
            $params['aggregates'] = implode(',', array_map(fn (Aggregate $a) => $a->value, $this->aggregates));
        }

        if ($this->siteId !== null) {
            $params['site_id'] = $this->siteId;
        }

        if ($this->entityName !== null) {
            $params['entity_name'] = $this->entityName;
        }

        if ($this->dateGrouping !== null) {
            $params['date_grouping'] = $this->dateGrouping->value;
        }

        if ($this->fieldGrouping !== null) {
            $params['field_grouping'] = $this->fieldGrouping->value;
        }

        if ($this->sortBy !== null) {
            $params['sort_by'] = $this->sortBy;
        }

        $params['timezone'] = $this->timezone ?? $settings->timezone;

        if ($this->dateFrom !== null) {
            $params['date_from'] = $this->dateFrom;
        }

        if ($this->dateTo !== null) {
            $params['date_to'] = $this->dateTo;
        }

        if ($this->limit !== null) {
            $params['limit'] = $this->limit;
        }

        if ($this->filters !== []) {
            $params['filters'] = json_encode($this->filters);
        }

        return $params;
    }
}
