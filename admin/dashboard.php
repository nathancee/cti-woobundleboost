<?php
define( 'CTI_LS', 1 );
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/license.php';
Auth::check();

$total_revenue  = (float) DB::val( "SELECT COALESCE(SUM(amount),0) FROM orders WHERE status='completed'" );
$total_orders   = (int)   DB::val( "SELECT COUNT(*) FROM orders WHERE status='completed'" );
$total_licenses = (int)   DB::val( "SELECT COUNT(*) FROM licenses" );
$total_sites    = (int)   DB::val( "SELECT COUNT(*) FROM activations" );

$recent_orders = DB::rows(
    "SELECT o.*, l.license_key FROM orders o
     LEFT JOIN licenses l ON l.order_id = o.id
     ORDER BY o.created_at DESC LIMIT 10"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Dashboard — CTI License Admin</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <h1>Dashboard</h1>
      <div class="topbar-right">
        <a href="../" target="_blank" class="btn btn-secondary btn-sm">&#127758; View Site</a>
        <a href="logout.php" class="btn btn-secondary btn-sm">Sign Out</a>
      </div>
    </div>
    <div class="page-body">

      <div class="stats-grid">
        <div class="stat-card">
          <div class="label">Total Revenue</div>
          <div class="value">$<?php echo number_format( $total_revenue, 2 ); ?></div>
          <div class="sub">all time</div>
        </div>
        <div class="stat-card">
          <div class="label">Orders</div>
          <div class="value"><?php echo $total_orders; ?></div>
          <div class="sub">completed</div>
        </div>
        <div class="stat-card">
          <div class="label">Licenses Issued</div>
          <div class="value"><?php echo $total_licenses; ?></div>
        </div>
        <div class="stat-card">
          <div class="label">Active Sites</div>
          <div class="value"><?php echo $total_sites; ?></div>
          <div class="sub">across all licenses</div>
        </div>
      </div>

      <div class="card">
        <div class="card-header">
          <h2>Recent Orders</h2>
          <a href="orders.php" class="btn btn-secondary btn-sm">View All</a>
        </div>
        <div class="card-body table-wrap">
          <table>
            <thead><tr>
              <th>Date</th>
              <th>Email</th>
              <th>Plan</th>
              <th>Amount</th>
              <th>License Key</th>
              <th>Status</th>
            </tr></thead>
            <tbody>
            <?php foreach ( $recent_orders as $o ) : ?>
            <tr>
              <td><?php echo htmlspecialchars( date( 'M j, Y', strtotime( $o['created_at'] ) ) ); ?></td>
              <td><?php echo htmlspecialchars( $o['email'] ); ?></td>
              <td><span class="badge badge-<?php echo $o['plan']; ?>"><?php echo htmlspecialchars( License::plan_label( $o['plan'] ) ); ?></span></td>
              <td>$<?php echo number_format( $o['amount'], 2 ); ?></td>
              <td class="mono"><?php echo htmlspecialchars( $o['license_key'] ?? '—' ); ?></td>
              <td><span class="badge badge-<?php echo $o['status']; ?>"><?php echo ucfirst( $o['status'] ); ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php if ( ! $recent_orders ) : ?>
            <tr><td colspan="6" style="text-align:center;color:#94a3b8;padding:32px;">No orders yet.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
</body>
</html>
