<?php
session_start();
if (isset($_SESSION['unique_id'])) {
    header("location: users.php");
    exit;
}
?>
<?php include_once "../public/header.php"; ?>
<body>
    <div class="wrapper">
        <section class="form signup">
            <header class="header-animation">Realtime Chat</header>
            <form action="#" enctype="multipart/form-data" id="signup-form">
                <div class="error-txt"></div>
                <div class="name-details">
                    <div class="field input">
                        <label>First Name</label>
                        <input type="text" name="fname" placeholder="First Name" required>
                    </div>
                    <div class="field input">
                        <label>Last Name</label>
                        <input type="text" name="lname" placeholder="Last Name" required>
                    </div>
                </div>
                <div class="name-details">
                    <div class="field input">
                        <label>Emp Code</label>
                        <input type="text" name="employee_code" placeholder="Emp Id" required>
                    </div>
                    <div class="field input">
                        <label>Designation</label>
                        <input type="text" name="designation" placeholder="Job Role" required>
                    </div>
                </div>
                <div class="field input">
                    <label>Location</label>
                    <input type="text" name="location" placeholder="Location" required>
                </div>
                <div class="field input">
                    <label>Email</label>
                    <input type="email" name="email" placeholder="New Email" required>
                </div>
                <div class="field input">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="New Password" required>
                    <i class="fas fa-eye"></i>
                </div>
                <div class="field image">
                    <label>Image</label>
                    <input type="file" name="image" required>
                </div>
                <div class="field button">
                    <input type="submit" value="Continue" required>
                </div>
            </form>
            <div class="link">Already signed up? <a href="./login.php">Login</a></div>
        </section>
    </div>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Arial', sans-serif;
        }

        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            overflow: auto;
        }

        .wrapper {
            width: 100%;
            max-width: 350px;
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeInUp 0.5s ease-out;
        }

        .form {
            padding: 20px;
            text-align: center;
        }

        .header-animation {
            font-size: 1.5rem;
            color: #00695c;
            margin-bottom: 15px;
            text-transform: uppercase;
            letter-spacing: 1px;
            animation: bounceIn 0.8s ease-out;
        }

        .name-details {
            display: flex;
            gap: 10px;
            margin-bottom: 15px;
        }

        .field {
            margin-bottom: 10px;
            text-align: left;
        }

        .field label {
            display: block;
            color: #26a69a;
            font-weight: bold;
            margin-bottom: 3px;
            font-size: 0.9rem;
        }

        .field input, .field input[type="file"] {
            width: 100%;
            padding: 8px;
            border: 2px solid #e0f2f1;
            border-radius: 20px;
            font-size: 0.9rem;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .field input:focus, .field input[type="file"]:focus {
            border-color: #00695c;
            box-shadow: 0 0 5px rgba(0, 105, 92, 0.2);
        }

        .field input[type="password"] {
            padding-right: 35px;
        }

        .field .fas {
            position: absolute;
            right: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #26a69a;
            cursor: pointer;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .field .fas:hover {
            color: #004d40;
        }

        .field.image input[type="file"] {
            padding: 8px;
            cursor: pointer;
        }

        .field.button input {
            background: #26a69a;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            padding: 8px;
            width: 100%;
            border-radius: 20px;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .field.button input:hover {
            background: #00695c;
            transform: translateY(-2px);
            animation: pulse 1.2s infinite;
        }

        .field.button input::after {
            content: '';
            position: absolute;
            width: 0;
            height: 0;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            transition: width 0.5s ease, height 0.5s ease;
        }

        .field.button input:hover::after {
            width: 150px;
            height: 150px;
        }

        .error-txt {
            color: #d32f2f;
            font-size: 0.8rem;
            margin-bottom: 10px;
            display: none;
            animation: shake 0.4s ease;
        }

        .link {
            margin-top: 15px;
            color: #666;
            font-size: 0.9rem;
        }

        .link a {
            color: #26a69a;
            text-decoration: none;
            font-weight: bold;
        }

        .link a:hover {
            color: #00695c;
            text-decoration: underline;
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes bounceIn {
            0% { transform: scale(0.9); opacity: 0; }
            50% { transform: scale(1.05); opacity: 0.5; }
            100% { transform: scale(1); opacity: 1; }
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-4px); }
            75% { transform: translateX(4px); }
        }

        @keyframes pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 105, 92, 0.4); }
            70% { box-shadow: 0 0 0 8px rgba(0, 105, 92, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 105, 92, 0); }
        }

        /* Responsive Design */
        @media (max-width: 350px) {
            .wrapper { max-width: 90%; padding: 10px; }
            .name-details { flex-direction: column; }
            .field input, .field input[type="file"] { font-size: 0.8rem; }
        }
    </style>

    <script src="./js/pass-show-hide.js"></script>
    <script src="./js/signup.js"></script>
</body>
</html>