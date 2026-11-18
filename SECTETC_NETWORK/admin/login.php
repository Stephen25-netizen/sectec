<?php

require_once "../config/auth.php";

// Redirect logged-in administrators
if (
    !empty($_SESSION['admin_id']) &&
    ($_SESSION['admin_role'] ?? '') === 'admin'
) {

    header("Location: dashboard.php");
    exit;

}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {

        $error = "Invalid request. Please refresh the page.";

    } else {

        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $error = "Enter a valid email address.";

        } else {

            $stmt = $conn->prepare(
                "SELECT id, full_name, email, password, role
                 FROM users
                 WHERE email = ?
                 AND role = 'admin'
                 LIMIT 1"
            );

            $stmt->execute([$email]);

            $admin = $stmt->fetch();

            if (
                $admin &&
                password_verify($password, $admin['password'])
            ) {

                session_regenerate_id(true);

                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['admin_role'] = $admin['role'];

                unset($_SESSION['csrf_token']);

                header("Location: dashboard.php");
                exit;

            } else {

                $error = "Incorrect email or password.";

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

    <title>Administrator Login | SECTEC NETWORK LTD</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #071b35, #0879a9);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        .login-container {
            background: white;
            width: 100%;
            max-width: 420px;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
        }

        h1 {
            text-align: center;
            color: #073763;
            margin-bottom: 10px;
            font-size: 25px;
        }

        .subtitle {
            text-align: center;
            color: #777;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 13px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #0879a9;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        button:hover {
            background: #055b82;
        }

        .error {
            background: #ffe0e0;
            color: #a00000;
            padding: 12px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
        }

        .footer {
            text-align: center;
            margin-top: 25px;
            color: #777;
            font-size: 12px;
        }

    </style>

</head>

<body>

<div class="login-container">

    <h1>SECTEC NETWORK LTD</h1>

    <p class="subtitle">Administrator Login</p>

    <?php if ($error): ?>

        <div class="error">
            <?= clean($error) ?>
        </div>

    <?php endif; ?>

    <form method="POST">

        <input
            type="hidden"
            name="csrf_token"
            value="<?= clean(csrfToken()) ?>"
        >

        <label>Email Address</label>

        <input
            type="email"
            name="email"
            placeholder="Enter administrator email"
            required
        >

        <label>Password</label>

        <input
            type="password"
            name="password"
            placeholder="Enter your password"
            required
        >

        <button type="submit">
            Login to Dashboard
        </button>

    </form>

    <div class="footer">
        &copy; <?= date('Y') ?> SECTEC NETWORK LTD
    </div>

</div>

</body>

</html>