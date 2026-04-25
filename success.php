<?php
define( 'CTI_LS', 1 );
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/stripe.php';
require __DIR__ . '/includes/db.php';

$session_id  = preg_replace( '/[^a-zA-Z0-9_]/', '', $_GET['session_id'] ?? '' );
$paid        = false;
$email       = '';
$plan        = 'single';
$license_key = '';

if ( $session_id ) {
    try {
        $session = Stripe::retrieve_session( $session_id );
        $paid    = ( $session['payment_status'] ?? '' ) === 'paid';
        $email   = $session['customer_details']['email'] ?? $session['customer_email'] ?? '';
        $plan    = $session['metadata']['plan'] ?? 'single';

        // Look up the license key issued by the webhook (may not exist yet if webhook is delayed).
        $row = DB::row(
            'SELECT l.license_key FROM orders o
             JOIN licenses l ON l.order_id = o.id
             WHERE o.paypal_id = ?',
            [ $session_id ]
        );
        $license_key = $row['license_key'] ?? '';

    } catch ( Throwable $e ) {
        // Leave $paid as false — show processing message.
    }
}

$plan_map = [
    'single'     => 'Single Site',
    'agency'     => 'Agency (5 Sites)',
    'enterprise' => 'Developer / Enterprise (100 Sites)',
];
$plan_label = $plan_map[ $plan ] ?? 'Pro';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?php echo $paid ? 'Thank You!' : 'Order Processing'; ?> — CTI WooBundleBoost</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="site-nav">
  <div class="nav-inner">
    <a class="nav-logo" href="/">CTI <span>WooBundleBoost</span></a>
  </div>
</nav>

<div class="success-wrap">

<?php if ( $paid ): ?>

  <div class="success-icon">&#127881;</div>
  <h1>You&#8217;re all set!</h1>
  <p>Thank you for purchasing <strong>CTI WooBundleBoost &#8212; <?php echo htmlspecialchars( $plan_label ); ?></strong>.<br>
     Your license key has been sent to <strong><?php echo htmlspecialchars( $email ); ?></strong>.</p>

  <?php if ( $license_key ): ?>
  <div class="license-box">
    <div class="label">Your License Key</div>
    <div class="license-key" id="license-key"><?php echo htmlspecialchars( $license_key ); ?></div>
    <button onclick="copyKey()" style="margin-top:14px;padding:7px 18px;background:#2563eb;color:#fff;border:none;border-radius:6px;font-size:13px;font-weight:600;cursor:pointer;" id="copy-btn">Copy Key</button>
  </div>
  <?php else: ?>
  <div class="license-box" style="border-color:#fbbf24;">
    <div class="label" style="color:#92400e;">License key on its way</div>
    <p style="margin:0;font-size:14px;color:#78350f;">Check your inbox at <strong><?php echo htmlspecialchars( $email ); ?></strong>.<br>It usually arrives within 30 seconds.</p>
  </div>
  <?php endif; ?>

  <div class="steps-list">
    <h3>How to activate</h3>
    <ol>
      <li>Install and activate <strong>CTI WooBundleBoost</strong> on your WordPress site.</li>
      <li>Go to <strong>WooCommerce &rarr; Bundle Pricing &rarr; License</strong>.</li>
      <li>Paste your license key and click <strong>Activate License</strong>.</li>
      <li>All Pro features are now unlocked!</li>
    </ol>
  </div>

  <p style="margin-top:28px;font-size:14px;color:#64748b;">
    Questions? Email us at <a href="mailto:<?php echo htmlspecialchars( MAIL_FROM ); ?>"><?php echo htmlspecialchars( MAIL_FROM ); ?></a>
  </p>
  <a href="/" style="display:inline-block;margin-top:20px;font-size:14px;color:#2563eb;">&#8592; Back to home</a>

<?php else: ?>

  <div class="success-icon">&#8987;</div>
  <h1>Processing Your Order&hellip;</h1>
  <p>Your payment is being confirmed. Your license key will arrive by email shortly.<br>
     If you don&#8217;t receive it within 5 minutes, please contact support.</p>
  <a href="mailto:<?php echo htmlspecialchars( MAIL_FROM ); ?>" class="btn-primary" style="display:inline-block;color:#1e3a8a;">Contact Support</a>

<?php endif; ?>

</div>

<script>
function copyKey() {
    var key = document.getElementById('license-key').textContent;
    navigator.clipboard.writeText(key).then(function() {
        var btn = document.getElementById('copy-btn');
        btn.textContent = 'Copied!';
        btn.style.background = '#15803d';
        setTimeout(function() {
            btn.textContent = 'Copy Key';
            btn.style.background = '#2563eb';
        }, 2000);
    });
}
</script>
</body>
</html>
