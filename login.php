<?php
session_start();

// Hardcoded user credentials for demonstration purposes
$validUsers = [
    'Feon' => 'password',
    'Vira' => 'password',
];

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
    $enteredUsername = $_POST['username'];
    $enteredPassword = $_POST['password'];
    
    if (isset($validUsers[$enteredUsername]) && $enteredPassword === $validUsers[$enteredUsername]) {

        // Authentication successful
        $timestamp = date('Y-m-d H:i:s'); // Get current date and time

        $_SESSION['user'] = [
            'username' => $enteredUsername,
            'login_datetime' => $timestamp
        ];
        header("Location: bank_account.php");
        exit();
    } else {
        // Authentication failed
        $errorMessage = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <link rel="stylesheet" href="style.css">

</head>

<body>
    <div class="login-form-card">
        <div class="login-form">

            <h1>Login</h1>

            <?php if (isset($errorMessage)) : ?>
                <p style="color: red;"><?php echo $errorMessage; ?></p>
            <?php endif; ?>
            <form method="post" action="login.php">
                <label for="username">Username:</label>
                <input type="text" name="username" id="username" required>

                <label for="password">Password:</label>
                <input type="password" name="password" id="password" required>

                <button type="submit" name="login">Login</button>
            </form>
        </div>

    </div>

</body>

</html>