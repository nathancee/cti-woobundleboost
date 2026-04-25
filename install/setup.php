<?php
/**
 * CTI License Server — One-time Setup Wizard
 * DELETE THIS FILE after setup is complete.
 */
define( 'CTI_LS', 1 );
require __DIR__ . '/../includes/config.php';

$errors  = [];
$success = '';
$step    = isset( $_POST['step'] ) ? (int) $_POST['step'] : 0;

// ── Step 0: check requirements ────────────────────────────────────────────────
$php_ok  = version_compare( PHP_VERSION, '8.0', '>=' );
$pdo_ok  = extension_loaded( 'pdo_mysql' );
$curl_ok = extension_loaded( 'curl' );

// ── Step 1: create tables ─────────────────────────────────────────────────────
if ( $step === 1 && $php_ok && $pdo_ok ) {
    try {
        $dsn = sprintf( 'mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET );
        $pdo = new PDO( $dsn, DB_USER, DB_PASS, [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ] );
        $sql = file_get_contents( __DIR__ . '/schema.sql' );
        foreach ( array_filter( array_map( 'trim', explode( ';', $sql ) ) ) as $stmt ) {
            if ( $stmt ) $pdo->exec( $stmt );
        }
        $success = 'Database tables created successfully!';
    } catch ( PDOException $e ) {
        $errors[] = 'Database error: ' . $e->getMessage();
    }
}

// ── Step 2: generate password hash ────────────────────────────────────────────
$hash_result = '';
if ( $step === 2 && ! empty( $_POST['password'] ) ) {
    $hash_result = password_hash( $_POST['password'], PASSWORD_DEFAULT );
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>CTI License Server — Setup</title>
<style>
  * { box-sizing: border-box; }
  body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f8fafc; color: #1e293b; margin: 0; padding: 40px 16px; }
  .card { max-width: 640px; margin: 0 auto; background: #fff; border-radius: 12px; box-shadow: 0 1px 4px rgba(0,0,0,.1); overflow: hidden; }
  .card-header { background: #2563eb; padding: 24px 32px; }
  .card-header h1 { margin: 0; color: #fff; font-size: 20px; }
  .card-body { padding: 32px; }
  h2 { font-size: 17px; margin: 0 0 16px; }
  .check { display: flex; align-items: center; gap: 10px; padding: 10px 0; border-bottom: 1px solid #f1f5f9; }
  .badge { font-size: 12px; font-weight: 600; padding: 3px 10px; border-radius: 20px; }
  .ok { background: #dcfce7; color: #15803d; }
  .fail { background: #fee2e2; color: #dc2626; }
  .btn { display: inline-block; background: #2563eb; color: #fff; border: none; padding: 10px 22px; border-radius: 6px; font-size: 14px; font-weight: 600; cursor: pointer; text-decoration: none; }
  .btn:hover { background: #1d4ed8; }
  input[type=password], input[type=text] { width: 100%; padding: 9px 12px; border: 1px solid #e2e8f0; border-radius: 6px; font-size: 14px; margin-top: 4px; }
  label { font-size: 13px; font-weight: 600; color: #475569; }
  .hash-box { background: #f1f5f9; border: 1px solid #e2e8f0; border-radius: 6px; padding: 12px 16px; font-family: monospace; font-size: 13px; word-break: break-all; margin-top: 12px; }
  .alert { padding: 12px 16px; border-radius: 6px; margin-bottom: 20px; font-size: 14px; }
  .alert-success { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
  .alert-error { background: #fee2e2; color: #dc2626; border: 1px solid #fecaca; }
  .section { margin-bottom: 28px; }
  .section h2 { border-bottom: 2px solid #2563eb; padding-bottom: 8px; color: #1e293b; }
</style>
</head>
<body>
<div class="card">
  <div class="card-header">
    <h1>&#9632; CTI License Server — Setup</h1>
  </div>
  <div class="card-body">

    <?php if ( $success ) : ?>
      <div class="alert alert-success"><?php echo htmlspecialchars( $success ); ?></div>
    <?php endif; ?>
    <?php foreach ( $errors as $e ) : ?>
      <div class="alert alert-error"><?php echo htmlspecialchars( $e ); ?></div>
    <?php endforeach; ?>

    <!-- Requirements -->
    <div class="section">
      <h2>1. Requirements</h2>
      <div class="check">PHP 8.0+ <span class="badge <?php echo $php_ok ? 'ok' : 'fail'; ?>"><?php echo PHP_VERSION; ?></span></div>
      <div class="check">PDO MySQL extension <span class="badge <?php echo $pdo_ok ? 'ok' : 'fail'; ?>"><?php echo $pdo_ok ? 'Enabled' : 'Missing'; ?></span></div>
      <div class="check">cURL extension <span class="badge <?php echo $curl_ok ? 'ok' : 'fail'; ?>"><?php echo $curl_ok ? 'Enabled' : 'Missing'; ?></span></div>
      <div class="check">Database (<?php echo htmlspecialchars( DB_NAME ); ?> @ <?php echo htmlspecialchars( DB_HOST ); ?>)
        <span class="badge <?php
          try { new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASS); echo 'ok'; } catch(Exception $ex) { echo 'fail'; }
        ?>"><?php
          try { new PDO('mysql:host='.DB_HOST.';dbname='.DB_NAME.';charset='.DB_CHARSET, DB_USER, DB_PASS); echo 'Connected'; } catch(Exception $ex) { echo 'Error'; }
        ?></span>
      </div>
    </div>

    <!-- Create tables -->
    <div class="section">
      <h2>2. Create Database Tables</h2>
      <p style="font-size:14px;color:#475569;">This will create the <code>orders</code>, <code>licenses</code>, and <code>activations</code> tables. Safe to run multiple times.</p>
      <form method="POST">
        <input type="hidden" name="step" value="1">
        <button type="submit" class="btn">Run Database Setup</button>
      </form>
    </div>

    <!-- Admin password -->
    <div class="section">
      <h2>3. Generate Admin Password Hash</h2>
      <p style="font-size:14px;color:#475569;">Enter your desired admin password below. Copy the hash into <code>includes/config.php</code> → <code>ADMIN_PASSWORD_HASH</code>.</p>
      <form method="POST">
        <input type="hidden" name="step" value="2">
        <label>Admin Password</label>
        <input type="password" name="password" required>
        <br><br>
        <button type="submit" class="btn">Generate Hash</button>
      </form>
      <?php if ( $hash_result ) : ?>
        <div class="hash-box"><?php echo htmlspecialchars( $hash_result ); ?></div>
        <p style="font-size:13px;color:#64748b;margin-top:8px;">Copy the hash above into <code>ADMIN_PASSWORD_HASH</code> in <code>includes/config.php</code>.</p>
      <?php endif; ?>
    </div>

    <!-- Final checklist -->
    <div class="section">
      <h2>4. Final Checklist</h2>
      <ul style="font-size:14px;color:#475569;line-height:1.8;">
        <li>Fill in PayPal Client ID &amp; Secret in <code>config.php</code></li>
        <li>Set <code>PAYPAL_MODE</code> to <code>live</code> for production</li>
        <li>Set <code>SITE_URL</code> to your actual domain</li>
        <li>Set <code>MAIL_FROM</code> to your support email</li>
        <li>Replace <code>LICENSE_API_SECRET</code> with a random 32+ char string</li>
        <li><strong>Delete this setup.php file when done!</strong></li>
      </ul>
    </div>

  </div>
</div>
</body>
</html>
