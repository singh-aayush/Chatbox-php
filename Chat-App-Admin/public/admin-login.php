<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #1a2a3a;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            font-family: Arial, sans-serif;
        }
        .login-container {
            display: flex;
            width: 90%;
            max-width: 800px;
            min-height: 400px;
            background-color: #1a2a3a;
            border: 1px solid #ccc;
            flex-direction: row;
            margin: 20px;
        }
        .logo-section {
            width: 50%;
            background-color: #1a2a3a;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #fff;
        }
        .logo-section img {
            max-width: 120px;
            width: 100%;
        }
        .form-section {
            width: 50%;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .form-section h2 {
            text-align: center;
            color: #1a2a3a;
            margin-bottom: 15px;
            font-size: 1.8rem;
        }
        .form-section p {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
            font-size: 0.9rem;
        }
        .form-control {
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 0.9rem;
        }
        .btn-primary {
            background-color: #f28c38;
            border: none;
            border-radius: 4px;
            padding: 10px;
            width: 100%;
            font-size: 1rem;
        }
        .btn-primary:hover {
            background-color: #e07b2c;
        }
        .forgot-password {
            text-align: right;
            color: #f28c38;
            text-decoration: none;
            font-size: 0.85rem;
        }
        .forgot-password:hover {
            text-decoration: underline;
        }
        #errorMessage {
            color: #d9534f;
            text-align: center;
            margin-top: 10px;
            font-size: 0.85rem;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .login-container {
                flex-direction: column;
                width: 100%;
                max-width: 400px;
                min-height: auto;
            }
            .logo-section, .form-section {
                width: 100%;
            }
            .logo-section {
                padding: 20px;
                min-height: 150px;
            }
            .logo-section img {
                max-width: 100px;
            }
            .form-section {
                padding: 15px;
            }
            .form-section h2 {
                font-size: 1.5rem;
            }
            .form-section p {
                font-size: 0.85rem;
            }
            .form-control {
                font-size: 0.85rem;
            }
            .btn-primary {
                padding: 8px;
                font-size: 0.9rem;
            }
        }

        @media (max-width: 480px) {
            .login-container {
                margin: 10px;
            }
            .logo-section {
                min-height: 120px;
            }
            .logo-section img {
                max-width: 80px;
            }
            .form-section {
                padding: 10px;
            }
            .form-section h2 {
                font-size: 1.3rem;
            }
            .form-section p {
                font-size: 0.8rem;
            }
            .form-control {
                font-size: 0.8rem;
            }
            .btn-primary {
                padding: 7px;
                font-size: 0.85rem;
            }
            .forgot-password {
                font-size: 0.75rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo-section">
            <div>
                <img src="./Images/admin.png" alt="Admin Logo">
            </div>
        </div>
        <div class="form-section">
            <form id="adminLoginForm" action="../php/admin-login.php" method="POST">
                <h2>Welcome</h2>
                <p>Please Login to Admin Dashboard</p>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Login</button>
                <div id="errorMessage"></div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#adminLoginForm').submit(function(e) {
                e.preventDefault();
                $('#errorMessage').text(''); // Clear previous errors
                
                const formData = $(this).serialize();
                $.post('../php/admin-login.php', formData)
                    .done(function(response) {
                        if (response === 'success') {
                            window.location.href = 'admin-dashboard.php';
                        } else {
                            $('#errorMessage').text(response);
                        }
                    })
                    .fail(function() {
                        $('#errorMessage').text('An error occurred. Please try again.');
                    });
            });
        });
    </script>
</body>
</html>