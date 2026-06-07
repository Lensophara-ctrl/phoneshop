# 🚀 Phoneshop Deployment Guide (Free)

## **Recommended: Deploy on Render**

Your project already has `render.yaml` configured for Render deployment.

### **Step 1: Prepare Your Repository**
✅ Already done! Credentials have been secured.

### **Step 2: Deploy on Render**

1. Go to https://render.com
2. Click **"New +"** → **"Web Service"**
3. Connect your GitHub account and select `Lensophara-ctrl/phoneshop`
4. Fill in the configuration:
   - **Name:** `phoneshop`
   - **Region:** Choose closest to you
   - **Branch:** `main`
   - **Build Command:** (leave empty - Docker builds automatically)
   - **Start Command:** (leave empty - Docker starts automatically)

5. Click **"Create Web Service"**

Render will automatically detect your `render.yaml` and create the database.

### **Step 3: Set Environment Variables**

In the Render dashboard, go to your service → **Environment**:

```
APP_KEY=base64:YOUR_RANDOM_KEY_HERE
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com
LOG_CHANNEL=stderr
```

To generate `APP_KEY`, run locally:
```bash
php artisan key:generate --show
```

### **Step 4: Database Setup**

Render creates the database automatically. After deployment:

1. Go to your Render service dashboard
2. Get the database connection string from the connected database
3. It will be automatically set as `DATABASE_URL`

### **Step 5: Run Migrations**

After first deployment, run migrations via Render shell:

1. Click your service name
2. Go to **"Shell"** tab
3. Run:
```bash
php artisan migrate --force
php artisan db:seed
```

### **Alternative: Deploy on Railway**

If you prefer Railway:

1. Go to https://railway.app
2. Click **"New Project"** → **"Deploy from GitHub repo"**
3. Select `Lensophara-ctrl/phoneshop`
4. Add the same environment variables
5. Railway auto-detects `render.yaml` and deploys

Railway offers $5/month free credit (better than Render's free tier).

---

## **Important Notes**

⚠️ **Free Tier Limitations:**
- Render free: 0.5GB RAM, auto-sleeps after 15 min inactivity
- Railway free: $5/month credit, 500 GB/month egress
- Databases reset every 90 days on Render free tier

💡 **Recommendations:**
- Use a paid database (PlanetScale, Supabase) for production
- Consider upgrading to paid tier after testing
- Monitor your app performance and costs

---

## **Troubleshooting**

### Database connection failed?
- Check `DATABASE_URL` environment variable
- Ensure database migration ran successfully

### App won't start?
- Check logs in Render/Railway dashboard
- Verify `APP_KEY` is set correctly
- Check PHP extensions in Dockerfile

### Upload/storage issues?
- Configure cloud storage (AWS S3, Google Cloud) for production
- Free local storage will be lost when service restarts

---

**Your app will be live at:** `https://phoneshop.onrender.com` (or your custom name)

Need help with any step?
