# Receipt Upload Feature

## Overview
This feature allows customers to upload payment receipts for their orders. The receipt is stored securely and linked to the sale record.

## Database Changes

### Migration
A new migration has been created: `2026_04_06_000000_add_receipt_path_to_sales_table.php`

This adds a `receipt_path` column to the `sales` table to store the file path of uploaded receipts.

### Running the Migration
```bash
php artisan migrate
```

## API Endpoint

### Upload Receipt
**Endpoint:** `POST /api/upload/receipt`

**Parameters:**
- `bill_no` (required, string) - The bill number of the sale
- `file` (required, file) - The receipt image/PDF file

**Accepted File Types:**
- JPEG, PNG, GIF, WebP, PDF

**Maximum File Size:** 10MB

**Example Request (cURL):**
```bash
curl -X POST http://your-domain.com/api/upload/receipt \
  -F "bill_no=INV-123456" \
  -F "file=@/path/to/receipt.jpg"
```

**Success Response:**
```json
{
  "success": true,
  "message": "Receipt uploaded successfully",
  "data": {
    "bill_no": "INV-123456",
    "filename": "receipt-INV-123456-1234567890.jpg",
    "path": "receipts/receipt-INV-123456-1234567890.jpg",
    "url": "http://your-domain.com/storage/receipts/receipt-INV-123456-1234567890.jpg"
  }
}
```

**Error Response:**
```json
{
  "success": false,
  "message": "Validation failed",
  "errors": {
    "bill_no": ["The bill no field is required."],
    "file": ["The file field is required."]
  }
}
```

## Testing

### Web Interface
A test page has been created at: `public/test-receipt-upload.html`

Access it at: `http://your-domain.com/test-receipt-upload.html`

### Features:
- Drag and drop file upload
- Image preview
- Real-time validation
- Success/error messages

## Storage

Receipts are stored in: `storage/app/public/receipts/`

Make sure the storage link is created:
```bash
php artisan storage:link
```

## Model Updates

The `Sale` model has been updated to include `receipt_path` in the fillable fields.

## Security Features

1. File validation (type and size)
2. Bill number verification (must exist in database)
3. Old receipt deletion when uploading a new one
4. Secure file naming with timestamp
5. Logging of all upload attempts

## Usage in Frontend

### JavaScript Example:
```javascript
async function uploadReceipt(billNo, file) {
  const formData = new FormData();
  formData.append('bill_no', billNo);
  formData.append('file', file);
  
  const response = await fetch('/api/upload/receipt', {
    method: 'POST',
    body: formData
  });
  
  return await response.json();
}
```

### HTML Form Example:
```html
<form id="receiptForm">
  <input type="text" name="bill_no" placeholder="Bill Number" required>
  <input type="file" name="file" accept="image/*,.pdf" required>
  <button type="submit">Upload Receipt</button>
</form>
```

## Retrieving Receipt URL

To get the receipt URL for a sale:

```php
$sale = Sale::where('bill_no', 'INV-123456')->first();

if ($sale && $sale->receipt_path) {
    $receiptUrl = asset('storage/' . $sale->receipt_path);
    echo $receiptUrl;
}
```

## Notes

- Receipts are public once uploaded (stored in public storage)
- Each sale can have only one receipt (uploading a new one replaces the old)
- The endpoint is public (no authentication required) for customer convenience
- All uploads are logged for audit purposes
