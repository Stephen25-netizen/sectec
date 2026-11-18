<?php

require_once "../config/auth.php";

// Check whether an administrator already exists
$stmt = $conn->query(
    "SELECT COUNT(*) FROM users WHERE role = 'admin'"
);

$adminExists = (int)$stmt->fetchColumn() > 0;

$error = "";

if (!$adminExists && $_SERVER["REQUEST_METHOD"] === "POST") {

    if (!verifyCsrf($_POST['csrf_token'] ?? '')) {

        $error = "Invalid request. Please refresh the page.";

    } else {

        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($fullName === '') {

            $error = "Please enter your full name.";

        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

            $error = "Please enter a valid email address.";

        } elseif (strlen($password) < 10) {

            $error = "Password must have at least 10 characters.";

        } else {

            try {

                $hashedPassword = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );

                $insert = $conn->prepare(
                    "INSERT INTO users
                    (full_name, email, phone, password, role)
                    VALUES (?, ?, ?, ?, 'admin')"
                );

                $insert->execute([
                    $fullName,
                    $email,
                    $phone,
                    $hashedPassword
                ]);

                header("Location: login.php?created=1");
                exit;

            } catch (PDOException $e) {

                error_log($e->getMessage());

                $error = "Account creation failed. Check whether the email is already registered.";

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

    <title>Create Admin | SECTEC NETWORK LTD</title>

    <style>

        * {
            box-sizing: border-box;
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

        .container {
            background: white;
            width: 100%;
            max-width: 450px;
            padding: 35px;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0,0,0,.2);
        }

        h2 {
            text-align: center;
            color: #073763;
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            color: #777;
            margin-bottom: 25px;
        }

        label {
            display: block;
            margin-bottom: 7px;
            font-weight: bold;
        }

        input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #ddd;
            border-radius: 6px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #0879a9;
            border: none;
            color: white;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background: #055b82;
        }

        .error {
            background: #ffe0e0;
            color: #a00000;
            padding: 12px;
            margin-bottom: 18px;
            border-radius: 5px;
        }

        .notice {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
        }

    </style>

</head>

<body>

<div class="container">

    <h2>SECTEC NETWORK LTD</h2>

    <p>Create Your First Administrator Account</p>

    <?php if ($adminExists): ?>

        <div class="notice">
            An administrator account already exists.
            Please proceed to the login page.
            <br><br>
            <a href="login.php">Go to Login</a>
        </div>

    <?php else: ?>

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

            <label>Full Name</label>

            <input
                type="text"
                name="full_name"
                placeholder="Enter your full name"
                required
            >

            <label>Email Address</label>

            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                required
            >

            <label>Phone Number</label>

            <input
                type="text"
                name="phone"
                placeholder="Enter your phone number"
            >

            <label>Password</label>

            <input
                type="password"
                name="password"
                minlength="10"
                placeholder="Create a strong password"
                required
            >

            <button type="submit">
                Create Administrator Account
            </button>

        </form>

    <?php endif; ?>

</div>

</body>

</html>