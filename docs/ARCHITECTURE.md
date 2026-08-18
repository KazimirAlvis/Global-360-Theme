# Global 360 Theme Architecture

The theme should favor shared services, helpers, components, and predictable ownership over page-specific implementations.

## Responsibility boundaries

### Templates and partials

Templates and partials should:

- retrieve or receive the minimum data required for the page;
- call shared helpers/components;
- output semantic markup;
- pass configuration to shared renderers.

Templates and partials should not:

- contain inline CSS;
- contain inline JavaScript;
- call third-party APIs directly;
- implement reusable business logic;
- duplicate queries already available through helpers;
- own reusable components.

### `inc/`

Use `inc/` for shared PHP logic such as:

- location/address helpers;
- state normalization;
- reusable queries;
- external-service clients;
- schema builders;
- shared component renderers;
- data transformers;
- settings helpers.

Every required module added to `inc/` must be explicitly loaded by the theme bootstrap. Do not rely on `function_exists()` to silently hide a missing required include.

### JavaScript

All JavaScript belongs in JS files. Scripts should be registered/enqueued from PHP and initialized against predictable component selectors or data attributes.

Do not place component initialization code inside templates.

### CSS

All CSS belongs in stylesheet files. Shared components should have shared CSS ownership. Template styles should only handle page composition around shared components.

## Data flow

Prefer this structure:

```text
WordPress/meta/API data
        ↓
shared query/helper/service
        ↓
normalized data shape
        ↓
shared renderer/component
        ↓
template/page
```

Avoid this structure:

```text
template
  ├─ direct meta parsing
  ├─ direct API calls
  ├─ custom data normalization
  ├─ custom renderer
  ├─ inline CSS
  └─ inline JS
```

## Reuse rule

When two pages need the same concept, create one shared implementation and make both pages consumers.

Configuration differences are allowed. Implementation duplication is not.

For example, clinic and state maps may use different heights or zoom levels, but must use the same map renderer, map JavaScript, map CSS, coordinate resolver, and dependency loader.

## Dependency ownership

Every external library or service should have one clear ownership point.

Examples:

- Leaflet: one enqueue/init path.
- OpenStreetMap tiles: configured in the shared map module.
- Google services: isolated to specific service helpers, not frontend templates.
- Analytics: loaded from one central integration point.

A third-party service should be replaceable without requiring unrelated templates to be rewritten.

## Backward compatibility

Legacy data support should be handled inside normalization helpers. Templates and components should receive one stable data shape regardless of how older records were stored.

Backward compatibility is a migration layer, not an excuse to preserve multiple competing architectures.
