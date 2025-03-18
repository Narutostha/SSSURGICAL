<?php
require_once 'config/database.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);

    if ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $pdo->prepare("INSERT INTO customers (name, email, password) VALUES (:name, :email, :password)");
        try {
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':password' => $hashed_password,
            ]);

            // Redirect to login page
            header('Location: login.php?registered=true');
            exit;
        } catch (PDOException $e) {
            if ($e->errorInfo[1] == 1062) {
                $error = 'Email already exists.';
            } else {
                $error = 'An error occurred. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Register</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">
    <style>
        /* Base Styles */
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Inter', -apple-system, system-ui, sans-serif;
    background: linear-gradient(135deg, #f0f2f5 0%, #e5e7eb 100%);
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 1.5rem;
}

/* Container Styles */
.auth-container {
    width: 100%;
    max-width: 440px;
    margin: 0 auto;
}

.auth-card {
    background: white;
    padding: 2.5rem;
    border-radius: 24px;
    box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
    animation: fadeIn 0.6s ease-out;
}

/* Heading */
h1 {
    color: #1a1a1a;
    font-size: 1.5rem;
    font-weight: 600;
    text-align: center;
    margin-bottom: 2rem;
}

/* Form Groups */
.form-group {
    margin-bottom: 1.25rem;
}

.form-group label {
    display: block;
    color: #666;
    font-size: 0.9rem;
    font-weight: 500;
    margin-bottom: 0.5rem;
}

/* Inputs */
input {
    width: 100%;
    padding: 0.875rem;
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    font-size: 0.95rem;
    transition: all 0.2s ease;
}

input:focus {
    outline: none;
    border-color: #6366f1;
    background: white;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.1);
}

input::placeholder {
    color: #a0aec0;
}

/* Button */
.btn {
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

.btn:hover {
    background: #4f46e5;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.2);
}

.btn:active {
    transform: translateY(0);
}

/* Messages */
.error {
    background: #fef2f2;
    color: #dc2626;
    padding: 0.875rem;
    border-radius: 12px;
    font-size: 0.875rem;
    margin-bottom: 1.5rem;
    text-align: center;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 0.5rem;
}

/* Login Link */
p {
    text-align: center;
    margin-top: 1.5rem;
    color: #64748b;
    font-size: 0.875rem;
}

p a {
    color: #6366f1;
    text-decoration: none;
    font-weight: 500;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    transition: all 0.2s ease;
}

p a:hover {
    background: rgba(99, 102, 241, 0.08);
    text-decoration: none;
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

/* Responsive Design */
@media (max-width: 480px) {
    .auth-card {
        padding: 2rem;
        border-radius: 16px;
    }

    h1 {
        font-size: 1.25rem;
    }

    .btn {
        padding: 0.75rem;
    }

    input {
        font-size: 0.9rem;
    }

    .form-group {
        margin-bottom: 1rem;
    }
}
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-card">
            <h1>Customer Register</h1>
            <?php if ($error): ?>
                <p class="error"><?php echo htmlspecialchars($error); ?></p>
            <?php endif; ?>
            <form action="registration.php" method="POST">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" id="name" name="name" placeholder="Enter your name" >
                </div>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" placeholder="Enter your email" >
                </div>
                <div class="form-group">
                    <label for="password">Password:</label>
                    <input type="password" id="password" name="password" placeholder="Enter your password" >
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirm Password:</label>
                    <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirm your password" >
                </div>
                <button type="submit" class="btn">Register</button>
            </form>
            <script>
document.querySelector('form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const name = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const password = document.getElementById('password').value.trim();
    const confirmPassword = document.getElementById('confirm_password').value.trim();
    let error = false;
    
    // Reset styles
    document.querySelectorAll('input').forEach(input => {
        input.style.borderColor = '#e5e7eb';
    });
    
    // Remove existing error message
    let errorDiv = document.querySelector('.error');
    if (errorDiv) errorDiv.remove();
    
    // Create error message div
    errorDiv = document.createElement('div');
    errorDiv.className = 'error';
    
    // Validate fields
    if (!name || !email || !password || !confirmPassword) {
        error = true;
        errorDiv.textContent = 'All fields are required';
        
        if (!name) document.getElementById('name').style.borderColor = '#dc2626';
        if (!email) document.getElementById('email').style.borderColor = '#dc2626';
        if (!password) document.getElementById('password').style.borderColor = '#dc2626';
        if (!confirmPassword) document.getElementById('confirm_password').style.borderColor = '#dc2626';
    }
    else if (!isValidEmail(email)) {
        error = true;
        errorDiv.textContent = 'Please enter a valid email address';
        document.getElementById('email').style.borderColor = '#dc2626';
    }
    else if (password.length < 6) {
        error = true;
        errorDiv.textContent = 'Password must be at least 6 characters';
        document.getElementById('password').style.borderColor = '#dc2626';
    }
    else if (password !== confirmPassword) {
        error = true;
        errorDiv.textContent = 'Passwords do not match';
        document.getElementById('password').style.borderColor = '#dc2626';
        document.getElementById('confirm_password').style.borderColor = '#dc2626';
    }
    
    if (error) {
        // Insert error message after h1
        document.querySelector('h1').after(errorDiv);
        return;
    }
    
    this.submit();
});

function isValidEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
}

// Clear error styling on input
document.querySelectorAll('input').forEach(input => {
    input.addEventListener('input', function() {
        this.style.borderColor = '#e5e7eb';
        const errorDiv = document.querySelector('.error');
        if (errorDiv) errorDiv.remove();
    });
});
</script>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
