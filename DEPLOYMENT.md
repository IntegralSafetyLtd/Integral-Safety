# Integral Safety - Krystal cPanel Deployment Guide

## Domain
- **URL:** https://integralsafetyltd.co.uk/
- **Hosting:** Krystal (cPanel with Node.js)

## GitHub Repository
- **URL:** https://github.com/IntegralSafetyLtd/Integral-Safety.git

## Required Node.js Version
- **Minimum:** 18.17+
- **Recommended:** 20.19.4 (LTS)

---

## Step 1: Prepare Next.js Config

Add `output: 'standalone'` to `next.config.mjs`:

```javascript
const nextConfig = {
  output: 'standalone',  // ADD THIS LINE
  images: {
    remotePatterns: [],
  },
  // ... rest of config
}
```

---

## Step 2: Create Node.js Application in cPanel

1. Log into Krystal cPanel
2. Go to **Setup Node.js App**
3. Click **Create Application**
4. Settings:
   - **Node.js version:** `20.19.4`
   - **Application mode:** `Production`
   - **Application root:** `integralsafetyltd.co.uk` (or your domain folder)
   - **Application URL:** `integralsafetyltd.co.uk`
   - **Application startup file:** `server.js`

---

## Step 3: Environment Variables

Add these in the cPanel Node.js app settings:

| Variable | Value |
|----------|-------|
| `DATABASE_URI` | `postgresql://neondb_owner:npg_OeYzsyuQH5w3@ep-floral-night-a2bqg8pl-pooler.eu-west-2.aws.neon.tech/neondb?sslmode=require` |
| `PAYLOAD_SECRET` | Generate a new random string for production (32+ chars) |
| `NEXT_PUBLIC_SITE_URL` | `https://integralsafetyltd.co.uk` |
| `CONTACT_EMAIL` | `info@integralsafetyltd.co.uk` |
| `RESEND_API_KEY` | Your Resend API key (get from resend.com) |
| `NODE_ENV` | `production` |
| `PORT` | (Usually set automatically by cPanel) |

---

## Step 4: Deploy Code via SSH

Connect to your server via SSH and run:

```bash
# Navigate to your domain folder
cd ~/integralsafetyltd.co.uk

# If folder is empty, clone directly
git clone https://github.com/IntegralSafetyLtd/Integral-Safety.git .

# OR if folder has existing files, clear it first
rm -rf * .[^.]*
git clone https://github.com/IntegralSafetyLtd/Integral-Safety.git .
```

---

## Step 5: Install Dependencies and Build

In SSH (from your app folder):

```bash
# Enter the Node.js virtual environment (cPanel provides this)
source /home/YOUR_USERNAME/nodevenv/integralsafetyltd.co.uk/20/bin/activate

# Install dependencies
npm install

# Build the application
npm run build
```

---

## Step 6: Start the Application

Option A: Via cPanel
- Go to **Setup Node.js App**
- Find your application
- Click **Start** or **Restart**

Option B: Via SSH
```bash
npm start
```

---

## Updating the Site

When you make changes:

1. Push changes to GitHub
2. SSH into server
3. Run:
```bash
cd ~/integralsafetyltd.co.uk
git pull
source /home/YOUR_USERNAME/nodevenv/integralsafetyltd.co.uk/20/bin/activate
npm install
npm run build
```
4. Restart the Node.js app in cPanel

---

## Troubleshooting

### App won't start
- Check Node.js version is 20.x
- Verify all environment variables are set
- Check logs in cPanel or via SSH: `cat ~/logs/integralsafetyltd.co.uk.error.log`

### Database connection errors
- Verify DATABASE_URI is correct
- Ensure Neon database is active
- Check if IP whitelist is needed on Neon

### 502/503 errors
- Application may still be starting (wait 30-60 seconds)
- Check if build completed successfully
- Verify PORT is not conflicting

---

## Database

- **Provider:** Neon (Cloud PostgreSQL)
- **Region:** eu-west-2
- **Database:** neondb
- **Dashboard:** https://console.neon.tech

---

## CMS Admin Access

Once deployed, access the admin panel at:
- **URL:** https://integralsafetyltd.co.uk/admin

Create your first admin user when you access it for the first time.

---

## Contact Form

The contact form uses Resend for email delivery.
- Sign up at: https://resend.com
- Add your API key to `RESEND_API_KEY` environment variable
- Verify your domain in Resend dashboard

---

## Tech Stack

- **Frontend:** Next.js 15, React 19, TypeScript
- **CMS:** Payload CMS 3.66
- **Database:** PostgreSQL (Neon)
- **Styling:** Tailwind CSS
- **Email:** Resend
- **Hosting:** Krystal cPanel (Node.js)
