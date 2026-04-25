<?php
define( 'CTI_LS', 1 );
require __DIR__ . '/../includes/config.php';
require __DIR__ . '/../includes/db.php';
require __DIR__ . '/../includes/license.php';

header( 'Content-Type: application/json' );
header( 'Access-Control-Allow-Origin: *' );

if ( $_SERVER['REQUEST_METHOD'] !== 'POST' ) {
    http_response_code( 405 );
    echo json_encode( [ 'success' => false, 'message' => 'Method not allowed.' ] );
    exit;
}

try {
    $body     = json_decode( file_get_contents( 'php://input' ), true ) ?? [];
    $key      = preg_replace( '/[^A-Z0-9\-]/', '', strtoupper( trim( $body['license_key'] ?? '' ) ) );
    $site_url = filter_var( trim( $body['site_url'] ?? '' ), FILTER_SANITIZE_URL );

    if ( ! $key )      throw new InvalidArgumentException( 'Missing license_key.' );
    if ( ! $site_url ) throw new InvalidArgumentException( 'Missing site_url.' );

    $result = License::deactivate( $key, $site_url );
    echo json_encode( $result );

} catch ( InvalidArgumentException $e ) {
    http_response_code( 400 );
    echo json_encode( [ 'success' => false, 'message' => $e->getMessage() ] );
} catch ( Throwable $e ) {
    http_response_code( 500 );
    echo json_encode( [ 'success' => false, 'message' => 'Server error.' ] );
}
