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
- Current version: matches `_S_VERSION` (currently `1.0.20251021104500`)

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