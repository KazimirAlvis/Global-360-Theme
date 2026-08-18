# Global 360 Theme Coding Standards

These rules apply to all new development and refactors in this theme.

## Core principles

### One concept, one implementation

If the same feature appears in more than one template, page type, shortcode, or partial, it must use a shared implementation unless there is a documented reason not to.

Templates may pass data and configuration, but they should not independently implement the same behavior.

Before building a feature, search the repository for an existing implementation and reuse or extend it.

### Keep templates thin

Templates should primarily assemble data, call shared functions/components, and output markup. Do not place business logic, third-party integration logic, reusable queries, CSS, or JavaScript directly in templates.

## PHP

- Reuse functions and helpers instead of copying logic between templates.
- Put reusable logic in `inc/` or another appropriate shared module.
- Prefix theme functions with the established `global360_` or `cpt360_` prefix.
- Prefer small functions with one responsibility.
- Use early returns to keep control flow readable.
- Escape output with the correct WordPress function: `esc_html()`, `esc_attr()`, `esc_url()`, or `wp_kses_post()`.
- Sanitize and validate input before storage or use.
- Do not repeat expensive queries inside loops when data can be fetched once.
- Centralize repeated clinic, doctor, state, address, and relationship queries.
- Do not duplicate state lists, mappings, normalization logic, or API handling.

## CSS

- No inline CSS in PHP templates, partials, rendered HTML, or JavaScript.
- CSS belongs in stylesheet files.
- Shared components must use shared component classes and shared stylesheet rules.
- Page-specific CSS may control layout around a component, but must not reimplement the component itself.
- Avoid near-duplicate selectors for the same UI on different page types.
- Test component changes at desktop, tablet, and mobile widths.

## JavaScript

- No inline JavaScript in PHP templates or partials.
- JavaScript belongs in `.js` files and must be enqueued through WordPress.
- Reuse an existing script/module before creating a new one.
- Shared UI behavior must have one initialization path.
- Do not initialize the same library differently on separate templates without a documented reason.
- Check that the target DOM element exists before initializing a component.
- Avoid unnecessary globals.

## Shared components

A shared component has one owner and many consumers.

Bad:

```text
clinic template -> custom map implementation
state template  -> separate map implementation
```

Good:

```text
clinic data ----\
                 -> shared location resolver -> shared map renderer
state data -----/
```

Consumers may pass configuration such as height, zoom, marker data, or wrapper classes, but rendering logic, scripts, styles, and dependency loading remain shared.

This rule applies to maps, cards, buttons, breadcrumbs, forms, schema helpers, address handling, state logic, and other repeated site functionality.

## External services

- Keep external API transport, data lookup, caching, and rendering separate.
- Do not couple a frontend component to a third-party service unless the service is actually required by that component.
- API keys must come from settings or environment configuration, never hard-coded in templates.
- When removing or disabling a service, search the entire repository for direct and indirect dependencies.
- Remove stale code paths once a replacement is verified.

## Refactoring

When replacing an implementation:

1. Search for every consumer of the current behavior.
2. Select or create the shared replacement.
3. Migrate all consumers in the same change when practical.
4. Remove obsolete code and dependencies.
5. Test every affected page type.
6. Search the repository again for the old implementation or dependency name.

A refactor is not complete because one visible page works.
