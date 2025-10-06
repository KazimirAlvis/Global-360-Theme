# Dynamic Doctor Info Pages Setup Guide

## Overview
I've created a dynamic doctor page system similar to your state pages. Here's what was implemented:

## New Features Added

### 1. Dynamic Doctor URLs
- **URL Pattern**: `/doctor/doctor-name/`
- **Example**: `/doctor/john-smith/` or `/doctor/dr-sarah-johnson/`
- Works just like your state pages: `/find-a-doctor/texas/`

### 2. New Template File
- **File**: `template-doctor-info.php`
- **Purpose**: Displays individual doctor information pages
- **Features**:
  - Doctor photo, name, title, bio
  - Associated clinic information with addresses/phones
  - Professional styling with responsive design
  - Back to Find a Doctor navigation

### 3. Helper Functions
Added to `functions.php`:
- `get_doctor_page_url($doctor)` - Generate doctor page URLs
- `get_doctors_for_clinic($clinic_id)` - Get doctors for specific clinic

### 4. Shortcodes for Display
Added to `clinic-meta.php`:

#### [cpt360_clinic_doctors]
Display doctors for a specific clinic:
```
[cpt360_clinic_doctors clinic_id="123" title="Our Doctors" show_photos="true"]
```

#### [cpt360_all_doctors] 
Display all doctors with filtering:
```
[cpt360_all_doctors limit="10" show_photos="true" show_clinics="true"]
```

### 5. Professional Styling
Added comprehensive CSS for:
- Doctor card layouts
- Photo styling with circular borders
- Hover effects and transitions
- Responsive grid layouts
- Professional color scheme

## How It Works

### URL Structure
1. **State Pages**: `/find-a-doctor/texas/` → `template-find-a-doctor-state.php`
2. **Doctor Pages**: `/doctor/john-smith/` → `template-doctor-info.php`

### Data Flow
1. URL slug matches doctor `post_name` in database
2. Template loads doctor data from CPT meta fields
3. Associated clinics loaded via `clinic_id` meta field
4. Professional layout displays all information

## Usage Examples

### On Clinic Pages
Add doctor listings to any clinic page:
```html
<h2>Meet Our Doctors</h2>
[cpt360_clinic_doctors show_photos="true" show_bios="false"]
```

### On General Pages
Display all doctors:
```html
<h2>Our Medical Team</h2>
[cpt360_all_doctors limit="6" show_clinics="true"]
```

### Manual Links
Generate doctor page links in templates:
```php
$doctor_url = get_doctor_page_url($doctor_post);
echo '<a href="' . esc_url($doctor_url) . '">View Profile</a>';
```

## Activation Steps

### IMPORTANT: Flush Rewrite Rules
After adding these files, you MUST flush WordPress rewrite rules:

**Method 1 - WordPress Admin:**
1. Go to WordPress Admin → Settings → Permalinks
2. Click "Save Changes" (don't change anything, just save)
3. This flushes the rewrite rules

**Method 2 - Add to functions.php temporarily:**
```php
// Add this temporarily, visit your site, then remove it
add_action('init', function() {
    flush_rewrite_rules();
});
```

### Testing
1. Create a test doctor in WordPress admin
2. Note the doctor's slug (URL-friendly name)
3. Visit: `yoursite.com/doctor/doctor-slug/`
4. Should display the doctor's information page

## File Structure Added

```
/wp-content/themes/Global-360-Theme/
├── template-doctor-info.php          (NEW - Individual doctor pages)
├── functions.php                     (MODIFIED - Added URL routing)
├── inc/meta-boxes/clinic-meta.php   (MODIFIED - Added shortcodes)
└── style.css                        (MODIFIED - Added doctor styling)
```

## Integration Points

### Existing CPT Fields Used
- `doctor_name` - Doctor's display name
- `doctor_title` - Professional title/specialty  
- `doctor_bio` - Biography text
- `_doctor_photo_id` - WordPress media library photo
- `clinic_id` - Array of associated clinic IDs

### Fallbacks
- If no uploaded photo: Uses `/assets/doctor-images/doctor-slug.jpg`
- If no custom name: Uses post title
- 404 handling for non-existent doctors

## SEO Benefits
- Clean, semantic URLs: `/doctor/dr-john-smith/`
- Individual pages for each doctor (better than modal popups)
- Structured data ready for enhancement
- Internal linking between doctors and clinics

## Next Steps
1. Flush rewrite rules (critical!)
2. Test with existing doctors
3. Add shortcodes to clinic pages as needed
4. Consider adding doctor search/filtering functionality

## Troubleshooting
- **404 errors**: Rewrite rules not flushed
- **Blank pages**: PHP errors in template
- **Missing styling**: CSS not loading properly
- **No doctors showing**: Check clinic_id associations