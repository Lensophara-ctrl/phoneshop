# Order Approval System

## Overview
The Order Approval System allows administrators to review and approve/reject customer orders before they are processed. This adds an extra layer of control and verification to the order fulfillment process.

## Features

✅ Pending order queue for admin review
✅ Approve or reject orders with reasons
✅ Receipt verification support
✅ Real-time statistics dashboard
✅ Telegram notifications for approval actions
✅ Automatic stock reduction on approval
✅ Filter orders by approval and payment status
✅ Detailed order information view

## Database Changes

### Migration
A new migration has been created: `2026_04_06_000001_add_approval_fields_to_sales_table.php`

This adds the following columns to the `sales` table:
- `approval_status` (enum: pending, approved, rejected) - Default: pending
- `approved_by` (foreign key to users table) - Admin who approved/rejected
- `approved_at` (timestamp) - When the approval/rejection occurred
- `rejection_reason` (text) - Reason for rejection (if applicable)

### Running the Migration
```bash
cd phoneshop/phoneshop
php artisan migrate
```

## API Endpoints

### 1. Get Pending Orders for Approval
**Endpoint:** `GET /orders/approval`

**Query Parameters:**
- `approval_status` (optional) - Filter by: pending, approved, rejected
- `status` (optional) - Filter by payment status: pending, completed

**Response:**
```json
{
  "success": true,
  "orders": [
    {
      "id": 1,
      "bill_no": "INV-123456",
      "customer_name": "John Doe",
      "customer_email": "john@example.com",
      "total_price": 999.99,
      "currency": "USD",
      "payment_method": "bank_transfer",
      "status": "completed",
      "approval_status": "pending",
      "receipt_url": "http://domain.com/storage/receipts/receipt-INV-123456.jpg",
      "items_count": 2,
      "created_at": "Apr 06, 2026 10:30"
    }
  ]
}
```

### 2. Get Order Details
**Endpoint:** `GET /orders/{order}/details`

**Response:**
```json
{
  "success": true,
  "order": {
    "id": 1,
    "bill_no": "INV-123456",
    "customer": {
      "name": "John Doe",
      "email": "john@example.com",
      "phone": "+1234567890",
      "address": "123 Main St",
      "city": "New York",
      "postal_code": "10001"
    },
    "items": [
      {
        "phone_name": "iPhone 15 Pro",
        "category": "Smartphones",
        "qty": 1,
        "price": 999.99,
        "subtotal": 999.99,
        "image": "http://domain.com/storage/phones/iphone15.jpg"
      }
    ],
    "subtotal": 999.99,
    "tax": 0,
    "total_price": 999.99,
    "currency": "USD",
    "payment_method": "bank_transfer",
    "status": "completed",
    "approval_status": "pending",
    "receipt_url": "http://domain.com/storage/receipts/receipt-INV-123456.jpg",
    "order_notes": "Please deliver before 5 PM",
    "created_at": "Apr 06, 2026 10:30 AM"
  }
}
```

### 3. Approve Order
**Endpoint:** `POST /orders/{order}/approve`

**Headers:**
- `Content-Type: application/json`
- `X-CSRF-TOKEN: {token}` (for web requests)

**Response:**
```json
{
  "success": true,
  "message": "Order approved successfully",
  "order": {
    "id": 1,
    "approval_status": "approved",
    "approved_at": "Apr 06, 2026 11:00 AM",
    "approved_by": "Admin User"
  }
}
```

### 4. Reject Order
**Endpoint:** `POST /orders/{order}/reject`

**Headers:**
- `Content-Type: application/json`
- `X-CSRF-TOKEN: {token}` (for web requests)

**Body:**
```json
{
  "reason": "Insufficient stock available"
}
```

**Response:**
```json
{
  "success": true,
  "message": "Order rejected successfully",
  "order": {
    "id": 1,
    "approval_status": "rejected",
    "approved_at": "Apr 06, 2026 11:00 AM",
    "approved_by": "Admin User",
    "rejection_reason": "Insufficient stock available"
  }
}
```

### 5. Get Approval Statistics
**Endpoint:** `GET /orders/approval/stats`

**Response:**
```json
{
  "success": true,
  "stats": {
    "pending": 5,
    "approved": 120,
    "rejected": 3,
    "total": 128
  }
}
```

### 6. Get Pending Orders (API Key Protected)
**Endpoint:** `GET /api/orders/pending-approval`

**Headers:**
- `X-API-Key: {your-api-key}`

**Response:**
```json
{
  "success": true,
  "orders": [...]
}
```

## Admin Interface

### Web Interface
Access the admin approval interface at:
```
http://your-domain.com/admin-order-approval.html
```

### Features:
- Real-time statistics dashboard
- Filter by approval status (pending, approved, rejected)
- Filter by payment status (pending, completed)
- View receipt images inline
- One-click approve/reject actions
- Rejection reason modal
- Auto-refresh every 30 seconds

## Workflow

### 1. Customer Places Order
- Order is created with `approval_status = 'pending'`
- Admin receives notification (if configured)

### 2. Admin Reviews Order
- Admin accesses approval interface
- Views order details, items, and receipt (if uploaded)
- Checks payment status

### 3. Admin Approves Order
- Click "Approve" button
- Order status changes to `approved`
- If payment is completed, stock is automatically reduced
- Telegram notification sent
- Customer can be notified (optional)

### 4. Admin Rejects Order
- Click "Reject" button
- Enter rejection reason
- Order status changes to `rejected`
- Telegram notification sent with reason
- Customer can be notified (optional)

## Stock Management

### Important Notes:
- Stock is NOT reduced when order is created (if approval is required)
- Stock is reduced when:
  1. Order is approved AND payment is completed
  2. OR payment is completed for cash orders (no approval needed)
- Rejected orders do NOT affect stock

## Integration with Receipt Upload

The approval system works seamlessly with the receipt upload feature:

1. Customer uploads receipt after payment
2. Receipt is linked to the order
3. Admin can view receipt in approval interface
4. Admin verifies receipt before approving

## Telegram Notifications

### Approval Notification
```
✅ Order Approved!

Bill No: #INV-123456
Customer: John Doe
Total: USD 999.99
Payment: BANK_TRANSFER
Approved by: Admin User
Items: 2

• iPhone 15 Pro x1 = USD 999.99
```

### Rejection Notification
```
❌ Order Rejected

Bill No: #INV-123456
Customer: John Doe
Total: USD 999.99
Rejected by: Admin User
Reason: Insufficient stock available
```

## Security

### Access Control
- Only users with `role = 'admin'` can access approval endpoints
- All approval actions are logged with admin user ID
- CSRF protection on all POST requests

### Audit Trail
- `approved_by` - Tracks which admin approved/rejected
- `approved_at` - Timestamp of approval/rejection
- `rejection_reason` - Reason for rejection (if applicable)

## Testing

### Manual Testing
1. Create a test order
2. Access admin interface: `http://localhost:8000/admin-order-approval.html`
3. View pending orders
4. Test approve action
5. Test reject action with reason

### API Testing with cURL

**Approve Order:**
```bash
curl -X POST http://localhost:8000/orders/1/approve \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=YOUR_SESSION_COOKIE"
```

**Reject Order:**
```bash
curl -X POST http://localhost:8000/orders/1/reject \
  -H "Content-Type: application/json" \
  -H "Cookie: laravel_session=YOUR_SESSION_COOKIE" \
  -d '{"reason": "Test rejection"}'
```

## Configuration

### Enable/Disable Approval Requirement
You can modify the order creation logic in `SaleController.php` to conditionally require approval:

```php
// In SaleController@store
$requiresApproval = in_array($request->payment_method, ['bank_transfer', 'aba_pay']);
$approvalStatus = $requiresApproval ? 'pending' : 'approved';

$sale = Sale::create([
    // ... other fields
    'approval_status' => $approvalStatus,
]);
```

## Best Practices

1. **Review Orders Promptly** - Check pending orders regularly to avoid delays
2. **Verify Receipts** - Always check uploaded receipts before approval
3. **Provide Clear Rejection Reasons** - Help customers understand why orders were rejected
4. **Monitor Statistics** - Keep track of approval/rejection rates
5. **Set Up Notifications** - Configure Telegram for real-time alerts

## Troubleshooting

### Orders Not Showing
- Check if you're logged in as admin
- Verify migration has been run
- Check filter settings

### Cannot Approve/Reject
- Ensure you have admin role
- Check CSRF token is present
- Verify order is in pending status

### Stock Not Reducing
- Check if payment status is completed
- Verify approval status is approved
- Check phone stock levels

## Future Enhancements

- Email notifications to customers
- Bulk approval/rejection
- Approval workflow with multiple levels
- Custom approval rules based on order value
- Approval history log
- Export approval reports
