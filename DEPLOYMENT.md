# Integral Safety - PHP Site Deployment Guide

## Overview
- **Site:** https://integralsafetyltd.co.uk
- **Hosting:** Krystal (cPanel)
- **Stack:** PHP 8.x + MySQL
- **Local Files:** `H:\VSProjects\Integral Safety\integral-safety-php`
- **Zip File:** `H:\VSProjects\Integral Safety\integral-safety-php.zip`

---

## Step 1: Create MySQL Database User

In cPanel → **MySQL Databases**:

1. Find your database: `integralsafetylt_integralsafety_cms`
2. Scroll to **MySQL Users** → **Add New User**
   - Username: `integralsafetylt_integralsafety_admin`
   - Password: `P4r4d0x!integral`
3. Click **Create User**
4. Scroll to **Add User To Database**
   - Select user: `integralsafetylt_integralsafety_admin`
   - Select database: `integralsafetylt_integralsafety_cms`
5. Click **Add**
6. On privileges page, check **ALL PRIVILEGES**
7. Click **Make Changes**

---

## Step 2: Import Database Schema

In cPanel → **phpMyAdmin**:

1. Click on `integralsafety_cms` database (left sidebar)
2. Click **Import** tab (top menu)
3. Click **Choose File**
4. Select `database_schema.sql` from the zip or local folder
5. Click **Go** (bottom of page)
6. Wait for "Import has been successfully finished" message

**What gets created:**
- `users` table (with default admin user)
- `pages` table (home, about, contact)
- `services` table
- `training` table
- `testimonials` table
- `gallery` table
- `settings` table
- `contact_submissions` table

---

## Step 3: Upload Files to Server

### Option A: Via SSH/SCP (Recommended)

SSH is already configured on the development machine:

```
Host krystal
    HostName integralsafetyltd.co.uk
    Port 722
    User integralsafetylt
    IdentityFile ~/.ssh/krystal_deploy
```

**Upload commands:**
```bash
# Delete existing files (if needed)
ssh krystal "rm -rf ~/public_html/* ~/public_html/.[!.]*"

# Upload PHP site
scp -r "H:/VSProjects/Integral Safety/integral-safety-php/"* krystal:~/public_html/

# Or from Linux/Mac:
scp -r /path/to/integral-safety-php/* krystal:~/public_html/

# Verify upload
ssh krystal "ls -la ~/public_html/"
```

### Option B: Via cPanel File Manager

1. Go to cPanel → **File Manager**
2. Navigate to `public_html`
3. **Clear the folder** (delete existing files, or move to backup folder)
4. Click **Upload** in toolbar
5. Upload `integral-safety-php.zip`
6. Once uploaded, **right-click** the zip → **Extract**
7. Choose to extract to current directory (`public_html`)
8. Delete the zip file after extraction

### Option C: Via FTP/SFTP

1. Use FileZilla or similar FTP client
2. Connect to your Krystal server
3. Navigate to `public_html`
4. Upload all contents of `integral-safety-php` folder

---

## Step 4: Set File Permissions

In cPanel File Manager:

| Folder/File | Permission |
|-------------|------------|
| `uploads/` | 755 |
| `config.php` | 644 |
| All other files | 644 |
| All folders | 755 |

To change permissions:
1. Right-click folder/file
2. Select **Change Permissions**
3. Enter the number (e.g., 755)
4. Click **Change Permissions**

---

## Step 5: Verify Configuration

The `config.php` file should have:

```php
define('DB_HOST', 'localhost');
define('DB_NAME', 'integralsafetylt_integralsafety_cms');
define('DB_USER', 'integralsafetylt_integralsafety_admin');
define('DB_PASS', 'P4r4d0x!integral');

define('SITE_URL', 'https://integralsafetyltd.co.uk');
define('SITE_EMAIL', 'info@integralsafetyltd.co.uk');
```

**Important:** Also update `SECURE_KEY` to a random 32+ character string for security.

---

## Step 6: Test the Site

1. **Public Site:** https://integralsafetyltd.co.uk
   - Should show the homepage

2. **Admin Panel:** https://integralsafetyltd.co.uk/admin
   - Login with: `admin` / `admin123`

3. **Test pages:**
   - /services
   - /training
   - /about
   - /contact

---

## Step 7: Run Security Migration

**Run the database migration for 2FA and user management:**

1. Go to https://integralsafetyltd.co.uk/admin/migrate-security.php
2. This creates:
   - `login_attempts` table (audit log)
   - `two_factor_codes` table (email 2FA codes)
   - New columns on `users` table (2FA settings, account status)
3. Click "Log Out & Set Up 2FA" when prompted
4. **Delete the migration file after running:**
   ```bash
   ssh -p 722 krystal "rm ~/public_html/admin/migrate-security.php"
   ```

---

## Step 8: Set Up Two-Factor Authentication

After running the migration, log back in:

1. You'll be redirected to `/admin/setup-2fa.php`
2. Choose your 2FA method:
   - **Authenticator App** (Recommended) - Google Authenticator, Authy, etc.
   - **Email Codes** - Receive code via email each login
   - **Both** - Use either method
3. If using Authenticator:
   - Scan the QR code with your app
   - Enter the 6-digit code to verify
4. Once verified, you're logged in with full access

---

## Step 9: Post-Deployment Security

**IMPORTANT - Do these immediately:**

1. **Update SECURE_KEY in config.php:**
   - Generate random string: https://randomkeygen.com/
   - Update the value in config.php

2. **Verify .htaccess is working:**
   - Try accessing https://integralsafetyltd.co.uk/config.php
   - Should show "Forbidden" or redirect (not show the file contents)

3. **Add additional admin users:**
   - Go to /admin/users.php
   - Click "Add User"
   - Only @integralsafetyltd.co.uk emails allowed

---

## Admin Panel Features

| Section | URL | Function |
|---------|-----|----------|
| Dashboard | /admin | Overview & stats |
| Pages | /admin/pages.php | Edit page content |
| Services | /admin/services.php | Manage services |
| Training | /admin/training.php | Manage courses |
| Testimonials | /admin/testimonials.php | Client reviews |
| Gallery | /admin/gallery.php | Upload images |
| Messages | /admin/messages.php | Contact form inbox |
| Settings | /admin/settings.php | Site settings & password |
| Users | /admin/users.php | User management & login audit |

---

## Security Features

### Two-Factor Authentication (2FA)
- **Methods:** Authenticator app (TOTP), Email codes, or both
- **Mandatory:** All admin users must set up 2FA on first login
- **Setup:** Automatic redirect to `/admin/setup-2fa.php` for new users

### Rate Limiting & Account Lockout
- **Max attempts:** 5 failed logins
- **Lockout duration:** 15 minutes
- **IP-based:** Also tracks failed attempts per IP address

### Session Security
- **Timeout:** 1 hour of inactivity
- **Secure cookies:** HttpOnly, Secure flags set

### Email Domain Restriction
- Only `@integralsafetyltd.co.uk` email addresses can be admin users

### Login Audit Log
- All login attempts logged with:
  - Email/username
  - IP address
  - User agent
  - Success/failure status
  - Failure reason
- View at `/admin/users.php` (Recent Login Attempts section)

---

## Troubleshooting

### "Database connection failed"
- Check DB credentials in config.php
- Verify user has privileges on database
- Check database name matches

### 500 Internal Server Error
- Check PHP version (needs 8.0+)
- Check .htaccess syntax
- Check file permissions

### Admin login not working
- Verify database was imported
- Check users table has the admin record
- Clear browser cookies

### Images not uploading
- Check `uploads/` folder permissions (755)
- Check PHP upload limits in cPanel

### Contact form not sending
- PHP mail() function must be enabled
- Check contact_email in settings

### 2FA not working
- **QR code not scanning:** Manually enter the secret code shown below QR
- **Code always invalid:** Check server time is correct (TOTP is time-based)
- **Email codes not arriving:** Check spam folder, verify PHP mail() works
- **Locked out:** Reset 2FA via phpMyAdmin:
  ```sql
  UPDATE users SET twofa_method='none', twofa_secret=NULL, twofa_verified=0 WHERE email='your@email.com';
  ```

### Account locked after failed logins
- Wait 15 minutes, or reset via phpMyAdmin:
  ```sql
  UPDATE users SET failed_attempts=0, locked_until=NULL WHERE email='your@email.com';
  ```

---

## Updating the Site

To update after making local changes:

1. Make changes locally
2. Re-create zip file (or upload changed files via FTP)
3. Upload to server
4. Clear browser cache

### Quick SCP Commands

```bash
# Upload single file
scp -P 722 "H:/VSProjects/Integral Safety/integral-safety-php/path/to/file.php" krystal:~/public_html/path/to/

# Upload includes folder
scp -P 722 "H:/VSProjects/Integral Safety/integral-safety-php/includes/"* krystal:~/public_html/includes/

# Upload admin folder
scp -P 722 "H:/VSProjects/Integral Safety/integral-safety-php/admin/"*.php krystal:~/public_html/admin/

# Upload everything
scp -r -P 722 "H:/VSProjects/Integral Safety/integral-safety-php/"* krystal:~/public_html/

# Verify files on server
ssh -p 722 krystal "ls -la ~/public_html/"
```

---

## Backup Recommendations

Regular backups via cPanel:
- **Files:** File Manager → Select all → Compress → Download
- **Database:** phpMyAdmin → Export → Download

---

## Support

- **Site Issues:** Check error logs in cPanel
- **Database:** Use phpMyAdmin to inspect/fix data
- **Files:** Use File Manager or FTP

---

## File Structure

```
public_html/
├── .htaccess           # URL routing & security
├── config.php          # Database & site settings
├── index.php           # Homepage
├── services.php        # Services listing
├── service.php         # Single service page
├── training.php        # Training listing
├── training-single.php # Single training page
├── about.php           # About page
├── contact.php         # Contact page & form
├── 404.php             # Error page
├── database_schema.sql # Database structure
│
├── admin/              # Admin panel
│   ├── index.php       # Dashboard
│   ├── login.php       # Login page (with 2FA flow)
│   ├── logout.php      # Logout handler
│   ├── verify-2fa.php  # 2FA verification page
│   ├── setup-2fa.php   # 2FA setup (QR code, email)
│   ├── users.php       # User management & audit log
│   ├── pages.php       # Page editor
│   ├── services.php    # Services manager
│   ├── training.php    # Training manager
│   ├── section-editor.php # Section editor
│   ├── testimonials.php
│   ├── gallery.php     # Image uploads
│   ├── messages.php    # Contact inbox
│   ├── settings.php    # Site settings
│   └── includes/       # Admin templates
│       ├── header.php
│       └── footer.php
│
├── includes/           # Shared libraries
│   ├── header.php      # Public header
│   ├── footer.php      # Public footer
│   ├── database.php    # Database connection
│   ├── functions.php   # Helper functions
│   ├── auth.php        # Authentication & 2FA
│   ├── totp.php        # TOTP library (Google Auth)
│   ├── sections.php    # Section rendering
│   └── image-processor.php # Image optimization
│
├── assets/
│   ├── css/
│   ├── js/
│   └── images/
│       └── logo.png
│
└── uploads/            # User uploaded images
```

---

*Last updated: 12 January 2026*
