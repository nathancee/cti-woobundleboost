<?php
define( 'CTI_LS', 1 );
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/auth.php';
require __DIR__ . '/../includes/license.php';
Auth::check();

$per_page = 25;
$page     = max( 1, (int) ( $_GET['p'] ?? 1 ) );
$search   = trim( $_GET['s'] ?? '' );
$offset   = ( $page - 1 ) * $per_page;

if ( $search ) {
    $like    = '%' . $search . '%';
    $total   = (int) DB::val( 'SELECT COUNT(*) FROM orders WHERE email LIKE ? OR paypal_id LIKE ?', [ $like, $like ] );
    $orders  = DB::rows( 'SELECT o.*, l.license_key FROM orders o LEFT JOIN licenses l ON l.order_id = o.id WHERE o.email LIKE ? OR o.paypal_id LIKE ? ORDER BY o.created_at DESC LIMIT ? OFFSET ?', [ $like, $like, $per_page, $offset ] );
} else {
    $total   = (int) DB::val( 'SELECT COUNT(*) FROM orders' );
    $orders  = DB::rows( 'SELECT o.*, l.license_key FROM orders o LEFT JOIN licenses l ON l.order_id = o.id ORDER BY o.created_at DESC LIMIT ? OFFSET ?', [ $per_page, $offset ] );
}
$pages = (int) ceil( $total / $per_page );
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Orders — CTI License Admin</title>
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<div class="admin-layout">
  <?php include __DIR__ . '/partials/sidebar.php'; ?>
  <div class="main-content">
    <div class="topbar">
      <h1>Orders <span style="font-size:14px;font-weight:400;color:#94a3b8;">(<?php echo $total; ?> total)</span></h1>
      <div class="topbar-right">
        <a href="logout.php" class="btn btn-secondary btn-sm">Sign Out</a>
      </div>
    </div>
    <div class="page-body">
      <div class="card">
        <div class="search-row">
          <form method="GET" style="display:flex;gap:8px;align-items:center;">
            <input type="search" name="s" value="<?php echo htmlspecialchars( $search ); ?>" placeholder="Search email or PayPal ID…">
            <button type="submit" class="btn btn-secondary btn-sm">Search</button>
            <?php if ( $search ) : ?><a href="orders.php" class="btn btn-secondary btn-sm">Clear</a><?php endif; ?>
          </form>
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr>
              <th>Date</th>
              <th>Email</th>
              <th>Plan</th>
              <th>Amount</th>
              <th>License Key</th>
              <th>Transaction ID</th>
              <th>Status</th>
            </tr></thead>
            <tbody>
            <?php foreach ( $orders as $o ) : ?>
            <tr>
              <td><?php echo date( 'M j, Y H:i', strtotime( $o['created_at'] ) ); ?></td>
              <td><?php echo htmlspecialchars( $o['email'] ); ?></td>
              <td><span class="badge badge-<?php echo $o['plan']; ?>"><?php echo htmlspecialchars( License::plan_label( $o['plan'] ) ); ?></span></td>
              <td>$<?php echo number_format( $o['amount'], 2 ); ?> <?php echo htmlspecialchars( $o['currency'] ); ?></td>
              <td class="mono"><?php echo htmlspecialchars( $o['license_key'] ?? '—' ); ?></td>
              <td class="mono" style="font-size:11px;color:#94a3b8;"><?php echo htmlspecialchars( $o['paypal_id'] ); ?></td>
              <td><span class="badge badge-<?php echo $o['status']; ?>"><?php echo ucfirst( $o['status'] ); ?></span></td>
            </tr>
            <?php endforeach; ?>
            <?php if ( ! $orders ) : ?>
            <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:32px;">No orders found.</td></tr>
            <?php endif; ?>
            </tbody>
          </table>
        </div>
        <?php if ( $pages > 1 ) : ?>
        <div class="pagination">
          <?php for ( $i = 1; $i <= $pages; $i++ ) : ?>
            <a href="?p=<?php echo $i; ?><?php echo $search ? '&s=' . urlencode( $search ) : ''; ?>"
               class="<?php echo $i === $page ? 'current' : ''; ?>"><?php echo $i; ?></a>
          <?php endfor; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
</body>
</html>
