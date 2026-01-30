# Integral Safety - Project Guidelines for Claude

## Project Overview

This is a PHP-based CMS for Integral Safety Ltd, a health and safety consultancy based in Leicestershire, UK. The site includes public pages, services, training courses, and a full admin panel.

## Technology Stack

- **Backend**: PHP 8.x (no framework, vanilla PHP)
- **Database**: MySQL/MariaDB
- **Frontend**: Tailwind CSS (via CDN), vanilla JavaScript
- **Rich Text Editor**: Quill.js
- **Email**: Resend API
- **Hosting**: Shared hosting with Apache/LiteSpeed

## Directory Structure

```
integral-safety-php/
├── admin/                  # Admin panel
│   ├── api/               # AJAX endpoints
│   ├── includes/          # Admin header/footer
│   ├── index.php          # Dashboard
│   ├── pages.php          # Page management
│   ├── services.php       # Services management
│   ├── training.php       # Training courses
│   ├── seo.php            # SEO settings
│   ├── settings.php       # Site settings
│   └── migrate-*.php      # Database migrations
├── includes/              # Shared PHP includes
│   ├── header.php         # Public header
│   ├── footer.php         # Public footer
│   ├── functions.php      # Helper functions
│   ├── database.php       # Database connection
│   ├── auth.php           # Authentication
│   ├── seo.php            # SEO helper functions
│   └── ...
├── uploads/               # User uploads (images)
├── assets/                # Static assets
├── config.php             # Configuration (contains credentials)
├── .htaccess              # URL rewriting & security
└── *.php                  # Public pages
```

## Database Strategy

**IMPORTANT**: This project connects to a **production database** on the live server.

- Database credentials are in `config.php`
- The database is hosted on the same server as the site
- **DO NOT** run destructive queries without explicit user confirmation
- **DO NOT** drop tables or truncate data
- Migrations are safe to run multiple times (they check for existing columns)

### Running Migrations

Migrations are accessed via the admin panel:
- `/admin/migrate-seo.php` - Adds SEO columns
- `/admin/migrate-security.php` - Security-related updates
- `/admin/migrate-sections.php` - Page sections

Always run migrations through the browser interface, not directly via CLI.

## Critical DO NOTs

### Database
- **DO NOT** run `DROP TABLE` or `TRUNCATE` commands
- **DO NOT** modify `config.php` database credentials without asking
- **DO NOT** delete user data without explicit confirmation

### Security
- **DO NOT** remove CSRF protection from forms
- **DO NOT** output user input without escaping (use `e()` function)
- **DO NOT** execute raw SQL with user input (use prepared statements)
- **DO NOT** remove password hashing or 2FA functionality

### Files
- **DO NOT** delete files in `/uploads/` without confirmation
- **DO NOT** modify `.htaccess` security rules without understanding implications
- **DO NOT** commit `config.php` to public repositories (contains API keys)

### Code Style
- **DO NOT** use US English spelling (use UK English: colour, organised, centre)
- **DO NOT** use browser `alert()` or `confirm()` dialogs - use styled modals/toasts
- **DO NOT** add emojis unless explicitly requested

## Coding Conventions

### PHP
- Use `<?php` opening tags (not short tags)
- Include files using `require_once` with path constants
- Use `e()` function for HTML escaping output
- Use `sanitize()` for input sanitization
- Use prepared statements for all database queries

### Database Functions
```php
dbFetchOne($sql, $params)   // Fetch single row
dbFetchAll($sql, $params)   // Fetch all rows
dbExecute($sql, $params)    // Execute INSERT/UPDATE/DELETE
dbLastId()                  // Get last insert ID
```

### Settings
```php
getSetting($key, $default)  // Get setting value
updateSetting($key, $value) // Save setting (INSERT ON DUPLICATE KEY UPDATE)
```

### Authentication
```php
requireLogin()              // Redirect to login if not authenticated
isAdmin()                   // Check if user has admin role
verifyCSRFToken($token)     // Validate CSRF token
csrfField()                 // Output hidden CSRF input field
```

### Form Pattern
```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        $error = 'Invalid request. Please try again.';
    } else {
        // Process form
    }
}
```

## Admin Panel Patterns

### Page Structure
```php
require_once __DIR__ . '/../config.php';
require_once INCLUDES_PATH . '/database.php';
require_once INCLUDES_PATH . '/auth.php';
require_once INCLUDES_PATH . '/functions.php';

requireLogin();

// Handle form submissions...

require_once __DIR__ . '/includes/header.php';
// Page content...
require_once __DIR__ . '/includes/footer.php';
```

### Flash Messages
```php
$_SESSION['flash_message'] = 'Success message';
$_SESSION['flash_type'] = 'success'; // or 'error'
header('Location: /admin/page.php');
exit;
```

### UI Components
- Use Tailwind CSS classes
- Orange accent colour: `orange-500`, `orange-600`
- Navy colour scheme: `navy-700`, `navy-800`, `navy-900`
- Form inputs: `border border-gray-300 rounded-lg focus:ring-2 focus:ring-orange-500`
- Buttons: `bg-orange-500 text-white rounded-lg hover:bg-orange-600`

## SEO System

The SEO system stores settings in the `settings` table with keys prefixed `seo_`:

### Verification Codes
- `seo_google_verification`, `seo_bing_verification`, `seo_pinterest_verification`, `seo_facebook_verification`

### Analytics
- `seo_ga4_id`, `seo_gtm_id`, `seo_clarity_id`, `seo_facebook_pixel_id`, `seo_linkedin_partner_id`
- `seo_custom_head_scripts`, `seo_custom_body_scripts`

### Meta Defaults
- `seo_title_suffix`, `seo_default_og_image`, `seo_twitter_card_type`, `seo_default_robots`

### Schema.org
- `seo_schema_organization_enabled`, `seo_schema_business_type`, `seo_schema_legal_name`
- Address fields: `seo_schema_street_address`, `seo_schema_address_locality`, etc.
- `seo_schema_latitude`, `seo_schema_longitude`, `seo_schema_opening_hours`

### Per-Page SEO (in pages/services/training tables)
- `seo_title` - Custom title for search results (max 70 chars)
- `focus_keyphrase` - Target keyword
- `canonical_url` - Custom canonical URL
- `robots_directive` - index/noindex settings
- `og_image` - Custom Open Graph image

## URL Structure

Clean URLs are handled via `.htaccess`:
- `/` → `index.php`
- `/services` → `services.php`
- `/services/{slug}` → `service.php?slug={slug}`
- `/training` → `training.php`
- `/training/{slug}` → `training-single.php?slug={slug}`
- `/about`, `/contact`, `/privacy`, `/terms` → corresponding `.php` files
- `/robots.txt` → `robots.php` (dynamic)
- `/sitemap.xml` → static file generated by admin

## Testing Changes

After making changes:
1. Test in browser (local development or staging)
2. Check for PHP errors in error logs
3. Verify CSRF tokens are working on forms
4. Test on mobile viewport (responsive design)
5. Validate structured data at https://search.google.com/test/rich-results

## Useful Commands

The project doesn't use Composer or npm for the main site. Dependencies are loaded via CDN.

To test locally, use PHP's built-in server:
```bash
cd integral-safety-php
php -S localhost:8000
```

## Contact & Support

- Site: https://integralsafetyltd.co.uk
- Admin: https://integralsafetyltd.co.uk/admin/
