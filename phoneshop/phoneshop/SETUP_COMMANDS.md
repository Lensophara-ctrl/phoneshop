# Setup Commands for Receipt Upload Feature

## 1. Run the Migration
```bash
cd phoneshop/phoneshop
php artisan migrate
```

## 2. Create Storage Link (if not already created)
```bash
php artisan storage:link
```

## 3. Set Proper Permissions (Linux/Mac)
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

## 4. Test the Feature

### Option A: Using the Web Interface
1. Start your Laravel server:
   ```bash
   php artisan serve
   ```

2. Open in browser:
   ```
   http://localhost:8000/test-receipt-upload.html
   ```

### Option B: Using cURL
```bash
# Replace with actual bill number and file path
curl -X POST http://localhost:8000/api/upload/receipt \
  -F "bill_no=YOUR_BILL_NUMBER" \
  -F "file=@/path/to/your/receipt.jpg"
```

### Option C: Using Postman
1. Method: POST
2. URL: `http://localhost:8000/api/upload/receipt`
3. Body: form-data
   - Key: `bill_no`, Value: `YOUR_BILL_NUMBER`
   - Key: `file`, Type: File, Value: Select your receipt image

## 5. Verify Upload
Check if the file was uploaded:
```bash
ls -la storage/app/public/receipts/
```

## 6. Access Uploaded Receipt
The receipt will be accessible at:
```
http://localhost:8000/storage/receipts/receipt-BILL_NO-TIMESTAMP.jpg
```

## Troubleshooting

### Issue: "Storage link not found"
```bash
php artisan storage:link
```

### Issue: "Permission denied"
```bash
chmod -R 775 storage
chown -R www-data:www-data storage  # Linux
```

### Issue: "Sale not found"
Make sure you're using a valid bill number from the `sales` table:
```bash
php artisan tinker
>>> \App\Models\Sale::pluck('bill_no');
```

## API Endpoint Summary

**Endpoint:** `POST /api/upload/receipt`

**Parameters:**
- `bill_no` (required) - Must exist in sales table
- `file` (required) - Image or PDF, max 10MB

**Accepted formats:** JPG, PNG, GIF, WebP, PDF
