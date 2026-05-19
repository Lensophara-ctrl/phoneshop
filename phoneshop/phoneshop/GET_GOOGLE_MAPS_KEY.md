# How to Get Google Maps API Key (Step by Step)

## Quick Steps (5 minutes)

### Step 1: Go to Google Cloud Console
Open this link in your browser:
**https://console.cloud.google.com/**

### Step 2: Sign In
- Use your Gmail account to sign in
- If you don't have one, create a free Gmail account first

### Step 3: Create a New Project
1. Click the project dropdown at the top (next to "Google Cloud")
2. Click "NEW PROJECT" button
3. Enter project name: `PhoneShop` (or any name you like)
4. Click "CREATE"
5. Wait a few seconds for the project to be created

### Step 4: Enable Maps JavaScript API
1. In the left menu, click "APIs & Services" → "Library"
2. In the search box, type: `Maps JavaScript API`
3. Click on "Maps JavaScript API" from the results
4. Click the blue "ENABLE" button
5. Wait for it to enable (takes a few seconds)

### Step 5: Create API Key
1. In the left menu, click "APIs & Services" → "Credentials"
2. Click "CREATE CREDENTIALS" at the top
3. Select "API key" from the dropdown
4. A popup will show your API key - COPY IT!
5. It will look like: `AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX`

### Step 6: Configure API Key (Optional but Recommended)
1. Click "RESTRICT KEY" in the popup (or click the key name later)
2. Under "Application restrictions":
   - Select "HTTP referrers (web sites)"
   - Click "ADD AN ITEM"
   - Add: `http://127.0.0.1:8000/*`
   - Click "ADD AN ITEM" again
   - Add: `http://localhost:8000/*`
3. Under "API restrictions":
   - Select "Restrict key"
   - Check only "Maps JavaScript API"
4. Click "SAVE"

### Step 7: Update Your .env File
1. Open: `phoneshop/phoneshop/.env`
2. Find the line: `GOOGLE_MAPS_API_KEY=...`
3. Replace with your new key:
```
GOOGLE_MAPS_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```
4. Save the file

### Step 8: Clear Laravel Cache
Open terminal in `phoneshop/phoneshop` folder and run:
```bash
php artisan config:clear
php artisan cache:clear
```

### Step 9: Test
1. Refresh your browser
2. The map should now work!

## Important Notes

### Free Tier
- Google gives you $200 FREE credit per month
- This equals about 28,000 map loads per month
- Perfect for development and small projects
- No credit card required initially

### If You See "For development purposes only" Watermark
- The map still works!
- This just means you haven't added billing
- To remove: Add a credit card in Google Cloud Console (you won't be charged unless you exceed free tier)

### Common Issues

**Issue: "This API project is not authorized to use this API"**
- Solution: Make sure you enabled "Maps JavaScript API" in Step 4

**Issue: "The provided API key is invalid"**
- Solution: Double-check you copied the entire key correctly

**Issue: Map still doesn't load**
- Solution: Clear browser cache (Ctrl+Shift+Delete)
- Solution: Make sure you ran `php artisan config:clear`

## Alternative: Use OpenStreetMap (No API Key Needed)

If you don't want to set up Google Maps, I can help you switch to OpenStreetMap which is completely free and requires no API key.

## Need Help?

If you get stuck at any step, let me know which step and I'll help you!
