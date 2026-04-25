<?php
define( 'CTI_LS', 1 );
require __DIR__ . '/includes/config.php';
$plan = htmlspecialchars( $_GET['plan'] ?? 'single' );
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Cancelled — CTI WooBundleBoost</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<nav class="site-nav">
  <div class="nav-inner">
    <a class="nav-logo" href="/">CTI <span>WooBundleBoost</span></a>
  </div>
</nav>
<div class="success-wrap">
  <div class="success-icon">&#128533;</div>
  <h1>Payment Cancelled</h1>
  <p>No charge was made. You can try again whenever you're ready.</p>
  <a href="checkout.php?plan=<?php echo $plan; ?>" class="btn-primary" style="margin-right:12px;">Try Again</a>
  <a href="/" class="btn-secondary" style="border-color:#e2e8f0;color:#475569;">Back to Home</a>
</div>
</body>
</html>
