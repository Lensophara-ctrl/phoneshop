# Reports Dashboard - Complete Guide

## Overview
The Reports Dashboard is fully integrated into the admin panel, providing 10 comprehensive reports with customer report locking and invoice download functionality.

## 🚀 Quick Access

### From Admin Panel
1. Login to admin panel
2. Click "Reports" in the sidebar
3. Select any report to view

### Direct URL
```
http://your-domain.com/reports
```

## 📊 Features

### 1. 10 Comprehensive Reports
- Sales Summary
- Top Selling Products
- Revenue by Category
- Daily Sales
- Customer Report
- Inventory Report
- Payment Methods
- Monthly Comparison
- Order Status
- Profit Analysis

### 2. Customer Report Locking 🔒
- Lock customer reports to prevent modifications
- Audit trail of who locked the report and when
- Locked reports cannot be altered

### 3. Invoice Download 📥
- Download customer invoices (HTML format)
- Download daily sales invoices
- Professional invoice templates
- Includes all order details

### 4. Date Filtering
- Filter reports by date range
- Default: Current month
- Custom date selection

### 5. Export Options
- CSV export (coming soon)
- PDF export (coming soon)

## 🔐 Customer Report Locking

### How to Lock a Customer Report

1. Navigate to Reports → Customer Report
2. Find the customer you want to lock
3. Click the "Lock" button
4. Confirm the action

### What Happens When Locked?
- Report data is frozen at that point in time
- A record is created in `locked_reports` table
- Includes: user_id, report_type, locked_by, locked_at
- Cannot be unlocked (permanent)

### Database Structure
```sql
CREATE TABLE locked_reports (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,
    report_type VARCHAR(255),
    locked_by BIGINT,
    locked_at TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 📥 Invoice Download

### Customer Invoice

**Features:**
- Customer information
- Order history for selected period
- Total orders and revenue
- Payment methods used
- Order status

**How to Download:**
1. Go to Customer Report
2. Click "Invoice" button next to customer
3. Invoice downloads as HTML file
4. Can be printed or converted to PDF

**Filename Format:**
```
customer-invoice-{customer_id}-{date}.html
```

### Daily Invoice

**Features:**
- All orders for specific date
- Order details and times
- Items sold summary
- Payment methods breakdown
- Total revenue

**How to Download:**
1. Go to Daily Sales Report
2. Click "Invoice" button next to date
3. Invoice downloads as HTML file

**Filename Format:**
```
daily-invoice-{date}.html
```

## 🎨 Report Details

### 1. Sales Summary
**Metrics:**
- Total Sales Revenue
- Total Orders
- Average Order Value
- Total Items Sold
- Pending Orders
- Approved Orders

**Use Case:** Quick overview of business performance

### 2. Top Selling Products
**Columns:**
- Rank
- Product Name
- Category
- Quantity Sold
- Revenue
- Current Stock

**Use Case:** Identify best performers for inventory planning

### 3. Revenue by Category
**Columns:**
- Category Name
- Total Revenue
- Quantity Sold
- Order Count

**Use Case:** Category performance analysis

### 4. Daily Sales
**Columns:**
- Date
- Orders
- Revenue
- Average Order Value
- Invoice Download Button

**Use Case:** Track daily performance trends

### 5. Customer Report
**Columns:**
- Customer Name
- Email
- Total Orders
- Total Spent
- Average Order Value
- Last Order Date
- Lock Button
- Invoice Download Button

**Use Case:** Customer lifetime value analysis

### 6. Inventory Report
**Columns:**
- Product Name
- Category
- Current Stock
- Price
- Stock Value
- Total Sold
- Status (In Stock/Low Stock/Out of Stock)

**Use Case:** Stock management and reordering

### 7. Payment Methods
**Columns:**
- Payment Method
- Transactions
- Total Revenue
- Average Transaction
- Percentage

**Use Case:** Payment preference analysis

### 8. Monthly Comparison
**Columns:**
- Month
- Orders
- Revenue
- Average Order Value
- New Customers

**Use Case:** Growth tracking and trends

### 9. Order Status
**Metrics:**
- Total Orders
- Completed
- Pending Payment
- Approved
- Pending Approval
- Rejected

**Use Case:** Order pipeline monitoring

### 10. Profit Analysis
**Metrics:**
- Total Revenue
- Tax Collected
- Net Revenue
- Average Order Value
- Revenue by Category
- Revenue by Currency

**Use Case:** Financial planning and analysis

## 🔧 Setup Instructions

### 1. Run Migrations
```bash
cd phoneshop/phoneshop
php artisan migrate
```

This creates the `locked_reports` table.

### 2. Verify Routes
```bash
php artisan route:list | grep reports
```

### 3. Access Dashboard
```
http://localhost:8000/reports
```

## 💻 API Endpoints

### Reports
- `GET /reports` - Reports dashboard
- `GET /reports/sales-summary` - Sales summary data
- `GET /reports/top-selling-products` - Top products
- `GET /reports/revenue-by-category` - Category revenue
- `GET /reports/daily-sales` - Daily sales
- `GET /reports/customer-report` - Customer data
- `GET /reports/inventory` - Inventory status
- `GET /reports/payment-methods` - Payment analysis
- `GET /reports/monthly-comparison` - Monthly data
- `GET /reports/order-status` - Order status
- `GET /reports/profit-analysis` - Profit data

### Customer Actions
- `POST /reports/customer/{id}/lock` - Lock customer report
- `GET /reports/customer/{id}/invoice` - Download customer invoice
- `GET /reports/daily-invoice/{date}` - Download daily invoice

## 🎯 Usage Examples

### Lock Customer Report
```javascript
async function lockCustomerReport(customerId) {
    const response = await fetch(`/reports/customer/${customerId}/lock`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });
    
    const data = await response.json();
    console.log(data);
}
```

### Download Customer Invoice
```javascript
function downloadCustomerInvoice(customerId) {
    const startDate = '2026-04-01';
    const endDate = '2026-04-30';
    window.open(`/reports/customer/${customerId}/invoice?start_date=${startDate}&end_date=${endDate}`, '_blank');
}
```

### Download Daily Invoice
```javascript
function downloadDailyInvoice(date) {
    window.open(`/reports/daily-invoice/${date}`, '_blank');
}
```

## 🔒 Security

### Access Control
- Only admin users can access reports
- Protected by `auth` and `admin` middleware
- CSRF protection on all POST requests

### Audit Trail
- All report locks are logged
- Includes who locked and when
- Cannot be reversed

## 📱 Responsive Design

The reports dashboard is fully responsive:
- Mobile: Single column layout
- Tablet: 2-column grid
- Desktop: 3-4 column grid
- Large screens: 4 column grid

## 🎨 Customization

### Change Report Colors
Edit `resources/views/reports/index.blade.php`:
```css
.stat-box {
    background: linear-gradient(135deg, #YOUR_COLOR_1 0%, #YOUR_COLOR_2 100%);
}
```

### Add Custom Report
1. Add method to `ReportController.php`
2. Add route in `routes/web.php`
3. Add card in `reports/index.blade.php`
4. Add render function in JavaScript

## 🐛 Troubleshooting

### Issue: "Reports not showing"
**Solution:**
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### Issue: "Cannot lock report"
**Solution:** Run migration
```bash
php artisan migrate
```

### Issue: "Invoice download fails"
**Solution:** Check if views exist
```bash
ls resources/views/reports/
```

## 📊 Performance Tips

1. Add database indexes (see REPORTS_SETUP.md)
2. Implement caching for frequently accessed reports
3. Use pagination for large datasets
4. Optimize date range queries

## 🚀 Future Enhancements

- [ ] PDF export functionality
- [ ] CSV export functionality
- [ ] Email report scheduling
- [ ] Chart visualizations
- [ ] Custom report builder
- [ ] Report templates
- [ ] Bulk invoice download
- [ ] Report sharing

## ✅ Success Checklist

- [ ] Reports dashboard accessible
- [ ] All 10 reports load correctly
- [ ] Date filtering works
- [ ] Customer report locking works
- [ ] Invoice downloads work
- [ ] Sidebar menu shows Reports
- [ ] No console errors
- [ ] Responsive on all devices

---

**Your comprehensive reports dashboard is ready to use!** 🎉
