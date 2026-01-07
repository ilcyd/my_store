# My Store - Complete E-commerce System

A full-featured e-commerce platform built with PHP, MySQL, and the Mazer Admin Dashboard template.

## Features

### Customer Features
- **Product Browsing**: Browse products by category, search, and filter
- **Product Details**: View detailed product information with images
- **Shopping Cart**: Add/remove items, update quantities
- **Checkout**: Secure checkout process with multiple payment options
- **User Accounts**: Customer registration and login
- **Order Management**: View order history and track orders
- **Customer Dashboard**: Personal dashboard with order statistics

### Admin Features
- **Dashboard**: Overview of sales, orders, and statistics
- **Product Management**: Add, edit, and delete products
- **Order Management**: View and manage customer orders
- **Category Management**: Organize products into categories
- **Customer Management**: View customer information
- **Inventory Tracking**: Monitor stock levels

## Installation

### Prerequisites
- XAMPP (or similar) with PHP 7.4+ and MySQL 5.7+
- Web browser

### Setup Instructions

1. **Start XAMPP Services**
   - Start Apache and MySQL from XAMPP Control Panel

2. **Create Database**
   - Open phpMyAdmin (http://localhost/phpmyadmin)
   - Import the database schema:
     ```
     File location: database/schema.sql
     ```
   - Or run the SQL file through phpMyAdmin Import feature

3. **Configure Database Connection**
   - The database configuration is in `includes/config.php`
   - Default settings:
     ```php
     DB_HOST = 'localhost'
     DB_USER = 'root'
     DB_PASS = ''
     DB_NAME = 'my_store'
     ```

4. **Access the Application**
   - Storefront: http://localhost/my_store/
   - Admin Panel: http://localhost/my_store/admin-dashboard.php

### Default Admin Login
- Email: `admin@mystore.com`
- Password: `admin123`

## Project Structure

```
my_store/
├── dist/                    # Mazer template assets
│   ├── assets/             # CSS, JS, images
│   └── auth-*.html         # Authentication pages
├── includes/               # PHP includes
│   ├── config.php         # Database configuration
│   └── functions.php      # Core functions
├── api/                    # API endpoints
│   ├── cart-*.php         # Cart operations
│   ├── login.php          # Authentication
│   ├── register.php       # User registration
│   └── process-order.php  # Order processing
├── assets/
│   └── js/
│       └── cart.js        # Cart JavaScript
├── database/
│   └── schema.sql         # Database schema
├── index.php              # Homepage
├── products.php           # Product listing
├── product-detail.php     # Product details
├── cart.php               # Shopping cart
├── checkout.php           # Checkout page
├── customer-dashboard.php # Customer dashboard
├── admin-dashboard.php    # Admin dashboard
└── logout.php             # Logout handler
```

## Database Schema

### Tables
- **users**: Customer and admin accounts
- **categories**: Product categories
- **products**: Product information
- **cart**: Shopping cart items
- **orders**: Customer orders
- **order_items**: Order line items

## Features Breakdown

### Frontend (Customer)
1. **Homepage** (`index.php`)
   - Featured products
   - Category navigation
   - Hero section

2. **Products** (`products.php`)
   - Product grid with filters
   - Search functionality
   - Pagination
   - Sort options

3. **Product Detail** (`product-detail.php`)
   - Full product information
   - Add to cart
   - Related products

4. **Shopping Cart** (`cart.php`)
   - Cart items list
   - Update quantities
   - Remove items
   - Order summary

5. **Checkout** (`checkout.php`)
   - Shipping information form
   - Payment method selection
   - Order review

6. **Customer Dashboard** (`customer-dashboard.php`)
   - Order history
   - Account information
   - Order statistics

### Backend (Admin)
1. **Admin Dashboard** (`admin-dashboard.php`)
   - Sales statistics
   - Recent orders
   - Quick stats

2. **Order Management**
   - View all orders
   - Update order status
   - Order details

3. **Product Management**
   - Add/edit products
   - Manage inventory
   - Set featured products

### API Endpoints
- `POST /api/cart-add.php` - Add item to cart
- `POST /api/cart-update.php` - Update cart quantity
- `POST /api/cart-remove.php` - Remove item from cart
- `GET /api/cart-count.php` - Get cart item count
- `POST /api/login.php` - User authentication
- `POST /api/register.php` - User registration
- `POST /api/process-order.php` - Process checkout

## Configuration

### Tax and Shipping
Edit `includes/config.php`:
```php
define('TAX_RATE', 0.08);              // 8% tax
define('FREE_SHIPPING_THRESHOLD', 50);  // Free shipping over $50
define('SHIPPING_COST', 5.99);          // Standard shipping cost
```

### Site Settings
```php
define('SITE_NAME', 'My Store');
define('SITE_URL', 'http://localhost/my_store');
define('SITE_EMAIL', 'support@mystore.com');
```

## Sample Data

The database schema includes:
- 1 Admin user
- 8 Categories (Electronics, Clothing, Books, etc.)
- 20 Sample products with images
- Categories with icons

## Security Features

- Password hashing with PHP's `password_hash()`
- SQL injection protection with PDO prepared statements
- XSS protection with `htmlspecialchars()`
- Session management
- CSRF protection (implement tokens as needed)

## Technologies Used

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript
- **Framework**: Bootstrap 5
- **Template**: Mazer Admin Dashboard
- **Icons**: Bootstrap Icons, Iconly

## Browser Support

- Chrome (latest)
- Firefox (latest)
- Safari (latest)
- Edge (latest)

## Development

### Adding New Products
Use the admin panel or insert directly into the database:
```sql
INSERT INTO products (category_id, name, slug, sku, description, price, stock, image)
VALUES (1, 'Product Name', 'product-name', 'SKU-001', 'Description', 99.99, 50, 'image-url');
```

### Customizing Styles
- Template styles: `dist/assets/compiled/css/`
- Custom styles: Add to individual page `<style>` tags or create new CSS file

## Troubleshooting

### Database Connection Issues
- Verify XAMPP MySQL is running
- Check database credentials in `includes/config.php`
- Ensure database `my_store` exists

### Cart Not Working
- Check if session is started
- Verify JavaScript files are loaded
- Check browser console for errors

### Admin Access Issues
- Ensure user has `is_admin = 1` in database
- Clear browser cache and cookies

## Future Enhancements

- Product reviews and ratings
- Wishlist functionality
- Advanced search with filters
- Email notifications
- Payment gateway integration
- Product variants (size, color)
- Coupon/promo code system
- Advanced analytics
- Export orders to CSV

## License

This project is for educational purposes.

## Support

For issues or questions, please contact: support@mystore.com

---

**Version**: 1.0.0  
**Last Updated**: December 2025
