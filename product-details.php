<?php
$page_title = 'Product Detail - Tech Shop';
$current_page = 'products';
require_once 'includes/header.php';

// Get product ID from URL
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

try {
    // Fetch product details
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name 
        FROM products p 
        LEFT JOIN categories c ON p.category_id = c.id 
        WHERE p.id = ?
    ");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        header('Location: products.php');
        exit;
    }

    // Fetch related products
    $stmt = $pdo->prepare("
        SELECT p.* 
        FROM products p 
        WHERE p.category_id = ? 
        AND p.id != ? 
        AND p.stock > 0
        ORDER BY RAND()
        LIMIT 8
    ");
    $stmt->execute([$product['category_id'], $product_id]);
    $related_products = $stmt->fetchAll();

} catch(PDOException $e) {
    error_log("Error: " . $e->getMessage());
    header('Location: products.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($product['name']); ?> - Tech Shop</title>
    <style>
        .product-detail-container {
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
        }

        .product-main {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 30px;
            margin-bottom: 30px;
        }

        .product-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
        }

        .product-gallery {
            background: #f8fafc;
            padding: 30px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .product-image {
            max-width: 100%;
            height: auto;
            max-height: 400px;
            object-fit: contain;
            transition: transform 0.3s ease;
        }

        .product-image:hover {
            transform: scale(1.05);
        }

        .product-info {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .product-title {
            font-size: 2.2rem;
            color: #1e293b;
            margin: 0;
            font-weight: 600;
            line-height: 1.3;
        }

        .category-badge {
            display: inline-block;
            padding: 8px 16px;
            background: #f1f5f9;
            color: #64748b;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 500;
        }

        .product-price {
            font-size: 2.5rem;
            color: #2563eb;
            font-weight: 700;
        }

        .price-inquiry {
            font-size: 1.5rem;
            font-style: italic;
            color: #64748b;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .price-inquiry i {
            font-size: 1.2rem;
        }

        .contact-btn {
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
            text-decoration: none;
            text-align: center;
            display: inline-block;
        }

        .contact-btn:hover {
            background: #1e40af;
            transform: translateY(-2px);
        }

        .stock-info {
            display: flex;
            align-items: center;
            gap: 12px; /* Slightly increased for a cleaner layout */
            font-weight: 600; /* Bolder for better readability */
            font-size: 1rem; /* Use relative units for scalability */
            padding: 8px 12px; /* Add padding for a more polished look */
            border-radius: 8px; /* Rounded edges for a modern feel */
            background-color: #f9f9f9; /* Subtle background for contrast */
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1); /* Modern shadow effect */
            transition: background-color 0.3s ease, transform 0.3s ease; /* Smooth hover effects */
        }

        .stock-info:hover {
            background-color: #eef2f7; /* Highlight on hover */
            transform: translateY(-2px); /* Subtle lift effect */
        }

        .in-stock {
            color: #16a34a;
            font-size: 1.1rem; /* Slightly larger for emphasis */
            display: flex;
            align-items: center;
        }

        .out-of-stock {
            color: #dc2626;
            font-size: 1.1rem; /* Slightly larger for emphasis */
            display: flex;
            align-items: center;
        }

        .stock-info svg {
            width: 16px; /* Icon size */
            height: 16px;
            fill: currentColor; /* Matches the text color */
        }


        .description-box {
            border-top: 1px solid #e2e8f0;
            padding-top: 20px;
        }

        .description-box h2 {
            color: #1e293b;
            font-size: 1.25rem;
            margin-bottom: 12px;
        }

        .description-box p {
            color: #64748b;
            line-height: 1.7;
        }

        .add-to-cart-form {
            display: flex;
            gap: 12px;
            margin-top: 20px;
        }

        .quantity-input {
            width: 100px;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 1rem;
        }

        .add-to-cart-btn {
            flex: 1;
            background: #2563eb;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .add-to-cart-btn:hover {
            background: #1e40af;
            transform: translateY(-2px);
        }

        /* Related Products Section */
        .related-products {
            background: white;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            padding: 30px;
        }

        .section-title {
            font-size: 1.5rem;
            color: #1e293b;
            margin-bottom: 25px;
            text-align: center;
        }

        .products-slider {
            position: relative;
            padding: 0 40px;
        }

        .slider-container {
            overflow: hidden;
        }

        .slider-track {
            display: flex;
            gap: 20px;
            overflow-x: auto;
            scroll-behavior: smooth;
            scrollbar-width: none;
            padding: 20px 0;
        }

        .slider-track::-webkit-scrollbar {
            display: none;
        }

        .product-card {
            flex: 0 0 280px;
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: transform 0.3s ease;
        }

        .product-card:hover {
            transform: translateY(-5px);
        }

        .card-image {
            width: 100%;
            height: 200px;
            object-fit: contain;
            background: #f8fafc;
            padding: 15px;
        }

        .card-content {
            padding: 15px;
        }

        .card-title {
            font-size: 1.1rem;
            color: #1e293b;
            margin: 0 0 10px 0;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .card-price {
            font-size: 1.25rem;
            color: #2563eb;
            margin: 0 0 15px 0;
            font-weight: 600;
        }

        .card-inquiry {
            font-size: 1rem;
            font-style: italic;
            color: #64748b;
            margin: 0 0 15px 0;
        }

        .view-btn {
            display: inline-block;
            padding: 8px 16px;
            background: #2563eb;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            font-size: 0.9rem;
            transition: all 0.3s ease;
        }

        .view-btn:hover {
            background: #1e40af;
        }

        .slider-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            width: 40px;
            height: 40px;
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 50%;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            color: #1e293b;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .slider-btn:hover {
            background: #f1f5f9;
        }

        .prev-btn { left: 0; }
        .next-btn { right: 0; }

        @media (max-width: 768px) {
            .product-grid {
                grid-template-columns: 1fr;
            }
            
            .product-card {
                flex: 0 0 240px;
            }
            
            .add-to-cart-form {
                flex-direction: column;
            }
            
            .quantity-input {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="product-detail-container">
    <!-- Main Product Section -->
    <div class="product-main">
        <div class="product-grid">
            <div class="product-gallery">
                <img src="assets/uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>" 
                     class="product-image">
            </div>

            <div class="product-info">
                <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
                
                <span class="category-badge">
                    <?php echo htmlspecialchars($product['category_name']); ?>
                </span>
                
                <?php if (!isset($product['show_price']) || $product['show_price'] == 1): ?>
                    <div class="product-price">
                        Rs. <?php echo number_format($product['price'], 2); ?>
                    </div>
                <?php else: ?>
                    <div class="price-inquiry">
                        <i class="fas fa-info-circle"></i> Contact us for pricing
                    </div>
                <?php endif; ?>
                
                <div class="stock-info">
                    <?php if($product['stock'] > 0): ?>
                        <span class="in-stock">● Available  </span>
                    <?php else: ?>
                        <span class="out-of-stock">● Out of Stock</span>
                    <?php endif; ?>
                </div>

                <div class="description-box">
                    <h2>Description</h2>
                    <p><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>
                </div>

                <?php if($product['stock'] > 0): ?>
                    <?php if (!isset($product['show_price']) || $product['show_price'] == 1): ?>
                        <form class="add-to-cart-form">
                            <input type="hidden" id="product_id" value="<?php echo $product['id']; ?>">
                            <input type="number" id="quantity" class="quantity-input" 
                                   value="1" min="1" max="<?php echo $product['stock']; ?>">
                            <button type="button" class="add-to-cart-btn" data-product-id="<?php echo $product['id']; ?>">
                                Add to Cart
                            </button>
                        </form>
                    <?php else: ?>
                        <a href="contact.php?product=<?php echo $product['id']; ?>" class="contact-btn">
                            <i class="fas fa-envelope"></i> Inquire About This Product
                        </a>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Products Section -->
    <?php if(!empty($related_products)): ?>
    <div class="related-products">
        <h2 class="section-title">You May Also Like</h2>
        <div class="products-slider">
            <button class="slider-btn prev-btn" onclick="slideProducts(-1)">❮</button>
            <div class="slider-container">
                <div class="slider-track">
                    <?php foreach($related_products as $related): ?>
                        <div class="product-card">
                            <img src="assets/uploads/<?php echo htmlspecialchars($related['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($related['name']); ?>"
                                 class="card-image">
                            <div class="card-content">
                                <h3 class="card-title"><?php echo htmlspecialchars($related['name']); ?></h3>
                                
                                <?php if (!isset($related['show_price']) || $related['show_price'] == 1): ?>
                                    <p class="card-price">Rs. <?php echo number_format($related['price'], 2); ?></p>
                                <?php else: ?>
                                    <p class="card-inquiry">Contact for price</p>
                                <?php endif; ?>
                                
                                <a href="product-details.php?id=<?php echo $related['id']; ?>" 
                                   class="view-btn">View Details</a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <button class="slider-btn next-btn" onclick="slideProducts(1)">❯</button>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function slideProducts(direction) {
    const track = document.querySelector('.slider-track');
    const cardWidth = document.querySelector('.product-card').offsetWidth + 20;
    track.scrollLeft += cardWidth * direction;
}

// Touch support for mobile
let touchStartX = 0;
let touchEndX = 0;

const track = document.querySelector('.slider-track');
track.addEventListener('touchstart', e => {
    touchStartX = e.changedTouches[0].screenX;
});

track.addEventListener('touchend', e => {
    touchEndX = e.changedTouches[0].screenX;
    if (touchStartX > touchEndX) {
        slideProducts(1);
    } else if (touchStartX < touchEndX) {
        slideProducts(-1);
    }
});
</script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
<script src="assets/js/add-to-cart.js"></script>

<?php require_once 'includes/footer.php'; ?>
</body>
</html>