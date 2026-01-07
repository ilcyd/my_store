# Admin Panel & POS System Guide

## Table of Contents
1. [Admin Login](#admin-login)
2. [Dashboard Overview](#dashboard-overview)
3. [POS System](#pos-system)
4. [Order Management](#order-management)
5. [Product Management](#product-management)
6. [Category Management](#category-management)
7. [Customer Management](#customer-management)

## Admin Login

### First Time Setup
1. Visit `http://localhost/my_store/reset-admin-password.php` to reset the admin password
2. This will set the admin password to `admin123`
3. Delete the `reset-admin-password.php` file after use for security

### Login Credentials
- **Email**: admin@mystore.com
- **Password**: admin123

### Accessing Admin Panel
1. Go to `http://localhost/my_store/login.php`
2. Enter admin credentials
3. You'll be redirected to the admin dashboard

---

## Dashboard Overview

The admin dashboard (`admin-dashboard.php`) provides:

### Statistics Cards
- **Total Revenue**: Sum of all completed orders
- **Total Orders**: Number of orders in the system
- **Total Products**: Number of products in catalog
- **Total Customers**: Number of registered users

### Recent Orders
- View last 10 orders
- Quick status overview
- Direct links to order details
- Link to view all orders

### Navigation Menu
- Dashboard
- POS System
- Orders
- Products
- Categories
- Customers
- View Store (frontend)
- Logout

---

## POS System

The Point of Sale system (`admin-pos.php`) allows in-store sales processing.

### Features

#### Product Selection
- **Search Bar**: Type to search products by name
- **Category Filters**: Click category buttons to filter products
- **Product Grid**: Click any product card to add to cart
- Visual display with product images and prices

#### Shopping Cart
- **Add Items**: Click products to add to cart
- **Quantity Control**: Use +/- buttons or type quantity
- **Remove Items**: Click trash icon to remove
- **Auto Calculation**: Automatic subtotal, tax (8%), and total

#### Checkout Process
1. Click **Checkout** button (requires items in cart)
2. Enter customer information:
   - Customer Name (required)
   - Customer Email (optional)
   - Customer Phone (optional)
3. Select payment method:
   - Cash
   - Credit Card
   - Debit Card
4. **For Cash Payments**:
   - Enter amount received
   - System calculates change automatically
5. Click **Complete Sale**

#### Post-Sale Options
- **View Receipt**: Opens order detail page in new tab
- **New Sale**: Clears cart for next customer
- Order automatically marked as "Completed"
- Inventory automatically updated

### POS Best Practices
- Always enter customer name for tracking
- For walk-in customers, use generic names (e.g., "Walk-in Customer")
- Verify cash amount before completing sale
- Print or save receipt for customer records

---

## Order Management

### View All Orders (`admin-orders.php`)

#### Features
- Paginated order list (20 per page)
- Filter by status:
  - All Status
  - Pending
  - Processing
  - Shipped
  - Completed
  - Cancelled

#### Order Information Displayed
- Order ID
- Customer name and email
- Order date
- Number of items
- Total amount
- Payment method
- Current status

#### Actions
- **Change Status**: Use dropdown to update order status
- **View Details**: Click "View" button for full order details

### Order Details (`admin-order-detail.php`)

#### Information Displayed
- **Order Items**: Products, quantities, prices
- **Order Summary**: Subtotal, tax, shipping, total
- **Customer Information**: Name, email, phone
- **Shipping Address**: Full delivery address
- **Payment Information**: Method and date

#### Order Management
- Update order status with dropdown
- Status automatically updates in real-time
- Changes reflected across admin panel

---

## Product Management

### View All Products (`admin-products.php`)

#### Features
- Search by product name
- Filter by category
- Pagination support
- View product images, prices, stock

#### Add New Product
1. Click **Add Product** button
2. Fill in form:
   - Product Name (required)
   - Category (required)
   - Price (required)
   - Stock quantity (required)
   - SKU (optional)
   - Description (optional)
   - Image URL (required)
3. Click **Add Product**

#### Edit Product
1. Click pencil icon on product row
2. Modify any fields
3. Click **Update Product**

#### Delete Product
1. Click trash icon on product row
2. Confirm deletion
3. Product permanently removed

### Product Information
- Name and description
- Category assignment
- Price and stock levels
- SKU for inventory tracking
- Product images
- Stock status (In Stock/Out of Stock)

---

## Category Management

### View All Categories (`admin-categories.php`)

#### Features
- List all product categories
- View product count per category
- Add, edit, delete categories

#### Add New Category
1. Click **Add Category** button
2. Enter:
   - Category Name (required)
   - Description (optional)
3. Click **Add Category**

#### Edit Category
1. Click pencil icon
2. Modify name or description
3. Click **Update Category**

#### Delete Category
- Only allowed if category has no products
- Click trash icon
- Confirm deletion

### Category Usage
- Categories help organize products
- Used in POS filtering
- Displayed on store frontend
- Essential for product navigation

---

## Customer Management

### View All Customers (`admin-customers.php`)

#### Information Displayed
- Customer ID
- Name and email
- Account role (Admin/Customer)
- Registration date
- Total orders
- Total amount spent

#### Actions
- **View Details**: Click "View" button for customer profile

### Customer Details (`admin-customer-detail.php`)

#### Customer Profile
- **Personal Information**:
  - Name
  - Email
  - Account role
  - Member since date

- **Statistics**:
  - Total orders
  - Total spent
  - Average order value

- **Order History**:
  - Complete list of customer orders
  - Order dates and totals
  - Status for each order
  - Quick links to order details

---

## Technical Information

### Database Structure
The system uses the following main tables:
- `users` - Customer and admin accounts
- `products` - Product catalog
- `categories` - Product categories
- `orders` - Order records
- `order_items` - Individual order products
- `cart` - Shopping cart items

### API Endpoints
All admin actions use REST API endpoints in `/api/` folder:
- `admin-update-order-status.php` - Update order status
- `admin-process-pos-sale.php` - Process POS transactions
- `admin-add-product.php` - Create new product
- `admin-get-product.php` - Get product details
- `admin-update-product.php` - Update product
- `admin-delete-product.php` - Delete product
- `admin-add-category.php` - Create category
- `admin-update-category.php` - Update category
- `admin-delete-category.php` - Delete category

### Security Features
- Session-based authentication
- Admin role verification on all pages
- Password hashing with bcrypt
- SQL injection protection (prepared statements)
- XSS protection (htmlspecialchars)
- CSRF consideration for form submissions

### Configuration
- Tax rate: 8% (defined in POS JavaScript)
- Shipping: $5.99 (free over $50) - for online orders
- POS orders have $0 shipping (store pickup)
- Session lifetime: 24 hours

---

## Troubleshooting

### Admin Can't Login
1. Run `reset-admin-password.php`
2. Check database for admin user
3. Verify session configuration in `includes/config.php`

### POS Not Calculating Correctly
- Tax rate is 8% (hardcoded in `admin-pos.php`)
- Check browser console for JavaScript errors
- Verify cart.js is loaded

### Orders Not Updating
- Check API endpoint responses in browser Network tab
- Verify database connection
- Check PHP error logs

### Products Not Showing in POS
- Verify products exist in database
- Check category assignments
- Ensure product images are accessible

---

## Support & Maintenance

### Regular Maintenance Tasks
1. **Inventory Management**: Update product stock regularly
2. **Order Processing**: Update order statuses as fulfilled
3. **Customer Data**: Keep customer information current
4. **Backup Database**: Regular backups recommended

### Performance Tips
- Archive old orders periodically
- Optimize product images for web
- Keep product catalog organized
- Regular database maintenance

---

## Quick Reference

### Common Tasks

**Process In-Store Sale:**
POS System → Add products → Checkout → Enter customer info → Complete Sale

**Update Order Status:**
Orders → Select order → Change status dropdown → Auto-saved

**Add New Product:**
Products → Add Product → Fill form → Submit

**View Customer History:**
Customers → View → See orders and statistics

**Check Today's Revenue:**
Dashboard → View Total Revenue card

---

## File Structure

```
/my_store/
├── admin-dashboard.php          # Main admin dashboard
├── admin-pos.php                # Point of Sale system
├── admin-orders.php             # Order management
├── admin-order-detail.php       # Individual order view
├── admin-products.php           # Product management
├── admin-categories.php         # Category management
├── admin-customers.php          # Customer list
├── admin-customer-detail.php    # Customer profile
├── /includes/
│   ├── admin-sidebar.php        # Admin navigation menu
│   ├── config.php               # Database & configuration
│   └── functions.php            # All business logic
├── /api/
│   ├── admin-*.php              # Admin API endpoints
│   └── ...                      # Other API files
└── /dist/                       # Mazer template assets
```

---

## Version Information
- **System Version**: 1.0
- **Template**: Mazer Admin Dashboard
- **PHP Version**: 7.4+
- **MySQL Version**: 5.7+
- **Framework**: Bootstrap 5

---

**Last Updated**: December 2025
**Contact**: Admin Support
