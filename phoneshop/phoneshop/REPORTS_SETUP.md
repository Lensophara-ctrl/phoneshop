# Reports System - Quick Setup Guide

## 🚀 Quick Start

### 1. Verify Routes
```bash
cd phoneshop/phoneshop
php artisan route:list | grep reports
```

Expected output:
```
GET|HEAD  reports/sales-summary .............. reports.sales-summary
GET|HEAD  reports/top-selling-products ....... reports.top-selling
GET|HEAD  reports/revenue-by-category ........ reports.revenue-category
GET|HEAD  reports/daily-sales ................ reports.daily-sales
GET|HEAD  reports/customer-report ............ reports.customers
GET|HEAD  reports/inventory .................. reports.inventory
GET|HEAD  reports/payment-methods ............ reports.payment-methods
GET|HEAD  reports/monthly-comparison ......... reports.monthly-comparison
GET|HEAD  reports/order-status ............... reports.order-status
GET|HEAD  reports/profit-analysis ............ reports.profit-analysis
```

### 2. Start the Server
```bash
php artisan serve
```

### 3. Access Reports Dashboard
Open in browser:
```
http://localhost:8000/reports-dashboard.html
```

## 📊 Testing Each Report

### Test 1: Sales Summary
```bash
curl "http://localhost:8000/reports/sales-summary" \
  -H "Accept: application/json" \
  -H "Cookie: laravel_session=YOUR_SESSION"
```

### Test 2: Top Selling Products
```bash
curl "http://localhost:8000/reports/top-selling-products?limit=5" \
  -H "Accept: application/json"
```

### Test 3: Revenue by Category
```bash
curl "http://localhost:8000/reports/revenue-by-category" \
  -H "Accept: application/json"
```

### Test 4: Daily Sales
```bash
curl "http://localhost:8000/reports/daily-sales" \
  -H "Accept: application/json"
```

### Test 5: Customer Report
```bash
curl "http://localhost:8000/reports/customer-report?limit=10" \
  -H "Accept: application/json"
```

### Test 6: Inventory Report
```bash
curl "http://localhost:8000/reports/inventory?low_stock_threshold=5" \
  -H "Accept: application/json"
```

### Test 7: Payment Methods
```bash
curl "http://localhost:8000/reports/payment-methods" \
  -H "Accept: application/json"
```

### Test 8: Monthly Comparison
```bash
curl "http://localhost:8000/reports/monthly-comparison?months=6" \
  -H "Accept: application/json"
```

### Test 9: Order Status
```bash
curl "http://localhost:8000/reports/order-status" \
  -H "Accept: application/json"
```

### Test 10: Profit Analysis
```bash
curl "http://localhost:8000/reports/profit-analysis" \
  -H "Accept: application/json"
```

## 🔧 Database Optimization

### Add Indexes for Better Performance
```bash
php artisan tinker
```

```php
// Run these queries
DB::statement('CREATE INDEX IF NOT EXISTS idx_sales_status_created ON sales(status, created_at)');
DB::statement('CREATE INDEX IF NOT EXISTS idx_sales_approval_created ON sales(approval_status, created_at)');
DB::statement('CREATE INDEX IF NOT EXISTS idx_sale_items_phone ON sale_items(phone_id)');
DB::statement('CREATE INDEX IF NOT EXISTS idx_sale_items_sale ON sale_items(sale_id)');
DB::statement('CREATE INDEX IF NOT EXISTS idx_users_created ON users(created_at)');
```

Or create a migration:
```bash
php artisan make:migration add_report_indexes
```

## 📝 Generate Sample Data (For Testing)

```bash
php artisan tinker
```

```php
// Create sample sales
$user = \App\Models\User::first();
$phones = \App\Models\Phone::take(5)->get();

for ($i = 0; $i < 20; $i++) {
    $sale = \App\Models\Sale::create([
        'bill_no' => 'TEST-' . uniqid(),
        'user_id' => $user->id,
        'customer_name' => 'Test Customer ' . $i,
        'customer_email' => 'test' . $i . '@example.com',
        'subtotal' => rand(100, 1000),
        'tax' => 0,
        'total_price' => rand(100, 1000),
        'currency' => 'USD',
        'payment_method' => ['cash', 'bank_transfer', 'bakong'][rand(0, 2)],
        'status' => 'completed',
        'approval_status' => 'approved',
        'created_at' => now()->subDays(rand(0, 30))
    ]);
    
    // Add sale items
    $phone = $phones->random();
    \App\Models\SaleItem::create([
        'sale_id' => $sale->id,
        'phone_id' => $phone->id,
        'qty' => rand(1, 3),
        'price' => $phone->price,
        'subtotal' => $phone->price * rand(1, 3)
    ]);
}

echo "Created 20 sample sales!";
```

## 🎯 Common Use Cases

### Get This Month's Sales
```bash
START_DATE=$(date +%Y-%m-01)
END_DATE=$(date +%Y-%m-%d)

curl "http://localhost:8000/reports/sales-summary?start_date=$START_DATE&end_date=$END_DATE" \
  -H "Accept: application/json"
```

### Get Last 7 Days Sales
```bash
START_DATE=$(date -d '7 days ago' +%Y-%m-%d)
END_DATE=$(date +%Y-%m-%d)

curl "http://localhost:8000/reports/daily-sales?start_date=$START_DATE&end_date=$END_DATE" \
  -H "Accept: application/json"
```

### Get Low Stock Items
```bash
curl "http://localhost:8000/reports/inventory?low_stock_threshold=10" \
  -H "Accept: application/json" \
  | jq '.data[] | select(.status == "Low Stock" or .status == "Out of Stock")'
```

## 🔍 Verification Checklist

- [ ] All 10 report endpoints are accessible
- [ ] Reports return data in JSON format
- [ ] Date filtering works correctly
- [ ] Admin authentication is required
- [ ] Reports dashboard loads properly
- [ ] Data tables render correctly
- [ ] Statistics cards display values
- [ ] No console errors in browser

## 🐛 Troubleshooting

### Issue: "Route not found"
**Solution:**
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### Issue: "Unauthorized"
**Solution:** Make sure you're logged in as admin
```bash
php artisan tinker
>>> $user = \App\Models\User::where('email', 'admin@example.com')->first();
>>> $user->role = 'admin';
>>> $user->save();
```

### Issue: "No data available"
**Solution:** Generate sample data (see above) or check date ranges

### Issue: Slow performance
**Solution:** Add database indexes (see above)

## 📊 Report Descriptions

| # | Report Name | Best For | Key Metrics |
|---|-------------|----------|-------------|
| 1 | Sales Summary | Overall performance | Revenue, Orders, AOV |
| 2 | Top Selling | Product performance | Quantity, Revenue |
| 3 | Revenue by Category | Category analysis | Revenue per category |
| 4 | Daily Sales | Daily tracking | Daily trends |
| 5 | Customer Report | Customer insights | Top customers, LTV |
| 6 | Inventory | Stock management | Stock levels, values |
| 7 | Payment Methods | Payment analysis | Payment distribution |
| 8 | Monthly Comparison | Growth tracking | Month-over-month |
| 9 | Order Status | Order pipeline | Status breakdown |
| 10 | Profit Analysis | Financial planning | Profit margins |

## 🎨 Customization

### Change Date Format
Edit `ReportController.php`:
```php
private function formatResponse($reportName, $data, $startDate = null, $endDate = null)
{
    // Change format here
    'start_date' => Carbon::parse($startDate)->format('d/m/Y'),
}
```

### Add Custom Metrics
Add to any report method:
```php
$data['custom_metric'] = YourModel::customCalculation();
```

### Modify Report Limits
Change default limits in controller:
```php
$limit = $request->get('limit', 20); // Change 20 to your preferred default
```

## 📱 Mobile Testing

Test on different screen sizes:
```bash
# Desktop
open "http://localhost:8000/reports-dashboard.html"

# Mobile simulation (Chrome DevTools)
# Press F12 > Toggle device toolbar > Select device
```

## 🔐 Security Checklist

- [ ] Admin middleware is applied to all routes
- [ ] CSRF protection is enabled
- [ ] SQL injection prevention (using Eloquent)
- [ ] XSS prevention (using Blade/JSON)
- [ ] Rate limiting configured
- [ ] HTTPS enabled in production

## 📈 Performance Benchmarks

Expected response times (with proper indexing):
- Sales Summary: < 100ms
- Top Selling: < 200ms
- Daily Sales: < 150ms
- Customer Report: < 200ms
- Inventory: < 100ms
- Other reports: < 150ms

## 🚀 Production Deployment

### 1. Optimize
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 2. Add Indexes
Run the index creation queries (see above)

### 3. Enable Caching
Consider implementing Redis or Memcached

### 4. Monitor Performance
Use Laravel Telescope or similar tools

## 📚 Next Steps

1. ✅ Test all 10 reports
2. ✅ Verify data accuracy
3. ✅ Add database indexes
4. ✅ Test with real data
5. ✅ Configure caching
6. ✅ Set up monitoring
7. ✅ Train staff on usage
8. ✅ Document custom requirements

## 🆘 Getting Help

If you encounter issues:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Enable debug mode: `APP_DEBUG=true` in `.env`
3. Check database connections
4. Verify admin permissions
5. Review browser console for errors

## ✅ Success Criteria

Your reports system is working correctly when:
- ✅ All 10 reports load without errors
- ✅ Data is accurate and up-to-date
- ✅ Date filtering works properly
- ✅ Dashboard is responsive on all devices
- ✅ Performance is acceptable (< 200ms)
- ✅ Admin authentication is enforced
- ✅ No console or server errors

---

**Congratulations!** Your comprehensive reports system is now ready to use! 🎉
