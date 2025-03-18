<?php
session_start();
require_once 'config/database.php';

$error = '';
$success = isset($_GET['registered']) ? 'Registration successful. Please login.' : '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
   $email = trim($_POST['email']);
   $password = trim($_POST['password']);

   if ($email && $password) {
       $stmt = $pdo->prepare("SELECT * FROM customers WHERE email = :email");
       $stmt->execute([':email' => $email]);
       $customer = $stmt->fetch();

       if ($customer && password_verify($password, $customer['password'])) {
           $_SESSION['customer_id'] = $customer['id'];
           $_SESSION['customer_name'] = $customer['name'];
           header('Location: index.php');
           exit;
       } else {
           $error = 'Invalid email or password.';
       }
   } else {
       $error = 'Please fill in both fields.';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Customer Login</title>
   <style>
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
           padding: 1rem;
       }

       .auth-container {
           width: 100%;
           max-width: 420px;
           margin: 0 auto;
       }

       .auth-card {
           background: white;
           padding: 2.5rem;
           border-radius: 24px;
           box-shadow: 0 4px 24px rgba(0, 0, 0, 0.08);
       }

       h1 {
           color: #1a1a1a;
           font-size: 1.5rem;
           font-weight: 600;
           text-align: center;
           margin-bottom: 2rem;
       }

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

       .error {
           display: none;
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
       }

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

       .auth-card {
           animation: fadeIn 0.6s ease-out;
       }
   </style>
</head>
<body>
   <div class="auth-container">
       <div class="auth-card">
           <h1>Customer Login</h1>
           
           <!-- Error/Success Messages -->
           <div class="error" id="errorMessage"></div>
           <?php if ($success): ?>
               <div class="success"><?php echo htmlspecialchars($success); ?></div>
           <?php endif; ?>
           <?php if ($error): ?>
               <div class="error" style="display: block;"><?php echo htmlspecialchars($error); ?></div>
           <?php endif; ?>
           
           <form action="login.php" method="POST" id="loginForm">
               <div class="form-group">
                   <label for="email">Email:</label>
                   <input type="email" id="email" name="email" placeholder="Enter your email">
               </div>
               <div class="form-group">
                   <label for="password">Password:</label>
                   <input type="password" id="password" name="password" placeholder="Enter your password">
               </div>
               <button type="submit" class="btn">Login</button>
           </form>

           <p>Don't have an account? <a href="registration.php">Register here</a></p>
       </div>
   </div>

   <script>
   document.getElementById('loginForm').addEventListener('submit', function(e) {
       e.preventDefault();
       
       const email = document.getElementById('email').value.trim();
       const password = document.getElementById('password').value.trim();
       const errorDiv = document.getElementById('errorMessage');
       let error = false;
       
       // Reset styles
       errorDiv.style.display = 'none';
       document.getElementById('email').style.borderColor = '#e5e7eb';
       document.getElementById('password').style.borderColor = '#e5e7eb';
       
       // Validate email
       if (!email) {
           document.getElementById('email').style.borderColor = '#dc2626';
           error = true;
       } else if (!isValidEmail(email)) {
           document.getElementById('email').style.borderColor = '#dc2626';
           errorDiv.textContent = 'Please enter a valid email address';
           errorDiv.style.display = 'block';
           return;
       }
       
       // Validate password
       if (!password) {
           document.getElementById('password').style.borderColor = '#dc2626';
           error = true;
       }
       
       if (error) {
           errorDiv.textContent = 'Please fill in all required fields';
           errorDiv.style.display = 'block';
           return;
       }
       
       this.submit();
   });

   // Email validation function
   function isValidEmail(email) {
       const re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
       return re.test(String(email).toLowerCase());
   }

   // Clear error on input
   document.querySelectorAll('input').forEach(input => {
       input.addEventListener('input', function() {
           this.style.borderColor = '#e5e7eb';
           document.getElementById('errorMessage').style.display = 'none';
       });
   });
   </script>
</body>
</html>