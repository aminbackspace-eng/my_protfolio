<?php
session_start();
require_once __DIR__ . '/../includes/db.php';

if (isset($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['admin_id'] = $user['id'];
        $_SESSION['admin_name'] = $user['full_name'];
        header('Location: index.php');
        exit;
    } else {
        $error = "Access denied. Invalid credentials.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Secure Access | Portfolio CMS</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body class="login-page">
    <div class="card login-card">
        <div style="text-align: center; margin-bottom: 40px;">
            <div
                style="width: 60px; height: 60px; background: var(--gradient); border-radius: 15px; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px; font-size: 1.5rem; color: #fff;">
                <i class="fas fa-shield-alt"></i>
            </div>
            <h1 style="font-size: 1.6rem; margin-bottom: 8px;">Admin Login</h1>
            <p style="color: var(--text-muted); font-size: 0.9rem;">Sign in to manage your portfolio</p>
        </div>

        <?php if ($error): ?>
            <div
                style="background: rgba(255, 77, 77, 0.1); color: #ff4d4d; padding: 12px; border-radius: 10px; text-align: center; margin-bottom: 25px; font-size: 0.9rem; border: 1px solid rgba(255, 77, 77, 0.2);">
                <i class="fas fa-exclamation-circle" style="margin-right: 8px;"></i> <?php echo $error; ?>
            </div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label><i class="fas fa-user" style="margin-right: 8px; color: var(--primary);"></i> Username</label>
                <input type="text" name="username" class="form-control" placeholder="Enter your username" required
                    autofocus>
            </div>
            <div class="form-group">
                <label><i class="fas fa-lock" style="margin-right: 8px; color: var(--primary);"></i> Password</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 10px; padding: 14px;">
                Secure Login <i class="fas fa-arrow-right" style="margin-left: 8px;"></i>
            </button>
        </form>

        <div style="text-align: center; margin-top: 35px; padding-top: 25px; border-top: 1px solid var(--border);">
            <p style="color: var(--text-muted); font-size: 0.85rem;">
                Forgot password? Contact system admin.
            </p>
            <p style="color: var(--text-muted); font-size: 0.75rem; margin-top: 10px;">
                Auth Key: admin / admin123
            </p>
        </div>
    </div>
</body>

</html>