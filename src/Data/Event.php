<?php

namespace JeffersonGoncalves\MetricsFathom\Data;

class Event
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $siteId,
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
        );
    }

    /**
     * @return array<string, string>
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'site_id' => $this->siteId,
        ];
    }
}
