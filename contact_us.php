<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'config/database.php';

$page_title = "Contact Us";
include 'includes/header.php';

// Check if this is a product inquiry
$product_id = isset($_GET['product']) ? (int)$_GET['product'] : 0;
$product = null;

// If product ID is provided, fetch product details
if ($product_id) {
    try {
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p 
                               LEFT JOIN categories c ON p.category_id = c.id 
                               WHERE p.id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch(PDO::FETCH_ASSOC);
    } catch(PDOException $e) {
        error_log("Error fetching product: " . $e->getMessage());
    }
}

// Fetch contact settings
try {
    $stmt = $pdo->query("SELECT * FROM contact_settings ORDER BY id DESC LIMIT 1");
    $contact_settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    error_log("Error fetching contact settings: " . $e->getMessage());
    $contact_settings = [];
}

// Get any form errors
$formErrors = $_SESSION['formErrors'] ?? [];
$formData = $_SESSION['formData'] ?? [];

// Clear the session data
unset($_SESSION['formErrors']);
unset($_SESSION['formData']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        .contact-page {
            padding: 2rem 5%;
            max-width: 1200px;
            margin: 20px auto;
        }

        .contact-info {
            text-align: center;
            padding: 3rem 5%;
        }

        .contact-info h2 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #1E3A8A;
        }

        .info-grid {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .info-item {
            display: flex;
            align-items: center;
            gap: 1rem;
            background: linear-gradient(135deg, #4F46E5, #3B82F6);
            color: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 320px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .info-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 14px rgba(0, 0, 0, 0.15);
        }

        .icon-wrapper {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.2);
        }

        .icon-wrapper i {
            font-size: 24px;
            color: white;
        }

        .contact-form {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            text-align: center;
            margin-top: 2rem;
        }

        .form-group {
            margin-bottom: 1.5rem;
            text-align: left;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: #374151;
        }

        input,
        textarea {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #D1D5DB;
            border-radius: 6px;
        }

        .btn-primary {
            background: #2563EB;
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 6px;
            font-weight: 500;
            cursor: pointer;
        }

        .btn-primary:hover {
            background: #1D4ED8;
        }

        .success-message, .error-message {
            padding: 1rem;
            margin-bottom: 1rem;
            border-radius: 6px;
            text-align: center;
        }

        .success-message {
            background: #D1FAE5;
            color: #065F46;
        }

        .error-message {
            background: #FECACA;
            color: #B91C1C;
        }

        .error {
            color: #DC2626;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }

        /* Product inquiry box styles */
        .product-inquiry-box {
            background: #EFF6FF;
            border: 1px solid #BFDBFE;
            border-radius: 8px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        .product-inquiry-image {
            width: 100px;
            height: 100px;
            object-fit: contain;
            border-radius: 6px;
            background: white;
            padding: 5px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }

        .product-inquiry-details {
            flex: 1;
        }

        .product-inquiry-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #1E3A8A;
            margin-bottom: 0.5rem;
        }

        .product-inquiry-category {
            font-size: 0.9rem;
            color: #6B7280;
            margin-bottom: 0.5rem;
        }

        .product-inquiry-message {
            font-style: italic;
            color: #4B5563;
            margin-top: 0.5rem;
        }

        @media (max-width: 768px) {
            .info-grid {
                flex-direction: column;
                align-items: center;
            }
            
            .product-inquiry-box {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .product-inquiry-image {
                margin-bottom: 1rem;
            }
        }
    </style>
</head>
<body>

<main class="contact-page">
    <section class="contact-info">
        <h2>Get in Touch</h2>
        <div class="info-grid">
            <div class="info-item">
                <div class="icon-wrapper">
                    <i class="fa-solid fa-map-marker-alt"></i>
                </div>
                <p><?php echo htmlspecialchars($contact_settings['address'] ?? 'Dhangadhi, Kailali'); ?></p>
            </div>
            <div class="info-item">
                <div class="icon-wrapper">
                    <i class="fa-solid fa-phone"></i>
                </div>
                <p><?php echo htmlspecialchars($contact_settings['phone'] ?? '091-5523539'); ?></p>
            </div>
        </div>
    </section>

    <section class="contact-form">
        <?php if ($product): ?>
            <h2>Product Inquiry</h2>
            <div class="product-inquiry-box">
                <img src="assets/uploads/<?php echo htmlspecialchars($product['image']); ?>" 
                     alt="<?php echo htmlspecialchars($product['name']); ?>"
                     class="product-inquiry-image">
                <div class="product-inquiry-details">
                    <div class="product-inquiry-title"><?php echo htmlspecialchars($product['name']); ?></div>
                    <div class="product-inquiry-category">Category: <?php echo htmlspecialchars($product['category_name']); ?></div>
                    <div class="product-inquiry-message">I would like to inquire about pricing and availability for this product.</div>
                </div>
            </div>
        <?php else: ?>
            <h2>Send us a Message</h2>
        <?php endif; ?>

        <?php if (isset($_SESSION['successMessage'])): ?>
            <div class="success-message">
                <?php 
                    echo $_SESSION['successMessage'];
                    unset($_SESSION['successMessage']);
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_SESSION['errorMessage'])): ?>
            <div class="error-message">
                <?php 
                    echo $_SESSION['errorMessage'];
                    unset($_SESSION['errorMessage']);
                ?>
            </div>
        <?php endif; ?>

        <form method="post" action="process_contact.php">
            <?php if ($product): ?>
                <input type="hidden" name="inquiry_type" value="product">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
            <?php endif; ?>
            
            <div class="form-group">
                <label>Name:</label>
                <input type="text" name="name" value="<?php echo htmlspecialchars($formData['name'] ?? ''); ?>" required>
                <?php if (isset($formErrors['name'])): ?>
                    <span class="error"><?php echo htmlspecialchars($formErrors['name']); ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Email:</label>
                <input type="email" name="email" value="<?php echo htmlspecialchars($formData['email'] ?? ''); ?>" required>
                <?php if (isset($formErrors['email'])): ?>
                    <span class="error"><?php echo htmlspecialchars($formErrors['email']); ?></span>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label>Phone (Optional):</label>
                <input type="tel" name="phone" value="<?php echo htmlspecialchars($formData['phone'] ?? ''); ?>">
            </div>
            <div class="form-group">
                <label>Message:</label>
                <textarea name="message" rows="4" required><?php echo htmlspecialchars($formData['message'] ?? ($product ? 'I am interested in getting more information about ' . $product['name'] . '. Please provide pricing details and availability.' : '')); ?></textarea>
                <?php if (isset($formErrors['message'])): ?>
                    <span class="error"><?php echo htmlspecialchars($formErrors['message']); ?></span>
                <?php endif; ?>
            </div>
            <button type="submit" class="btn-primary">
                <?php echo $product ? 'Send Inquiry' : 'Send Message'; ?>
            </button>
        </form>
    </section>
</main>

</body>
</html>

<?php include 'includes/footer.php'; ?>