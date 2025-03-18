<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        foreach ($_POST['platforms'] as $id => $platform) {
            $stmt = $pdo->prepare("UPDATE social_media_platforms SET 
                url = ?,
                is_active = ?,
                display_order = ?
                WHERE id = ?");
            
            $stmt->execute([
                $platform['url'],
                isset($platform['is_active']) ? 1 : 0,
                $platform['display_order'],
                $id
            ]);
        }
        $_SESSION['success'] = "Social media settings updated successfully!";
    } catch(PDOException $e) {
        $_SESSION['error'] = "Error updating settings: " . $e->getMessage();
    }
    header('Location: social_media.php');
    exit();
}

// Fetch all platforms
try {
    $stmt = $pdo->query("SELECT * FROM social_media_platforms ORDER BY display_order ASC");
    $platforms = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching platforms: " . $e->getMessage();
    $platforms = [];
}

require_once 'includes/header.php';
?>

<!-- Add this style section after your existing styles -->
<style>
.card {
    background: #ffffff;
    border-radius: 15px;
    border: none;
    box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
    transition: all 0.3s ease;
    overflow: hidden;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 30px rgba(37, 99, 235, 0.1);
}

.card-header {
    background: linear-gradient(45deg, #f8fafc, #fff);
    border-bottom: 1px solid rgba(37, 99, 235, 0.1);
    padding: 1.25rem;
}

.card-header h3 {
    color: #1e293b;
    font-size: 1.5rem;
    font-weight: 600;
    margin: 0;
}

.card-body {
    padding: 1.5rem;
}

.social-card {
    position: relative;
    overflow: hidden;
}

.social-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: linear-gradient(90deg, #3b82f6, #60a5fa);
    transform: scaleX(0);
    transform-origin: left;
    transition: transform 0.3s ease;
}

.social-card:hover::before {
    transform: scaleX(1);
}

.form-control {
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem;
    transition: all 0.3s ease;
    font-size: 0.95rem;
}

.form-control:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
    outline: none;
}

.form-label {
    color: #475569;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

/* Enhanced Switch Styling */
.form-check-input {
    width: 3rem;
    height: 1.5rem;
    background-color: #e2e8f0;
    border: none;
    border-radius: 2rem;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
}

.form-check-input:checked {
    background-color: #3b82f6;
}

.form-check-input:focus {
    box-shadow: none;
}

/* Custom Button Styling */
.btn-primary {
    background: linear-gradient(45deg, #3b82f6, #60a5fa);
    border: none;
    border-radius: 8px;
    padding: 0.75rem 1.5rem;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(59, 130, 246, 0.3);
}

.btn-primary::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: -100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: all 0.5s ease;
}

.btn-primary:hover::after {
    left: 100%;
}

/* Alert Styling */
.alert {
    border-radius: 10px;
    padding: 1rem;
    margin-bottom: 1.5rem;
    border: none;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.alert-success {
    background: linear-gradient(45deg, #dcfce7, #f0fdf4);
    color: #166534;
}

.alert-danger {
    background: linear-gradient(45deg, #fee2e2, #fef2f2);
    color: #991b1b;
}

/* Social Media Icon Container */
.social-icon {
    width: 40px;
    height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(45deg, #f8fafc, #fff);
    border-radius: 10px;
    margin-right: 1rem;
    box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
}

/* Platform Name */
.platform-name {
    font-size: 1.1rem;
    color: #1e293b;
    font-weight: 600;
}

/* Grid Layout */
.row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 1.5rem;
    margin: 0;
    padding: 1rem;
}

/* Responsive Adjustments */
@media (max-width: 768px) {
    .card-header {
        padding: 1rem;
    }
    
    .card-body {
        padding: 1rem;
    }
    
    .btn-primary {
        width: 100%;
    }
}

/* Animation for Cards */
@keyframes fadeInUp {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.social-card {
    animation: fadeInUp 0.5s ease forwards;
}

.social-card:nth-child(2) {
    animation-delay: 0.1s;
}

.social-card:nth-child(3) {
    animation-delay: 0.2s;
}
</style>


<div class="container-fluid">
    <div class="card">
        <div class="card-header">
            <h3>Social Media Management</h3>
        </div>
        <div class="card-body">
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="row">
                    <?php foreach ($platforms as $platform): ?>
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card">
                                <div class="card-header d-flex justify-content-between align-items-center">
                                    <div class="d-flex align-items-center">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="currentColor" class="me-2">
                                            <?php echo $platform['icon_svg']; ?>
                                        </svg>
                                        <h5 class="mb-0"><?php echo htmlspecialchars($platform['name']); ?></h5>
                                    </div>
                                    <div class="form-check form-switch">
                                        <input class="form-check-input" type="checkbox" 
                                               name="platforms[<?php echo $platform['id']; ?>][is_active]" 
                                               <?php echo $platform['is_active'] ? 'checked' : ''; ?>>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <label class="form-label">URL</label>
                                        <input type="url" class="form-control" 
                                               name="platforms[<?php echo $platform['id']; ?>][url]"
                                               value="<?php echo htmlspecialchars($platform['url'] ?? ''); ?>">
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Display Order</label>
                                        <input type="number" class="form-control" 
                                               name="platforms[<?php echo $platform['id']; ?>][display_order]"
                                               value="<?php echo htmlspecialchars($platform['display_order']); ?>"
                                               min="1">
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'includes/footer.php'; ?>