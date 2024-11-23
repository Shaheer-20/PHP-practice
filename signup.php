<?php
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        
        $stmt = $conn->prepare("SELECT * FROM Users WHERE username = ? OR email = ?");
        $stmt->bind_param("ss", $username, $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $error = "Username or email already exists. Please choose another.";
        } else {
            $stmt = $conn->prepare("INSERT INTO Users (username, password, email) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $username, $hashed_password, $email);
            
            if ($stmt->execute()) {
                header("Location: login.php?signup_success=1");
                exit();
            } else {
                $error = "Error: Could not create account. Please try again.";
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
    <title>Sign Up - Modern Shop</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #2c3e50;
            --accent-color: #3498db;
        }

        body {
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .signup-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            padding: 2.5rem;
            max-width: 500px;
            width: 90%;
            margin: 2rem auto;
        }

        .logo {
            text-align: center;
            margin-bottom: 2rem;
            color: var(--primary-color);
        }

        .logo i {
            font-size: 3rem;
            margin-bottom: 1rem;
        }

        .form-control {
            border: 2px solid #eee;
            border-radius: 10px;
            padding: 0.8rem 1rem;
            margin-bottom: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: var(--accent-color);
            box-shadow: 0 0 0 0.2rem rgba(52, 152, 219, 0.25);
        }

        .input-group {
            position: relative;
            margin-bottom: 1.5rem;
        }

        .input-group i {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #666;
        }

        .input-group input {
            padding-left: 3rem;
        }

        .password-strength {
            height: 5px;
            margin-top: 0.5rem;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-signup {
            background: linear-gradient(135deg, var(--primary-color), var(--accent-color));
            border: none;
            border-radius: 10px;
            padding: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            transition: all 0.3s ease;
            color: white;
        }

        .btn-signup:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.2);
        }

        .login-link {
            text-align: center;
            margin-top: 1.5rem;
            color: #666;
        }

        .login-link a {
            color: var(--accent-color);
            text-decoration: none;
            font-weight: 600;
            transition: color 0.3s ease;
        }

        .login-link a:hover {
            color: var(--primary-color);
        }

        .alert {
            border-radius: 10px;
            padding: 1rem;
            margin-bottom: 1.5rem;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .signup-container {
            animation: fadeIn 0.5s ease-out;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="signup-container">
            <div class="logo">
                <i class="fas fa-store"></i>
                <h2>Modern Shop</h2>
                <p class="text-muted">Create your account</p>
            </div>

            <form method="POST" action="signup.php" id="signupForm">
                <?php if (!empty($error)) { ?>
                    <div class="alert alert-danger">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <?php echo $error; ?>
                    </div>
                <?php } ?>

                <div class="input-group">
                    <i class="fas fa-user"></i>
                    <input 
                        type="text" 
                        name="username" 
                        id="username" 
                        class="form-control" 
                        placeholder="Choose a username"
                        required
                        minlength="3"
                    >
                </div>

                <div class="input-group">
                    <i class="fas fa-envelope"></i>
                    <input 
                        type="email" 
                        name="email" 
                        id="email" 
                        class="form-control" 
                        placeholder="Enter your email"
                        required
                    >
                </div>

                <div class="input-group">
                    <i class="fas fa-lock"></i>
                    <input 
                        type="password" 
                        name="password" 
                        id="password" 
                        class="form-control" 
                        placeholder="Choose a password"
                        required
                        minlength="8"
                    >
                    <div class="password-strength" id="passwordStrength"></div>
                </div>

                <div class="input-group">
                    <i class="fas fa-check-circle"></i>
                    <input 
                        type="password" 
                        name="confirm_password" 
                        id="confirm_password" 
                        class="form-control" 
                        placeholder="Confirm your password"
                        required
                    >
                </div>

                <div class="form-check mb-3">
                    <input type="checkbox" class="form-check-input" id="terms" required>
                    <label class="form-check-label" for="terms">
                        I agree to the <a href="terms.php">Terms & Conditions</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-signup w-100">
                    <i class="fas fa-user-plus me-2"></i>Create Account
                </button>
            </form>

            <div class="login-link">
                Already have an account? <a href="login.php">Login here</a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function(e) {
            const password = e.target.value;
            const strength = document.getElementById('passwordStrength');
            let score = 0;

            // Length check
            if(password.length >= 8) score++;
            // Uppercase check
            if(/[A-Z]/.test(password)) score++;
            // Lowercase check
            if(/[a-z]/.test(password)) score++;
            // Number check
            if(/[0-9]/.test(password)) score++;
            // Special character check
            if(/[^A-Za-z0-9]/.test(password)) score++;

            // Update strength indicator
            switch(score) {
                case 0:
                case 1:
                    strength.style.width = '20%';
                    strength.style.backgroundColor = '#e74c3c';
                    break;
                case 2:
                    strength.style.width = '40%';
                    strength.style.backgroundColor = '#f1c40f';
                    break;
                case 3:
                    strength.style.width = '60%';
                    strength.style.backgroundColor = '#3498db';
                    break;
                case 4:
                    strength.style.width = '80%';
                    strength.style.backgroundColor = '#2ecc71';
                    break;
                case 5:
                    strength.style.width = '100%';
                    strength.style.backgroundColor = '#27ae60';
                    break;
            }
        });

        // Password match validation
        document.getElementById('signupForm').addEventListener('submit', function(e) {
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;

            if(password !== confirmPassword) {
                e.preventDefault();
                alert('Passwords do not match!');
            }
        });
    </script>
</body>
</html>