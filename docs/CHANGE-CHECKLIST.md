# Change Checklist

Use this checklist before completing a feature, refactor, or bug fix.

## Before coding

- Search the repository for an existing implementation of the feature.
- Identify every template, partial, shortcode, and script that uses the same concept.
- Decide which shared module/component owns the behavior.
- Check whether the change affects a third-party service or shared data format.

## During coding

- Keep templates thin.
- Reuse or extend existing functions.
- Put reusable PHP in shared files.
- Put CSS in stylesheet files only.
- Put JavaScript in JS files only.
- Do not duplicate data normalization.
- Do not create page-specific versions of an existing shared component.
- Explicitly load required modules.
- Keep third-party API calls out of templates.

## Before commit

- Search for duplicate implementations introduced by the change.
- Search for old dependency/service names if replacing an integration.
- Verify there is no inline `<style>` added.
- Verify there is no inline `<script>` added.
- Verify JS files are properly enqueued.
- Verify CSS files are properly enqueued/loaded.
- Test all consumers of a shared component, not just the page that triggered the change.
- Test desktop, tablet, and mobile where UI changed.
- Confirm missing optional data fails gracefully.
- Confirm required dependencies do not fail silently.
- Remove obsolete code and comments from the previous implementation.

## Refactor completion rule

A refactor is complete only when all intended consumers use the shared implementation and the old path has been removed or explicitly documented as still required.
