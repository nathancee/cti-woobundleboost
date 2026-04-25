<?php
define( 'CTI_LS', 1 );
require __DIR__ . '/includes/config.php';

$valid_plans = [ 'single', 'agency', 'enterprise' ];
$plan        = in_array( $_GET['plan'] ?? '', $valid_plans, true ) ? $_GET['plan'] : 'single';

$plan_info = [
    'single'     => [ 'label' => 'Single Site',            'price' => PRICE_SINGLE,     'sites' => '1 site' ],
    'agency'     => [ 'label' => 'Agency (5 Sites)',        'price' => PRICE_AGENCY,     'sites' => '5 sites' ],
    'enterprise' => [ 'label' => 'Developer / Enterprise', 'price' => PRICE_ENTERPRISE, 'sites' => '100 sites' ],
];
$info = $plan_info[ $plan ];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Checkout — CTI WooBundleBoost</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<nav class="site-nav">
  <div class="nav-inner">
    <a class="nav-logo" href="/">CTI <span>WooBundleBoost</span></a>
  </div>
</nav>

<div class="checkout-wrap">

  <!-- Order Summary -->
  <div class="order-summary">
    <h2>Order Summary</h2>
    <div class="summary-row"><span>Plan</span><span><?php echo htmlspecialchars( $info['label'] ); ?></span></div>
    <div class="summary-row"><span>Activations</span><span><?php echo htmlspecialchars( $info['sites'] ); ?></span></div>
    <div class="summary-row"><span>Updates</span><span>Lifetime</span></div>
    <div class="summary-row"><span>Type</span><span>One-time payment</span></div>
    <div class="summary-row"><span>Total</span><span>$<?php echo number_format( $info['price'], 2 ); ?></span></div>

    <ul class="plan-features-mini" style="margin-top:24px;">
      <li><span class="check-icon" style="background:#dcfce7;color:#15803d;">&#10003;</span> All Pro features included</li>
      <li><span class="check-icon" style="background:#dcfce7;color:#15803d;">&#10003;</span> Lifetime updates</li>
      <li><span class="check-icon" style="background:#dcfce7;color:#15803d;">&#10003;</span> 30-day money-back guarantee</li>
      <li><span class="check-icon" style="background:#dcfce7;color:#15803d;">&#10003;</span> License key delivered by email</li>
    </ul>

    <p style="margin-top:24px;font-size:13px;color:#94a3b8;">
      Want a different plan? <a href="/#pricing" style="color:#2563eb;">Back to pricing</a>
    </p>
  </div>

  <!-- Checkout Form -->
  <div class="checkout-form">
    <h2>Complete Your Purchase</h2>
    <p>Your license key will be emailed instantly after payment.</p>

    <div class="form-group">
      <label for="buyer-email">Email Address *</label>
      <input type="email" id="buyer-email" placeholder="you@example.com" required>
    </div>

    <button id="checkout-btn" class="btn-stripe">
      <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="white" style="margin-right:8px;flex-shrink:0;"><path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/></svg>
      Pay $<?php echo number_format( $info['price'], 2 ); ?> with Stripe
    </button>

    <div id="checkout-error" style="color:#dc2626;font-size:13px;margin-top:8px;display:none;"></div>
    <p class="secure-note">&#128274; Secured by Stripe. We never see your payment details.</p>
  </div>

</div>

<script>
document.getElementById('checkout-btn').addEventListener('click', async function () {
    var email = document.getElementById('buyer-email').value.trim();
    var errEl = document.getElementById('checkout-error');
    errEl.style.display = 'none';

    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errEl.textContent = 'Please enter a valid email address.';
        errEl.style.display = 'block';
        return;
    }

    this.disabled = true;
    this.textContent = 'Redirecting to Stripe…';

    try {
        var res = await fetch('stripe-create-session.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ plan: '<?php echo $plan; ?>', email: email })
        });
        var data = await res.json();

        if (data.error) throw new Error(data.error);
        window.location.href = data.url;

    } catch (err) {
        errEl.textContent = err.message || 'Something went wrong. Please try again.';
        errEl.style.display = 'block';
        this.disabled = false;
        this.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="white" style="margin-right:8px;flex-shrink:0;"><path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-1.875 0-4.965-.921-6.99-2.109l-.9 5.555C5.175 22.99 8.385 24 11.714 24c2.641 0 4.843-.624 6.328-1.813 1.664-1.305 2.525-3.236 2.525-5.732 0-4.128-2.524-5.851-6.591-7.305z"/></svg>Pay $<?php echo number_format( $info['price'], 2 ); ?> with Stripe';
    }
});
</script>
</body>
</html>
