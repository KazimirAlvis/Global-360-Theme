# Global 360 Theme Update System

## Overview
The Global 360 Theme now includes automatic update functionality that integrates with WordPress admin.

## How It Works

### Automatic Updates
- **Auto-update enabled**: WordPress will automatically check for and install theme updates
- **GitHub Integration**: Updates are pulled from the GitHub repository releases
- **Version Checking**: Compares local version with latest GitHub release

### Manual Update Checking
- Navigate to **Appearance → Theme Updates** in WordPress admin
- View current vs. latest version information
- Manually trigger update checks
- Direct link to themes page for updates

## Versioning System

### Current Version Management
- Version defined in `functions.php` as `_S_VERSION` constant
- Must match version in `style.css` header
- Current version: matches `_S_VERSION` (currently `1.0.20251027101500`)

### Release Process
1. Update version number in both:
   - `functions.php` - `_S_VERSION` constant
   - `style.css` - Version header
2. Commit and push changes to GitHub
3. Create a new release on GitHub with tag (e.g., `v1.0.1`)
4. WordPress will detect the update within 24 hours

### Version Format
- Automatic versions generated as `1.0.YYYYMMDDHHMMSS` (date + time) so every commit is unique
- If you cut a manual release, keep the same structure for consistency (e.g., `1.0.20251009130545`)

### Release History

#### v1.0.20251027101500 (2025-10-27)
- Applied the 360 Settings typography choices directly to headings and paragraph defaults while keeping the admin area untouched.

#### v1.0.20251024164826 (2025-10-24)
- Added 20px bottom margin to `.single-clinic pr360-questionnaire::part(begin-button)` to improve spacing beneath CTA buttons

#### v1.0.20251024162841 (2025-10-24)
- Restored dynamic font variables by outputting CSS both inline and in wp_head/login/admin contexts
- Enqueued only the Google Fonts required by the current 360 settings selection

#### v1.0.20251024160827 (2025-10-24)
- Added text-transform: uppercase to #floating-assessment-button pr360-questionnaire::part(begin-button)
- Reduced font-size from 18px to 16px for #floating-assessment-button pr360-questionnaire::part(begin-button)
- Reordered divs on find-a-doctor page: moved .body_heading before .state_grid_wrapper

#### v1.0.20251024031545 (2025-10-24)
- Updated the shadow DOM animation to use the same charcoal glow, ensuring the floating CTA never falls back to the old green tint.

#### v1.0.20251024023045 (2025-10-24)
- Swapped the pulse glow to a neutral charcoal tint so the floating CTA effect matches the theme palette without the green highlight.

#### v1.0.20251024014500 (2025-10-24)
- Bundled the shadow DOM pulse bootstrap script so the floating assessment CTA animates immediately after WordPress updates from GitHub.

#### v1.0.20251024002239 (2025-10-24)
- Synced theme packaging version with the latest deployed build so the WordPress updater recognises the pulse animation enhancements without requiring a GitHub release.

#### v1.0.20251023104512 (2025-10-23)
- Supercharged the floating assessment CTA with a shadow-root-aware pulse animation, persistent drop shadow, and 0px margin footprint so the button hugs its container cleanly.

#### v1.0.20251022143000 (2025-10-22)
- Refined the footer layout for responsive breakpoints, keeping desktop columns compact while introducing tablet/mobile wrapping with consistent padding and horizontal social links.

#### v1.0.20251022114500 (2025-10-22)
- Rebuilt the mobile navigation with an accessible hamburger toggle, full-screen overlay menu, and scroll locking on open, plus improved hover/focus handling across breakpoints.

#### v1.0.20251021241001 (2025-10-21)
- Updated primary navigation hover/focus states to use the brand green while keeping the CTA button styling intact, and reset default `.post`/`.page` margins to zero for tighter layouts.

#### v1.0.20251021234500 (2025-10-21)
- Restrict Google review debug details to the WordPress admin area so public pages only show the friendly “No reviews yet” message and call-to-action.

#### v1.0.20251021231500 (2025-10-21)
- Added graceful handling when the Places API omits aggregate ratings, including computed fallbacks, improved admin debug messaging, and guidance for clinics with no published reviews yet.

#### v1.0.20251021213045 (2025-10-21)
- Added Google Places API key guidance, better admin error diagnostics, and explicit handling when Google returns partial review data.

#### v1.0.20251021104500 (2025-10-21)
- Added a dedicated Google Places API key field, updated admin guidance, and improved error reporting for clinic reviews so server-side Place Details requests succeed with restricted keys.

#### v1.0.20251017114500 (2025-10-17)
- Ensure the Anton typeface appears in the admin font dropdown and front-end critical CSS, and load it via Google Fonts.

#### v1.0.20251017094500 (2025-10-17)
- Integrated the Anton typeface across the theme font settings and front-end mapping so heading selections render immediately.

## Update Sources
- **Repository**: https://github.com/KazimirAlvis/Global-360-Theme
- **Release API**: GitHub Releases API
- **Download**: Main branch ZIP file

## Features
- ✅ Automatic update notifications in WordPress admin
- ✅ Manual update checking
- ✅ Version comparison and status display
- ✅ Integration with WordPress themes page
- ✅ Admin menu for update management
- ✅ GitHub repository integration

## Troubleshooting
- If updates don't appear, try the manual update check
- Ensure GitHub repository is public and accessible
- Check that version numbers are properly formatted
- WordPress checks for updates every 12 hours by default

## Admin Access
Access the theme update page via:
**WordPress Admin → Appearance → Theme Updates**