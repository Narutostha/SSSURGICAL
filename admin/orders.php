<?php
session_start();
require_once '../config/database.php';
require_once 'includes/header.php';

// Ensure admin is logged in
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Handle order status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET delivery_status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $_SESSION['success'] = "Order status updated successfully";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating order status: " . $e->getMessage();
    }
}

// Handle payment status update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_payment_status'])) {
    $order_id = $_POST['order_id'];
    $new_status = $_POST['new_status'];
    
    try {
        $stmt = $pdo->prepare("UPDATE orders SET payment_status = ? WHERE id = ?");
        $stmt->execute([$new_status, $order_id]);
        $_SESSION['success'] = "Payment status updated successfully";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Error updating payment status: " . $e->getMessage();
    }
}

// Fetch orders
try {
    $stmt = $pdo->query("
        SELECT o.*, c.name AS customer_name 
        FROM orders o 
        LEFT JOIN customers c ON o.user_id = c.id 
        ORDER BY o.created_at DESC
    ");
    $orders = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching orders: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - Manage Orders</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        /* Modern CSS Variables */
        :root {
            /* Colors - Modern Tech Theme */
            --primary: #4f46e5;
            --primary-light: #818cf8;
            --primary-dark: #3730a3;
            --secondary: #64748b;
            --success: #10b981;
            --warning: #f59e0b;
            --danger: #ef4444;
            --surface-white: #ffffff;
            --surface-light: #f8fafc;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --border-light: #e2e8f0;

            /* Status Colors */
            --status-pending-bg: #fef3c7;
            --status-pending-text: #92400e;
            --status-processing-bg: #dbeafe;
            --status-processing-text: #1e40af;
            --status-shipped-bg: #f3e8ff;
            --status-shipped-text: #6b21a8;
            --status-delivered-bg: #dcfce7;
            --status-delivered-text: #166534;

            /* Shadows */
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1);

            /* Transitions */
            --transition-fast: 150ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-normal: 200ms cubic-bezier(0.4, 0, 0.2, 1);
            --transition-slow: 300ms cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Base Styles */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', system-ui, -apple-system, sans-serif;
            background: linear-gradient(135deg, var(--surface-light) 0%, var(--border-light) 100%);
            color: var(--text-primary);
            line-height: 1.5;
            min-height: 100vh;
        }

        /* Container */
        .container {
            max-width: 1400px;
            margin: 2rem auto;
            padding: 0 1.5rem;
        }

        /* Table Container */
        .table-container {
            background: var(--surface-white);
            border-radius: 24px;
            box-shadow: var(--shadow-lg);
            overflow: hidden;
        }

        /* Card Header */
        .card-header {
            padding: 1.5rem 2rem;
            background: linear-gradient(to right, var(--primary), var(--primary-dark));
            color: white;
            margin-bottom: 2rem;
        }

        .card-title {
            font-size: 1.75rem;
            font-weight: 600;
        }

        /* Search and Controls */
        .d-flex {
            display: flex;
            gap: 1rem;
            padding: 0 2rem;
            margin-bottom: 2rem;
        }

        .justify-content-between {
            justify-content: space-between;
        }

        .align-items-center {
            align-items: center;
        }

        .search-bar {
            flex: 1;
        }

        /* Form Controls */
        .form-control {
            width: 100%;
            padding: 0.875rem 1.25rem;
            border: 2px solid var(--border-light);
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all var(--transition-normal);
        }

        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
        }

        /* Buttons */
        .btn {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.875rem 1.5rem;
            border: none;
            border-radius: 12px;
            font-weight: 500;
            cursor: pointer;
            transition: all var(--transition-normal);
        }

        .btn-success {
            background: var(--success);
            color: white;
        }

        .btn-success:hover {
            filter: brightness(110%);
            transform: translateY(-2px);
        }

        .btn-sm {
            padding: 0.5rem;
            border-radius: 8px;
        }

        /* Table Styles */
        .table-responsive {
            overflow-x: auto;
            padding: 0 2rem 2rem 2rem;
        }

        .table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
        }

        .table th {
            background: var(--surface-light);
            padding: 1.25rem 1rem;
            font-weight: 600;
            color: var(--text-secondary);
            text-align: left;
            border-bottom: 2px solid var(--border-light);
        }

        .table td {
            padding: 1rem;
            border-bottom: 1px solid var(--border-light);
            vertical-align: middle;
        }

        .table-hover tbody tr {
            transition: background-color var(--transition-normal);
        }

        .table-hover tbody tr:hover {
            background-color: var(--surface-light);
        }

        /* Status Badges */
        .status-badge {
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.875rem;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
        }

        .status-badge[onclick] {
            cursor: pointer;
            transition: all var(--transition-normal);
        }

        .status-badge[onclick]:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-sm);
        }

        .payment-pending, .status-pending {
            background: var(--status-pending-bg);
            color: var(--status-pending-text);
        }

        .payment-processing, .status-processing {
            background: var(--status-processing-bg);
            color: var(--status-processing-text);
        }

        .payment-shipped, .status-shipped {
            background: var(--status-shipped-bg);
            color: var(--status-shipped-text);
        }

        .payment-delivered, .status-delivered, .payment-completed {
            background: var(--status-delivered-bg);
            color: var(--status-delivered-text);
        }

        .payment-failed {
            background: #fee2e2;
            color: #991b1b;
        }

        .payment-refunded {
            background: #f3e8ff;
            color: #6b21a8;
        }

        /* Action Buttons */
        .action-buttons {
            display: flex;
            gap: 0.5rem;
        }

        .btn-info { background-color: var(--primary); color: white; }
        .btn-primary { background-color: var(--success); color: white; }
        .btn-warning { background-color: var(--warning); color: white; }
        .btn-danger { background-color: var(--danger); color: white; }

        /* Alerts */
        .alert {
            margin: 0 2rem 2rem;
            padding: 1rem 1.5rem;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .alert-success {
            background: var(--status-delivered-bg);
            color: var(--status-delivered-text);
        }

        .alert-danger {
            background: #fee2e2;
            color: #991b1b;
        }

        .alert-dismissible {
            position: relative;
        }

        .btn-close {
            background: none;
            border: none;
            color: currentColor;
            opacity: 0.7;
            cursor: pointer;
            padding: 0.5rem;
            margin-left: auto;
        }

        .btn-close:hover {
            opacity: 1;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .container {
                padding: 1rem;
                margin: 1rem auto;
            }

            .d-flex {
                flex-direction: column;
                padding: 0 1rem;
            }

            .table-responsive {
                padding: 0 1rem 1rem;
            }

            .btn {
                width: 100%;
                justify-content: center;
            }

            .action-buttons {
                flex-wrap: wrap;
            }

            .alert {
                margin: 0 1rem 1rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="table-container">
            <div class="card-header">
                <h2 class="card-title">Manage Orders</h2>
            </div>

            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['success'];
                    unset($_SESSION['success']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- Search and Export Section -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div class="search-bar">
                    <input type="text" id="orderSearch" class="form-control" placeholder="Search orders...">
                </div>
                <button class="btn btn-success" onclick="exportToCSV()">
                    <i class="fas fa-download"></i> Export to CSV
                </button>
            </div>

            <!-- Orders Table -->
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Amount</th>
                            <th>Payment Method</th>
                            <th>Payment Status</th>
                            <th>Delivery Status</th>
                            <th>Order Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td>#<?php echo $order['id']; ?></td>
                                <td><?php echo htmlspecialchars($order['customer_name'] ?? 'Guest User'); ?></td>
                                <td>Rs. <?php echo number_format($order['total_amount'], 2); ?></td>
                                <td><?php echo ucfirst($order['payment_method']); ?></td>
                                <td>
                                    <span class="status-badge payment-<?php echo $order['payment_status']; ?>" 
                                          style="cursor: pointer;" 
                                          onclick="updatePaymentStatus(<?php echo $order['id']; ?>, '<?php echo $order['payment_status']; ?>')">
                                        <i class="fas fa-coins"></i>
                                        <?php echo ucfirst($order['payment_status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="status-badge status-<?php echo $order['delivery_status']; ?>">
                                        <?php echo ucfirst($order['delivery_status']); ?>
                                    </span>
                                    </td>
                                <td><?php echo date('M d, Y', strtotime($order['created_at'])); ?></td>
                                <td class="action-buttons">
                                    <button class="btn btn-primary btn-sm" onclick="updateTracking(<?php echo $order['id']; ?>)">
                                        <i class="fas fa-truck"></i>
                                    </button>
                                    <button class="btn btn-warning btn-sm" onclick="updateStatus(<?php echo $order['id']; ?>)">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" onclick="deleteOrder(<?php echo $order['id']; ?>)">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Search functionality
        document.getElementById('orderSearch').addEventListener('input', function(e) {
            const searchText = e.target.value.toLowerCase();
            const tableRows = document.querySelectorAll('tbody tr');
            
            tableRows.forEach(row => {
                const text = row.textContent.toLowerCase();
                row.style.display = text.includes(searchText) ? '' : 'none';
            });
        });

        // Update Payment Status
        function updatePaymentStatus(orderId, currentStatus) {
            Swal.fire({
                title: 'Update Payment Status',
                input: 'select',
                inputOptions: {
                    'pending': 'Pending',
                    'processing': 'Processing',
                    'completed': 'Completed',
                    'failed': 'Failed',
                    'refunded': 'Refunded'
                },
                inputValue: currentStatus,
                showCancelButton: true,
                confirmButtonText: 'Update',
                showLoaderOnConfirm: true,
                preConfirm: (status) => {
                    const formData = new FormData();
                    formData.append('update_payment_status', '1');
                    formData.append('order_id', orderId);
                    formData.append('new_status', status);

                    return fetch('orders.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.text();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Updated!', 'Payment status has been updated.', 'success')
                    .then(() => location.reload());
                }
            });
        }

        // Update Tracking
        function updateTracking(orderId) {
            Swal.fire({
                title: 'Update Tracking Information',
                html: `
                    <form id="trackingForm" class="text-start">
                        <div class="mb-3">
                            <label class="form-label">Status</label>
                            <select class="form-select" id="trackingStatus">
                                <option value="pending">Pending</option>
                                <option value="processing">Processing</option>
                                <option value="shipped">Shipped</option>
                                <option value="delivered">Delivered</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Location</label>
                            <input type="text" class="form-control" id="trackingLocation">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" id="trackingDescription"></textarea>
                        </div>
                    </form>
                `,
                showCancelButton: true,
                confirmButtonText: 'Update',
                focusConfirm: false,
                preConfirm: () => {
                    return {
                        status: document.getElementById('trackingStatus').value,
                        location: document.getElementById('trackingLocation').value,
                        description: document.getElementById('trackingDescription').value
                    }
                }
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send to backend
                    fetch('update_tracking.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            order_id: orderId,
                            ...result.value
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Success', 'Tracking updated successfully', 'success')
                            .then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message || 'Failed to update tracking', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Failed to update tracking', 'error');
                    });
                }
            });
        }

        // Update Order Status
        function updateStatus(orderId) {
            Swal.fire({
                title: 'Update Order Status',
                input: 'select',
                inputOptions: {
                    'pending': 'Pending',
                    'processing': 'Processing',
                    'shipped': 'Shipped',
                    'delivered': 'Delivered'
                },
                showCancelButton: true,
                confirmButtonText: 'Update',
                showLoaderOnConfirm: true,
                preConfirm: (status) => {
                    const formData = new FormData();
                    formData.append('update_status', '1');
                    formData.append('order_id', orderId);
                    formData.append('new_status', status);

                    return fetch('orders.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(response.statusText);
                        }
                        return response.text();
                    })
                    .catch(error => {
                        Swal.showValidationMessage(`Request failed: ${error}`);
                    });
                },
                allowOutsideClick: () => !Swal.isLoading()
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire('Updated!', 'Order status has been updated.', 'success')
                    .then(() => location.reload());
                }
            });
        }

        // Delete Order
        function deleteOrder(orderId) {
            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Send delete request to backend
                    fetch(`delete_order.php?id=${orderId}`, {
                        method: 'DELETE'
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire('Deleted!', 'Order has been deleted.', 'success')
                            .then(() => location.reload());
                        } else {
                            Swal.fire('Error', data.message || 'Failed to delete order', 'error');
                        }
                    })
                    .catch(error => {
                        Swal.fire('Error', 'Failed to delete order', 'error');
                    });
                }
            });
        }

        // Export to CSV
        function exportToCSV() {
            let csv = 'Order ID,Customer,Amount,Payment Method,Payment Status,Delivery Status,Order Date\n';
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                const columns = row.querySelectorAll('td');
                const rowData = [];
                columns.forEach((column, index) => {
                    if (index < 7) { // Skip the Actions column
                        let data = column.textContent.trim();
                        // Handle commas in the data
                        data = data.includes(',') ? `"${data}"` : data;
                        rowData.push(data);
                    }
                });
                csv += rowData.join(',') + '\n';
            });

            const blob = new Blob([csv], { type: 'text/csv' });
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.setAttribute('hidden', '');
            a.setAttribute('href', url);
            a.setAttribute('download', 'orders.csv');
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
        }
    </script>
</body>
</html>