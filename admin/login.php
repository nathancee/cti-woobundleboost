<?php
define( 'CTI_LS', 1 );
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/auth.php';

if ( Auth::is_logged_in() ) {
    header( 'Location: dashboard.php' );
    exit;
}

$error = '';
if ( $_SERVER['REQUEST_METHOD'] === 'POST' ) {
    $user = $_POST['username'] ?? '';
    $pass = $_POST['password'] ?? '';
    if ( Auth::login( $user, $pass ) ) {
        header( 'Location: dashboard.php' );
        exit;
    }
    $error = 'Invalid username or password.';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin Login — CTI License Server</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="login-wrap">
  <div class="login-card">
    <div class="login-logo">CTI <span>WooBundleBoost</span><br><small style="font-size:12px;color:#475569;font-weight:400;">License Admin</small></div>
    <?php if ( $error ) : ?>
      <div class="login-error"><?php echo htmlspecialchars( $error ); ?></div>
    <?php endif; ?>
    <form method="POST">
      <div class="form-group">
        <label>Username</label>
        <input type="text" name="username" autocomplete="username" required placeholder="admin">
      </div>
      <div class="form-group">
        <label>Password</label>
        <input type="password" name="password" autocomplete="current-password" required>
      </div>
      <button type="submit" class="btn btn-primary">Sign In</button>
    </form>
  </div>
</div>
</body>
</html>
