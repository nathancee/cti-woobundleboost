<?php
$current = basename( $_SERVER['PHP_SELF'], '.php' );
?>
<aside class="sidebar">
  <div class="sidebar-logo">
    <a href="dashboard.php">CTI <span>WooBundleBoost</span></a>
  </div>
  <nav class="sidebar-nav">
    <a href="dashboard.php" class="<?php echo $current === 'dashboard' ? 'active' : ''; ?>">
      <span class="icon">&#128200;</span> Dashboard
    </a>
    <a href="orders.php" class="<?php echo $current === 'orders' ? 'active' : ''; ?>">
      <span class="icon">&#128176;</span> Orders
    </a>
    <a href="licenses.php" class="<?php echo $current === 'licenses' ? 'active' : ''; ?>">
      <span class="icon">&#128273;</span> Licenses
    </a>
  </nav>
  <div class="sidebar-footer">
    <a href="logout.php">&#128274; Sign Out</a>
  </div>
</aside>
