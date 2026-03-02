<?php

use Spatie\LaravelSettings\Migrations\SettingsBlueprint;
use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->inGroup('metrics-fathom', function (SettingsBlueprint $blueprint): void {
            $blueprint->add('api_token', env('FATHOM_API_TOKEN', ''));
            $blueprint->add('site_id', env('FATHOM_SITE_ID', ''));
            $blueprint->add('base_url', env('FATHOM_BASE_URL', 'https://api.usefathom.com/v1'));
            $blueprint->add('timezone', env('FATHOM_TIMEZONE', 'UTC'));
        });
    }
};
