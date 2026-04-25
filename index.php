<?php
define( 'CTI_LS', 1 );
require __DIR__ . '/includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>CTI WooBundleBoost — Quantity Bundle Pricing for WooCommerce</title>
<meta name="description" content="Boost your WooCommerce average order value with beautiful, conversion-optimized bundle pricing. Starting at $49 for a single site.">
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- ── Navigation ─────────────────────────────────────────────────────────────── -->
<nav class="site-nav">
  <div class="nav-inner">
    <a class="nav-logo" href="/">CTI <span>WooBundleBoost</span></a>
    <ul class="nav-links">
      <li><a href="#features">Features</a></li>
      <li><a href="#pricing">Pricing</a></li>
      <li><a href="#faq">FAQ</a></li>
      <li><a href="#pricing" class="btn-nav">Get Started</a></li>
    </ul>
  </div>
</nav>

<!-- ── Hero ───────────────────────────────────────────────────────────────────── -->
<section class="hero">
  <div class="container">
    <div class="hero-badge">WooCommerce Plugin</div>
    <h1>Turn Browsers Into Buyers with<br><em>Smart Bundle Pricing</em></h1>
    <p>CTI WooBundleBoost adds a conversion-focused quantity selector to your product pages — proven to increase average order value through smart bundle discounts.</p>
    <div class="hero-cta">
      <a href="#pricing" class="btn-primary">View Pricing</a>
      <a href="#features" class="btn-secondary">See Features</a>
    </div>
    <div class="hero-stats">
      <div class="stat">
        <div class="stat-num">+35%</div>
        <div class="stat-label">Average AOV Increase</div>
      </div>
      <div class="stat">
        <div class="stat-num">5 min</div>
        <div class="stat-label">Setup Time</div>
      </div>
      <div class="stat">
        <div class="stat-num">100%</div>
        <div class="stat-label">WooCommerce Compatible</div>
      </div>
    </div>
  </div>
</section>

<!-- ── Features ───────────────────────────────────────────────────────────────── -->
<section class="section features" id="features">
  <div class="container">
    <div class="section-title">
      <h2>Everything You Need to Sell More</h2>
      <p>Built for WooCommerce store owners who want real results without complex setups.</p>
    </div>
    <div class="features-grid">
      <div class="feature-card">
        <div class="feature-icon">&#128250;</div>
        <h3>Beautiful Bundle Selector</h3>
        <p>Replace the default quantity input with a visual bundle selector that shows savings at a glance — making the upsell effortless.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">&#127775;</div>
        <h3>Flexible Discount Rules</h3>
        <p>Create unlimited bundle tiers with custom quantities, percentage discounts, labels, sub-labels, and promotional badges.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">&#127912;</div>
        <h3>Style Customizer</h3>
        <p>Three built-in presets (Boxed, Bordered, Minimal) with color picker, font size, and border radius controls to match any theme.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">&#128200;</div>
        <h3>Live Admin Preview</h3>
        <p>See exactly how your bundle selector will look on the product page — right inside the WordPress dashboard as you configure it.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">&#128084;</div>
        <h3>Pro Button Styling</h3>
        <p>10 premium Add-to-Cart button presets including Modern Gradient, Luxury Gold, Glassmorphism — with pulse animation and flare effects.</p>
      </div>
      <div class="feature-card">
        <div class="feature-icon">&#128274;</div>
        <h3>Per-Product Overrides</h3>
        <p>Set global defaults and override them on any individual product — full control over which products show which bundle options.</p>
      </div>
    </div>
  </div>
</section>

<!-- ── Pricing ─────────────────────────────────────────────────────────────────── -->
<section class="section" id="pricing">
  <div class="container">
    <div class="section-title">
      <h2>Simple, Transparent Pricing</h2>
      <p>One-time payment. Lifetime updates. No subscriptions.</p>
    </div>
    <div class="pricing-grid">

      <!-- Single Site -->
      <div class="pricing-card">
        <div class="plan-name">Single Site</div>
        <div class="plan-price"><sup>$</sup><?php echo number_format( PRICE_SINGLE, 0 ); ?></div>
        <div class="plan-period">one-time payment</div>
        <div class="plan-sites">1 WordPress Site</div>
        <ul class="plan-features">
          <li><span class="check-icon">&#10003;</span> All Pro features</li>
          <li><span class="check-icon">&#10003;</span> Unlimited bundle tiers</li>
          <li><span class="check-icon">&#10003;</span> 10 button design presets</li>
          <li><span class="check-icon">&#10003;</span> Lifetime plugin updates</li>
          <li><span class="check-icon">&#10003;</span> Email support</li>
        </ul>
        <a href="checkout.php?plan=single" class="btn-buy btn-buy-default">Buy Now</a>
      </div>

      <!-- Agency -->
      <div class="pricing-card featured">
        <div class="popular-badge">Most Popular</div>
        <div class="plan-name">Agency</div>
        <div class="plan-price"><sup>$</sup><?php echo number_format( PRICE_AGENCY, 0 ); ?></div>
        <div class="plan-period">one-time payment</div>
        <div class="plan-sites">Up to 5 WordPress Sites</div>
        <ul class="plan-features">
          <li><span class="check-icon">&#10003;</span> Everything in Single Site</li>
          <li><span class="check-icon">&#10003;</span> 5 site activations</li>
          <li><span class="check-icon">&#10003;</span> Manage multiple clients</li>
          <li><span class="check-icon">&#10003;</span> Lifetime plugin updates</li>
          <li><span class="check-icon">&#10003;</span> Priority email support</li>
        </ul>
        <a href="checkout.php?plan=agency" class="btn-buy btn-buy-featured">Buy Now</a>
      </div>

      <!-- Enterprise -->
      <div class="pricing-card">
        <div class="plan-name">Developer / Enterprise</div>
        <div class="plan-price"><sup>$</sup><?php echo number_format( PRICE_ENTERPRISE, 0 ); ?></div>
        <div class="plan-period">one-time payment</div>
        <div class="plan-sites">Up to 100 WordPress Sites</div>
        <ul class="plan-features">
          <li><span class="check-icon">&#10003;</span> Everything in Agency</li>
          <li><span class="check-icon">&#10003;</span> 100 site activations</li>
          <li><span class="check-icon">&#10003;</span> Build for clients at scale</li>
          <li><span class="check-icon">&#10003;</span> Lifetime plugin updates</li>
          <li><span class="check-icon">&#10003;</span> Priority email support</li>
        </ul>
        <a href="checkout.php?plan=enterprise" class="btn-buy btn-buy-default">Buy Now</a>
      </div>

    </div>
  </div>
</section>

<!-- ── Guarantee ───────────────────────────────────────────────────────────────── -->
<section class="section-sm guarantee">
  <div class="container">
    <div class="guarantee-inner">
      <div class="guarantee-icon">&#128737;</div>
      <h2>30-Day Money-Back Guarantee</h2>
      <p>Not satisfied? Contact us within 30 days of purchase for a full refund, no questions asked.</p>
    </div>
  </div>
</section>

<!-- ── FAQ ────────────────────────────────────────────────────────────────────── -->
<section class="section faq" id="faq">
  <div class="container">
    <div class="section-title">
      <h2>Frequently Asked Questions</h2>
    </div>
    <div class="faq-list">
      <div class="faq-item">
        <button class="faq-q">What is a "site activation"? <span class="arrow">+</span></button>
        <div class="faq-a">Each unique WordPress installation where you activate the plugin counts as one site activation. Your license key controls how many sites you can activate. You can always deactivate a site from your WooCommerce dashboard to free up a slot.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q">Does this work with WooCommerce variable products? <span class="arrow">+</span></button>
        <div class="faq-a">Yes. CTI WooBundleBoost is fully compatible with simple and variable WooCommerce products. Bundle pricing applies per variation.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q">Is the pricing a subscription or one-time? <span class="arrow">+</span></button>
        <div class="faq-a">One-time payment — no subscriptions. You get the plugin plus all future updates for life.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q">What are the requirements? <span class="arrow">+</span></button>
        <div class="faq-a">WordPress 5.8+, WooCommerce 6.0+, and PHP 7.4+. The plugin is tested up to WooCommerce 9.0.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q">How do I get my license key after purchase? <span class="arrow">+</span></button>
        <div class="faq-a">Your license key is emailed immediately after your PayPal payment is confirmed. Check your spam folder if you don't see it within a few minutes.</div>
      </div>
      <div class="faq-item">
        <button class="faq-q">Can I upgrade from Single to Agency later? <span class="arrow">+</span></button>
        <div class="faq-a">Yes. Contact us at <a href="mailto:<?php echo htmlspecialchars( MAIL_FROM ); ?>"><?php echo htmlspecialchars( MAIL_FROM ); ?></a> and we'll provide an upgrade discount.</div>
      </div>
    </div>
  </div>
</section>

<!-- ── Footer ─────────────────────────────────────────────────────────────────── -->
<footer>
  <p>&copy; <?php echo date('Y'); ?> <?php echo htmlspecialchars( SITE_NAME ); ?>. All rights reserved.
    &nbsp;&middot;&nbsp; <a href="mailto:<?php echo htmlspecialchars( MAIL_FROM ); ?>">Support</a>
    &nbsp;&middot;&nbsp; <a href="/admin/">Admin</a>
  </p>
</footer>

<script>
document.querySelectorAll('.faq-q').forEach(function(btn) {
    btn.addEventListener('click', function() {
        this.closest('.faq-item').classList.toggle('open');
    });
});
</script>
</body>
</html>
