<?php
// admin/login.php - Admin Login

session_start();

require_once '../config/database.php';

// Check if admin is already logged in
if (isset($_SESSION['admin_logged_in'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if ($username && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username AND is_admin = 1");
        $stmt->execute([':username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_id'] = $admin['id'];
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid username or password';
        }
    } else {
        $error = 'Please fill in both fields';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <style>
body {
    font-family: 'Inter', -apple-system, system-ui, sans-serif;
    background: linear-gradient(135deg, #f0f2f5 0%, #e5e7eb 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
    margin: 0;
    padding: 1rem;
}

.login-container {
    background: white;
    border-radius: 24px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
    width: 100%;
    max-width: 400px;
    padding: 2.5rem;
}

h1 {
    font-size: 1.5rem;
    font-weight: 600;
    color: #1a1a1a;
    margin: 0 0 2rem 0;
    text-align: center;
}

.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    color: #666;
    font-size: 0.9rem;
    font-weight: 500;
}

input {
    width: 100%;
    padding: 0.875rem;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
    background: #f9fafb;
}

input:focus {
    outline: none;
    border-color: #6366f1;
    background: white;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

button {
    width: 100%;
    padding: 0.875rem;
    background: #6366f1;
    color: white;
    border: none;
    border-radius: 12px;
    font-size: 1rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s ease;
    margin: 1.5rem 0;
}

button:hover {
    background: #4f46e5;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
}

.create-admin {
    display: block;
    text-align: center;
    color: #6366f1;
    text-decoration: none;
    font-size: 0.875rem;
    font-weight: 500;
    margin-top: 1rem;
    padding: 0.5rem;
    border-radius: 8px;
    transition: all 0.2s ease;
}

.create-admin:hover {
    background: rgba(99, 102, 241, 0.08);
    transform: translateY(-1px);
}

.error {
    background: #fef2f2;
    color: #dc2626;
    padding: 0.875rem;
    border-radius: 12px;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    text-align: center;
}

.success {
    background: #f0fdf4;
    color: #16a34a;
    padding: 0.875rem;
    border-radius: 12px;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    text-align: center;
}   
    </style>
</head>
<body>
<div class="login-container">
    <h1>Admin Login</h1>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <form action="login.php" method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <button type="submit">Login</button>
    </form>
    <a href="create_admin.php" class="create-admin">Create Admin Account</a>
</div>
</body>
</html>
