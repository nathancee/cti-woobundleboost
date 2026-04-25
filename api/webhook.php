<?php
/**
 * Stripe webhook endpoint.
 * Register this URL in: Stripe Dashboard → Developers → Webhooks
 * URL: https://your-project.vercel.app/api/webhook.php
 * Event to listen for: checkout.session.completed
 */

define( 'CTI_LS', 1 );
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/stripe.php';
require __DIR__ . '/../includes/license.php';
require __DIR__ . '/../includes/email.php';

header( 'Content-Type: application/json' );

// Must read raw body BEFORE any parsing.
$payload    = file_get_contents( 'php://input' );
$sig_header = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';

try {
    $event = Stripe::verify_webhook( $payload, $sig_header );
} catch ( Throwable $e ) {
    http_response_code( 400 );
    echo json_encode( [ 'error' => $e->getMessage() ] );
    exit;
}

// Only act on successful payment completions.
if ( $event['type'] !== 'checkout.session.completed' ) {
    echo json_encode( [ 'received' => true ] );
    exit;
}

$session = $event['data']['object'];

if ( ( $session['payment_status'] ?? '' ) !== 'paid' ) {
    echo json_encode( [ 'received' => true ] );
    exit;
}

$stripe_session_id = $session['id'];
$email             = $session['customer_details']['email'] ?? $session['customer_email'] ?? '';
$plan              = $session['metadata']['plan'] ?? 'single';
$amount            = ( $session['amount_total'] ?? 0 ) / 100;
$currency          = strtoupper( $session['currency'] ?? STRIPE_CURRENCY );

// Idempotency — skip if this session was already processed.
if ( DB::val( 'SELECT id FROM orders WHERE paypal_id = ?', [ $stripe_session_id ] ) ) {
    echo json_encode( [ 'received' => true ] );
    exit;
}

try {
    $order_id = (int) DB::insert(
        'INSERT INTO orders (paypal_id, email, plan, amount, currency, status, created_at)
         VALUES (?, ?, ?, ?, ?, "completed", NOW())',
        [ $stripe_session_id, $email, $plan, $amount, $currency ]
    );

    $license_key = License::create( $order_id, $email, $plan );
    Email::send_license( $email, $license_key, $plan );

    echo json_encode( [ 'received' => true ] );

} catch ( Throwable $e ) {
    error_log( 'Webhook fulfillment error: ' . $e->getMessage() );
    http_response_code( 500 );
    echo json_encode( [ 'error' => 'Fulfillment failed — will retry.' ] );
}
