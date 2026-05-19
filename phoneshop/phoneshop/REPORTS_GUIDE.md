# Reports System - Complete Guide

## Overview
The PhoneShop Reports System provides 10 comprehensive reports for business analytics, sales tracking, inventory management, and customer insights.

## 📊 Available Reports

### 1. Sales Summary Report
**Endpoint:** `GET /reports/sales-summary`

**Description:** Overview of total sales, orders, and revenue metrics

**Metrics:**
- Total Sales Revenue
- Total Orders Count
- Average Order Value
- Total Items Sold
- Pending Orders
- Approved Orders
- Rejected Orders

**Example Response:**
```json
{
  "success": true,
  "report_name": "Sales Summary Report",
  "data": {
    "total_sales": 15000.50,
    "total_orders": 45,
    "average_order_value": 333.34,
    "total_items_sold": 120,
    "pending_orders": 5,
    "approved_orders": 38,
    "rejected_orders": 2
  },
  "period": {
    "start_date": "Apr 01, 2026",
    "end_date": "Apr 06, 2026"
  }
}
```

---

### 2. Top Selling Products
**Endpoint:** `GET /reports/top-selling-products`

**Description:** Best performing products by quantity and revenue

**Query Parameters:**
- `limit` (optional, default: 10) - Number of products to return

**Metrics:**
- Product Name & Category
- Quantity Sold
- Total Revenue
- Current Stock Level

**Example Response:**
```json
{
  "success": true,
  "report_name": "Top Selling Products",
  "data": [
    {
      "product_id": 1,
      "product_name": "iPhone 15 Pro",
      "category": "Smartphones",
      "quantity_sold": 25,
      "total_revenue": "24999.75",
      "current_stock": 15
    }
  ]
}
```

---

### 3. Revenue by Category
**Endpoint:** `GET /reports/revenue-by-category`

**Description:** Sales performance breakdown by product categories

**Metrics:**
- Category Name
- Total Revenue
- Total Quantity Sold
- Order Count

**Use Cases:**
- Identify best-performing categories
- Optimize inventory by category
- Plan marketing campaigns

---

### 4. Daily Sales Report
**Endpoint:** `GET /reports/daily-sales`

**Description:** Day-by-day sales analysis and trends

**Metrics:**
- Date
- Order Count
- Total Revenue
- Average Order Value

**Use Cases:**
- Track daily performance
- Identify sales patterns
- Monitor day-to-day trends

---

### 5. Customer Report
**Endpoint:** `GET /reports/customer-report`

**Description:** Top customers and purchasing behavior analysis

**Query Parameters:**
- `limit` (optional, default: 20) - Number of customers to return

**Metrics:**
- Customer Name & Email
- Total Orders
- Total Spent
- Average Order Value
- Last Order Date

**Use Cases:**
- Identify VIP customers
- Customer retention analysis
- Targeted marketing campaigns

---

### 6. Inventory Report
**Endpoint:** `GET /reports/inventory`

**Description:** Stock levels, values, and inventory status

**Query Parameters:**
- `low_stock_threshold` (optional, default: 10) - Threshold for low stock warning

**Metrics:**
- Product Name & Category
- Current Stock
- Price
- Stock Value
- Total Sold
- Status (In Stock / Low Stock / Out of Stock)

**Use Cases:**
- Monitor stock levels
- Identify reorder needs
- Calculate inventory value

---

### 7. Payment Method Report
**Endpoint:** `GET /reports/payment-methods`

**Description:** Revenue distribution by payment method

**Metrics:**
- Payment Method
- Transaction Count
- Total Revenue
- Average Transaction
- Percentage of Total

**Use Cases:**
- Understand payment preferences
- Optimize payment options
- Financial reconciliation

---

### 8. Monthly Comparison Report
**Endpoint:** `GET /reports/monthly-comparison`

**Description:** Month-over-month performance comparison

**Query Parameters:**
- `months` (optional, default: 6) - Number of months to compare

**Metrics:**
- Month
- Total Orders
- Total Revenue
- Average Order Value
- New Customers

**Use Cases:**
- Track growth trends
- Seasonal analysis
- Performance benchmarking

---

### 9. Order Status Report
**Endpoint:** `GET /reports/order-status`

**Description:** Order fulfillment and approval status breakdown

**Metrics:**
- By Payment Status (Pending, Completed)
- By Approval Status (Pending, Approved, Rejected)
- Summary Statistics

**Use Cases:**
- Monitor order pipeline
- Track approval workflow
- Identify bottlenecks

---

### 10. Profit Analysis Report
**Endpoint:** `GET /reports/profit-analysis`

**Description:** Detailed profit margins and financial analysis

**Metrics:**
- Total Revenue
- Tax Collected
- Net Revenue
- Average Order Value
- Revenue by Category
- Revenue by Currency

**Use Cases:**
- Financial planning
- Profitability analysis
- Tax reporting

---

## 🔧 Common Query Parameters

All reports support these parameters:

| Parameter | Type | Default | Description |
|-----------|------|---------|-------------|
| `start_date` | date | First day of current month | Start date for report period (YYYY-MM-DD) |
| `end_date` | date | Today | End date for report period (YYYY-MM-DD) |

## 📱 Web Interface

Access the interactive reports dashboard at:
```
http://your-domain.com/reports-dashboard.html
```

### Features:
- 📅 Date range filtering
- 🎨 Beautiful, responsive design
- 📊 Interactive data tables
- 💾 Export functionality (coming soon)
- 🔄 Real-time data loading
- 📈 Visual statistics cards

## 🚀 Usage Examples

### Using cURL

**Get Sales Summary:**
```bash
curl "http://localhost:8000/reports/sales-summary?start_date=2026-04-01&end_date=2026-04-06" \
  -H "Accept: application/json" \
  -H "Cookie: laravel_session=YOUR_SESSION"
```

**Get Top 5 Selling Products:**
```bash
curl "http://localhost:8000/reports/top-selling-products?limit=5" \
  -H "Accept: application/json"
```

**Get Inventory with Custom Threshold:**
```bash
curl "http://localhost:8000/reports/inventory?low_stock_threshold=5" \
  -H "Accept: application/json"
```

### Using JavaScript

```javascript
async function getSalesReport(startDate, endDate) {
  const params = new URLSearchParams({
    start_date: startDate,
    end_date: endDate
  });
  
  const response = await fetch(`/reports/sales-summary?${params}`, {
    headers: {
      'Accept': 'application/json'
    }
  });
  
  return await response.json();
}

// Usage
const report = await getSalesReport('2026-04-01', '2026-04-06');
console.log(report.data);
```

### Using PHP

```php
use App\Http\Controllers\ReportController;

$controller = new ReportController();
$request = request()->merge([
    'start_date' => '2026-04-01',
    'end_date' => '2026-04-06'
]);

$report = $controller->salesSummary($request);
```

## 📊 Response Format

All reports follow this standard format:

```json
{
  "success": true,
  "report_name": "Report Name",
  "data": { /* Report-specific data */ },
  "period": {
    "start_date": "Apr 01, 2026",
    "end_date": "Apr 06, 2026"
  },
  "generated_at": "2026-04-06 10:30:00"
}
```

## 🔐 Security

### Access Control
- All report endpoints require admin authentication
- Protected by `auth` and `admin` middleware
- Session-based authentication required

### Best Practices
1. Always use HTTPS in production
2. Implement rate limiting for API calls
3. Log report access for audit trails
4. Validate date ranges to prevent excessive queries

## 🎯 Use Cases by Role

### Store Manager
- Daily Sales Report - Monitor daily performance
- Top Selling Products - Stock management decisions
- Inventory Report - Reorder planning

### Financial Officer
- Sales Summary - Revenue tracking
- Payment Method Report - Financial reconciliation
- Profit Analysis - Financial planning

### Marketing Manager
- Customer Report - Campaign targeting
- Revenue by Category - Product focus
- Monthly Comparison - Trend analysis

### Business Owner
- All Reports - Comprehensive business overview
- Monthly Comparison - Growth tracking
- Profit Analysis - Strategic planning

## 📈 Performance Optimization

### Database Indexing
Ensure these indexes exist for optimal performance:

```sql
-- Sales table
CREATE INDEX idx_sales_status_created ON sales(status, created_at);
CREATE INDEX idx_sales_approval_created ON sales(approval_status, created_at);

-- Sale Items table
CREATE INDEX idx_sale_items_phone ON sale_items(phone_id);
CREATE INDEX idx_sale_items_sale ON sale_items(sale_id);

-- Users table
CREATE INDEX idx_users_created ON users(created_at);
```

### Caching Strategy
Consider caching reports for frequently accessed periods:

```php
use Illuminate\Support\Facades\Cache;

$cacheKey = "report_sales_summary_{$startDate}_{$endDate}";
$report = Cache::remember($cacheKey, 3600, function() use ($startDate, $endDate) {
    return $this->salesSummary($request);
});
```

## 🐛 Troubleshooting

### Issue: Slow Report Generation
**Solution:**
- Add database indexes
- Implement caching
- Limit date ranges
- Use pagination for large datasets

### Issue: Empty Data
**Solution:**
- Check date range parameters
- Verify sales exist in the period
- Check order status filters
- Ensure proper authentication

### Issue: Memory Errors
**Solution:**
- Increase PHP memory limit
- Use chunking for large datasets
- Implement pagination
- Optimize queries

## 🔄 Export Functionality

### CSV Export (Coming Soon)
```javascript
function exportToCSV(reportData, filename) {
  const csv = convertToCSV(reportData);
  const blob = new Blob([csv], { type: 'text/csv' });
  const url = window.URL.createObjectURL(blob);
  const a = document.createElement('a');
  a.href = url;
  a.download = filename;
  a.click();
}
```

### PDF Export (Coming Soon)
Integration with libraries like:
- DomPDF
- TCPDF
- Snappy (wkhtmltopdf)

## 📱 Mobile Responsiveness

The reports dashboard is fully responsive and works on:
- 📱 Mobile phones (320px+)
- 📱 Tablets (768px+)
- 💻 Desktops (1024px+)
- 🖥️ Large screens (1440px+)

## 🎨 Customization

### Adding Custom Reports

1. Add method to `ReportController.php`:
```php
public function customReport(Request $request)
{
    $data = // Your custom logic
    return $this->formatResponse('Custom Report', $data);
}
```

2. Add route in `routes/web.php`:
```php
Route::get('reports/custom', [ReportController::class, 'customReport']);
```

3. Add card in `reports-dashboard.html`:
```html
<div class="report-card" onclick="loadReport('custom')">
    <div class="report-icon">🎯</div>
    <div class="report-title">Custom Report</div>
    <div class="report-description">Your custom report description</div>
</div>
```

## 📚 Additional Resources

- [Laravel Query Builder](https://laravel.com/docs/queries)
- [Carbon Date Library](https://carbon.nesbot.com/docs/)
- [Chart.js for Visualizations](https://www.chartjs.org/)

## 🆘 Support

For issues or questions:
1. Check the troubleshooting section
2. Review Laravel logs: `storage/logs/laravel.log`
3. Enable debug mode in `.env`: `APP_DEBUG=true`
4. Check database query logs

## 🔮 Future Enhancements

- [ ] Real-time charts and graphs
- [ ] Scheduled report emails
- [ ] Custom report builder
- [ ] Advanced filtering options
- [ ] Comparison with previous periods
- [ ] Forecasting and predictions
- [ ] Export to Excel/PDF
- [ ] Report scheduling
- [ ] Dashboard widgets
- [ ] Mobile app integration
