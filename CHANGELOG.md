# Changelog

All notable changes to `laravel-metrics-fathom` will be documented in this file.

## v1.0.0 - 2026-03-02

### What's New

- Full Fathom Analytics API client (sites, events, milestones, aggregations, current visitors)
- Fluent `AggregationQuery` builder with date/field grouping, filters, sorting, and timezone support
- Settings stored via `spatie/laravel-settings` (no config file needed)
- DTOs for `Site`, `Event`, `Milestone`, `CurrentVisitors`
- Typed Enums: `Entity`, `Aggregate`, `DateGrouping`, `FieldGrouping`, `FilterOperator`, `Sharing`
- Facade with full PHPDoc annotations
- PHPStan level 8 passing
- 25 Pest tests (unit + feature)
- GitHub workflows: Pint auto-fix, PHPStan, dependabot auto-merge, changelog updater

## Unreleased
