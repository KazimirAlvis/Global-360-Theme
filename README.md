# Global 360 Theme

A comprehensive WordPress theme designed for Patient 360 medical websites, featuring dynamic clinic finder functionality, interactive maps, and complete practice management capabilities.

## 🌟 Features

### Core Functionality

-   **Custom Post Types**: Clinics and Doctors with comprehensive meta fields
-   **Dynamic State Pages**: Interactive clinic finder with `/find-a-doctor/{state}` routing
-   **Interactive Maps**: Leaflet integration with Google Maps geocoding
-   **Global Settings**: Comprehensive admin interface for theme customization
-   **Social Media Integration**: Font Awesome icons with dynamic social links
-   **SASS Architecture**: Modular styling with automatic compilation
-   **Gutenberg Support**: Full block editor compatibility

### Custom Post Types

#### Clinics

-   Complete address information with geocoding
-   Custom logos and thumbnail images
-   Phone numbers and website links
-   Detailed clinic descriptions and bios
-   Associated doctors management

#### Doctors

-   Professional headshots and thumbnails
-   Detailed biographical information
-   Specialty and practice details
-   Clinic associations

### Dynamic Features

-   **State-based Clinic Finder**: Interactive US map with state-specific clinic listings
-   **Google Maps Integration**: Automatic geocoding for precise clinic locations
-   **Map Filtering**: Dynamic pin filtering by selected state
-   **Responsive Design**: Mobile-optimized interface for all devices

## 🚀 Installation

> **Local workflow note**
>
> When developing on Patient‑360 infrastructure you will see **two copies** of the theme:
>
> - `wp-content/themes/Global-360-Theme` – the copy WordPress actively runs.
> - `Global-360-Theme.repo/` – the Git checkout you commit and push to GitHub.
>
> To keep version bumps painless:
>
> 1. Always start in `Global-360-Theme.repo/` and run `git pull` so the repo matches GitHub.
> 2. Make and test your changes **inside the repo copy first**.
> 3. Copy the updated files into `wp-content/themes/Global-360-Theme` when you need WordPress to use them.
> 4. Bump versions / update `THEME-UPDATES.md`, commit, and push from the repo directory.
>
> Skipping step 1 or editing the live copy first is what forces the long “rebase & conflict” cleanup at the end of the day.

### Requirements

-   WordPress 5.0 or higher
-   PHP 7.4 or higher
-   Google Maps API key (for geocoding)

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

-   Primary and secondary color schemes
-   Custom typography settings
-   Font family selections

#### Header & Footer Tab

-   Custom logo upload
-   Header styling options
-   Footer content management
-   Social media links with Font Awesome icons

#### Assessment Tab

-   Google Maps API key configuration
-   Assessment tool integration settings

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

-   **Base**: Variables, mixins, reset styles
-   **Components**: Buttons, cards, forms, modals
-   **Layout**: Grid, header, footer, global layout
-   **Pages**: Page-specific styling
-   **Themes**: Color schemes and typography

## 🗺️ Dynamic Routing

### State Pages

The theme automatically creates dynamic routes for state-based clinic listings:

-   `/find-a-doctor/texas` - Texas clinics
-   `/find-a-doctor/california` - California clinics
-   `/find-a-doctor/florida` - Florida clinics

### Rewrite Rules

Custom rewrite rules handle state-based URLs and fallback to default clinic page for states without clinics.

## 📱 Responsive Design

-   **Mobile-first approach** with responsive breakpoints
-   **Touch-friendly interfaces** for mobile devices
-   **Optimized map interactions** for all screen sizes
-   **Accessible navigation** with keyboard support
-   **Adaptive footer layout** that wraps gracefully on tablets and centers content on small screens

## 🔌 API Integration

### Google Maps Geocoding

-   Automatic address geocoding for clinic locations
-   Fallback handling for failed geocoding attempts
-   Optimized API usage with caching

### Social Media

-   Dynamic social link management
-   Font Awesome icon integration
-   Customizable social platforms

## 🛠️ Development Commands

### SASS Compilation

```bash
# Manual compilation (if needed)
sass sass/main.scss style.css --watch
```

### Code Quality

```bash
# PHP Code Standards
composer lint:wpcs

# JavaScript Linting
npm run lint:js

# SASS Linting
npm run lint:scss
```

## � Theme Update Workflow (No GitHub Release Required)

When you ship theme changes, keep the following lightweight flow so the WordPress updater notices the new build without creating a GitHub release:

1. **Bump the version numbers**
    - Update `_S_VERSION` in `functions.php`.
    - Update the `Version:` header at the top of `style.css`.
    - (Optional) add a bullet to `THEME-UPDATES.md` describing the change.
2. **Commit and push to `main`**
    ```bash
    git add functions.php style.css THEME-UPDATES.md
    git commit -m "Sync theme version to <new version>"
    git push origin main
    ```
3. **Refresh WordPress**
    - In the WordPress admin, open **Dashboard → Updates** and click **Check Again**.
    - WordPress compares the version in GitHub to the installed theme and shows the familiar **Update Theme** button.

You only need to create a GitHub release if you specifically want to distribute a packaged ZIP or trigger third-party tooling. Routine deployments can stay on the “push to GitHub → update from the WP admin” loop.

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

-   Modify SASS files in the `sass/` directory
-   Use theme settings for colors and fonts
-   Override specific components in `sass/components/`

### Template Customization

-   Override templates by copying to child theme
-   Modify clinic partials in `clinic-partials/` directory
-   Customize meta boxes in `inc/meta-boxes/`

## 🔒 Security Features

-   **Nonce verification** for all form submissions
-   **Capability checks** for admin functions
-   **Input sanitization** for all user data
-   **SQL injection prevention** with prepared statements

## 📞 Support

For technical support or customization requests, please contact the development team or create an issue in the GitHub repository.

## 📄 License

Licensed under GPLv2 or later. Use it to create amazing medical practice websites!
