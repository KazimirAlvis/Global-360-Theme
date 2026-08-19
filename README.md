# Global 360 Theme

> Stage 2 responsibility note: when **360 Platform Core** is active, Core owns canonical `clinic` and `doctor` post-type registration and the shared state/data contracts. This theme retains a deprecated registration fallback for local transition safety and continues to own presentation and editing UI.

API-sourced Clinic and Doctor identity/operational fields are presented as read-only in wp-admin. API Sync remains authoritative; any future manual override must use a separate field with explicit precedence.

Current release: `1.0.20260814001500`

A comprehensive WordPress theme designed for Patient 360 medical websites, featuring dynamic clinic finder functionality, interactive maps, and complete practice management capabilities.

## 🌟 Features

### Core Functionality

- **Custom Post Types**: Clinics and Doctors with comprehensive meta fields
- **Dynamic State Pages**: Interactive clinic finder with `/find-a-doctor/{state}` routing
- **Interactive Maps**: Leaflet integration with Google Maps geocoding
- **Global Settings**: Comprehensive admin interface for theme customization
- **Favicon Bundle Manager**: Upload PNG/SVG/ICO/Apple-touch/manifest assets directly from the 360 Settings UI
- **Social Media Integration**: Font Awesome icons with dynamic social links
- **SASS Architecture**: Modular styling with automatic compilation
- **Gutenberg Support**: Full block editor compatibility

### Custom Post Types

#### Clinics

- Complete address information with geocoding
- Custom logos and thumbnail images
- Phone numbers and website links
- Detailed clinic descriptions and bios
- Associated doctors management

#### Doctors

- Professional headshots and thumbnails
- Detailed biographical information
- Specialty and practice details
- Clinic associations

### Dynamic Features

- **State-based Clinic Finder**: Interactive US map with state-specific clinic listings
- **Google Maps Integration**: Automatic geocoding for precise clinic locations
- **Map Filtering**: Dynamic pin filtering by selected state
- **Responsive Design**: Mobile-optimized interface for all devices

## 🚀 Installation

> **Local development workflow**
>
> The active WordPress theme directory is the Git checkout:
>
> `wp-content/themes/Global-360-Theme/`
>
> WordPress runs the same files that Git tracks. To develop locally:
>
> 1. Pull the latest `main` before starting work.
> 2. Edit and test directly in the active theme directory.
> 3. Review `git status` and `git diff`.
> 4. When shipping theme changes, follow [`docs/THEME-UPDATES.md`](docs/THEME-UPDATES.md) for the coordinated version bump, changelog, commit, push, and production update process.

### Requirements

- WordPress 5.0 or higher
- PHP 7.4 or higher
- Google Maps API key (for geocoding)

### Quick Installation

1. **Download or Clone**:

    ```bash
    git clone https://github.com/KazimirAlvis/Global-360-Theme.git
    cd Global-360-Theme
    ```

2. **Upload to WordPress**:
    - Upload the theme folder to `/wp-content/themes/`
    - Activate via WordPress admin

3. **Configure API Keys**:
    - Navigate to **Appearance > Theme Settings > Assessment**
    - Add your Google Maps API key

## ⚙️ Configuration

### Theme Settings

Access comprehensive theme settings via **Appearance > Theme Settings**:

#### Colors & Fonts Tab

- Primary and secondary color schemes
- Custom typography settings
- Font family selections

#### Header & Footer Tab

- Custom logo upload
- Header styling options
- Footer content management
- Social media links with Font Awesome icons
- Favicon bundle manager with bulk upload for PNG, SVG, ICO, Apple touch icon, and web manifest files
- Optional Apple web app title override for home screen installs

#### Favicons & Web App Manifest

The 360 Settings header tab now includes a **Favicon & Manifest** panel:

1. Click **Upload favicon files** to open the bulk uploader. The theme auto-maps filenames like `favicon-96x96.png`, `favicon.svg`, `favicon.ico`, `apple-touch-icon.png`, and `site.webmanifest` to the correct slots.
2. Use the per-row **Select file** buttons if you prefer to upload assets individually; invalid formats trigger an inline warning.
3. Set the optional **Apple web app title** to override the default site name for iOS home screen shortcuts.
4. After saving, the theme outputs the appropriate `<link rel="icon">`, Apple touch icon, manifest, and Apple title meta tags in the site `<head>`.

#### Assessment Tab

- Google Maps API key configuration
- Assessment tool integration settings

### Linktree Landing Page

1. Open **Appearance → 360 Settings → Header & Footer** and upload the optional _Linktree Logo_ (the header logo is used as a fallback).
2. Verify your primary color, phone number, assessment ID, and social links are filled out in the 360 settings—they drive the auto-generated buttons.
3. Create a new WordPress page, assign the **Linktree Landing** template from the Page Attributes panel, and publish.
4. The page renders a white Linktree-style card with CTAs for the PR360 assessment, Find a Doctor, homepage, tap-to-call, and social icons using the brand primary color.

### Google Maps Setup

1. Obtain a Google Maps API key from [Google Cloud Console](https://console.cloud.google.com/)
2. Enable the following APIs:
    - Maps JavaScript API
    - Geocoding API
3. Add the API key in **Theme Settings > Assessment**

## 📁 File Structure

```
global-360-theme/
├── assets/                     # Images and media assets
│   ├── clinic-images/         # Clinic logos and images
│   ├── doctor-images/         # Doctor photos
│   └── state_svg/             # US state SVG files
├── clinic-partials/           # Clinic template components
├── inc/                       # Theme includes
│   ├── meta-boxes/           # Custom meta box configurations
│   │   ├── clinic-meta.php   # Clinic custom fields
│   │   ├── doctors-meta.php  # Doctor custom fields
│   │   └── clinic-doctors.php # Clinic-doctor associations
│   ├── settings.php          # Admin settings interface
│   └── template-*.php        # Template helper functions
├── sass/                      # SASS source files
│   ├── base/                 # Base styles and variables
│   ├── components/           # UI components
│   ├── layout/               # Layout styles
│   ├── pages/                # Page-specific styles
│   └── themes/               # Color and font themes
├── template-parts/           # Template partials
├── functions.php             # Core theme functionality
├── page-find-a-doctor.php    # State grid page template
├── template-find-a-doctor-state.php # Dynamic state pages
├── single-clinic.php         # Individual clinic pages
└── front-page.php           # Homepage template
```

## 🎨 SASS Development

### Live Compilation

The theme includes VS Code Live Sass Compiler configuration:

1. Install the **Live Sass Compiler** extension in VS Code
2. Open the theme folder in VS Code
3. Click "Watch Sass" in the status bar
4. SASS files will automatically compile to CSS on save

### SASS Architecture

- **Base**: Variables, mixins, reset styles
- **Components**: Buttons, cards, forms, modals
- **Layout**: Grid, header, footer, global layout
- **Pages**: Page-specific styling
- **Themes**: Color schemes and typography

## 🗺️ Dynamic Routing

### State Pages

The theme automatically creates dynamic routes for state-based clinic listings:

- `/find-a-doctor/texas` - Texas clinics
- `/find-a-doctor/california` - California clinics
- `/find-a-doctor/florida` - Florida clinics

### Rewrite Rules

Custom rewrite rules handle state-based URLs and fallback to default clinic page for states without clinics.

## 📱 Responsive Design

- **Mobile-first approach** with responsive breakpoints
- **Touch-friendly interfaces** for mobile devices
- **Optimized map interactions** for all screen sizes
- **Accessible navigation** with keyboard support
- **Adaptive footer layout** that wraps gracefully on tablets and centers content on small screens

## 🔌 API Integration

### Google Maps Geocoding

- Automatic address geocoding for clinic locations
- Fallback handling for failed geocoding attempts
- Optimized API usage with caching

### Social Media

- Dynamic social link management
- Font Awesome icon integration
- Customizable social platforms

## 🛠️ Development Commands

### SASS Compilation

```bash
# Manual compilation (if needed)
sass sass/main.scss style.css --watch
```

### CSS Minification

After editing `style.css`, regenerate the minified build with a one-off `npx` command—no local install required:

```bash
npx --yes clean-css-cli@5.6.3 -o style-min.css style.css
```

Run the command from the theme directory (`wp-content/themes/Global-360-Theme/`). The tool will fetch `clean-css-cli` on the fly, compress `style.css`, and overwrite `style-min.css`. Commit both files together so WordPress loads the updated minified asset everywhere.

### Code Quality

```bash
# PHP Code Standards
composer lint:wpcs

# JavaScript Linting
npm run lint:js

# SASS Linting
npm run lint:scss
```

## Theme Updates and Deployment

[`docs/THEME-UPDATES.md`](docs/THEME-UPDATES.md) is the authoritative versioning, changelog, and deployment guide.

For each release, edit and test in the active Git checkout, review the diff, coordinate the version in `functions.php` and `style.css`, update the changelog, commit, push `main`, and update production sites through the WordPress theme update mechanism. Routine deployments do not require a GitHub Release.

## �📝 Content Management

### Adding Clinics

1. Navigate to **Clinics > Add New**
2. Fill in all required fields:
    - Clinic name and description
    - Complete address information
    - Contact details
    - Logo/thumbnail images
3. Associate doctors if applicable
4. Publish to make available on maps

### Adding Doctors

1. Navigate to **Doctors > Add New**
2. Complete doctor profile:
    - Professional photo
    - Biographical information
    - Specialties and credentials
3. Associate with relevant clinics

### State Page Management

State pages are automatically generated based on clinic locations. No manual page creation required.

## 🎯 Customization

### Adding New States

1. Add state SVG file to `assets/state_svg/`
2. Update state mapping in `page-find-a-doctor.php`
3. Clinics in new states will automatically appear

### Custom Styling

- Modify SASS files in the `sass/` directory
- Use theme settings for colors and fonts
- Override specific components in `sass/components/`

### Template Customization

- Override templates by copying to child theme
- Modify clinic partials in `clinic-partials/` directory
- Customize meta boxes in `inc/meta-boxes/`

## 🔒 Security Features

- **Nonce verification** for all form submissions
- **Capability checks** for admin functions
- **Input sanitization** for all user data
- **SQL injection prevention** with prepared statements

## 📞 Support

For technical support or customization requests, please contact the development team or create an issue in the GitHub repository.

## 📄 License

Licensed under GPLv2 or later. Use it to create amazing medical practice websites!
