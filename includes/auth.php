<?php
defined( 'CTI_LS' ) || die;

class Auth {

    private static string $cookie = 'cti_admin_token';
    private static int    $ttl    = 86400; // 24 hours

    /** No-op — kept for backward compatibility (sessions no longer used). */
    public static function start(): void {}

    public static function is_logged_in(): bool {
        $token = $_COOKIE[ self::$cookie ] ?? '';
        return $token && self::verify_token( $token );
    }

    public static function check(): void {
        if ( ! self::is_logged_in() ) {
            header( 'Location: ' . SITE_URL . '/admin/login.php' );
            exit;
        }
    }

    public static function login( string $username, string $password ): bool {
        if ( $username !== ADMIN_USERNAME ) return false;
        if ( ! password_verify( $password, ADMIN_PASSWORD_HASH ) ) return false;

        setcookie( self::$cookie, self::create_token(), [
            'expires'  => time() + self::$ttl,
            'path'     => '/',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ] );
        return true;
    }

    public static function logout(): void {
        setcookie( self::$cookie, '', [
            'expires'  => time() - 3600,
            'path'     => '/',
            'secure'   => true,
            'httponly' => true,
            'samesite' => 'Lax',
        ] );
    }

    /**
     * Deterministic CSRF token: HMAC of today's UTC date.
     * Rotates at midnight UTC; valid for the current calendar day.
     */
    public static function csrf_token(): string {
        return hash_hmac( 'sha256', gmdate( 'Y-m-d' ), ADMIN_SECRET );
    }

    public static function verify_csrf( string $token ): bool {
        return hash_equals( self::csrf_token(), $token );
    }

    // ── Internal ──────────────────────────────────────────────────────────────

    private static function create_token(): string {
        $payload = time() . '.' . bin2hex( random_bytes( 12 ) );
        $sig     = hash_hmac( 'sha256', $payload, ADMIN_SECRET );
        return base64_encode( $payload . '.' . $sig );
    }

    private static function verify_token( string $token ): bool {
        $decoded = base64_decode( $token, true );
        if ( ! $decoded ) return false;

        $last_dot = strrpos( $decoded, '.' );
        if ( $last_dot === false ) return false;

        $payload = substr( $decoded, 0, $last_dot );
        $sig     = substr( $decoded, $last_dot + 1 );

        if ( ! hash_equals( hash_hmac( 'sha256', $payload, ADMIN_SECRET ), $sig ) ) return false;

        $ts = (int) explode( '.', $payload )[0];
        return ( time() - $ts ) < self::$ttl;
    }
}
