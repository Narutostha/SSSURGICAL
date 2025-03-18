<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'config/database.php';

// Initialize variables
$page_title = "Premium Equipments - TechEquipments";
$current_page = 'products';
$search = isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'default';
$category_id = isset($_GET['category']) ? (int)$_GET['category'] : null;

// Check database connection
if (!$pdo) {
    die('Database connection failed.');
}

// Fetch categories
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name");
    $categories = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error fetching categories: " . $e->getMessage());
}

// Build product query
$query = "SELECT p.*, c.name as category_name 
          FROM products p 
          LEFT JOIN categories c ON p.category_id = c.id 
          WHERE 1=1";
$params = [];

if ($category_id) {
    $query .= " AND p.category_id = :category_id";
    $params[':category_id'] = $category_id;
}

if ($search) {
    $query .= " AND (p.name LIKE :search OR p.description LIKE :search)";
    $params[':search'] = "%$search%";
}

switch ($sort) {
    case 'price_low':
        $query .= " ORDER BY p.price ASC";
        break;
    case 'price_high':
        $query .= " ORDER BY p.price DESC";
        break;
    default:
        $query .= " ORDER BY p.created_at DESC";
}

// Fetch products
try {
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $products = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    die("Error fetching products: " . $e->getMessage());
}

// Include header
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <style>
        :root {
            --primary: #2563eb;
            --secondary: #1e40af;
            --accent: #dbeafe;
            --text: #1e293b;
            --gray: #64748b;
            --light: #f8fafc;
            --border: #e2e8f0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: var(--light);
            color: var(--text);
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .page-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .page-header h1 {
            font-size: 2.5rem;
            color: var(--text);
            margin-bottom: 0.5rem;
        }

        .page-header p {
            color: var(--gray);
        }

        .filters {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            margin-bottom: 2rem;
        }

        .filter-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .filter-group label {
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text);
        }

        .filter-input {
            padding: 0.75rem 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            font-size: 0.95rem;
            color: var(--text);
            background: white;
            transition: all 0.2s;
        }

        .filter-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 3px var(--accent);
        }

        .filter-button {
            background: var(--primary);
            color: white;
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s;
        }

        .filter-button:hover {
            background: var(--secondary);
            transform: translateY(-2px);
        }

        .products-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 2rem;
        }

        .product-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            transition: all 0.3s;
        }

        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 15px rgba(0,0,0,0.1);
        }

        .product-image {
            position: relative;
            padding-top: 75%;
            background: #f8fafc;
        }

        .product-image img {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: contain;
            padding: 1rem;
            transition: transform 0.3s;
        }

        .product-card:hover .product-image img {
            transform: scale(1.05);
        }

        .product-badge {
            position: absolute;
            top: 1rem;
            right: 1rem;
            background: #ef4444;
            color: white;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            font-weight: 500;
        }

        .product-content {
            padding: 1.5rem;
        }

        .product-category {
            font-size: 0.85rem;
            color: var(--gray);
            margin-bottom: 0.5rem;
        }

        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 0.75rem;
            color: var(--text);
        }

        .product-specs {
            display: flex;
            flex-wrap: wrap;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .spec-tag {
            background: var(--accent);
            color: var(--primary);
            padding: 0.25rem 0.75rem;
            border-radius: 15px;
            font-size: 0.8rem;
        }

        .product-price {
            font-size: 1.25rem;
            font-weight: 600;
            color: var(--primary);
            margin-bottom: 1rem;
        }

        .price-inquiry {
            font-size: 1.1rem;
            font-style: italic;
            color: var(--gray);
            margin-bottom: 1rem;
        }

        .product-actions {
            display: flex;
            gap: 0.75rem;
        }

        .btn {
            flex: 1;
            padding: 0.75rem;
            border-radius: 6px;
            font-weight: 500;
            text-align: center;
            text-decoration: none;
            transition: all 0.2s;
            cursor: pointer;
        }

        .btn-primary {
            background: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            background: var(--secondary);
        }

        .btn-outline {
            border: 1px solid var(--primary);
            color: var(--primary);
        }

        .btn-outline:hover {
            background: var(--accent);
        }

        .no-products {
            text-align: center;
            padding: 3rem;
            color: var(--gray);
            font-size: 1.1rem;
            grid-column: 1 / -1;
        }

        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .filter-grid {
                grid-template-columns: 1fr;
            }

            .page-header h1 {
                font-size: 2rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="page-header">
            <h1>Premium Equipments</h1>
            <p>Find the perfect Equipment for your needs</p>
        </div>

        <div class="filters">
            <form action="" method="GET" class="filter-grid">
                <div class="filter-group">
                    <label for="category">Categories</label>
                    <select name="category" id="category" class="filter-input">
                        <option value="">All Categories</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo ($category_id == $category['id']) ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="search">Search</label>
                    <input type="text" id="search" name="search" class="filter-input" 
                           placeholder="Search Equipments..." value="<?php echo $search; ?>">
                </div>

                <div class="filter-group">
                    <label for="sort">Sort By</label>
                    <select name="sort" id="sort" class="filter-input">
                        <option value="default" <?php echo ($sort == 'default') ? 'selected' : ''; ?>>Latest</option>
                        <option value="price_low" <?php echo ($sort == 'price_low') ? 'selected' : ''; ?>>Price: Low to High</option>
                        <option value="price_high" <?php echo ($sort == 'price_high') ? 'selected' : ''; ?>>Price: High to Low</option>
                    </select>
                </div>

                <button type="submit" class="filter-button">Apply Filters</button>
            </form>
        </div>

        <div class="products-grid">
            <?php if (empty($products)): ?>
                <div class="no-products">
                    <p>No products found matching your criteria.</p>
                </div>
            <?php else: ?>
                <?php foreach ($products as $product): ?>
                    <div class="product-card">
                        <div class="product-image">
                            <img src="assets/uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                 alt="<?php echo htmlspecialchars($product['name']); ?>"
                                 onerror="this.src='assets/images/default-placeholder.png'">
                           
                        </div>
                        <div class="product-content">
                            <div class="product-category">
                                <?php echo htmlspecialchars($product['category_name']); ?>
                            </div>
                            <h3 class="product-title">
                                <?php echo htmlspecialchars($product['name']); ?>
                            </h3>
                            
                            <?php if (!isset($product['show_price']) || $product['show_price'] == 1): ?>
                                <div class="product-price">
                                    Rs. <?php echo number_format($product['price'], 2); ?>
                                </div>
                            <?php else: ?>
                                <div class="price-inquiry">
                                    <i class="fas fa-info-circle"></i> Contact for Price
                                </div>
                            <?php endif; ?>
                            
                            <div class="product-actions">
                                <?php if (!isset($product['show_price']) || $product['show_price'] == 1): ?>
                                    <button type="button" class="add-to-cart-btn btn btn-primary" data-product-id="<?php echo $product['id']; ?>">
                                        Add to Cart
                                    </button>
                                <?php else: ?>
                                    <a href="contact_us?product=<?php echo $product['id']; ?>" class="btn btn-primary">
                                        Inquire Now
                                    </a>
                                <?php endif; ?>
                                <a href="product-details?id=<?php echo $product['id']; ?>" class="btn btn-outline">
                                    View Details
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <?php require_once 'includes/footer.php'; ?>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            // Function to handle Add to Cart
            const addToCart = (productId) => {
                fetch('ajax/add_to_cart.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `product_id=${productId}&quantity=1`, // Default quantity is 1
                })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Added to Cart',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false,
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message,
                            });
                        }
                    })
                    .catch(err => {
                        console.error('Error:', err);
                    });
            };

            // Add to Cart buttons
            document.querySelectorAll('.add-to-cart-btn').forEach(button => {
                button.addEventListener('click', function () {
                    const productId = this.getAttribute('data-product-id');
                    addToCart(productId);
                });
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>