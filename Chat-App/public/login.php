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
        <section class="form login">
            <header class="header-animation">Realtime Chat App</header>
            <form action="#" id="login-form">
                <div class="error-txt"></div>
                <div class="field input">
                    <label>Email</label>
                    <input type="text" name="email" placeholder="Enter Your Email" required>
                </div>
                <div class="field input">
                    <label>Password</label>
                    <input type="password" name="password" placeholder="Enter Your Password" required>
                    <i class="fas fa-eye"></i>
                </div>
                <div class="field button">
                    <input type="submit" value="Continue to Chat">
                </div>
            </form>
            <div class="link">Not yet signed up? <a href="./index.php">Signup now</a></div>
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
            min-height: 100vh;
            background: linear-gradient(135deg, #e0f2f1 0%, #b2dfdb 100%);
            overflow: hidden;
        }

        .wrapper {
            width: 100%;
            max-width: 400px;
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            animation: fadeInUp 0.6s ease-out;
        }

        .form {
            padding: 30px;
            text-align: center;
        }

        .header-animation {
            font-size: 2rem;
            color: #00695c;
            margin-bottom: 20px;
            text-transform: uppercase;
            letter-spacing: 2px;
            animation: bounceIn 1s ease-out;
        }

        .field {
            margin-bottom: 20px;
            text-align: left;
        }

        .field label {
            display: block;
            color: #26a69a;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .field input {
            width: 100%;
            padding: 12px;
            border: 2px solid #e0f2f1;
            border-radius: 25px;
            font-size: 1rem;
            outline: none;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }

        .field input:focus {
            border-color: #00695c;
            box-shadow: 0 0 10px rgba(0, 105, 92, 0.2);
        }

        .field input[type="password"] {
            padding-right: 40px;
        }

        .field .fas {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #26a69a;
            cursor: pointer;
            transition: color 0.3s ease;
        }

        .field .fas:hover {
            color: #004d40;
        }

        .field.button input {
            background: #26a69a;
            color: #fff;
            border: none;
            cursor: pointer;
            font-weight: bold;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .field.button input:hover {
            background: #00695c;
            transform: translateY(-2px);
        }

        .error-txt {
            color: #d32f2f;
            font-size: 0.9rem;
            margin-bottom: 15px;
            display: none;
            animation: shake 0.5s ease;
        }

        .link {
            margin-top: 20px;
            color: #666;
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
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
    </style>

    <script src="./js/pass-show-hide.js"></script>
    <script src="./js/login.js"></script>
</body>
</html>