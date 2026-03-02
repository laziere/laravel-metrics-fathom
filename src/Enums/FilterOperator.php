<?php

namespace JeffersonGoncalves\MetricsFathom\Enums;

enum FilterOperator: string
{
    case Is = 'is';
    case IsNot = 'is not';
    case IsLike = 'is like';
    case IsNotLike = 'is not like';
    case Matching = 'matching';
    case NotMatching = 'not matching';
}
