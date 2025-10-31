<?php
require __DIR__ . '/db/connect.php';

// Handle logout
if (isset($_GET['logout'])) {
    session_destroy();
    header('Location: login.php');
    exit;
}

// Register
if (isset($_POST['action']) && $_POST['action'] === 'register') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';

    if ($name && filter_var($email, FILTER_VALIDATE_EMAIL) && strlen($pass) >= 8) {
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)');
        try {
            $stmt->execute([$name, $email, $hash]);
            $msg = 'Registration successful. Please log in.';
        } catch (PDOException $e) {
            $err = 'Email already in use.';
        }
    } else {
        $err = 'Please provide valid name, email, and 8+ char password.';
    }
}

// Login
if (isset($_POST['action']) && $_POST['action'] === 'login') {
    $email = trim($_POST['email'] ?? '');
    $pass  = $_POST['password'] ?? '';
    $stmt = $pdo->prepare('SELECT id, name, email, password_hash FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if ($u && password_verify($pass, $u['password_hash'])) {
        $_SESSION['user'] = ['id' => $u['id'], 'name' => $u['name'], 'email' => $u['email']];
        header('Location: dashboard.php');
        exit;
    } else {
        $err = 'Invalid email or password.';
    }
}
?>
<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Login • Project Tracker</title>
    <link rel="stylesheet" href="style.css">
</head>

<body class="auth">
    <div class="card">
        <h1>Project Tracker</h1>
        <?php if (!empty($msg)): ?><div class="notice success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>
        <?php if (!empty($err)): ?><div class="notice error"><?= htmlspecialchars($err) ?></div><?php endif; ?>

        <div class="tabs">
            <button class="tab active" data-tab="login">Login</button>
            <button class="tab" data-tab="register">Register</button>
        </div>

        <form method="post" id="login" class="tab-pane active">
            <input type="hidden" name="action" value="login">
            <label>Email: <input name="email" type="email" required></label>
            <label>Password: <input name="password" type="password" required></label>
            <button type="submit">Sign in</button>
        </form>

        <form method="post" id="register" class="tab-pane">
            <input type="hidden" name="action" value="register">
            <label>Name <input name="name" required></label>
            <label>Email <input name="email" type="email" required></label>
            <label>Password (8+ chars) <input name="password" type="password" required minlength="8"></label>
            <button type="submit">Create account</button>
        </form>
    </div>

    <script src="script.js"></script>
</body>

</html>