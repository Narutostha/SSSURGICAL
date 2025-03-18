<?php
// create_admin.php - Create Admin Account

session_start();

require_once '../config/database.php';



$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($username && $password && $confirm_password) {
        if ($password === $confirm_password) {
            // Check if username already exists
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = :username");
            $stmt->execute([':username' => $username]);
            if ($stmt->rowCount() > 0) {
                $error = 'Username already exists';
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Insert new admin into the database
                $stmt = $pdo->prepare("INSERT INTO users (username, password, is_admin, created_at) VALUES (:username, :password, 1, NOW())");
                $stmt->execute([':username' => $username, ':password' => $hashed_password]);

                $success = 'Admin account created successfully';
                header('Location: login.php?success=Account created successfully');
            }
        } else {
            $error = 'Passwords do not match';
        }
    } else {
        $error = 'Please fill in all fields';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Admin Account</title>
    <style>
            body {
            font-family: 'Inter', -apple-system, system-ui, sans-serif;
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 1rem;
            }

            .create-container {
            background: white;
            border-radius: 24px;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 600px;
            overflow: hidden;
            }

            .header-section {
            background: linear-gradient(to right, #4f46e5, #3730a3);
            padding: 2rem;
            text-align: center;
            color: white;
            }

            .logo-icon {
            width: 64px;
            height: 64px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 16px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 1.75rem;
            }

            h1 {
            padding-left: 20;
            margin: 1.5;
            font-size: 1.5rem;
            font-weight: 600;
            }

            form {
            padding: 2rem;
            }

            .form-group {
            margin-bottom: 1.5rem;
            }

            .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
            }

            .input-group {
            position: relative;
            }

            .input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #64748b;
            transition: all 0.2s ease;
            }

            input {
            width: 90%;
            padding: 0.875rem 1rem 0.875rem 2.75rem;
            border: 2px solid #e2e8f0;
            border-radius: 12px;
            font-size: 0.95rem;
            transition: all 0.2s ease;
            background: #f8fafc;
            }

            input:focus {
            outline: none;
            border-color: #4f46e5;
            background: white;
            box-shadow: 0 0 0 4px rgba(79, 70, 229, 0.1);
            }

            input:focus + i {
            color: #4f46e5;
            }

            .error {
            background: #fef2f2;
            color: #dc2626;
            padding: 1rem;
            border-radius: 12px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            }

            .success {
            background: #f0fdf4;
            color: #16a34a;
            padding: 1rem;
            border-radius: 12px;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            }

            button {
            width: 100%;
            padding: 1rem;
            background: #4f46e5;
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-bottom: 1rem;
            }

            button:hover {
            background: #3730a3;
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            }

            .create-admin {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            padding: 0.75rem 1rem;
            color: #4f46e5;
            text-decoration: none;
            font-size: 0.875rem;
            border-radius: 8px;
            transition: all 0.2s ease;
            margin-top: 1rem;
            }

            .create-admin:hover {
            background: rgba(79, 70, 229, 0.1);
            transform: translateX(5px);
            }

            @media (max-width: 480px) {
            .create-container {
                border-radius: 16px;
            }

            .header-section {
                padding: 1.5rem;
            }

            form {
                padding: 1.5rem;
            }

            .logo-icon {
                width: 48px;
                height: 48px;
                font-size: 1.25rem;
            }
            }
    </style>
</head>
<body>
<div class="create-container">
    <h1> .                   Create Admin Account</h1>
    <?php if ($error): ?>
        <p class="error"><?php echo htmlspecialchars($error); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
    <div class="success">
        <i class="fas fa-check-circle"></i>
        <?php echo htmlspecialchars($success); ?>
    </div>
<?php endif; ?>
    <form action="create_admin.php" method="POST">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" id="password" name="password" required>
        </div>
        <div class="form-group">
            <label for="confirm_password">Confirm Password:</label>
            <input type="password" id="confirm_password" name="confirm_password" required>
        </div>
        <button type="submit">Create Admin</button>
        <a href="login.php" class="create-admin">Login</a>
    </form>
</div>
</body>
</html>
