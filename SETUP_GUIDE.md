# Quick Setup Guide for My Store E-commerce System

## Step-by-Step Installation

### Step 1: Start XAMPP
1. Open XAMPP Control Panel
2. Click "Start" for Apache
3. Click "Start" for MySQL
4. Wait for both services to show "Running" status

### Step 2: Create Database
1. Open your web browser
2. Go to: http://localhost/phpmyadmin
3. Click "New" in the left sidebar
4. Database name: `my_store`
5. Collation: `utf8mb4_general_ci`
6. Click "Create"

### Step 3: Import Database Schema
1. In phpMyAdmin, select the `my_store` database (click on it in left sidebar)
2. Click the "Import" tab at the top
3. Click "Choose File"
4. Navigate to: `C:\xampp\htdocs\my_store\database\schema.sql`
5. Click "Go" at the bottom
6. Wait for success message

### Step 4: Access Your Store
Open your browser and go to:
- **Storefront**: http://localhost/my_store/
- **Admin Panel**: http://localhost/my_store/admin-dashboard.php

### Step 5: Login as Admin
Use these credentials:
- **Email**: admin@mystore.com
- **Password**: admin123

## What's Included

✅ Complete customer storefront
✅ Shopping cart functionality
✅ User registration and login
✅ Checkout process
✅ Admin dashboard
✅ Product management
✅ Order management
✅ 20 sample products
✅ 8 product categories

## Quick Test

1. **Browse Products**: Go to http://localhost/my_store/ and click "Shop Now"
2. **Add to Cart**: Click "Add to Cart" on any product
3. **View Cart**: Click the cart icon in the navbar
4. **Admin Access**: Go to http://localhost/my_store/admin-dashboard.php

## Default Data Included

### Admin Account
- Email: admin@mystore.com
- Password: admin123

### Sample Categories
- Electronics
- Clothing
- Books
- Home & Garden
- Sports
- Toys
- Beauty
- Food

### Sample Products
20 products across all categories with images and descriptions

## Common Issues

### Issue: "Database connection failed"
**Solution**: 
- Make sure MySQL is running in XAMPP
- Check that database name is `my_store`
- Verify credentials in `includes/config.php`

### Issue: "Page not found"
**Solution**:
- Ensure files are in `C:\xampp\htdocs\my_store\`
- Check that Apache is running
- Clear browser cache

### Issue: "Can't add to cart"
**Solution**:
- Check browser console for JavaScript errors
- Make sure `assets/js/cart.js` exists
- Verify API files are in `api/` folder

## File Checklist

Make sure these files exist:
- ✅ index.php
- ✅ products.php
- ✅ product-detail.php
- ✅ cart.php
- ✅ checkout.php
- ✅ customer-dashboard.php
- ✅ admin-dashboard.php
- ✅ includes/config.php
- ✅ includes/functions.php
- ✅ database/schema.sql
- ✅ assets/js/cart.js
- ✅ api/ folder with all API files

## Next Steps

1. **Customize Your Store**
   - Edit site name in `includes/config.php`
   - Change colors in template CSS
   - Add your own logo

2. **Add Products**
   - Login to admin panel
   - Go to Products section
   - Click "Add New Product"

3. **Test the System**
   - Create a customer account
   - Add items to cart
   - Complete a test order
   - View order in admin panel

## Need Help?

- Check the README.md for detailed documentation
- Review database/schema.sql for database structure
- Inspect browser console for JavaScript errors
- Check PHP error logs in XAMPP

## Security Reminder

⚠️ **Important**: Change the admin password after first login!

1. Go to admin dashboard
2. Navigate to profile settings
3. Update your password
4. Use a strong password

---

Your e-commerce system is now ready to use! 🎉
