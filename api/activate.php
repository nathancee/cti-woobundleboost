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
    $body      = json_decode( file_get_contents( 'php://input' ), true ) ?? [];
    $key       = sanitize_key_input( $body['license_key'] ?? '' );
    $site_url  = sanitize_url_input( $body['site_url'] ?? '' );
    $site_name = substr( strip_tags( $body['site_name'] ?? '' ), 0, 255 );

    if ( ! $key )      throw new InvalidArgumentException( 'Missing license_key.' );
    if ( ! $site_url ) throw new InvalidArgumentException( 'Missing site_url.' );

    $result = License::activate( $key, $site_url, $site_name );
    echo json_encode( $result );

} catch ( InvalidArgumentException $e ) {
    http_response_code( 400 );
    echo json_encode( [ 'success' => false, 'message' => $e->getMessage() ] );
} catch ( Throwable $e ) {
    http_response_code( 500 );
    error_log( 'CTI activate error: ' . $e->getMessage() );
    echo json_encode( [ 'success' => false, 'message' => 'Server error.' ] );
}

function sanitize_key_input( string $val ): string {
    return preg_replace( '/[^A-Z0-9\-]/', '', strtoupper( trim( $val ) ) );
}

function sanitize_url_input( string $val ): string {
    $url = filter_var( trim( $val ), FILTER_SANITIZE_URL );
    return ( $url && filter_var( $url, FILTER_VALIDATE_URL ) ) ? $url : '';
}
