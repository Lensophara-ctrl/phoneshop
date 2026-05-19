# Google Maps API Setup Guide

## Why Google Maps is Not Showing

The Google Maps is not displaying because the API key in your `.env` file is set to a placeholder value:
```
GOOGLE_MAPS_API_KEY=your_google_maps_api_key_here
```

You need to obtain a real Google Maps API key from Google Cloud Console.

## How to Get Google Maps API Key

### Step 1: Go to Google Cloud Console
1. Visit: https://console.cloud.google.com/
2. Sign in with your Google account

### Step 2: Create a New Project (or select existing)
1. Click on the project dropdown at the top
2. Click "New Project"
3. Enter project name (e.g., "PhoneShop")
4. Click "Create"

### Step 3: Enable Google Maps JavaScript API
1. In the left sidebar, go to "APIs & Services" → "Library"
2. Search for "Maps JavaScript API"
3. Click on it and click "Enable"

### Step 4: Create API Key
1. Go to "APIs & Services" → "Credentials"
2. Click "Create Credentials" → "API Key"
3. Copy the generated API key

### Step 5: Restrict API Key (Recommended for Security)
1. Click on the API key you just created
2. Under "Application restrictions":
   - Select "HTTP referrers (web sites)"
   - Add your domain: `http://127.0.0.1:8000/*` and `http://localhost:8000/*`
3. Under "API restrictions":
   - Select "Restrict key"
   - Check "Maps JavaScript API"
4. Click "Save"

### Step 6: Update .env File
1. Open `phoneshop/phoneshop/.env`
2. Replace the placeholder with your real API key:
```env
GOOGLE_MAPS_API_KEY=AIzaSyXXXXXXXXXXXXXXXXXXXXXXXXXXXXXX
```
3. Save the file

### Step 7: Clear Cache and Restart
```bash
php artisan config:clear
php artisan cache:clear
```

## Free Tier Limits

Google Maps API has a free tier:
- $200 free credit per month
- Approximately 28,000 map loads per month for free
- No credit card required for basic usage (but recommended)

## Alternative: Use Without API Key (Limited)

If you don't want to set up Google Maps API, you can:

1. Use OpenStreetMap instead (free, no API key needed)
2. Disable the map feature temporarily
3. Use a static map image

## Testing the Map

After setting up the API key:
1. Go to Deliveries section in admin panel
2. Click "View Map"
3. The map should now display with delivery locations

## Common Issues

### Map shows "For development purposes only" watermark
- This means you haven't set up billing in Google Cloud Console
- The map will still work but with the watermark
- To remove: Add a billing account in Google Cloud Console

### Map doesn't load at all
- Check browser console for errors (F12)
- Verify API key is correct in .env
- Make sure Maps JavaScript API is enabled
- Check API key restrictions

### "This page can't load Google Maps correctly"
- API key is invalid or restricted
- Check API key restrictions in Google Cloud Console
- Make sure your domain is whitelisted

## Current Delivery Map Location

The delivery map page is at:
- URL: `http://127.0.0.1:8000/deliveries/map`
- Menu: Admin Panel → Deliveries → View Map button

## Need Help?

If you encounter issues:
1. Check browser console (F12) for error messages
2. Verify API key is enabled for Maps JavaScript API
3. Check API key restrictions match your domain
4. Make sure you've cleared Laravel cache after updating .env
