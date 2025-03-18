<?php
// Remove session_start() from here since it's already called in contact-management.php
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}

require_once '../config/database.php';

// Handle message status updates
if (isset($_POST['action'])) {
    switch($_POST['action']) {
        case 'update_status':
            try {
                $stmt = $pdo->prepare("UPDATE contact_submissions SET status = ? WHERE id = ?");
                $stmt->execute([$_POST['status'], $_POST['message_id']]);
                $_SESSION['success'] = "Message status updated successfully!";
            } catch(PDOException $e) {
                $_SESSION['error'] = "Error updating status: " . $e->getMessage();
            }
            break;

        case 'update_settings':
            try {
                $stmt = $pdo->prepare("
                    INSERT INTO contact_settings (address, phone, email, business_hours) 
                    VALUES (?, ?, ?, ?) 
                    ON DUPLICATE KEY UPDATE 
                    address = VALUES(address),
                    phone = VALUES(phone),
                    email = VALUES(email),
                    business_hours = VALUES(business_hours)
                ");
                
                $stmt->execute([
                    $_POST['address'],
                    $_POST['phone'],
                    $_POST['email'],
                    $_POST['business_hours']
                ]);
                
                $_SESSION['success'] = "Contact settings updated successfully!";
            } catch(PDOException $e) {
                $_SESSION['error'] = "Error updating settings: " . $e->getMessage();
            }
            break;

        case 'delete':
            try {
                $stmt = $pdo->prepare("DELETE FROM contact_submissions WHERE id = ?");
                $stmt->execute([$_POST['message_id']]);
                $_SESSION['success'] = "Message deleted successfully!";
            } catch(PDOException $e) {
                $_SESSION['error'] = "Error deleting message: " . $e->getMessage();
            }
            break;
    }
    
    header('Location: contact-management.php');
    exit();
}
?>