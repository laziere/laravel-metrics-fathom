<?php

namespace JeffersonGoncalves\MetricsFathom\Enums;

enum FieldGrouping: string
{
    case Hostname = 'hostname';
    case Pathname = 'pathname';
    case ReferrerHostname = 'referrer_hostname';
    case Referrer = 'referrer';
    case Browser = 'browser';
    case BrowserVersion = 'browser_version';
    case CountryCode = 'country_code';
    case City = 'city';
    case DeviceType = 'device_type';
    case OperatingSystem = 'operating_system';
    case OperatingSystemVersion = 'operating_system_version';
    case UtmSource = 'utm_source';
    case UtmMedium = 'utm_medium';
    case UtmCampaign = 'utm_campaign';
    case UtmContent = 'utm_content';
    case UtmTerm = 'utm_term';
}
