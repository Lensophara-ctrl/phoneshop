# Order Approval System - Setup Commands

## Quick Setup

### 1. Run the Migration
```bash
cd phoneshop/phoneshop
php artisan migrate
```

### 2. Verify Admin User Exists
```bash
php artisan tinker
>>> \App\Models\User::where('role', 'admin')->get();
```

If no admin exists, create one:
```bash
>>> \App\Models\User::create([
...   'name' => 'Admin',
...   'email' => 'admin@example.com',
...   'password' => bcrypt('admin123'),
...   'role' => 'admin'
... ]);
```

### 3. Start the Server
```bash
php artisan serve
```

### 4. Access Admin Interface
Open in browser:
```
http://localhost:8000/admin-order-approval.html
```

## Testing the System

### Create a Test Order
```bash
php artisan tinker
```

```php
// Create a test user
$user = \App\Models\User::first();

// Create a test phone
$phone = \App\Models\Phone::first();

// Create a test sale
$sale = \App\Models\Sale::create([
    'bill_no' => 'INV-TEST-' . time(),
    'user_id' => $user->id,
    'customer_name' => 'Test Customer',
    'customer_email' => 'test@example.com',
    'subtotal' => 100,
    'tax' => 0,
    'total_price' => 100,
    'currency' => 'USD',
    'payment_method' => 'bank_transfer',
    'status' => 'completed',
    'approval_status' => 'pending'
]);

// Create sale item
\App\Models\SaleItem::create([
    'sale_id' => $sale->id,
    'phone_id' => $phone->id,
    'qty' => 1,
    'price' => 100,
    'subtotal' => 100
]);

echo "Test order created: " . $sale->bill_no;
```

### Test API Endpoints

**Get Pending Orders:**
```bash
curl http://localhost:8000/orders/approval \
  -H "Accept: application/json" \
  -H "Cookie: laravel_session=YOUR_SESSION"
```

**Get Statistics:**
```bash
curl http://localhost:8000/orders/approval/stats \
  -H "Accept: application/json"
```

**Approve Order (replace {id} with actual order ID):**
```bash
curl -X POST http://localhost:8000/orders/{id}/approve \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=YOUR_SESSION"
```

**Reject Order:**
```bash
curl -X POST http://localhost:8000/orders/{id}/reject \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=YOUR_SESSION" \
  -d '{"reason": "Test rejection reason"}'
```

## Integration with Existing System

### Update Existing Orders
If you have existing orders, update them to have approval status:

```bash
php artisan tinker
```

```php
// Set all existing orders to approved
\App\Models\Sale::whereNull('approval_status')->update([
    'approval_status' => 'approved',
    'approved_at' => now()
]);
```

### Make Specific Payment Methods Require Approval
Edit `app/Http/Controllers/SaleController.php`:

```php
// In the store() method, after creating the sale
$requiresApproval = in_array($request->payment_method, ['bank_transfer', 'aba_pay', 'bakong']);

$sale = Sale::create([
    // ... other fields
    'approval_status' => $requiresApproval ? 'pending' : 'approved',
]);
```

## Verification

### Check Migration Status
```bash
php artisan migrate:status
```

### Check Database Schema
```bash
php artisan tinker
>>> Schema::hasColumn('sales', 'approval_status');
>>> Schema::hasColumn('sales', 'approved_by');
>>> Schema::hasColumn('sales', 'approved_at');
>>> Schema::hasColumn('sales', 'rejection_reason');
```

### Check Routes
```bash
php artisan route:list | grep approval
```

Expected output:
```
GET|HEAD  orders/approval .................. orders.approval
GET|HEAD  orders/approval/stats ............ orders.approval.stats
GET|HEAD  orders/{order}/details ........... orders.details
POST      orders/{order}/approve ........... orders.approve
POST      orders/{order}/reject ............ orders.reject
```

## Common Issues

### Issue: "Column not found: approval_status"
**Solution:**
```bash
php artisan migrate
php artisan config:clear
php artisan cache:clear
```

### Issue: "Unauthorized access"
**Solution:** Make sure you're logged in as admin
```bash
php artisan tinker
>>> $user = \App\Models\User::where('email', 'your@email.com')->first();
>>> $user->role = 'admin';
>>> $user->save();
```

### Issue: "CSRF token mismatch"
**Solution:** For testing, you can temporarily disable CSRF for specific routes in `app/Http/Middleware/VerifyCsrfToken.php`:

```php
protected $except = [
    'orders/*/approve',
    'orders/*/reject',
];
```

**Note:** Remove this in production!

## Production Deployment

### 1. Run Migrations
```bash
php artisan migrate --force
```

### 2. Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 3. Optimize
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 4. Set Permissions
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
chown -R www-data:www-data storage
chown -R www-data:www-data bootstrap/cache
```

## Monitoring

### Check Pending Orders Count
```bash
php artisan tinker
>>> \App\Models\Sale::where('approval_status', 'pending')->count();
```

### Get Recent Approvals
```bash
php artisan tinker
>>> \App\Models\Sale::where('approval_status', 'approved')
...     ->latest('approved_at')
...     ->take(5)
...     ->get(['bill_no', 'approved_at', 'approved_by']);
```

### Get Rejection Statistics
```bash
php artisan tinker
>>> \App\Models\Sale::where('approval_status', 'rejected')
...     ->count();
```

## Backup

Before deploying, backup your database:
```bash
php artisan db:backup  # If you have backup package
# OR
mysqldump -u username -p database_name > backup.sql
```

## Rollback

If you need to rollback the migration:
```bash
php artisan migrate:rollback --step=1
```

This will remove the approval fields from the sales table.
