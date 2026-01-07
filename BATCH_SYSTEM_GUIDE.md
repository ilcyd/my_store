# Batch & Expiry Tracking System

## Overview
The batch tracking system allows you to manage products with multiple batches, each having different expiry dates, manufacture dates, and suppliers. The system automatically implements FIFO (First In First Out) logic to ensure oldest batches sell first.

## Database Structure

### product_batches Table
- **id**: Unique batch identifier
- **product_id**: Link to products table
- **batch_number**: Unique batch identifier (e.g., BATCH-001, LOT-2024-01)
- **quantity**: Current quantity in this batch
- **cost_price**: Purchase/cost price per unit
- **manufacture_date**: When the batch was manufactured
- **expiry_date**: When the batch expires (NULL if no expiry)
- **received_date**: When batch was received in inventory
- **supplier**: Supplier name/info
- **notes**: Additional information
- **status**: ENUM('active', 'expired', 'recalled')
- **created_at**, **updated_at**: Timestamps

### order_items.batch_id
- Links each sold item to the specific batch it came from
- Enables full traceability: which customer received products from which batch

## Key Features

### 1. FIFO Inventory Management
- **Automatic Selection**: System automatically selects oldest expiring batch first
- **Multi-Batch Deduction**: If one batch doesn't have enough quantity, automatically pulls from next oldest
- **Stock Synchronization**: Product stock automatically syncs with total batch quantities

### 2. Expiry Management
- **Expiry Warnings**: Dashboard shows batches expiring within 7 days
- **Expired Alerts**: Red alerts for batches past expiry date
- **Block Expired Sales**: System prevents selling from expired batches
- **Manual Expiry**: Mark batches as expired to remove from available stock

### 3. Batch Operations
- **Add Batch**: Create new batch with optional expiry/manufacture dates
- **View Batches**: See all batches with expiry countdown
- **Edit Batch**: Update quantity, dates, supplier info
- **Delete Batch**: Remove batch and adjust product stock
- **Mark Expired**: Manually expire a batch

## Usage Guide

### Adding a New Batch

1. Navigate to **Batches & Expiry** in admin sidebar
2. Click **Add Batch** button
3. Fill in required fields:
   - Product (required)
   - Batch Number (required) - e.g., BATCH-2024-001
   - Quantity (required)
4. Optional fields:
   - Cost Price
   - Manufacture Date
   - Expiry Date (important for perishable goods)
   - Supplier name
   - Notes
5. Click **Add Batch**

**Result**: Product stock automatically increases by batch quantity

### Processing POS Sales with Batches

When you process a sale through the POS system:

1. Customer adds products to cart
2. Click **Complete Sale**
3. System automatically:
   - Selects oldest expiring batch(es)
   - Deducts quantity using FIFO logic
   - Records batch_id in order_items for traceability
   - Updates product stock
   - Skips any expired batches

**Example**:
```
Product: Milk
- Batch A: 50 units, expires Jan 15
- Batch B: 100 units, expires Jan 20
- Batch C: 75 units, expires Jan 10

Customer orders 80 units:
- 75 from Batch C (oldest expiring)
- 5 from Batch A (next oldest)
- Batch B untouched (newest)
```

### Managing Expiry Dates

#### View Expiring Batches
1. Dashboard shows alerts for:
   - Expired batches (red alert)
   - Expiring within 7 days (yellow alert)
2. Click alert links to view batch details

#### Mark Batch as Expired
1. Go to **Batches & Expiry**
2. Find expired batch (red row)
3. Click **Expire** button
4. Confirm action
5. **Result**: 
   - Batch marked as expired
   - Quantity removed from product stock
   - Batch no longer available for sale

#### Filter Batches by Status
Use dropdown filter:
- **Active Batches**: Only available inventory
- **Expired Batches**: Past expiry or manually expired
- **All Batches**: Complete history

### Editing Batch Quantities

1. Click **Edit** (pencil icon) on any batch
2. Modify quantity field
3. Click **Update Batch**
4. **Result**: Product stock automatically recalculates

**Example**:
```
Product stock: 200 units
Batch A: 100 units

Edit Batch A to 150 units:
- Product stock becomes 250 units (automatically)

Edit Batch A to 50 units:
- Product stock becomes 150 units (automatically)
```

### Traceability & Recalls

#### View Which Batch Went to Which Customer
1. Go to **Orders** page
2. Click on any completed order
3. Order items now show batch information
4. Useful for product recalls or quality issues

#### Scenario: Product Recall
```
Contaminated Batch: BATCH-2024-005

1. Go to Batches & Expiry
2. Search for BATCH-2024-005
3. View batch details
4. Check order history to see which customers received items
5. Mark batch as 'recalled' (update status)
6. Contact affected customers
```

## Functions Reference

### Core Functions (in includes/functions.php)

```php
// Add new batch
addProductBatch($product_id, $batch_number, $quantity, $cost_price, $manufacture_date, $expiry_date, $supplier, $notes)

// Get batches for a product
getProductBatches($product_id, $status = 'active')

// Get single batch with product info
getBatchById($batch_id)

// Update batch quantity (auto syncs stock)
updateBatchQuantity($batch_id, $new_quantity)

// FIFO deduction (returns array of batch_id/quantity pairs)
deductFromBatches($product_id, $quantity)

// Get oldest available batch
getOldestBatch($product_id)

// Get batches expiring in X days
getExpiringBatches($days = 7)

// Get all expired batches
getExpiredBatches()

// Mark batch as expired
markBatchExpired($batch_id)

// Full CRUD operations
updateBatch($batch_id, $data)
deleteBatch($batch_id)
getAllBatches($status = 'active')
```

## Best Practices

### 1. Batch Numbering
- Use consistent format: `BATCH-YYYY-MMM-001`
- Include year/month for easy tracking
- Sequential numbers within each period

### 2. Regular Monitoring
- Check dashboard alerts daily
- Review expiring batches weekly
- Process expired batches immediately

### 3. Data Entry
- Always enter expiry dates for perishable goods
- Include supplier information for traceability
- Add manufacture dates for quality tracking
- Use notes field for special conditions (storage temp, etc.)

### 4. Inventory Receiving
- Create batch immediately when receiving stock
- Don't mix different expiry dates in same batch
- Use different batch numbers for different delivery dates

### 5. Stock Management
- Let system handle FIFO automatically
- Don't manually edit product stock when using batches
- Batch quantities determine product stock

## API Endpoints

- `POST /api/admin-add-batch.php` - Create new batch
- `GET /api/admin-get-batch.php?id=X` - Get batch details
- `POST /api/admin-update-batch.php` - Update batch
- `POST /api/admin-delete-batch.php` - Delete batch
- `POST /api/admin-mark-batch-expired.php` - Expire batch

## Troubleshooting

### Problem: Product stock doesn't match batch totals
**Solution**: Stock automatically syncs when batch quantities change. If mismatch occurs:
1. Go to each batch for the product
2. Edit batch and save without changes
3. System recalculates stock from batches

### Problem: Can't sell product with available stock
**Check**:
1. Are all batches expired?
2. Go to Batches & Expiry
3. Filter by product
4. Verify at least one active, non-expired batch exists

### Problem: Wrong batch deducted in sale
**Note**: System uses FIFO (First In First Out) automatically:
- Oldest expiry date sells first
- This is intentional and correct for inventory management

### Problem: Need to manually select batch
**Current system** uses automatic FIFO. For manual batch selection:
- Feature can be added to POS system
- Contact developer to implement manual batch picker

## Reports & Analytics

### Available Reports (Current)
- Expiring batches (7-day window)
- Expired batches
- Active batch inventory
- Batch history per product

### Future Enhancements
- Waste/expiry reports
- Batch turnover rate
- Supplier quality metrics
- FIFO compliance reports
- Batch movement history

## Integration Notes

- **POS System**: Fully integrated with automatic FIFO
- **Order Management**: batch_id tracked in order_items
- **Product Management**: Stock syncs automatically
- **Dashboard**: Real-time expiry alerts

## Database Migration

If upgrading from non-batch system:

```sql
-- Already in schema.sql, but for reference:

-- Add batch tracking to order_items
ALTER TABLE order_items ADD COLUMN batch_id INT NULL AFTER price;
ALTER TABLE order_items ADD FOREIGN KEY (batch_id) REFERENCES product_batches(id) ON DELETE SET NULL;

-- Create product_batches table (see schema.sql for full definition)
```

## Support

For issues or questions about the batch tracking system:
1. Check this documentation
2. Review functions.php batch functions
3. Check database schema in schema.sql
4. Test with sample data in development environment
