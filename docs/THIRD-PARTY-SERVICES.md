# Third-Party Service Standards

Third-party dependencies must be isolated so they can be changed or removed without breaking unrelated templates.

## General rules

- Never call an external API directly from a page template.
- Keep API transport in a shared service/helper.
- Keep rendering independent from lookup/geocoding whenever possible.
- Keep API keys in settings or environment configuration.
- Cache external responses when appropriate.
- Handle service failures explicitly.
- Document what feature depends on each service.

## Removing a service

When a service is disabled or removed:

1. Search the repository for its API name, URLs, settings keys, functions, scripts, and CSS.
2. Identify direct and indirect consumers.
3. Replace shared dependencies first.
4. Migrate all page consumers.
5. Remove stale settings and legacy paths only after migration is verified.
6. Test every affected page type.

## Maps

Leaflet/OpenStreetMap rendering should not depend on Google Places.

If coordinates are required, coordinate resolution should be a separate concern from map rendering.

The frontend map component should only need normalized location data. This makes the renderer independent from whichever service generated or stored the coordinates.
