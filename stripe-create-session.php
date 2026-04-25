<?php
define( 'CTI_LS', 1 );
require __DIR__ . '/includes/config.php';
require __DIR__ . '/includes/stripe.php';

header( 'Content-Type: application/json' );

if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
    http_response_code( 405 );
    echo json_encode( [ 'error' => 'Method not allowed.' ] );
    exit;
}

try {
    $body  = json_decode( file_get_contents( 'php://input' ), true ) ?? [];
    $valid = [ 'single', 'agency', 'enterprise' ];
    $plan  = in_array( $body['plan'] ?? '', $valid, true ) ? $body['plan'] : 'single';
    $email = filter_var( trim( $body['email'] ?? '' ), FILTER_VALIDATE_EMAIL ) ?: '';

    $prices = [
        'single'     => PRICE_SINGLE,
        'agency'     => PRICE_AGENCY,
        'enterprise' => PRICE_ENTERPRISE,
    ];
    $labels = [
        'single'     => 'CTI WooBundleBoost — Single Site',
        'agency'     => 'CTI WooBundleBoost — Agency (5 Sites)',
        'enterprise' => 'CTI WooBundleBoost — Developer / Enterprise (100 Sites)',
    ];

    $session = Stripe::create_checkout_session( $plan, $prices[ $plan ], $labels[ $plan ], $email );
    echo json_encode( [ 'url' => $session['url'] ] );

} catch ( Throwable $e ) {
    http_response_code( 500 );
    error_log( 'Stripe session error: ' . $e->getMessage() );
    echo json_encode( [ 'error' => 'Could not create checkout session. Please try again.' ] );
}
