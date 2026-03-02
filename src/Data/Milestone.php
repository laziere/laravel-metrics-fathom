<?php

namespace JeffersonGoncalves\MetricsFathom\Data;

class Milestone
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $siteId,
        public readonly ?string $milestoneDate = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['object'] ?? $data['name'] ?? '',
            siteId: $data['site_id'] ?? '',
            milestoneDate: $data['milestone_date'] ?? null,
        );
    }

    /**
     * @return array<string, string|null>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'site_id' => $this->siteId,
            'milestone_date' => $this->milestoneDate,
        ];
    }
}
