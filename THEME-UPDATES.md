# Global 360 Theme Update System

## Overview

The Global 360 Theme now includes automatic update functionality that integrates with WordPress admin.

## How It Works

### Automatic Updates

-   **Auto-update enabled**: WordPress will automatically check for and install theme updates
-   **GitHub Integration**: Updates are pulled from the GitHub repository releases
-   **Version Checking**: Compares local version with latest GitHub release

### Manual Update Checking

-   Navigate to **Appearance → Theme Updates** in WordPress admin
-   View current vs. latest version information
-   Manually trigger update checks
-   Direct link to themes page for updates

## Versioning System

### Current Version Management

-   Version defined in `functions.php` as `_S_VERSION` constant
-   Must match version in `style.css` header
-   Current version: matches `_S_VERSION` (currently `1.0.20260204120000`)

### Release Process

1. Update version number in both:
    - `functions.php` - `_S_VERSION` constant
    - `style.css` - Version header
2. Commit and push changes to GitHub
3. Create a new release on GitHub with tag (e.g., `v1.0.1`)
4. WordPress will detect the update within 24 hours

### Version Format

-   Automatic versions generated as `1.0.YYYYMMDDHHMMSS` (date + time) so every commit is unique
-   If you cut a manual release, keep the same structure for consistency (e.g., `1.0.20251009130545`)

### Release History

#### v1.0.20260204120000 (2026-02-04)

-   Updated the header logo markup to load eagerly with async decoding while removing the `fetchpriority` attribute to let browsers manage priority.

#### v1.0.20260203133000 (2026-02-03)

-   Renamed the North Star Vascular & Interventional logo asset to `north-star-vascular-interventional-logo.jpg` to match the CPT logo loader pattern.

#### v1.0.20260203123000 (2026-02-03)

-   Added the North Star Vascular & Interventional clinic logo so the refreshed branding appears across listings and detail pages.
-   Uploaded new doctor headshots for Amin Astani, Andy Manos, Jafar Golzarian, and Kayla Halleron to keep provider bios current.
-   Let the Blog template render Gutenberg content ahead of the fallback hero while preserving the `.sm_hero` styles when no blocks are present.

#### v1.0.20260128131500 (2026-01-28)

-   Updated clinic branding assets with the latest logo file so location pages and grid views show the refreshed mark.
-   Replaced the featured doctor headshots to match current photography, ensuring profile and listing layouts use the new images.


#### v1.0.20251223134500 (2025-12-23)

-   Added a dedicated `footer-nav` menu class plus a depth limit so the footer navigation only renders top-level items and no longer exposes dropdowns.
-   Hid any leftover submenu indicators in the footer column so the layout stays compact and visually distinct from the main header navigation.

#### v1.0.20251223120500 (2025-12-23)

-   Added a desktop-only hover buffer for submenu parents so dropdowns stay open while moving the cursor from the parent link into the submenu content.

#### v1.0.20251211100955 (2025-12-11)

-   Pointed the "Site Name (for footer copyright)" default back to the active WordPress site title instead of the provider CTA URL so new installs inherit the correct label.
-   Added a fallback so the "Become a Provider" URL field repopulates with `https://www.patientreach360.com/get-started` whenever the saved value is empty.
-   Mirrored that behavior for the contact phone input, automatically restoring `513-587-6827` after users clear or whitespace the field.

#### v1.0.20251208212958 (2025-12-08)

-   Prefilled the 360 Settings contact phone field with `(513) 587-6827` so new installs inherit the Patient Reach support line automatically.
-   Added `https://www.patientreach360.com/get-started` as the default "Become a provider" button URL, ensuring the CTA points to the standard onboarding form without manual setup.

#### v1.0.20251208211305 (2025-12-08)

-   Added Playfair Display to the 360 Settings font dropdown so teams can select it for headings right from the admin UI.
-   Updated the Google Fonts loader, font stacks, and default heading weight so the new option renders consistently on the front end.

#### v1.0.20251125181016 (2025-11-25)

-   Synced the Linktree landing page SCSS partial (`sass/pages/_linktree.scss`) into the repository, added the missing import, and rebuilt `style.css` so the live theme matches the local view.
-   Updated Linktree PHP and clinic partials to render inline SVG icons via `global_360_get_icon_svg` instead of Font Awesome placeholders, keeping assets self-contained.
-   Regenerated `style-min.css` from the freshly compiled stylesheet to keep minified assets in lockstep.

#### v1.0.20251125175136 (2025-11-25)

-   Recompiled `style-min.css` from the latest `style.css` so the Linktree landing page polish ships to live sites using the minified bundle.
-   Bumped the theme version to trigger the WordPress updater and ensure hosted environments pick up the refreshed styling.

#### v1.0.20251125172813 (2025-11-25)

-   Reviewed both `README.md` and `readme.txt` to ensure the deployment and distribution guidance is current before publishing this build.
-   Enabled the Claude Sonnet 4.5 assistant tier for all clients and documented the change so customer success teams can reference the update.

#### v1.0.20251111221908 (2025-11-12)

-   Added a favicon bundle manager to 360 Settings with bulk media uploads for PNG, SVG, ICO, Apple touch, and manifest files.
-   Output the uploaded favicon links, manifest reference, and optional Apple web app title into the site `<head>` so browsers pick up the custom icons.
-   Allowed administrators to upload SVG, ICO, and `.webmanifest` files, ensured WordPress accepts and stores them without triggering image processing failures, and polished the settings UI preview styling.

#### v1.0.20251111160000 (2025-11-11)

-   Styled the Linktree risk assessment button via the PR360 `begin-button` shadow part so it matches the other CTAs without adding extra host classes.
-   Unified all Linktree CTA heights at 52px and aligned spacing so anchors and the embedded PR360 button sit flush in the list.
-   Regenerated the minified stylesheet after polishing the Linktree layout.

#### v1.0.20251111143000 (2025-11-11)

-   Cache-busted the admin media uploader script so the Linktree logo field immediately works after updates and ships with the new release version.
-   Added the missing `style-admin-meta.css` placeholder so the admin enqueue stops returning 404s and keeps both logo preview blocks styled.

#### v1.0.20251111134500 (2025-11-11)

-   Added a Linktree landing page template that auto-builds primary CTAs from the global 360 settings (assessment ID, links, phone, and social profiles).
-   Introduced a Linktree-specific logo field in 360 Settings plus dedicated styling, including hiding the floating assessment button on that layout and polishing the social icons.

#### v1.0.20251110121500 (2025-11-10)

-   Updated `style-min.css` to capture the latest design adjustments and bumped the theme version so WordPress sites pick up the refreshed minified assets.

#### v1.0.20251104113000 (2025-11-04)

-   Version bump to trigger WordPress update checks after removing the legacy social meta tags in the previous release.

#### v1.0.20251104104500 (2025-11-04)

-   Removed hardcoded Open Graph and Twitter meta tags from the theme header so Yoast SEO can manage social metadata without duplicates.

#### v1.0.20251029012827 (2025-10-29)

-   Introduced a global `--heading-letter-spacing` variable sourced from theme settings so Anton headings default to 0.5px tracking.
-   Updated clinic, hero, CTA, state-card, and footer heading selectors to consume the shared letter-spacing value.
-   Ensured long URLs inside standard paragraph blocks wrap cleanly on mobile by applying `overflow-wrap` safeguards.

#### v1.0.20251028154000 (2025-10-28)

-   Enhanced Yoast SEO integration so Clinics and Doctors custom meta content, internal links, and external links contribute to analysis.
-   Added child-theme override support for state SVG assets so per-site colors persist across parent theme updates.

#### v1.0.20251027124500 (2025-10-27)

-   Applied the 360 Settings typography choices directly to headings and paragraph defaults while keeping the admin area untouched.
-   Locked Anton’s heading weight to 400 across the board, including footer callouts, by driving `--heading-font-weight` with dynamic defaults.

#### v1.0.20251024164826 (2025-10-24)

-   Added 20px bottom margin to `.single-clinic pr360-questionnaire::part(begin-button)` to improve spacing beneath CTA buttons

#### v1.0.20251024162841 (2025-10-24)

-   Restored dynamic font variables by outputting CSS both inline and in wp_head/login/admin contexts
-   Enqueued only the Google Fonts required by the current 360 settings selection

#### v1.0.20251024160827 (2025-10-24)

-   Added text-transform: uppercase to #floating-assessment-button pr360-questionnaire::part(begin-button)
-   Reduced font-size from 18px to 16px for #floating-assessment-button pr360-questionnaire::part(begin-button)
-   Reordered divs on find-a-doctor page: moved .body_heading before .state_grid_wrapper

#### v1.0.20251024031545 (2025-10-24)

-   Updated the shadow DOM animation to use the same charcoal glow, ensuring the floating CTA never falls back to the old green tint.

#### v1.0.20251024023045 (2025-10-24)

-   Swapped the pulse glow to a neutral charcoal tint so the floating CTA effect matches the theme palette without the green highlight.

#### v1.0.20251024014500 (2025-10-24)

-   Bundled the shadow DOM pulse bootstrap script so the floating assessment CTA animates immediately after WordPress updates from GitHub.

#### v1.0.20251024002239 (2025-10-24)

-   Synced theme packaging version with the latest deployed build so the WordPress updater recognises the pulse animation enhancements without requiring a GitHub release.

#### v1.0.20251023104512 (2025-10-23)

-   Supercharged the floating assessment CTA with a shadow-root-aware pulse animation, persistent drop shadow, and 0px margin footprint so the button hugs its container cleanly.

#### v1.0.20251022143000 (2025-10-22)

-   Refined the footer layout for responsive breakpoints, keeping desktop columns compact while introducing tablet/mobile wrapping with consistent padding and horizontal social links.

#### v1.0.20251022114500 (2025-10-22)

-   Rebuilt the mobile navigation with an accessible hamburger toggle, full-screen overlay menu, and scroll locking on open, plus improved hover/focus handling across breakpoints.

#### v1.0.20251021241001 (2025-10-21)

-   Updated primary navigation hover/focus states to use the brand green while keeping the CTA button styling intact, and reset default `.post`/`.page` margins to zero for tighter layouts.

#### v1.0.20251021234500 (2025-10-21)

-   Restrict Google review debug details to the WordPress admin area so public pages only show the friendly “No reviews yet” message and call-to-action.

#### v1.0.20251021231500 (2025-10-21)

-   Added graceful handling when the Places API omits aggregate ratings, including computed fallbacks, improved admin debug messaging, and guidance for clinics with no published reviews yet.

#### v1.0.20251021213045 (2025-10-21)

-   Added Google Places API key guidance, better admin error diagnostics, and explicit handling when Google returns partial review data.

#### v1.0.20251021104500 (2025-10-21)

-   Added a dedicated Google Places API key field, updated admin guidance, and improved error reporting for clinic reviews so server-side Place Details requests succeed with restricted keys.

#### v1.0.20251017114500 (2025-10-17)

-   Ensure the Anton typeface appears in the admin font dropdown and front-end critical CSS, and load it via Google Fonts.

#### v1.0.20251017094500 (2025-10-17)

-   Integrated the Anton typeface across the theme font settings and front-end mapping so heading selections render immediately.

## Update Sources

-   **Repository**: https://github.com/KazimirAlvis/Global-360-Theme
-   **Release API**: GitHub Releases API
-   **Download**: Main branch ZIP file

## Features

-   ✅ Automatic update notifications in WordPress admin
-   ✅ Manual update checking
-   ✅ Version comparison and status display
-   ✅ Integration with WordPress themes page
-   ✅ Admin menu for update management
-   ✅ GitHub repository integration

## Troubleshooting

-   If updates don't appear, try the manual update check
-   Ensure GitHub repository is public and accessible
-   Check that version numbers are properly formatted
-   WordPress checks for updates every 12 hours by default

## Admin Access

Access the theme update page via:
**WordPress Admin → Appearance → Theme Updates**
