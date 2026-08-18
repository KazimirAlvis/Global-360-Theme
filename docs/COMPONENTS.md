# Shared Component Standards

Reusable UI and behavior must be implemented once and consumed everywhere it appears.

## Component ownership

A component should own:

- its markup renderer or template partial;
- its CSS;
- its JavaScript behavior;
- its data contract;
- dependency loading where applicable.

Pages should only provide data and configuration.

## Required shared components

Where the same concept appears in more than one place, use shared implementations for:

- maps;
- clinic cards;
- doctor cards;
- buttons and calls to action;
- breadcrumbs;
- address formatting;
- state labels/mappings;
- forms;
- schema fragments;
- reusable clinic/doctor lists;
- repeated modal or interactive behavior.

## Maps

All maps must use the same shared map system.

The map system should separate:

```text
address/location data
        ↓
coordinate resolver
        ↓
normalized locations
        ↓
shared Leaflet renderer
        ↓
shared map JS + CSS
```

Page-specific code may configure:

- map height;
- default zoom;
- center;
- markers;
- wrapper classes;
- fit-bounds behavior.

Page-specific code may not create a second renderer, inline Leaflet initialization, duplicate tile setup, or duplicate marker/popup logic.

## Data contracts

Components should receive predictable arrays/objects. Normalize legacy meta formats before rendering.

For a location, use one stable shape such as:

```php
[
    'coords'  => [34.123, -118.123],
    'name'    => 'Clinic Name',
    'address' => '123 Main St, Los Angeles, CA 90000',
    'link'    => 'https://example.com/clinic/',
]
```

Do not make the renderer understand every historical storage format.

## Styling

Component CSS belongs in a shared stylesheet. Do not put CSS in the PHP renderer or template.

## Behavior

Component JavaScript belongs in a JS file. PHP may provide data through attributes, JSON data objects, or localized script data, but should not output inline executable JavaScript.
