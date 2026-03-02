<?php

namespace JeffersonGoncalves\MetricsFathom\Data;

class CurrentVisitors
{
    /**
     * @param  list<array<string, mixed>>  $content
     * @param  list<array<string, mixed>>  $referrers
     */
    public function __construct(
        public readonly int $total,
        public readonly array $content = [],
        public readonly array $referrers = [],
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            total: $data['total'] ?? 0,
            content: $data['content'] ?? [],
            referrers: $data['referrers'] ?? [],
        );
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'total' => $this->total,
            'content' => $this->content,
            'referrers' => $this->referrers,
        ];
    }
}
