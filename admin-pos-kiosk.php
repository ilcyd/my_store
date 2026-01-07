<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

requireAdmin();

$categories = getAllCategories();
$all_products = getProductsForPOS();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>POS Kiosk Mode</title>
    
    <link rel="shortcut icon" href="./dist/assets/compiled/svg/favicon.svg" type="image/x-icon">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app.css">
    <link rel="stylesheet" href="./dist/assets/compiled/css/app-dark.css">
    <link rel="stylesheet" href="./dist/assets/extensions/sweetalert2/sweetalert2.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        * {
            -webkit-tap-highlight-color: transparent;
            -webkit-touch-callout: none;
            -webkit-user-select: none;
            user-select: none;
        }
        
        input, textarea {
            -webkit-user-select: text;
            user-select: text;
        }
        
        body {
            margin: 0;
            padding: 0;
            overflow: hidden;
            font-family: 'Nunito', sans-serif;
            touch-action: manipulation;
            -ms-touch-action: manipulation;
        }
        
        button, .product-card, .cart-item, .category-btn {
            touch-action: manipulation;
            -ms-touch-action: manipulation;
        }
        
        .kiosk-container {
            display: flex;
            height: 100vh;
            background: #f8f9fa;
        }
        .products-section {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 20px;
            overflow: hidden;
        }
        .kiosk-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid #435ebe;
        }
        .kiosk-header h2 {
            margin: 0;
            color: #435ebe;
            font-size: 2rem;
        }
        .mode-toggle {
            display: flex;
            gap: 10px;
        }
        .mode-btn {
            padding: 10px 20px;
            border: 2px solid #435ebe;
            background: white;
            color: #435ebe;
            border-radius: 8px;
            cursor: pointer;
            font-size: 0.95rem;
            font-weight: 600;
            transition: all 0.3s;
        }
        .mode-btn.active {
            background: #435ebe;
            color: white;
        }
        .mode-btn.return-mode {
            border-color: #ffc107;
            color: #ffc107;
        }
        .mode-btn.return-mode.active {
            background: #ffc107;
            color: #333;
        }
        .exit-btn {
            background: #dc3545;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 600;
        }
        .category-tabs {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            overflow-x: auto;
            padding-bottom: 10px;
        }
        .category-btn {
            padding: 15px 30px;
            border: 2px solid #435ebe;
            background: white;
            color: #435ebe;
            border-radius: 12px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 600;
            transition: all 0.3s;
            white-space: nowrap;
        }
        .category-btn.active {
            background: #435ebe;
            color: white;
        }
        .category-btn:hover {
            transform: scale(1.05);
        }
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 20px;
            overflow-y: auto;
            padding-right: 10px;
            flex: 1;
        }
        .product-card {
            background: white;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            padding: 15px;
            cursor: pointer;
            transition: all 0.3s;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .product-card:hover {
            transform: scale(1.05);
            border-color: #435ebe;
            box-shadow: 0 4px 16px rgba(67, 94, 190, 0.3);
        }
        .product-card img {
            width: 100%;
            height: 140px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 12px;
        }
        .product-card .name {
            font-size: 1.1rem;
            font-weight: 700;
            margin-bottom: 8px;
            color: #333;
            min-height: 40px;
        }
        .product-card .price {
            color: #28a745;
            font-size: 1.3rem;
            font-weight: 800;
        }
        .product-card .stock {
            font-size: 0.85rem;
            color: #6c757d;
            margin-top: 5px;
        }
        .cart-section {
            width: 450px;
            background: white;
            border-left: 3px solid #435ebe;
            display: flex;
            flex-direction: column;
            box-shadow: -4px 0 16px rgba(0,0,0,0.1);
        }
        .mobile-cart-toggle {
            display: none;
            background: #435ebe;
            color: white;
            padding: 12px 15px;
            border: none;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            align-items: center;
            justify-content: space-between;
        }
        .mobile-cart-toggle i {
            transition: transform 0.3s;
        }
        .mobile-cart-toggle.expanded i {
            transform: rotate(180deg);
        }
        .cart-section.expanded .cart-items,
        .cart-section.expanded .cart-header {
            display: block !important;
        }
        .cart-section.expanded .summary-row:not(.total) {
            display: flex !important;
        }
        .cart-header {
            padding: 20px;
            background: #435ebe;
            color: white;
        }
        .cart-header h3 {
            margin: 0;
            font-size: 1.8rem;
        }
        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding: 15px;
        }
        .cart-item {
            display: flex;
            gap: 15px;
            padding: 15px;
            border-bottom: 2px solid #f0f0f0;
            align-items: center;
        }
        .cart-item img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 8px;
        }
        .cart-item-details {
            flex: 1;
        }
        .cart-item-name {
            font-weight: 700;
            font-size: 1.1rem;
            margin-bottom: 5px;
        }
        .cart-item-price {
            color: #28a745;
            font-weight: 600;
        }
        .quantity-control {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-top: 8px;
        }
        .quantity-control button {
            width: 35px;
            height: 35px;
            border: none;
            background: #435ebe;
            color: white;
            border-radius: 6px;
            font-size: 1.2rem;
            cursor: pointer;
            font-weight: bold;
        }
        .quantity-control button:hover {
            background: #364a92;
        }
        .quantity-control input {
            width: 60px;
            text-align: center;
            border: 2px solid #dee2e6;
            border-radius: 6px;
            padding: 8px;
            font-size: 1rem;
            font-weight: 600;
        }
        .cart-item-remove {
            background: #dc3545;
            border: none;
            color: white;
            width: 40px;
            height: 40px;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1.2rem;
        }
        .cart-empty {
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .cart-empty i {
            font-size: 4rem;
            margin-bottom: 15px;
            opacity: 0.3;
        }
        .cart-summary {
            padding: 20px;
            border-top: 3px solid #f0f0f0;
            background: #f8f9fa;
        }
        .summary-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 1.1rem;
        }
        .summary-row.total {
            font-size: 1.5rem;
            font-weight: 800;
            color: #435ebe;
            padding-top: 12px;
            border-top: 2px solid #dee2e6;
        }
        .cart-actions {
            padding: 20px;
            display: flex;
            gap: 12px;
        }
        .cart-actions button {
            flex: 1;
            padding: 18px;
            border: none;
            border-radius: 10px;
            font-size: 1.2rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s;
        }
        .clear-btn {
            background: #6c757d;
            color: white;
        }
        .clear-btn:hover {
            background: #5a6268;
        }
        .checkout-btn {
            background: #28a745;
            color: white;
        }
        .checkout-btn:hover {
            background: #218838;
            transform: scale(1.05);
        }
        .checkout-btn:disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
        .search-box {
            margin-bottom: 20px;
        }
        .search-box input {
            width: 100%;
            padding: 15px;
            border: 2px solid #dee2e6;
            border-radius: 10px;
            font-size: 1.1rem;
        }
        .search-box input:focus {
            outline: none;
            border-color: #435ebe;
        }
        /* Custom scrollbar */
        .product-grid::-webkit-scrollbar,
        .cart-items::-webkit-scrollbar {
            width: 8px;
        }
        .product-grid::-webkit-scrollbar-track,
        .cart-items::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        .product-grid::-webkit-scrollbar-thumb,
        .cart-items::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 4px;
        }
        .product-grid::-webkit-scrollbar-thumb:hover,
        .cart-items::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        /* Tablet and Small Desktop */
        @media screen and (max-width: 1024px) {
            .cart-section {
                width: 380px;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                gap: 15px;
            }
        }
        
        /* Mobile Landscape */
        @media screen and (max-width: 992px) and (orientation: landscape) {
            body {
                overflow: auto;
            }
            
            .kiosk-container {
                flex-direction: row;
                height: auto;
                min-height: 100vh;
            }
            
            .products-section {
                flex: 0 0 60%;
                padding: 10px;
            }
            
            .cart-section {
                flex: 0 0 40%;
                width: auto;
                position: sticky;
                top: 0;
                height: 100vh;
            }
            
            .kiosk-header {
                flex-wrap: wrap;
                margin-bottom: 10px;
                padding-bottom: 10px;
            }
            
            .kiosk-header h2 {
                font-size: 1.2rem;
                flex: 1 1 100%;
                margin-bottom: 10px;
            }
            
            .kiosk-header > div {
                flex-wrap: wrap;
                gap: 5px !important;
            }
            
            .mode-toggle {
                gap: 5px;
            }
            
            .mode-btn {
                padding: 6px 12px;
                font-size: 0.8rem;
            }
            
            .exit-btn, .btn-success {
                padding: 6px 12px !important;
                font-size: 0.8rem !important;
            }
            
            .category-tabs {
                margin-bottom: 8px;
                gap: 5px;
            }
            
            .category-btn {
                padding: 8px 15px;
                font-size: 0.85rem;
            }
            
            .search-box {
                margin-bottom: 8px;
            }
            
            .search-box input {
                padding: 8px;
                font-size: 0.9rem;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(110px, 1fr));
                gap: 10px;
            }
            
            .product-card {
                padding: 10px;
            }
            
            .product-card img {
                height: 80px;
                margin-bottom: 8px;
            }
            
            .product-card .name {
                font-size: 0.85rem;
                min-height: 32px;
            }
            
            .product-card .price {
                font-size: 1rem;
            }
            
            .cart-header {
                padding: 15px;
            }
            
            .cart-header h3 {
                font-size: 1.3rem;
            }
            
            .cart-items {
                padding: 10px;
            }
            
            .cart-item {
                padding: 10px;
                gap: 10px;
            }
            
            .cart-item img {
                width: 55px;
                height: 55px;
            }
            
            .cart-item-name {
                font-size: 0.9rem;
            }
            
            .quantity-control button {
                width: 30px;
                height: 30px;
                font-size: 1rem;
            }
            
            .quantity-control input {
                width: 50px;
                padding: 6px;
                font-size: 0.9rem;
            }
            
            .cart-summary {
                padding: 12px;
            }
            
            .summary-row {
                font-size: 0.95rem;
                margin-bottom: 8px;
            }
            
            .summary-row.total {
                font-size: 1.3rem;
            }
            
            .cart-actions {
                padding: 12px;
                gap: 8px;
            }
            
            .cart-actions button {
                padding: 14px;
                font-size: 1rem;
            }
        }
        
        /* Mobile Portrait */
        @media screen and (max-width: 768px) {
            body {
                overflow: auto;
            }
            
            .kiosk-container {
                flex-direction: column;
                height: auto;
                min-height: 100vh;
                padding-bottom: 180px;
            }
            
            .products-section {
                padding: 10px;
                overflow: visible;
            }
            
            .cart-section {
                width: 100%;
                border-left: none;
                border-top: 3px solid #435ebe;
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                max-height: none;
                z-index: 1000;
                box-shadow: 0 -4px 20px rgba(0,0,0,0.15);
                transition: max-height 0.3s ease;
            }
            
            .cart-section:not(.expanded) {
                max-height: 200px;
            }
            
            .cart-section.expanded {
                max-height: 80vh;
                overflow-y: auto;
            }
            
            .mobile-cart-toggle {
                display: flex;
            }
            
            .cart-header {
                display: none;
            }
            
            .cart-section.expanded .cart-header {
                display: block;
                padding: 12px 15px;
            }
            
            .cart-section.expanded .cart-header h3 {
                font-size: 1.3rem;
            }
            
            .cart-items {
                display: none;
            }
            
            .cart-section.expanded .cart-items {
                display: block;
                padding: 10px;
                max-height: calc(80vh - 250px);
                overflow-y: auto;
            }
            
            .cart-section.expanded .cart-item {
                padding: 10px;
                position: relative;
            }
            
            .cart-section.expanded .cart-item img {
                width: 60px;
                height: 60px;
            }
            
            .cart-section.expanded .cart-item-details {
                flex: 1;
                min-width: 0;
            }
            
            .cart-section.expanded .cart-item-name {
                font-size: 0.95rem;
                white-space: normal;
            }
            
            .cart-section.expanded .quantity-control {
                display: flex;
                margin-top: 10px;
            }
            
            .cart-section.expanded .cart-item-remove {
                position: absolute;
                right: 10px;
                top: 10px;
            }
            
            .cart-summary {
                background: white;
                border-top: none;
            }
            
            .summary-row:not(.total) {
                display: none;
            }
            
            .kiosk-header {
                flex-direction: column;
                align-items: stretch;
                gap: 10px;
            }
            
            .kiosk-header h2 {
                font-size: 1.5rem;
                text-align: center;
            }
            
            .kiosk-header > div {
                flex-direction: column;
                gap: 8px !important;
            }
            
            .mode-toggle {
                flex-direction: row;
                justify-content: stretch;
            }
            
            .mode-btn {
                flex: 1;
                padding: 12px;
                font-size: 0.95rem;
            }
            
            .exit-btn {
                width: 100%;
                padding: 12px !important;
                font-size: 1rem !important;
            }
            
            .btn-success {
                width: 100%;
                padding: 12px !important;
                font-size: 1rem !important;
            }
            
            .mb-3 {
                margin-bottom: 10px !important;
            }
            
            .mb-3 label {
                font-size: 0.9rem;
            }
            
            .mb-3 input {
                font-size: 1rem !important;
                padding: 10px !important;
            }
            
            .category-tabs {
                gap: 8px;
                margin-bottom: 10px;
            }
            
            .category-btn {
                padding: 10px 15px;
                font-size: 0.9rem;
            }
            
            .search-box {
                margin-bottom: 10px;
            }
            
            .search-box input {
                padding: 12px;
                font-size: 1rem;
            }
            
            .product-grid {
                grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
                gap: 12px;
                overflow: visible;
                padding-bottom: 20px;
            }
            
            .product-card {
                padding: 12px;
            }
            
            .product-card img {
                height: 100px;
            }
            
            .product-card .name {
                font-size: 0.95rem;
            }
            
            .product-card .price {
                font-size: 1.2rem;
            }
            
            .cart-summary {
                padding: 12px 15px;
            }
            
            .summary-row {
                font-size: 1rem;
            }
            
            .summary-row:not(.total) {
                display: none;
            }
            
            .summary-row.total {
                font-size: 1.5rem;
                margin-bottom: 0;
                padding-top: 0;
                border-top: none;
            }
            
            .cart-actions {
                padding: 12px 15px;
                flex-direction: row;
                gap: 10px;
            }
            
            .cart-actions button {
                padding: 18px;
                font-size: 1.2rem;
                font-weight: bold;
            }
            
            .clear-btn {
                flex: 0 0 auto;
                width: 60px;
            }
            
            .checkout-btn {
                flex: 1;
            }
        }
        
        /* Small Mobile */
        @media screen and (max-width: 480px) {
            .kiosk-header h2 {
                font-size: 1.2rem;
            }
            
            .product-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 10px;
            }
            
            .product-card {
                padding: 10px;
            }
            
            .product-card img {
                height: 80px;
            }
            
            .product-card .name {
                font-size: 0.85rem;
                min-height: 34px;
            }
            
            .product-card .price {
                font-size: 1rem;
            }
            
            .product-card .stock {
                font-size: 0.75rem;
            }
            
            .category-btn {
                padding: 8px 12px;
                font-size: 0.85rem;
            }
            
            .cart-item {
                position: relative;
                padding-right: 50px;
            }
            
            .cart-item img {
                width: 50px;
                height: 50px;
            }
            
            .cart-item-name {
                font-size: 0.85rem;
            }
            
            .cart-item-price {
                font-size: 0.85rem;
            }
        }
    </style>
</head>
<body>
    <div class="kiosk-container">
        <!-- Products Section -->
        <div class="products-section">
            <div class="kiosk-header">
                <h2><i class="bi bi-cart-check"></i> Point of Sale</h2>
                <div style="display: flex; gap: 10px; align-items: center;">
                    <div class="mode-toggle">
                        <button class="mode-btn active" id="saleMode" onclick="switchMode('sale')">
                            <i class="bi bi-cart-plus"></i> Sale Mode
                        </button>
                        <button class="mode-btn return-mode" id="returnMode" onclick="switchMode('return')">
                            <i class="bi bi-arrow-return-left"></i> Return Mode
                        </button>
                    </div>
                    <button class="btn btn-success" style="padding: 12px 24px; border-radius: 8px; font-size: 1rem; font-weight: 600;" onclick="expressCheckout()" id="expressBtn" disabled>
                        <i class="bi bi-lightning-charge"></i> Express Checkout
                    </button>
                    <button class="exit-btn" onclick="exitKiosk()">
                        <i class="bi bi-box-arrow-right"></i> Exit Kiosk
                    </button>
                </div>
            </div>
            
            <div class="mb-3" style="background: #fff; padding: 15px; border-radius: 10px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                <label style="font-weight: 600; margin-bottom: 8px; display: block;"><i class="bi bi-upc-scan"></i> Barcode / SKU Scanner</label>
                <input type="text" id="barcodeInput" class="form-control" placeholder="Scan barcode or enter SKU..." style="font-size: 1.1rem; padding: 12px;" autofocus>
                <small class="text-muted">Press Enter or scan to add product</small>
            </div>
            
            <div class="search-box">
                <input type="text" id="searchProduct" placeholder="🔍 Search products..." onkeyup="searchProducts()">
            </div>
            
            <div class="category-tabs">
                <button class="category-btn active" onclick="filterCategory('all')">All Products</button>
                <?php foreach($categories as $cat): ?>
                <button class="category-btn" onclick="filterCategory(<?php echo $cat['id']; ?>)">
                    <?php echo htmlspecialchars($cat['name']); ?>
                </button>
                <?php endforeach; ?>
            </div>
            
            <div class="product-grid" id="productGrid">
                <?php foreach($all_products as $product): ?>
                <div class="product-card" data-category="<?php echo $product['category_id']; ?>" data-name="<?php echo strtolower($product['name']); ?>" onclick="addToCart(<?php echo $product['id']; ?>, '<?php echo addslashes($product['name']); ?>', <?php echo $product['price']; ?>, '<?php echo addslashes($product['image']); ?>')">
                    <img src="<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    <div class="name"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="price">₱<?php echo number_format($product['price'], 2); ?></div>
                    <div class="stock"><?php echo $product['stock']; ?> in stock</div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <!-- Cart Section -->
        <div class="cart-section" id="cartSection">
            <button class="mobile-cart-toggle" id="mobileCartToggle" onclick="toggleMobileCart()">
                <span>
                    <i class="bi bi-bag-check"></i> 
                    <span id="mobileCartCount">0 items</span> - 
                    <span id="mobileCartTotal">₱0.00</span>
                </span>
                <i class="bi bi-chevron-up"></i>
            </button>
            
            <div class="cart-header">
                <h3><i class="bi bi-bag-check"></i> Current Order</h3>
            </div>
            
            <div class="cart-items" id="cartItems">
                <div class="cart-empty">
                    <i class="bi bi-cart-x"></i>
                    <p>Cart is empty<br>Tap products to add</p>
                </div>
            </div>
            
            <div class="cart-summary">
                <div class="summary-row total">
                    <span>TOTAL:</span>
                    <span id="total">₱0.00</span>
                </div>
            </div>
            
            <div class="cart-actions">
                <button class="clear-btn" onclick="clearCart()">
                    <i class="bi bi-trash"></i> Clear
                </button>
                <button class="checkout-btn" id="checkoutBtn" onclick="showPaymentModal()" disabled>
                    <i class="bi bi-credit-card"></i> Checkout
                </button>
            </div>
        </div>
    </div>
    
    <!-- Payment Modal -->
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Complete Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Customer Name <small class="text-muted">(Optional)</small></label>
                                <input type="text" class="form-control" id="customerName" placeholder="Walk-in Customer">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Customer Phone <small class="text-muted">(Optional)</small></label>
                                <input type="tel" class="form-control" id="customerPhone">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label">Payment Method</label>
                                <select class="form-select" id="paymentMethod">
                                    <option value="cash">Cash</option>
                                    <option value="credit_card">Credit Card</option>
                                    <option value="debit_card">Debit Card</option>
                                    <option value="gcash">GCash</option>
                                    <option value="paymaya">PayMaya</option>
                                </select>
                            </div>
                            <div class="mb-3" id="cashPaymentSection">
                                <label class="form-label">Amount Received</label>
                                <input type="number" step="0.01" class="form-control" id="amountReceived" style="font-size: 1.2rem; padding: 12px;">
                                <div class="mt-2" style="display: flex; gap: 8px; flex-wrap: wrap;">
                                    <button type="button" class="btn btn-outline-primary" onclick="setQuickAmount(100)">₱100</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="setQuickAmount(200)">₱200</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="setQuickAmount(500)">₱500</button>
                                    <button type="button" class="btn btn-outline-primary" onclick="setQuickAmount(1000)">₱1000</button>
                                    <button type="button" class="btn btn-outline-success" onclick="setExactAmount()">Exact Amount</button>
                                </div>
                                <small class="text-muted">Total: <span id="modalTotal">₱0.00</span></small>
                                <div id="changeAmount" class="mt-2"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-success btn-lg" onclick="processPayment()">
                        <i class="bi bi-check-circle"></i> Complete Sale
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="dist/assets/compiled/js/app.js"></script>
    <script src="dist/assets/extensions/sweetalert2/sweetalert2.min.js"></script>
    <script>
        let cart = [];
        let currentMode = 'sale'; // 'sale' or 'return'
        
        function switchMode(mode) {
            currentMode = mode;
            const saleBtn = document.getElementById('saleMode');
            const returnBtn = document.getElementById('returnMode');
            const header = document.querySelector('.kiosk-header h2');
            const checkoutBtn = document.getElementById('checkoutBtn');
            const expressBtn = document.getElementById('expressBtn');
            
            if(mode === 'sale') {
                saleBtn.classList.add('active');
                returnBtn.classList.remove('active');
                header.innerHTML = '<i class="bi bi-cart-check"></i> Point of Sale - SALE MODE';
                checkoutBtn.innerHTML = '<i class="bi bi-cash-coin"></i> Checkout';
                expressBtn.innerHTML = '<i class="bi bi-lightning-charge"></i> Express Checkout';
            } else {
                saleBtn.classList.remove('active');
                returnBtn.classList.add('active');
                header.innerHTML = '<i class="bi bi-arrow-return-left"></i> Point of Sale - RETURN MODE';
                header.style.color = '#ffc107';
                checkoutBtn.innerHTML = '<i class="bi bi-arrow-return-left"></i> Process Return';
                expressBtn.innerHTML = '<i class="bi bi-arrow-return-left"></i> Express Return';
            }
            
            // Clear cart when switching modes
            if(cart.length > 0) {
                Swal.fire({
                    title: 'Switch Mode?',
                    text: 'Your current cart will be cleared.',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, switch mode'
                }).then((result) => {
                    if(result.isConfirmed) {
                        cart = [];
                        updateCart();
                    } else {
                        // Revert mode switch
                        currentMode = mode === 'sale' ? 'return' : 'sale';
                        switchMode(currentMode);
                    }
                });
            }
        }
        
        // Barcode scanner handler
        document.getElementById('barcodeInput').addEventListener('keypress', function(e) {
            if(e.key === 'Enter') {
                e.preventDefault();
                const sku = this.value.trim().toUpperCase();
                if(sku) {
                    addProductBySKU(sku);
                    this.value = '';
                }
            }
        });
        
        function addProductBySKU(sku) {
            // Find product by SKU
            const products = <?php echo json_encode($all_products); ?>;
            const product = products.find(p => p.sku.toUpperCase() === sku);
            
            if(product) {
                addToCart(product.id, product.name, parseFloat(product.price), product.image);
                // Visual feedback
                const input = document.getElementById('barcodeInput');
                input.style.borderColor = '#28a745';
                setTimeout(() => { input.style.borderColor = ''; }, 500);
            } else {
                // Not found feedback
                const input = document.getElementById('barcodeInput');
                input.style.borderColor = '#dc3545';
                Swal.fire({
                    icon: 'error',
                    title: 'Product Not Found',
                    text: 'SKU: ' + sku,
                    timer: 1500,
                    showConfirmButton: false
                });
                setTimeout(() => { input.style.borderColor = ''; }, 1000);
            }
        }
        
        function expressCheckout() {
            if(cart.length === 0) return;
            
            // Quick cash checkout without modal
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            const orderData = {
                customer_name: 'Walk-in Customer',
                customer_email: 'walk-in@store.com',
                customer_phone: 'N/A',
                shipping_address: 'Store Pickup',
                shipping_city: 'N/A',
                shipping_state: 'N/A',
                shipping_zip: '00000',
                payment_method: 'cash',
                items: cart,
                subtotal: total,
                tax: 0,
                shipping: 0,
                total: total
            };
            
            fetch('/my_store/api/admin-process-pos-sale.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server error: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if(data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Sale Complete!',
                            html: '<div style="font-size: 1.5rem; margin: 20px 0;">Order #' + data.order_id + '</div>' +
                                  '<div style="font-size: 2rem; font-weight: bold; color: #28a745;">₱' + total.toFixed(2) + '</div>',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            cart = [];
                            updateCart();
                            document.getElementById('barcodeInput').focus();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Failed to process sale', 'error');
                    }
                } catch(e) {
                    console.error('JSON Parse Error:', e);
                    console.error('Server Response:', text);
                    Swal.fire('Error', 'Invalid server response. Check console for details.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to connect to server. Please check your connection.', 'error');
            });
        }
        
        function setQuickAmount(amount) {
            document.getElementById('amountReceived').value = amount;
            document.getElementById('amountReceived').dispatchEvent(new Event('input'));
        }
        
        function setExactAmount() {
            const total = parseFloat(document.getElementById('total').textContent.replace('₱', ''));
            document.getElementById('amountReceived').value = total.toFixed(2);
            document.getElementById('amountReceived').dispatchEvent(new Event('input'));
        }
        
        function addToCart(id, name, price, image) {
            const existingItem = cart.find(item => item.id === id);
            if(existingItem) {
                existingItem.quantity++;
            } else {
                cart.push({ id, name, price, image, quantity: 1 });
            }
            updateCart();
        }
        
        function updateQuantity(id, newQty) {
            const item = cart.find(item => item.id === id);
            if(item) {
                if(newQty <= 0) {
                    removeFromCart(id);
                } else {
                    item.quantity = parseInt(newQty);
                    updateCart();
                }
            }
        }
        
        function removeFromCart(id) {
            cart = cart.filter(item => item.id !== id);
            updateCart();
        }
        
        function updateCart() {
            const cartItems = document.getElementById('cartItems');
            
            if(cart.length === 0) {
                cartItems.innerHTML = `
                    <div class="cart-empty">
                        <i class="bi bi-cart-x"></i>
                        <p>Cart is empty<br>Tap products to add</p>
                    </div>
                `;
                document.getElementById('checkoutBtn').disabled = true;
                document.getElementById('expressBtn').disabled = true;
            } else {
                let html = '';
                cart.forEach(item => {
                    html += `
                        <div class="cart-item">
                            <img src="${item.image}" alt="${item.name}">
                            <div class="cart-item-details">
                                <div class="cart-item-name">${item.name}</div>
                                <div class="cart-item-price">₱${item.price.toFixed(2)} each</div>
                                <div class="quantity-control">
                                    <button onclick="updateQuantity(${item.id}, ${item.quantity - 1})">-</button>
                                    <input type="number" value="${item.quantity}" onchange="updateQuantity(${item.id}, this.value)" min="1">
                                    <button onclick="updateQuantity(${item.id}, ${item.quantity + 1})">+</button>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 1.2rem; font-weight: bold; color: #28a745;">
                                    ₱${(item.price * item.quantity).toFixed(2)}
                                </div>
                                <button class="cart-item-remove" onclick="removeFromCart(${item.id})">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    `;
                });
                cartItems.innerHTML = html;
                document.getElementById('checkoutBtn').disabled = false;
                document.getElementById('expressBtn').disabled = false;
            }
            
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            document.getElementById('total').textContent = '₱' + total.toFixed(2);
            
            // Update mobile cart toggle
            const itemCount = cart.reduce((sum, item) => sum + item.quantity, 0);
            const mobileCartCount = document.getElementById('mobileCartCount');
            const mobileCartTotal = document.getElementById('mobileCartTotal');
            if(mobileCartCount) {
                mobileCartCount.textContent = itemCount + (itemCount === 1 ? ' item' : ' items');
            }
            if(mobileCartTotal) {
                mobileCartTotal.textContent = '₱' + total.toFixed(2);
            }
        }
        
        function toggleMobileCart() {
            const cartSection = document.getElementById('cartSection');
            const toggleBtn = document.getElementById('mobileCartToggle');
            cartSection.classList.toggle('expanded');
            toggleBtn.classList.toggle('expanded');
        }
        
        function clearCart() {
            if(cart.length === 0) return;
            
            Swal.fire({
                title: 'Clear Cart?',
                text: 'Remove all items from cart?',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Yes, clear it',
                cancelButtonText: 'Cancel'
            }).then((result) => {
                if(result.isConfirmed) {
                    cart = [];
                    updateCart();
                }
            });
        }
        
        function filterCategory(categoryId) {
            const products = document.querySelectorAll('.product-card');
            const buttons = document.querySelectorAll('.category-btn');
            
            buttons.forEach(btn => btn.classList.remove('active'));
            event.target.classList.add('active');
            
            products.forEach(product => {
                if(categoryId === 'all' || product.dataset.category == categoryId) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }
        
        function searchProducts() {
            const searchTerm = document.getElementById('searchProduct').value.toLowerCase();
            const products = document.querySelectorAll('.product-card');
            
            products.forEach(product => {
                const productName = product.dataset.name;
                if(productName.includes(searchTerm)) {
                    product.style.display = 'block';
                } else {
                    product.style.display = 'none';
                }
            });
        }
        
        function showPaymentModal() {
            if(cart.length === 0) return;
            
            const total = parseFloat(document.getElementById('total').textContent.replace('₱', ''));
            document.getElementById('modalTotal').textContent = '₱' + total.toFixed(2);
            
            const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
            modal.show();
        }
        
        document.getElementById('paymentMethod').addEventListener('change', function() {
            const cashSection = document.getElementById('cashPaymentSection');
            if(this.value === 'cash') {
                cashSection.style.display = 'block';
            } else {
                cashSection.style.display = 'none';
            }
        });
        
        document.getElementById('amountReceived').addEventListener('input', function() {
            const total = parseFloat(document.getElementById('total').textContent.replace('₱', ''));
            const received = parseFloat(this.value) || 0;
            const change = received - total;
            
            const changeDiv = document.getElementById('changeAmount');
            if(received >= total) {
                changeDiv.innerHTML = '<div class="alert alert-success">Change: ₱' + change.toFixed(2) + '</div>';
            } else {
                changeDiv.innerHTML = '<div class="alert alert-danger">Insufficient amount</div>';
            }
        });
        
        function processPayment() {
            const customerName = document.getElementById('customerName').value || 'Walk-in Customer';
            const paymentMethod = document.getElementById('paymentMethod').value;
            
            // Handle return mode
            if(currentMode === 'return') {
                processReturn(customerName, paymentMethod);
                return;
            }
            
            // Handle sale mode
            if(paymentMethod === 'cash') {
                const total = parseFloat(document.getElementById('total').textContent.replace('₱', ''));
                const received = parseFloat(document.getElementById('amountReceived').value) || 0;
                if(received < total) {
                    Swal.fire('Error', 'Insufficient payment amount', 'error');
                    return;
                }
            }
            
            const total = cart.reduce((sum, item) => sum + (item.price * item.quantity), 0);
            
            const orderData = {
                customer_name: customerName,
                customer_email: 'walk-in@store.com',
                customer_phone: document.getElementById('customerPhone').value || 'N/A',
                shipping_address: 'Store Pickup',
                shipping_city: 'N/A',
                shipping_state: 'N/A',
                shipping_zip: '00000',
                payment_method: paymentMethod,
                items: cart,
                subtotal: total,
                tax: 0,
                shipping: 0,
                total: total
            };
            
            fetch('/my_store/api/admin-process-pos-sale.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(orderData)
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Server error: ' + response.status);
                }
                return response.text();
            })
            .then(text => {
                try {
                    const data = JSON.parse(text);
                    if(data.success) {
                        bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                        Swal.fire({
                            icon: 'success',
                            title: 'Sale Complete!',
                            text: 'Order #' + data.order_id + ' created successfully',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(() => {
                            cart = [];
                            updateCart();
                            document.getElementById('customerName').value = '';
                            document.getElementById('customerPhone').value = '';
                            document.getElementById('amountReceived').value = '';
                            document.getElementById('changeAmount').innerHTML = '';
                            document.getElementById('barcodeInput').focus();
                        });
                    } else {
                        Swal.fire('Error', data.message || 'Failed to process sale', 'error');
                    }
                } catch(e) {
                    console.error('JSON Parse Error:', e);
                    console.error('Server Response:', text);
                    Swal.fire('Error', 'Invalid server response. Check console for details.', 'error');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire('Error', 'Failed to connect to server. Please check your connection.', 'error');
            });
        }
        
        function processReturn(customerName, refundMethod) {
            const customerPhone = document.getElementById('customerPhone').value || '';
            
            // Close the payment modal first
            const paymentModal = bootstrap.Modal.getInstance(document.getElementById('paymentModal'));
            if(paymentModal) {
                paymentModal.hide();
            }
            
            // Wait for modal to close before showing SweetAlert
            setTimeout(() => {
                Swal.fire({
                    title: 'Return Reason',
                    input: 'textarea',
                    inputLabel: 'Why is this being returned?',
                    inputPlaceholder: 'Enter return reason...',
                    inputAttributes: {
                        'aria-label': 'Return reason'
                    },
                    showCancelButton: true,
                    confirmButtonText: 'Process Return',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'Please provide a reason';
                        }
                    }
                }).then((result) => {
                if(result.isConfirmed) {
                    const returnData = {
                        return_type: 'pos',
                        refund_method: refundMethod,
                        reason: result.value,
                        customer_name: customerName,
                        customer_phone: customerPhone,
                        notes: 'POS Return processed',
                        items: cart.map(item => ({
                            product_id: item.id,
                            quantity: item.quantity,
                            price: item.price,
                            condition: 'Good'
                        }))
                    };
                    
                    fetch('/my_store/api/process-return.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify(returnData)
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                            Swal.fire({
                                icon: 'success',
                                title: 'Return Processed!',
                                html: `
                                    <p>Return #${data.return_id}</p>
                                    <p><strong>Refund: ₱${data.total_refund.toFixed(2)}</strong></p>
                                    <p>Method: ${refundMethod.toUpperCase()}</p>
                                `,
                                timer: 3000,
                                showConfirmButton: true
                            }).then(() => {
                                cart = [];
                                updateCart();
                                document.getElementById('customerName').value = '';
                                document.getElementById('customerPhone').value = '';
                                document.getElementById('amountReceived').value = '';
                                document.getElementById('changeAmount').innerHTML = '';
                                document.getElementById('barcodeInput').focus();
                            });
                        } else {
                            Swal.fire('Error', data.message || 'Failed to process return', 'error');
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire('Error', 'Failed to process return', 'error');
                    });
                }
            });
            }, 500); // Wait 500ms for modal to fully close
        }
        
        function exitKiosk() {
            if(cart.length > 0) {
                Swal.fire({
                    title: 'Exit Kiosk Mode?',
                    text: 'There are items in the cart. Are you sure?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, exit',
                    cancelButtonText: 'Cancel'
                }).then((result) => {
                    if(result.isConfirmed) {
                        window.location.href = 'admin-dashboard.php';
                    }
                });
            } else {
                window.location.href = 'admin-dashboard.php';
            }
        }
        
        // Keyboard shortcuts
        document.addEventListener('keydown', function(e) {
            // Don't trigger shortcuts when typing in inputs
            if(e.target.tagName === 'INPUT' && e.target.id !== 'barcodeInput') return;
            
            // F1 - Focus barcode scanner
            if(e.key === 'F1') {
                e.preventDefault();
                document.getElementById('barcodeInput').focus();
            }
            // F2 - Clear cart
            if(e.key === 'F2') {
                e.preventDefault();
                clearCart();
            }
            // F3 - Search
            if(e.key === 'F3') {
                e.preventDefault();
                document.getElementById('searchProduct').focus();
            }
            // F4 - Checkout
            if(e.key === 'F4') {
                e.preventDefault();
                if(cart.length > 0) showPaymentModal();
            }
            // F5 - Express Checkout
            if(e.key === 'F5') {
                e.preventDefault();
                if(cart.length > 0) expressCheckout();
            }
            // ESC - Exit kiosk
            if(e.key === 'Escape') {
                if(!document.querySelector('.modal.show')) {
                    exitKiosk();
                }
            }
        });
    </script>
</body>
</html>
