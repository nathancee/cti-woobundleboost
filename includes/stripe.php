<?php
defined( 'CTI_LS' ) || die;

class Stripe {

    private static function request( string $method, string $path, array $params = [] ): array {
        $url = 'https://api.stripe.com/v1' . $path;

        if ( $method === 'GET' && $params ) {
            $url .= '?' . http_build_query( $params );
        }

        $opts = [
            'http' => [
                'method'        => $method,
                'header'        => "Authorization: Bearer " . STRIPE_SECRET_KEY . "\r\n" .
                                   "Content-Type: application/x-www-form-urlencoded\r\n",
                'ignore_errors' => true,
            ],
        ];

        if ( $method === 'POST' && $params ) {
            $opts['http']['content'] = http_build_query( $params );
        }

        $ctx      = stream_context_create( $opts );
        $response = file_get_contents( $url, false, $ctx );

        if ( $response === false ) {
            throw new RuntimeException( 'Stripe request failed: network error.' );
        }

        $data = json_decode( $response, true );

        if ( isset( $data['error'] ) ) {
            throw new RuntimeException( 'Stripe error: ' . ( $data['error']['message'] ?? 'Unknown error' ) );
        }

        return $data;
    }

    /**
     * Create a hosted Checkout Session and return the full session object.
     * Redirect the user to $session['url'].
     */
    public static function create_checkout_session( string $plan, float $amount, string $description, string $email = '' ): array {
        $params = [
            'mode'                                           => 'payment',
            'line_items[0][price_data][currency]'            => STRIPE_CURRENCY,
            'line_items[0][price_data][product_data][name]'  => $description,
            'line_items[0][price_data][unit_amount]'         => (int) round( $amount * 100 ),
            'line_items[0][quantity]'                        => 1,
            'success_url'                                    => SITE_URL . '/success.php?session_id={CHECKOUT_SESSION_ID}',
            'cancel_url'                                     => SITE_URL . '/cancel.php',
            'metadata[plan]'                                 => $plan,
            'payment_intent_data[metadata][plan]'            => $plan,
        ];

        if ( $email ) {
            $params['customer_email'] = $email;
        }

        return self::request( 'POST', '/checkout/sessions', $params );
    }

    /**
     * Retrieve a Checkout Session from Stripe (used on success page).
     */
    public static function retrieve_session( string $session_id ): array {
        return self::request( 'GET', '/checkout/sessions/' . rawurlencode( $session_id ) );
    }

    /**
     * Verify and decode an incoming Stripe webhook.
     * Returns the decoded event array, or throws on invalid signature.
     */
    public static function verify_webhook( string $payload, string $sig_header ): array {
        if ( empty( STRIPE_WEBHOOK_SECRET ) ) {
            throw new RuntimeException( 'Webhook secret not configured.' );
        }

        $parts = [];
        foreach ( explode( ',', $sig_header ) as $item ) {
            [ $k, $v ] = array_pad( explode( '=', $item, 2 ), 2, '' );
            $parts[ $k ][] = $v;
        }

        $timestamp  = $parts['t'][0]  ?? '0';
        $signatures = $parts['v1'] ?? [];

        if ( ! $timestamp || ! $signatures ) {
            throw new RuntimeException( 'Invalid Stripe-Signature header.' );
        }

        // Reject replayed events older than 5 minutes.
        if ( abs( time() - (int) $timestamp ) > 300 ) {
            throw new RuntimeException( 'Webhook timestamp too old (possible replay attack).' );
        }

        $expected = hash_hmac( 'sha256', $timestamp . '.' . $payload, STRIPE_WEBHOOK_SECRET );

        foreach ( $signatures as $sig ) {
            if ( hash_equals( $expected, $sig ) ) {
                return json_decode( $payload, true );
            }
        }

        throw new RuntimeException( 'Webhook signature verification failed.' );
    }
}
