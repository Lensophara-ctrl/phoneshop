# 🚀 Complete Render Deployment Guide for Phoneshop

## **STEP 1: Generate APP_KEY Locally**

Before deploying, generate a secure APP_KEY:

```bash
cd phoneshop/phoneshop
php artisan key:generate --show
```

Copy the output (looks like: `base64:xxxxxxxxxxxxx`)

---

## **STEP 2: Create Render Account & Connect GitHub**

1. Go to https://render.com
2. Sign up with GitHub (click "Continue with GitHub")
3. Authorize Render to access your GitHub account

---

## **STEP 3: Create Web Service**

1. Click **"New +"** in top-right
2. Select **"Web Service"**
3. Click **"Connect Repository"**
4. Find and select **`Lensophara-ctrl/phoneshop`**
5. Click **"Connect"**

---

## **STEP 4: Configure Service**

Fill in the configuration form:

| Field | Value |
|-------|-------|
| **Name** | `phoneshop` |
| **Environment** | Docker |
| **Region** | Select closest to you (e.g., Singapore, US) |
| **Branch** | `main` |
| **Build Command** | Leave empty (Docker handles it) |
| **Start Command** | Leave empty (Docker handles it) |

---

## **STEP 5: Create PostgreSQL Database**

1. In the same Render dashboard, click **"New +"**
2. Select **"PostgreSQL"**
3. Fill in:
   - **Name:** `phoneshop-db`
   - **Database:** `phoneshop`
   - **User:** `phoneshop_user`
   - **Region:** Same as your Web Service
   - **Plan:** Free

4. Click **"Create Database"**
5. Wait for database to be created (2-3 minutes)
6. Copy the **Internal Database URL** from the database details page

---

## **STEP 6: Set Environment Variables**

Go back to your Web Service → **Environment**

Add these variables:

```
APP_KEY=base64:YOUR_KEY_HERE
APP_ENV=production
APP_DEBUG=false
APP_URL=https://phoneshop.onrender.com
LOG_CHANNEL=stderr
DATABASE_URL=postgresql://phoneshop_user:PASSWORD@HOST:5432/phoneshop
```

**Replace:**
- `YOUR_KEY_HERE` - from Step 1
- `PASSWORD@HOST:5432` - from your database connection string

---

## **STEP 7: Deploy**

1. Click **"Create Web Service"**
2. Render will:
   - Pull your code from GitHub
   - Build Docker image
   - Start the container
   - Assign you a URL

**⏳ Wait 5-10 minutes for deployment to complete**

Monitor the deploy log:
- If you see ❌ errors, scroll down to see what failed
- Common issues: Wrong DATABASE_URL, missing APP_KEY

---

## **STEP 8: Run Database Migrations**

After successful deployment:

1. Go to your service → **"Shell"** tab
2. Run these commands:

```bash
php artisan migrate --force
```

This creates all database tables.

---

## **STEP 9: Access Your App**

Your app is now live at the Render URL provided (e.g., `https://phoneshop.onrender.com`)

**Admin login:**
- Email: `Lensophara@gmail.com`
- Password: `77778888`

⚠️ **Change these credentials immediately in production!**

---

## **STEP 10: (Optional) Set Custom Domain**

In Render service settings:
1. Go to **"Settings"** → **"Custom Domains"**
2. Add your domain
3. Follow DNS configuration steps

---

## **📋 Troubleshooting**

### ❌ **Deployment fails**
- Check logs for specific errors
- Verify DATABASE_URL is correct
- Ensure APP_KEY is set

### ❌ **App crashes after deploy**
- Go to **"Logs"** tab
- Look for error messages
- Common: Database not initialized (run migrations)

### ❌ **Database connection error**
- Verify DATABASE_URL environment variable
- Ensure database is in same region
- Check database credentials

### ❌ **File uploads not working**
- Render free tier doesn't have persistent storage
- Files are lost when service restarts
- **Solution:** Use S3 or another cloud storage

---

## **Free Tier Limitations**

- **RAM:** 0.5 GB (shared)
- **Auto-sleep:** Service sleeps after 15 minutes of no traffic
- **Database:** PostgreSQL database is free but resets every 90 days
- **Bandwidth:** Limited

**For production, upgrade to paid tier (~$12/month).**

---

## **Next Steps**

✅ Your phoneshop is live and ready!

- Change admin password
- Upload product images
- Configure payment gateway (if needed)
- Monitor logs and performance

**Need help?** Check Render docs: https://render.com/docs/

---

**Summary:**
1. Generate APP_KEY ✅
2. Create Render account ✅
3. Connect GitHub repo ✅
4. Create Web Service + Database ✅
5. Set environment variables ✅
6. Deploy ✅
7. Run migrations ✅
8. Access your app! 🎉

