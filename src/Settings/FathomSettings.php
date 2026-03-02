<?php

namespace JeffersonGoncalves\MetricsFathom\Settings;

use Spatie\LaravelSettings\Settings;

class FathomSettings extends Settings
{
    public string $api_token;

    public string $site_id;

    public string $base_url;

    public string $timezone;

    public static function group(): string
    {
        return 'metrics-fathom';
    }
}
