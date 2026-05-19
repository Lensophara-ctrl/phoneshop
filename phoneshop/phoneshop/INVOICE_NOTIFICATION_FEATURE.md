# 📄 Invoice Notification Feature

## Overview
Automatic Telegram notifications are now sent when orders are completed and invoices are ready for download.

## Features Implemented

### 1. Invoice Ready Notifications 🎉
When an order is completed (payment verified), customers and admins receive a Telegram notification with:
- ✅ Order completion confirmation
- 📋 Bill number
- 👤 Customer name and email
- 💰 Total amount with currency
- 🔗 Direct link to download invoice
- ⏰ Timestamp

### 2. Invoice Download Tracking 📥
When a customer downloads their invoice, a notification is sent with:
- 📋 Bill number
- 👤 Customer information
- 💰 Order amount
- 🔒 Lock status (if report is locked)
- ⏰ Download timestamp

### 3. Delivery Status Notifications 🚚
Enhanced delivery tracking with notifications for status changes:
- ⏳ Pending
- 📦 Processing
- 🚚 Shipped
- 🛵 Out for Delivery
- ✅ Delivered

Each notification includes driver info, estimated time, and completion time.

## Notification Triggers

### Automatic Notifications Sent When:

1. **Cash Payment (POS)** - Immediate notification when sale is created
   - Payment confirmation
   - Invoice ready notification

2. **ABA Pay** - When payment is verified
   - Payment confirmation
   - Invoice ready notification

3. **Bakong Payment** - When payment is verified
   - Payment confirmation with transaction ID
   - Invoice ready notification

4. **Bank Transfer** - When payment is verified
   - Payment confirmation
   - Invoice ready notification

5. **Invoice Download** - When customer downloads invoice
   - Download confirmation with customer details

6. **Delivery Status Change** - When delivery status is updated
   - Status change notification with driver info

## Configuration

All notifications use your existing Telegram configuration in `.env`:

```env
TELEGRAM_ENABLED=true
TELEGRAM_BOT_TOKEN=8578514013:AAE6HUX-lAN7rATHPJQSy7bFioIfxt8T2_k
TELEGRAM_CHAT_ID=1886655363
```

## Files Modified

1. **TelegramService.php**
   - Added `sendInvoiceNotification()` method
   - Added `sendOrderStatusNotification()` method

2. **SaleController.php**
   - Added invoice notifications for all payment methods
   - Notifications sent when payment is completed

3. **CustomerOrderController.php**
   - Added download tracking notification
   - Sends notification when customer downloads invoice

4. **DeliveryController.php**
   - Added delivery status change notifications
   - Includes driver and timing information

## Notification Examples

### Invoice Ready Notification
```
🎉 Order Completed - Invoice Ready!

📋 Bill No: INV-ABC123
👤 Customer: John Doe
📧 Email: john@example.com
💰 Total: $299.99 USD

📄 Invoice: Ready to download
🔗 Link: https://yoursite.com/customer/orders/INV-ABC123/invoice

⏰ Time: 2026-04-08 14:30:00
```

### Invoice Downloaded Notification
```
📥 Invoice Downloaded

📋 Bill No: INV-ABC123
👤 Customer: John Doe
📧 Email: john@example.com
💰 Amount: USD 299.99
🔒 Locked: Yes
⏰ Downloaded: 2026-04-08 14:35:00
```

### Delivery Status Notification
```
🛵 Delivery Status Updated

📋 Bill No: #INV-ABC123
👤 Customer: John Doe
Status: Out For Delivery
Driver: Mike Smith
Driver Phone: +855 12 345 678
Estimated: Apr 08, 2026 16:00
⏰ Time: 2026-04-08 14:30:00
```

## Testing

To test the notifications:

1. **Create a new order** in POS system
2. **Complete payment** (cash or QR code)
3. **Check Telegram** for notifications
4. **Download invoice** from customer portal
5. **Check Telegram** for download notification

## Benefits

✅ Real-time order tracking
✅ Instant invoice availability notification
✅ Download tracking for audit purposes
✅ Better customer communication
✅ Automated record keeping
✅ No manual notification needed

## Notes

- All notifications are sent to the configured Telegram chat
- Notifications include clickable links for easy access
- Invoice URLs are automatically generated
- Works with all payment methods (Cash, ABA Pay, Bakong, Bank Transfer)
- Delivery tracking includes driver information
- Download tracking helps monitor customer engagement
