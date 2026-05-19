# Customer Orders - Lock & Download Invoice Guide

## Overview
Customers can now view their orders, lock order reports for permanent records, and download invoices directly from their account.

## 🎯 Features

### 1. View Orders
- Customers can see all their orders
- Order details including items, prices, and status
- Payment and delivery information
- Order history with pagination

### 2. Lock Order Reports 🔒
- Lock completed orders to create permanent records
- Once locked, orders cannot be modified
- Locked status is visible on order cards
- Audit trail of when and who locked the report

### 3. Download Invoices 📥
- Download professional HTML invoices
- Invoices show lock status
- Print-ready format
- Includes all order details

## 🚀 Access

### Customer Access
```
http://your-domain.com/my-orders
```

### From Profile
Customers can access their orders from their profile page.

## 📋 How to Use

### View Orders
1. Login to your account
2. Go to "My Orders" or visit `/my-orders`
3. See all your orders with details

### Lock an Order Report
1. Find a completed order
2. Click "Lock Report" button
3. Confirm the action
4. Order is now permanently locked

**Requirements:**
- Order must be completed
- Order cannot already be locked
- Only the order owner can lock

### Download Invoice
1. Find any order
2. Click "Download Invoice" button
3. Invoice downloads as HTML file
4. Can be printed or saved as PDF

## 🔐 Security

### Access Control
- Only authenticated customers can access
- Customers can only see their own orders
- Ownership verification on all actions

### Locking Rules
- Only completed orders can be locked
- Locking is permanent (cannot be undone)
- Creates audit trail in database
- Locked orders show special badge

## 💾 Database

### Locked Reports Table
```sql
CREATE TABLE locked_reports (
    id BIGINT PRIMARY KEY,
    user_id BIGINT,          -- Order ID (not customer ID)
    report_type VARCHAR(255), -- 'order'
    locked_by BIGINT,         -- Customer ID who locked
    locked_at TIMESTAMP,
    notes TEXT,
    created_at TIMESTAMP,
    updated_at TIMESTAMP
);
```

## 🎨 Features by Order Status

### Pending Orders
- View details
- Download invoice
- Complete payment button
- Cannot lock (not completed)

### Completed Orders
- View details
- Lock report button
- Download invoice
- View receipt (if uploaded)

### Locked Orders
- View details
- Download invoice (with lock badge)
- View receipt
- Lock notice displayed
- Cannot be modified

## 📱 Responsive Design

The orders page is fully responsive:
- Mobile: Single column, stacked layout
- Tablet: Optimized grid
- Desktop: Full grid layout

## 🔗 API Endpoints

### View Orders
```
GET /my-orders
```

### Lock Order
```
POST /customer/orders/{order}/lock
```

**Response:**
```json
{
  "success": true,
  "message": "Order report locked successfully"
}
```

### Download Invoice
```
GET /customer/orders/{order}/invoice
```

Returns HTML invoice file.

## 📄 Invoice Features

### Header
- Invoice title
- Order number
- Order date
- Lock badge (if locked)

### Customer Information
- Name
- Email
- Phone
- Address

### Order Details
- Order date and time
- Payment method
- Currency
- Status badges

### Items Table
- Product name and category
- Quantity
- Unit price
- Subtotal

### Totals
- Subtotal
- Tax (if applicable)
- Grand total

### Footer
- Company information
- Generation timestamp
- Lock status

## 🎯 Use Cases

### For Customers
1. **Track Orders**: View all order history
2. **Permanent Records**: Lock important orders
3. **Tax Records**: Download invoices for accounting
4. **Proof of Purchase**: Official invoice documents
5. **Warranty Claims**: Locked invoices as proof

### For Business
1. **Customer Self-Service**: Reduce support requests
2. **Audit Trail**: Track locked reports
3. **Legal Protection**: Permanent order records
4. **Customer Satisfaction**: Easy access to invoices

## 🔧 Customization

### Change Invoice Design
Edit `resources/views/invoices/customer-order.blade.php`

### Add Custom Fields
Modify the invoice template to include:
- Company logo
- Tax ID
- Terms and conditions
- Custom messages

### Modify Lock Rules
Edit `CustomerOrderController.php`:
```php
// Example: Allow locking after 30 days
if ($order->created_at->diffInDays(now()) < 30) {
    return response()->json([
        'success' => false,
        'message' => 'Orders can only be locked after 30 days'
    ], 400);
}
```

## 🐛 Troubleshooting

### Issue: "Cannot access orders"
**Solution:** Make sure customer is logged in
```
http://your-domain.com/login
```

### Issue: "Cannot lock order"
**Solution:** Check order status
- Must be completed
- Cannot already be locked
- Must be order owner

### Issue: "Invoice download fails"
**Solution:** Check file permissions
```bash
chmod -R 775 storage
```

## 📊 Statistics

Track customer engagement:
```php
// Total locked orders
$lockedCount = DB::table('locked_reports')
    ->where('report_type', 'order')
    ->count();

// Locked orders by customer
$customerLocked = DB::table('locked_reports')
    ->where('report_type', 'order')
    ->where('locked_by', $customerId)
    ->count();
```

## 🚀 Future Enhancements

- [ ] PDF invoice generation
- [ ] Email invoice to customer
- [ ] Bulk invoice download
- [ ] Invoice templates selection
- [ ] Order tracking integration
- [ ] Return/refund requests
- [ ] Order reviews and ratings
- [ ] Reorder functionality

## ✅ Testing Checklist

- [ ] Customer can view their orders
- [ ] Order details display correctly
- [ ] Lock button works for completed orders
- [ ] Lock confirmation dialog appears
- [ ] Order shows locked badge after locking
- [ ] Invoice downloads successfully
- [ ] Invoice shows correct information
- [ ] Locked invoices show lock badge
- [ ] Pagination works
- [ ] Responsive on mobile devices
- [ ] Only owner can access their orders
- [ ] Cannot lock pending orders
- [ ] Cannot lock already locked orders

## 📞 Support

For issues or questions:
1. Check order status requirements
2. Verify customer is logged in
3. Check browser console for errors
4. Review Laravel logs: `storage/logs/laravel.log`

---

**Your customer order management system is ready!** 🎉

Customers can now manage their orders, create permanent records, and download professional invoices.
