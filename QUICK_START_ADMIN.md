# Quick Start - Admin Panel

## Initial Setup (First Time Only)

1. **Reset Admin Password**
   ```
   Visit: http://localhost/my_store/reset-admin-password.php
   This sets password to: admin123
   ```

2. **Login to Admin**
   ```
   URL: http://localhost/my_store/login.php
   Email: admin@mystore.com
   Password: admin123
   ```

3. **Delete Reset Script (Security)**
   ```
   Delete: reset-admin-password.php
   ```

## Admin Panel Overview

### Main Menu
- 🏠 **Dashboard** - Statistics and overview
- 🛒 **POS System** - Process in-store sales
- 📦 **Orders** - Manage all orders
- 📦 **Products** - Add/edit products
- 🏷️ **Categories** - Manage categories
- 👥 **Customers** - View customer data

---

## Common Tasks

### 1. Process In-Store Sale (POS)
```
1. Click "POS System" in menu
2. Search or browse products
3. Click products to add to cart
4. Adjust quantities with +/- buttons
5. Click "Checkout"
6. Enter customer name
7. Select payment method
8. For cash: enter amount received
9. Click "Complete Sale"
```

### 2. Update Order Status
```
1. Click "Orders" in menu
2. Find order in list
3. Use dropdown to change status:
   - Pending → Processing → Shipped → Completed
4. Status saves automatically
```

### 3. Add New Product
```
1. Click "Products" in menu
2. Click "Add Product" button
3. Fill in:
   - Name*
   - Category*
   - Price*
   - Stock*
   - Image URL*
   - Description (optional)
   - SKU (optional)
4. Click "Add Product"
```

### 4. Manage Inventory
```
1. Go to "Products"
2. Click pencil icon on product
3. Update stock quantity
4. Click "Update Product"
```

### 5. View Customer Orders
```
1. Click "Customers" in menu
2. Click "View" on customer
3. See order history and stats
```

---

## POS System Features

### Product Selection
- **Search**: Type product name in search box
- **Filter by Category**: Click category buttons at top
- **Add to Cart**: Click any product card

### Cart Management
- **Change Quantity**: Use +/- buttons or type number
- **Remove Item**: Click trash icon
- **Clear Cart**: Click "Clear Cart" button at bottom

### Payment Methods
- Cash (shows change calculator)
- Credit Card
- Debit Card

### After Sale
- View Receipt (opens order details)
- New Sale (clears cart for next customer)

---

## Product Management

### Product Information
- **Name**: Display name
- **Price**: Sale price in dollars
- **Stock**: Available quantity
- **Category**: Product classification
- **Image**: Full URL to image
- **SKU**: Stock Keeping Unit (optional)
- **Description**: Product details (optional)

### Stock Status
- **In Stock**: Green badge (stock > 0)
- **Out of Stock**: Red badge (stock = 0)

---

## Order Management

### Order Statuses
1. **Pending** - New order, payment received
2. **Processing** - Order being prepared
3. **Shipped** - Order sent to customer
4. **Completed** - Order delivered
5. **Cancelled** - Order cancelled

### Order Information
- Order ID
- Customer details
- Order items
- Payment method
- Shipping address
- Order totals (subtotal, tax, shipping)

---

## Tips & Best Practices

### POS System
✓ Always enter customer name
✓ Verify cash amount before completing
✓ Use "Walk-in Customer" for anonymous sales
✓ Check inventory after large sales

### Order Management
✓ Update statuses promptly
✓ Review pending orders daily
✓ Check shipped orders weekly
✓ Archive completed orders monthly

### Product Management
✓ Keep stock counts accurate
✓ Use clear product names
✓ Add descriptions for better customer info
✓ Use high-quality images
✓ Organize with categories

### Customer Service
✓ Review customer order history before contact
✓ Monitor customer spending patterns
✓ Track frequent customers
✓ Respond to order inquiries quickly

---

## Keyboard Shortcuts

- **Search Products**: Focus search box (no shortcut needed)
- **Esc**: Close modals
- **Enter**: Submit forms

---

## Troubleshooting

### "Unauthorized" Error
→ Your session expired. Log in again.

### Products Not Showing
→ Check if products exist in "Products" page
→ Verify category assignments

### Cart Not Updating
→ Refresh page (F5)
→ Clear browser cache

### Order Status Won't Save
→ Check internet connection
→ Refresh page and try again

### POS Change Calculator Wrong
→ Verify amount entered
→ Check if total is correct

---

## System Information

### Tax Rate
- 8% applied to all sales

### Shipping
- Online orders: $5.99 (free over $50)
- POS orders: $0 (store pickup)

### Payment Processing
- Cash: Manual handling
- Card: Requires external processing
- All payments marked as received

---

## Security Notes

⚠️ **Important**:
- Change admin password after first login
- Delete reset-admin-password.php after use
- Log out when leaving computer
- Don't share admin credentials
- Regular database backups recommended

---

## Support

For technical issues:
1. Check ADMIN_GUIDE.md for detailed documentation
2. Review PHP error logs
3. Check browser console for JavaScript errors
4. Verify database connection

---

## Quick Reference URLs

- **Admin Login**: http://localhost/my_store/login.php
- **Dashboard**: http://localhost/my_store/admin-dashboard.php
- **POS**: http://localhost/my_store/admin-pos.php
- **Orders**: http://localhost/my_store/admin-orders.php
- **Products**: http://localhost/my_store/admin-products.php
- **Categories**: http://localhost/my_store/admin-categories.php
- **Customers**: http://localhost/my_store/admin-customers.php
- **Store Frontend**: http://localhost/my_store/index.php

---

**Happy Managing! 🚀**
