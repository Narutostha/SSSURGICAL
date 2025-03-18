<?php
// Move all PHP processing to the top
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Initialize $action
$action = isset($_GET['action']) ? $_GET['action'] : 'list';
$product_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Handle form submission for adding or editing products
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Your existing POST handling code here
    $name = trim($_POST['name']);
    $description = trim($_POST['description']);
    $price = floatval($_POST['price']);
    $stock = intval($_POST['stock']);
    $category_id = intval($_POST['category_id']);
    // Add show_price field (will be 1 if checked, otherwise 0)
    $show_price = isset($_POST['show_price']) ? 1 : 0;
    

    // Image handling
    $image_name = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        // Your existing image handling code
        $upload_dir = __DIR__ . '/../assets/uploads/';
        if (!file_exists($upload_dir)) {
            mkdir($upload_dir, 0777, true);
        }
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_file = $upload_dir . $image_name;
        move_uploaded_file($_FILES['image']['tmp_name'], $target_file);
    }

    if ($action === 'add') {
        // Your existing add code - add show_price field
        $stmt = $pdo->prepare("INSERT INTO products (name, description, price, stock, category_id, image, show_price, created_at) 
            VALUES (:name, :description, :price, :stock, :category_id, :image, :show_price, NOW())");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':category_id' => $category_id,
            ':image' => $image_name,
            ':show_price' => $show_price
        ]);
        header('Location: products.php');
        exit;
    } elseif ($action === 'edit' && $product_id) {
        // Your existing edit code - update to include show_price
        $stmt = $pdo->prepare("UPDATE products SET name = :name, description = :description, price = :price, stock = :stock, 
            category_id = :category_id, show_price = :show_price,
            image = CASE WHEN :image = '' THEN image ELSE :image END WHERE id = :id");
        $stmt->execute([
            ':name' => $name,
            ':description' => $description,
            ':price' => $price,
            ':stock' => $stock,
            ':category_id' => $category_id,
            ':image' => $image_name,
            ':show_price' => $show_price,
            ':id' => $product_id
        ]);
        header('Location: products.php');
        exit;
    }
}
// Handle product deletion
if ($action === 'delete' && $product_id) {
    try {
        // Start transaction
        $pdo->beginTransaction();

        // First delete from cart table to remove all references
        $stmt = $pdo->prepare("DELETE FROM cart WHERE product_id = :id");
        $stmt->execute([':id' => $product_id]);

        // Then delete from order_items if exists
        $stmt = $pdo->prepare("DELETE FROM order_items WHERE product_id = :id");
        $stmt->execute([':id' => $product_id]);

        // Finally delete the product
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = :id");
        $stmt->execute([':id' => $product_id]);

        // If everything went fine, commit the transaction
        $pdo->commit();

        header('Location: products.php');
        exit;
    } catch (PDOException $e) {
        // Something went wrong, rollback
        $pdo->rollBack();
        echo "Error: " . $e->getMessage();
    }
}
// Fetch product details for editing
$product = [];
if ($action === 'edit' && $product_id) {
    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = :id");
    $stmt->execute([':id' => $product_id]);
    $product = $stmt->fetch();
}

// Fetch all products and categories
$products = $pdo->query("SELECT p.*, c.name AS category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id")->fetchAll();
$categories = $pdo->query("SELECT * FROM categories")->fetchAll();

// Now include the header
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Product Management</title>
    <style>
        :root {
    --primary: #2563eb;
    --primary-dark: #1d4ed8;
    --success: #059669;
    --danger: #dc2626;
    --warning: #d97706;
    --text-dark: #1e293b;
    --text-light: #64748b;
    --bg-light: #f8fafc;
    --bg-white: #ffffff;
    --border-light: #e2e8f0;
    --shadow-sm: 0 1px 3px rgba(0, 0, 0, 0.1);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
    --radius-sm: 0.375rem;
    --radius-md: 0.5rem;
    --radius-lg: 0.75rem;
}

/* Reset & Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', system-ui, -apple-system, sans-serif;
    background-color: var(--bg-light);
    color: var(--text-dark);
    line-height: 1.5;
}

.main-content {
    max-width: 1400px;
    margin: 0 auto;
    padding: 2rem;
}

/* Page Header */
.page-header {
    background: linear-gradient(135deg, var(--primary), var(--primary-dark));
    padding: 2rem;
    border-radius: var(--radius-lg);
    margin-bottom: 2rem;
    color: var(--bg-white);
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.page-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -50%;
    width: 100%;
    height: 200%;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1));
    transform: rotate(45deg);
    pointer-events: none;
}

.page-header h1 {
    font-size: 1.875rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    position: relative;
    z-index: 1;
}

/* Table Styles */
.table-container {
    background: var(--bg-white);
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    overflow: hidden;
    margin-top: 1.5rem;
}

.table {
    width: 100%;
    border-collapse: collapse;
}

.table th {
    background: var(--bg-light);
    padding: 1rem 1.5rem;
    text-align: left;
    font-weight: 600;
    color: var(--text-light);
    border-bottom: 2px solid var(--border-light);
    white-space: nowrap;
}

.table td {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border-light);
    vertical-align: middle;
}

.table tbody tr {
    transition: all 0.3s ease;
}

.table tbody tr:hover {
    background: var(--bg-light);
    transform: translateX(4px);
}

/* Product Image */
.product-image {
    width: 64px;
    height: 64px;
    border-radius: var(--radius-md);
    object-fit: cover;
    box-shadow: var(--shadow-sm);
    transition: transform 0.3s ease;
}

.product-image:hover {
    transform: scale(1.1);
}

/* Buttons & Actions */
.action-button {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--primary);
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: var(--radius-md);
    text-decoration: none;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: var(--shadow-md);
}

.action-button:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
}

.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.75rem 1rem;
    border-radius: 8px;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s ease;
    gap: 0.5rem;
}

.btn-icon {
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: var(--radius-md);
    color: white;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
}


.btn-icon:hover {
    transform: scale(1.1);
    box-shadow: var(--shadow-sm);
}

/* Form Styles */
.form-container {
    background: var(--bg-white);
    padding: 2rem;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    max-width: 800px;
    margin: 2rem auto;
}

.form-title {
    font-size: 1.5rem;
    color: var(--text-dark);
    margin-bottom: 2rem;
    padding-bottom: 1rem;
    border-bottom: 2px solid var(--border-light);
    position: relative;
}

.form-title::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 0;
    width: 100px;
    height: 2px;
    background: var(--primary);
}

.form-group {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.form-label {
    display: block;
    margin-bottom: 0.5rem;
    color: var(--text-light);
    font-weight: 500;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 2px solid var(--border-light);
    border-radius: var(--radius-md);
    font-size: 1rem;
    transition: all 0.3s ease;
    background: var(--bg-light);
}

.form-control:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
    background: var(--bg-white);
}

.form-control::placeholder {
    color: var(--text-light);
}
.mb-4 {
    margin-bottom: 1.5rem;
}
.btn-add {
    background-color: #6366f1;
    color: white;
    border: none;
    cursor: pointer;
}

.btn-add:hover {
    background-color: #4f46e5;
    transform: translateY(-1px);
}
/* Active state */
.btn-add:active {
    transform: translateY(1px);
    box-shadow: 0 2px 4px -1px rgba(79, 70, 229, 0.2);
}

/* Icon styles */
.btn-add i {
    font-size: 1rem;
    transition: transform 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

.btn-add:hover i {
    transform: rotate(90deg);
}

/* Shine effect on hover */
.btn-add::before {
    content: '';
    position: absolute;
    top: 0;
    left: -75%;
    width: 50%;
    height: 100%;
    background: linear-gradient(
        to right,
        transparent,
        rgba(255, 255, 255, 0.3),
        transparent
    );
    transform: skewX(-25deg);
    transition: 0.75s;
}

.btn-add:hover::before {
    left: 125%;
}

/* Ripple effect on click */
.btn-add::after {
    content: '';
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at center, 
                               rgba(255, 255, 255, 0.3) 0%,
                               transparent 50%);
    opacity: 0;
    transition: opacity 0.5s;
    pointer-events: none;
}

.btn-add:active::after {
    opacity: 1;
}

/* Media query for responsive design */
@media (max-width: 768px) {
    .btn-add {
        padding: 0.75rem 1.25rem;
        font-size: 0.9rem;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .btn-add {
        background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
        box-shadow: 0 4px 6px -1px rgba(99, 102, 241, 0.2),
                   0 2px 4px -2px rgba(99, 102, 241, 0.1);
    }

    .btn-add:hover {
        box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.3),
                   0 4px 6px -4px rgba(99, 102, 241, 0.2);
    }
}

/* Status Badges */
.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.875rem;
    font-weight: 500;
    display: inline-flex;
    align-items: center;
    gap: 0.375rem;
}

.status-high {
    background: rgba(5, 150, 105, 0.1);
    color: var(--success);
}

.status-medium {
    background: rgba(217, 119, 6, 0.1);
    color: var(--warning);
}

.status-low {
    background: rgba(220, 38, 38, 0.1);
    color: var(--danger);
}

/* Price Display */
.price {
    font-weight: 600;
    color: var(--success);
}

/* File Upload */
.file-input-wrapper {
    position: relative;
}

.file-input-label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    padding: 0.75rem 1rem;
    background: var(--bg-light);
    border-radius: var(--radius-md);
    cursor: pointer;
    transition: all 0.3s ease;
}

.file-input-label:hover {
    background: var(--border-light);
}

.file-input {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    border: 0;
}

.btn-secondary {
    background-color: #f3f4f6;
    color: #374151;
    border: 1px solid #e5e7eb;
}

.btn-secondary:hover {
    background-color: #e5e7eb;
    transform: translateY(-1px);
}

.btn-secondary:active {
    transform: translateY(0);
    box-shadow: none;
    
}

.w-100 {
    width: 50%;
    align-items: center;
    justify-content: center;
}
.mt-4 {
    margin-top: 1rem;
}

.mt-2 {
    margin-top: 0.5rem;
    
}

.me-2 {
    margin-right: 0.5rem;
}

/* Image Preview */
.image-preview {
    max-width: 200px;
    border-radius: var(--radius-md);
    margin-top: 1rem;
    box-shadow: var(--shadow-md);
}

/* Checkbox styling */
.form-check {
    display: flex;
    align-items: center;
    margin-bottom: 1rem;
}

.form-check-input {
    margin-right: 0.5rem;
    width: 18px;
    height: 18px;
    cursor: pointer;
}

.form-check-label {
    font-weight: 500;
    cursor: pointer;
}

/* Hidden price styling */
.price-hidden {
    color: var(--text-light);
    font-style: italic;
}

/* Responsive Design */
@media (max-width: 768px) {
    .main-content {
        padding: 1rem;
    }

    .page-header {
        padding: 1.5rem;
    }

    .table-container {
        overflow-x: auto;
    }

    .form-container {
        margin: 1rem;
        padding: 1.5rem;
    }

    .action-buttons {
        flex-direction: column;
    }

    .btn-icon {
        width: 100%;
    }
}

/* Animations */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.animate-fade-in {
    animation: fadeIn 0.3s ease-out forwards;
}

/* Loading States */
.loading {
    opacity: 0.7;
    pointer-events: none;
    position: relative;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 1.5rem;
    height: 1.5rem;
    border: 2px solid var(--border-light);
    border-top-color: var(--primary);
    border-radius: 50%;
    animation: spin 0.8s linear infinite;
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}
    </style>
</head>
<body>

<div class="main-content">
   

    <?php if ($action === 'list'): ?>
        <div class="mb-4">
            <a href="products.php?action=add" class="btn btn-add">
                <i class="fas fa-plus me-2"></i>Add New Product
            </a>
        </div>
        
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Price</th>
                        <th>Stock</th>
                        <th>Price Visible</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?php echo $product['id']; ?></td>
                        <td>
                            <img src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                                alt="Product" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                        </td>
                        <td><?php echo htmlspecialchars($product['name']); ?></td>
                        <td><?php echo htmlspecialchars($product['category_name']); ?></td>
                        <td class="<?php echo (isset($product['show_price']) && $product['show_price'] == 0) ? 'price-hidden' : 'price'; ?>">
                            <?php if (isset($product['show_price']) && $product['show_price'] == 0): ?>
                                <span><i class="fas fa-eye-slash"></i> Hidden</span>
                            <?php else: ?>
                                Rs. <?php echo number_format($product['price'], 2); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo $product['stock']; ?></td>
                        <td>
                            <?php if (isset($product['show_price']) && $product['show_price'] == 1): ?>
                                <span class="status-badge status-high"><i class="fas fa-check"></i> Yes</span>
                            <?php else: ?>
                                <span class="status-badge status-low"><i class="fas fa-times"></i> No</span>
                            <?php endif; ?>
                        </td>
                        <td class="action-buttons">
                            <a href="products.php?action=edit&id=<?php echo $product['id']; ?>" 
                               class="btn btn-edit">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="products.php?action=delete&id=<?php echo $product['id']; ?>" 
                               class="btn btn-delete"
                               onclick="return confirm('Are you sure you want to delete this product?');">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php elseif ($action === 'add' || $action === 'edit'): ?>
        <div class="form-container">
            <h2 class="mb-4"><?php echo $action === 'add' ? 'Add New Product' : 'Edit Product'; ?></h2>
            
            <form action="products.php?action=<?php echo $action; ?><?php echo $action === 'edit' ? '&id=' . $product_id : ''; ?>" 
                  method="POST" 
                  enctype="multipart/form-data">
                <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" name="name" 
                           value="<?php echo htmlspecialchars($product['name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Description</label>
                    <textarea class="form-control" name="description" rows="4" required><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Price</label>
                            <input type="number" step="0.01" class="form-control" name="price" 
                                   value="<?php echo htmlspecialchars($product['price'] ?? ''); ?>" required>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="stock" 
                                   value="<?php echo htmlspecialchars($product['stock'] ?? ''); ?>" required>
                        </div>
                    </div>
                </div>
                
                <!-- Add checkbox for showing/hiding price -->
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" id="showPrice" name="show_price" 
                           <?php echo (isset($product['show_price']) && $product['show_price'] == 1) ? 'checked' : ''; ?>>
                    <label class="form-check-label" for="showPrice">
                        Show Price to Customers
                    </label>
                </div>

                <div class="form-group">
                    <label class="form-label">Category</label>
                    <select class="form-control" name="category_id" required>
                        <option value="">Select Category</option>
                        <?php foreach ($categories as $category): ?>
                            <option value="<?php echo $category['id']; ?>" 
                                    <?php echo isset($product['category_id']) && $product['category_id'] == $category['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($category['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Product Image</label>
                    <input type="file" class="form-control" name="image" accept="image/*">
                    <?php if ($action === 'edit' && !empty($product['image'])): ?>
                        <img src="../assets/uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                             alt="Current Product Image" class="preview-image mt-2">
                    <?php endif; ?>
                </div>

                <div class="form-group mt-4">
                    <button type="submit" class="btn btn-add w-100">
                        <i class="fas fa-save me-2"></i>
                        <?php echo $action === 'add' ? 'Add Product' : 'Update Product'; ?>
                    </button>
                    <a href="products.php" class="btn btn-secondary w-100 mt-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                </div>
            </form>
        </div>
    <?php endif; ?>
</div>

<script>
    // Preview image before upload
    document.querySelector('input[type="file"]')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = document.createElement('img');
                img.src = event.target.result;
                img.className = 'preview-image mt-2';
                
                // Remove existing preview
                const existingPreview = document.querySelector('.preview-image');
                if (existingPreview) {
                    existingPreview.remove();
                }
                
                // Add new preview
                e.target.parentNode.appendChild(img);
            }
            reader.readAsDataURL(file);
        }
    });

    // Animation for table rows
    document.querySelectorAll('tbody tr').forEach(row => {
        row.style.opacity = '0';
        row.style.transform = 'translateY(20px)';
    });

    // Animate rows on page load
    window.addEventListener('load', () => {
        document.querySelectorAll('tbody tr').forEach((row, index) => {
            setTimeout(() => {
                row.style.transition = 'all 0.3s ease';
                row.style.opacity = '1';
                row.style.transform = 'translateY(0)';
            }, index * 100);
        });
    });

    // Enhance form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const requiredFields = form.querySelectorAll('[required]');
            let isValid = true;

            requiredFields.forEach(field => {
                if (!field.value.trim()) {
                    isValid = false;
                    field.classList.add('is-invalid');
                    
                    // Create error message if doesn't exist
                    if (!field.nextElementSibling?.classList.contains('invalid-feedback')) {
                        const feedback = document.createElement('div');
                        feedback.className = 'invalid-feedback';
                        feedback.textContent = 'This field is required';
                        field.parentNode.appendChild(feedback);
                    }
                } else {
                    field.classList.remove('is-invalid');
                }
            });

            if (!isValid) {
                e.preventDefault();
            }
        });
    }
</script>

<!-- Add this right before closing body tag -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/js/all.min.js"></script>
</body>
</html>

<?php require_once 'includes/footer.php'; ?>