<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';
require_once 'process_contact_management.php';

// Fetch contact submissions
try {
    $stmt = $pdo->query("SELECT * FROM contact_submissions ORDER BY created_at DESC");
    $submissions = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching submissions: " . $e->getMessage();
    $submissions = [];
}

// Fetch current settings
try {
    $stmt = $pdo->query("SELECT * FROM contact_settings ORDER BY id DESC LIMIT 1");
    $settings = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $_SESSION['error'] = "Error fetching settings: " . $e->getMessage();
    $settings = [];
}

require_once 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Management - Admin</title>
    <style>
        .admin-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }

        .card {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }

        .card-header {
            padding: 15px 20px;
            border-bottom: 1px solid #eee;
        }

        .card-body {
            padding: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: 500;
        }

        .form-control {
            width: 100%;
            padding: 8px 12px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }

        .btn {
            padding: 8px 15px;
            border-radius: 4px;
            border: none;
            cursor: pointer;
            font-weight: 500;
            margin-right: 5px;
        }

        .btn-primary {
            background: #4f46e5;
            color: white;
        }

        .btn-warning {
            background: #eab308;
            color: white;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .alert {
            padding: 15px;
            border-radius: 4px;
            margin-bottom: 20px;
        }

        .alert-success {
            background: #86efac;
            color: #166534;
        }

        .alert-danger {
            background: #fecaca;
            color: #991b1b;
        }

        .table {
            width: 100%;
            border-collapse: collapse;
        }

        .table th,
        .table td {
            padding: 12px;
            border-bottom: 1px solid #eee;
            text-align: left;
        }

        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
        }

        .status-new {
            background: #bfdbfe;
            color: #1e40af;
        }

        .status-read {
            background: #fde68a;
            color: #92400e;
        }

        .status-replied {
            background: #86efac;
            color: #166534;
        }

        .action-buttons {
            display: flex;
            gap: 5px;
            align-items: center;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            position: relative;
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .close-modal {
            position: absolute;
            right: 20px;
            top: 15px;
            font-size: 24px;
            cursor: pointer;
            color: #666;
        }

        .close-modal:hover {
            color: #000;
        }

        .message-details {
            margin-top: 20px;
        }

        .message-details p {
            margin-bottom: 10px;
        }

        .message-details label {
            font-weight: bold;
            display: inline-block;
            width: 100px;
        }

        @media (max-width: 768px) {
            .table {
                display: block;
                overflow-x: auto;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 10px;
            }
            
            .action-buttons form,
            .action-buttons button {
                width: 100%;
            }
        }
    </style>
</head>
<body>

<div class="admin-container">
    <h1>Contact Management</h1>

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

    <!-- Settings Section -->
    <div class="card">
        <div class="card-header">
            <h2>Contact Information Settings</h2>
        </div>
        <div class="card-body">
            <form method="post" action="process_contact_management.php">
                <input type="hidden" name="action" value="update_settings">
                
                <div class="form-group">
                    <label>Address</label>
                    <input type="text" name="address" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['address'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Phone</label>
                    <input type="text" name="phone" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['phone'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['email'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label>Business Hours</label>
                    <input type="text" name="business_hours" class="form-control" 
                           value="<?php echo htmlspecialchars($settings['business_hours'] ?? ''); ?>" required>
                </div>

                <button type="submit" class="btn btn-primary">Update Settings</button>
            </form>
        </div>
    </div>

    <!-- Messages Section -->
    <div class="card">
        <div class="card-header">
            <h2>Contact Form Submissions</h2>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Message</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($submissions as $submission): ?>
                            <tr>
                                <td><?php echo date('Y-m-d H:i', strtotime($submission['created_at'])); ?></td>
                                <td><?php echo htmlspecialchars($submission['name']); ?></td>
                                <td><?php echo htmlspecialchars($submission['email']); ?></td>
                                <td><?php echo htmlspecialchars(substr($submission['message'], 0, 100)) . '...'; ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo $submission['status']; ?>">
                                        <?php echo ucfirst($submission['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <button type="button" class="btn btn-primary" onclick="viewMessageDetails('<?php 
                                            echo htmlspecialchars(date('Y-m-d H:i', strtotime($submission['created_at']))); ?>', '<?php 
                                            echo htmlspecialchars($submission['name']); ?>', '<?php 
                                            echo htmlspecialchars($submission['email']); ?>', '<?php 
                                            echo htmlspecialchars($submission['status']); ?>', '<?php 
                                            echo htmlspecialchars(str_replace("'", "\\'", $submission['message'])); ?>')">
                                            View Details
                                        </button>
                                        
                                        <form method="post" action="process_contact_management.php" style="display: inline;">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="message_id" value="<?php echo $submission['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" class="form-control">
                                                <option value="new" <?php echo $submission['status'] === 'new' ? 'selected' : ''; ?>>New</option>
                                                <option value="read" <?php echo $submission['status'] === 'read' ? 'selected' : ''; ?>>Read</option>
                                                <option value="replied" <?php echo $submission['status'] === 'replied' ? 'selected' : ''; ?>>Replied</option>
                                            </select>
                                        </form>
                                        
                                        <form method="post" action="process_contact_management.php" style="display: inline;" 
                                              onsubmit="return confirm('Are you sure you want to delete this message?');">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="message_id" value="<?php echo $submission['id']; ?>">
                                            <button type="submit" class="btn btn-danger">Delete</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal for message details -->
<div id="messageModal" class="modal">
    <div class="modal-content">
        <span class="close-modal">&times;</span>
        <h2>Message Details</h2>
        <div class="message-details">
            <p><label>Date:</label> <span id="modalDate"></span></p>
            <p><label>Name:</label> <span id="modalName"></span></p>
            <p><label>Email:</label> <span id="modalEmail"></span></p>
            <p><label>Status:</label> <span id="modalStatus"></span></p>
            <p><label>Message:</label></p>
            <p id="modalMessage" style="white-space: pre-wrap;"></p>
        </div>
    </div>
</div>

<script>
// Auto-hide alerts after 3 seconds
document.addEventListener('DOMContentLoaded', function() {
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(function(alert) {
            alert.style.display = 'none';
        });
    }, 3000);
});

// Modal functionality
const modal = document.getElementById('messageModal');
const closeBtn = document.getElementsByClassName('close-modal')[0];

function viewMessageDetails(date, name, email, status, message) {
    document.getElementById('modalDate').textContent = date;
    document.getElementById('modalName').textContent = name;
    document.getElementById('modalEmail').textContent = email;
    document.getElementById('modalStatus').textContent = status;
    document.getElementById('modalMessage').textContent = message;
    modal.style.display = "block";
}

// Close modal when clicking the x button
closeBtn.onclick = function() {
    modal.style.display = "none";
}

// Close modal when clicking outside
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}
</script>

</body>
</html>

<?php require_once 'includes/footer.php'; ?>