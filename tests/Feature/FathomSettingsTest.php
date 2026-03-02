<?php

use JeffersonGoncalves\MetricsFathom\Settings\FathomSettings;

it('resolves settings from container', function () {
    $settings = app(FathomSettings::class);

    expect($settings)->toBeInstanceOf(FathomSettings::class);
});

it('has correct default values from seed', function () {
    $settings = app(FathomSettings::class);

    expect($settings->api_token)->toBe('test-token')
        ->and($settings->site_id)->toBe('TESTSITE')
        ->and($settings->base_url)->toBe('https://api.usefathom.com/v1')
        ->and($settings->timezone)->toBe('UTC');
});

it('can update and persist settings', function () {
    $settings = app(FathomSettings::class);
    $settings->site_id = 'NEWSITE';
    $settings->timezone = 'America/Sao_Paulo';
    $settings->save();

    $fresh = app(FathomSettings::class);

    expect($fresh->site_id)->toBe('NEWSITE')
        ->and($fresh->timezone)->toBe('America/Sao_Paulo');
});
