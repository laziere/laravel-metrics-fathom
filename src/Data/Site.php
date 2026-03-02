<?php

namespace JeffersonGoncalves\MetricsFathom\Data;

class Site
{
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $sharing,
        public readonly ?string $sharePassword = null,
    ) {}

    /**
     * @param  array<string, mixed>  $data
     */
    public static function fromArray(array $data): self
    {
        return new self(
            id: $data['id'],
            name: $data['object'] ?? $data['name'] ?? '',
            sharing: $data['sharing'] ?? 'none',
            sharePassword: $data['share_password'] ?? null,
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
            'sharing' => $this->sharing,
            'share_password' => $this->sharePassword,
        ];
    }
}
