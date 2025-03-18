<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit;
}

require_once '../config/database.php';

// Create uploads directory if it doesn't exist
$upload_dir = '../assets/uploads/categories/';
if (!file_exists($upload_dir)) {
    mkdir($upload_dir, 0755, true);
} elseif (!is_writable($upload_dir)) {
    error_log("Upload directory is not writable: " . $upload_dir);
    throw new Exception("Server configuration error. Please contact administrator.");
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['action'])) {
        try {
            switch ($_POST['action']) {
                case 'create':
                    $name = trim($_POST['name']);
                    $description = trim($_POST['description']);
                    
                    // Handle image upload
                    $image_name = null;
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        $filename = $_FILES['image']['name'];
                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        
                        if (!in_array($ext, $allowed)) {
                            throw new Exception("Invalid file type. Allowed types: " . implode(', ', $allowed));
                        }
                        
                        // Check file size (2MB max)
                        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                            throw new Exception("File size too large. Maximum size is 2MB.");
                        }
                        
                        $image_name = uniqid() . '.' . $ext;
                        $upload_path = $upload_dir . $image_name;
                        
                        if (!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                            throw new Exception("Failed to upload image");
                        }
                    }
                    
                    $stmt = $pdo->prepare("INSERT INTO categories (name, description, image) VALUES (?, ?, ?)");
                    if ($stmt->execute([$name, $description, $image_name])) {
                        $_SESSION['success'] = "Category created successfully!";
                    }
                    break;

                case 'update':
                    $id = $_POST['id'];
                    $name = trim($_POST['name']);
                    $description = trim($_POST['description']);
                    
                    // Handle image upload for update
                    $image_update = "";
                    if (isset($_FILES['image']) && $_FILES['image']['error'] === 0) {
                        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                        $filename = $_FILES['image']['name'];
                        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                        
                        if (!in_array($ext, $allowed)) {
                            throw new Exception("Invalid file type. Allowed types: " . implode(', ', $allowed));
                        }
                        
                        if ($_FILES['image']['size'] > 2 * 1024 * 1024) {
                            throw new Exception("File size too large. Maximum size is 2MB.");
                        }
                        
                        // Delete old image if exists
                        $stmt = $pdo->prepare("SELECT image FROM categories WHERE id = ?");
                        $stmt->execute([$id]);
                        $old_image = $stmt->fetchColumn();
                        
                        if ($old_image && file_exists($upload_dir . $old_image)) {
                            unlink($upload_dir . $old_image);
                        }
                        
                        $image_name = uniqid() . '.' . $ext;
                        $upload_path = $upload_dir . $image_name;
                        
                        if (move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                            $image_update = ", image = ?";
                        }
                    }
                    
                    $sql = "UPDATE categories SET name = ?, description = ?" . $image_update . " WHERE id = ?";
                    $params = [$name, $description];
                    if ($image_update) {
                        $params[] = $image_name;
                    }
                    $params[] = $id;
                    
                    $stmt = $pdo->prepare($sql);
                    if ($stmt->execute($params)) {
                        $_SESSION['success'] = "Category updated successfully!";
                    }
                    break;

                case 'delete':
                    $id = $_POST['id'];
                    
                    // Delete associated image first
                    $stmt = $pdo->prepare("SELECT image FROM categories WHERE id = ?");
                    $stmt->execute([$id]);
                    $image = $stmt->fetchColumn();
                    
                    if ($image && file_exists($upload_dir . $image)) {
                        unlink($upload_dir . $image);
                    }
                    
                    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
                    if ($stmt->execute([$id])) {
                        $_SESSION['success'] = "Category deleted successfully!";
                    }
                    break;

                case 'remove_image':
                    $id = $_POST['id'];
                    
                    // Delete the image file
                    $stmt = $pdo->prepare("SELECT image FROM categories WHERE id = ?");
                    $stmt->execute([$id]);
                    $image = $stmt->fetchColumn();
                    
                    if ($image && file_exists($upload_dir . $image)) {
                        unlink($upload_dir . $image);
                    }
                    
                    // Update database to remove image reference
                    $stmt = $pdo->prepare("UPDATE categories SET image = NULL WHERE id = ?");
                    if ($stmt->execute([$id])) {
                        $_SESSION['success'] = "Image removed successfully!";
                    }
                    break;
            }
        } catch(Exception $e) {
            $_SESSION['error'] = "Error: " . $e->getMessage();
        }
        header('Location: categories.php');
        exit();
    }
}

// Include header
require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Category Management</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        :root {
            --primary: #4f46e5;
            --danger: #dc2626;
            --success: #059669;
            --bg-light: #f9fafb;
            --bg-white: #ffffff;
            --text-dark: #1f2937;
            --text-gray: #6b7280;
            --border-gray: #e5e7eb;
            --shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-light);
            color: var(--text-dark);
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 2rem;
        }

        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }

        .title {
            font-size: 1.5rem;
            font-weight: 600;
        }

        .btn {
            padding: 0.5rem 1rem;
            border-radius: 0.375rem;
            border: none;
            cursor: pointer;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background-color: var(--primary);
            color: white;
        }

        .btn-primary:hover {
            opacity: 0.9;
        }

        .btn-danger {
            background-color: var(--danger);
            color: white;
        }

        .table-container {
            background: var(--bg-white);
            border-radius: 0.5rem;
            box-shadow: var(--shadow);
            overflow: hidden;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 1rem;
            text-align: left;
            border-bottom: 1px solid var(--border-gray);
        }

        th {
            background-color: var(--bg-light);
            font-weight: 600;
            color: var(--text-gray);
        }

        tr:hover {
            background-color: var(--bg-light);
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background: var(--bg-white);
            padding: 2rem;
            border-radius: 0.5rem;
            width: 100%;
            max-width: 500px;
            position: relative;
            max-height: 90vh;
            overflow-y: auto;
        }

        .close {
            position: absolute;
            top: 1rem;
            right: 1rem;
            cursor: pointer;
            font-size: 1.5rem;
        }

        .form-group {
            margin-bottom: 1rem;
        }

        .form-label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 0.5rem;
            border: 1px solid var(--border-gray);
            border-radius: 0.375rem;
        }

        .alert {
            padding: 1rem;
            border-radius: 0.375rem;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d1fae5;
            color: var(--success);
        }

        .alert-danger {
            background-color: #fee2e2;
            color: var(--danger);
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-edit, .btn-delete {
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .btn-edit {
            background-color: #eab308;
            color: white;
        }

        .btn-delete {
            background-color: var(--danger);
            color: white;
        }

        .category-image {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid var(--border-gray);
        }

        .image-preview-container {
            position: relative;
            display: inline-block;
            margin-bottom: 1rem;
        }

        .current-image {
            max-width: 200px;
            max-height: 150px;
            border-radius: 8px;
            border: 1px solid var(--border-gray);
            object-fit: cover;
        }

        .remove-image {
            position: absolute;
            top: -10px;
            right: -10px;
            background: var(--danger);
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            font-size: 16px;
        }

        .image-upload-preview {
            max-width: 200px;
            max-height: 150px;
            display: none;
            margin-top: 10px;
            border-radius: 8px;
            border: 1px solid var(--border-gray);
            object-fit: cover;
        }

        .file-input-wrapper {
            position: relative;
            overflow: hidden;
            display: inline-block;
            margin: 10px 0;
        }

        .file-input-wrapper input[type=file] {
            font-size: 100px;
            position: absolute;
            left: 0;
            top: 0;
            opacity: 0;
            cursor: pointer;
        }

        .file-input-button {
            background: var(--primary);
            color: white;
            padding: 8px 16px;
            border-radius: 4px;
            cursor: pointer;
            display: inline-block;
        }

        .text-gray-500 {
            color: var(--text-gray);
            font-size: 0.875rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
            }

            .modal-content {
                margin: 1rem;
                padding: 1rem;
            }

            .table-container {
                overflow-x: auto;
            }

            th, td {
                padding: 0.5rem;
            }

            .category-image {
                width: 60px;
                height: 60px;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <div class="header">
        <h1 class="title">Category Management</h1>
        <button class="btn btn-primary" onclick="openModal('add')">
            <i class="fas fa-plus"></i> Add Category
        </button>
    </div>

    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
            <?php 
                echo $_SESSION['success'];
                unset($_SESSION['success']);
            ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
                echo $_SESSION['error'];
                unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Image</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)):
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['id']); ?></td>
                        <td>
                            <?php if ($row['image']): ?>
                                <img src="<?php echo $upload_dir . htmlspecialchars($row['image']); ?>" 
                                     alt="<?php echo htmlspecialchars($row['name']); ?>" 
                                     class="category-image">
                            <?php else: ?>
                                <div class="category-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-image text-gray-400"></i>
                                </div>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($row['name']); ?></td>
                        <td><?php echo htmlspecialchars($row['description']); ?></td>
                        <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                        <td class="action-buttons">
                            <button class="btn btn-edit" onclick="openModal('edit', <?php echo htmlspecialchars(json_encode($row)); ?>)">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn btn-delete" onclick="deleteCategory(<?php echo $row['id']; ?>, '<?php echo htmlspecialchars($row['name']); ?>')">
                                <i class="fas fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                <?php 
                    endwhile;
                } catch(PDOException $e) {
                    echo "<tr><td colspan='6'>Error: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div id="addModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('add')">&times;</span>
        <h2>Add Category</h2>
        <form id="addForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="create">
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Category Image</label>
                <div class="file-input-wrapper">
                    <div class="file-input-button">Choose Image</div>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImage(this, 'add')">
                </div>
                <small class="text-gray-500">Maximum size: 2MB. Allowed types: JPG, PNG, GIF, WebP</small>
                <img id="addImagePreview" class="image-upload-preview">
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
</div>

<!-- Edit Modal -->
<div id="editModal" class="modal">
    <div class="modal-content">
        <span class="close" onclick="closeModal('edit')">&times;</span>
        <h2>Edit Category</h2>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="action" value="update">
            <input type="hidden" name="id" id="editId">
            <div class="form-group">
                <label class="form-label">Name</label>
                <input type="text" name="name" id="editName" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" id="editDescription" class="form-control" rows="3"></textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Category Image</label>
                <div id="currentImage"></div>
                <div class="file-input-wrapper">
                    <div class="file-input-button">Choose New Image</div>
                    <input type="file" name="image" accept="image/jpeg,image/png,image/gif,image/webp" onchange="previewImage(this, 'edit')">
                </div>
                <small class="text-gray-500">Maximum size: 2MB. Allowed types: JPG, PNG, GIF, WebP</small>
                <img id="editImagePreview" class="image-upload-preview">
            </div>
            <button type="submit" class="btn btn-primary">Update</button>
        </form>
    </div>
</div>

<script>
    // Modal functions
    function openModal(type, data = null) {
        const modal = document.getElementById(type + 'Modal');
        modal.style.display = 'flex';

        // Reset image preview
        document.getElementById(type + 'ImagePreview').style.display = 'none';
        document.getElementById(type + 'ImagePreview').src = '';

        if (type === 'edit' && data) {
            document.getElementById('editId').value = data.id;
            document.getElementById('editName').value = data.name;
            document.getElementById('editDescription').value = data.description || '';
            
            // Show current image if exists
            const currentImageDiv = document.getElementById('currentImage');
            if (data.image) {
                currentImageDiv.innerHTML = `
                    <div class="image-preview-container">
                        <img src="../assets/uploads/categories/${data.image}" 
                             class="current-image" 
                             alt="Current category image">
                        <span class="remove-image" onclick="removeImage(${data.id})">×</span>
                    </div>`;
            } else {
                currentImageDiv.innerHTML = '<p class="text-gray-500">No image currently set</p>';
            }
        }
    }

    function closeModal(type) {
        const modal = document.getElementById(type + 'Modal');
        modal.style.display = 'none';
        
        // Reset form
        if (type === 'add') {
            document.getElementById('addForm').reset();
        }
        // Reset image previews
        document.getElementById(type + 'ImagePreview').style.display = 'none';
        document.getElementById(type + 'ImagePreview').src = '';
    }

    function previewImage(input, type) {
        const preview = document.getElementById(type + 'ImagePreview');
        const file = input.files[0];
        
        if (file) {
            // Validate file size (2MB)
            if (file.size > 2 * 1024 * 1024) {
                alert('File size too large. Maximum size is 2MB.');
                input.value = '';
                return;
            }
            
            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                alert('Invalid file type. Allowed types: JPG, PNG, GIF, WebP');
                input.value = '';
                return;
            }
            
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.src = e.target.result;
                preview.style.display = 'block';
            }
            reader.readAsDataURL(file);
        } else {
            preview.src = '';
            preview.style.display = 'none';
        }
    }

    // Delete function
    function deleteCategory(id, name) {
        if (confirm('Are you sure you want to delete ' + name + '? This will also delete the associated image.')) {
            const form = document.createElement('form');
            form.method = 'POST';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'delete';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = id;
            
            form.appendChild(actionInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Remove image function
    function removeImage(categoryId) {
        if (confirm('Are you sure you want to remove this image?')) {
            const form = document.createElement('form');
            form.method = 'POST';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'remove_image';
            
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'id';
            idInput.value = categoryId;
            
            form.appendChild(actionInput);
            form.appendChild(idInput);
            document.body.appendChild(form);
            form.submit();
        }
    }

    // Close modal when clicking outside
    window.onclick = function(event) {
        if (event.target.className === 'modal') {
            event.target.style.display = 'none';
        }
    }

    // Auto-hide alerts after 3 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.getElementsByClassName('alert');
            for (let alert of alerts) {
                alert.style.display = 'none';
            }
        }, 3000);

        // Handle image load errors
        document.querySelectorAll('.category-image, .current-image').forEach(img => {
            img.addEventListener('error', function() {
                this.style.display = 'none';
                this.parentElement.innerHTML = `
                    <div class="category-image" style="background: #f3f4f6; display: flex; align-items: center; justify-content: center;">
                        <i class="fas fa-image text-gray-400"></i>
                    </div>
                `;
            });
        });
    });
</script>

</body>
</html>

<?php require_once 'includes/footer.php'; ?>