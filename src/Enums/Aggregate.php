<?php

namespace JeffersonGoncalves\MetricsFathom\Enums;

enum Aggregate: string
{
    // Pageview aggregates
    case Visits = 'visits';
    case Uniques = 'uniques';
    case Pageviews = 'pageviews';
    case AvgDuration = 'avg_duration';
    case BounceRate = 'bounce_rate';

    // Event aggregates
    case Conversions = 'conversions';
    case UniqueConversions = 'unique_conversions';
    case Value = 'value';
}
