<?php
defined( 'CTI_LS' ) || die;

class License {

    public static function generate(): string {
        $segments = [];
        for ( $i = 0; $i < 4; $i++ ) {
            $segments[] = strtoupper( bin2hex( random_bytes( 2 ) ) );
        }
        return 'CTI-' . implode( '-', $segments );
    }

    public static function max_sites( string $plan ): int {
        return match ( $plan ) {
            'agency'     => SITES_AGENCY,
            'enterprise' => SITES_ENTERPRISE,
            default      => SITES_SINGLE,
        };
    }

    public static function create( int $order_id, string $email, string $plan ): string {
        do {
            $key = self::generate();
        } while ( DB::val( 'SELECT id FROM licenses WHERE license_key = ?', [ $key ] ) );

        DB::insert(
            'INSERT INTO licenses (license_key, order_id, email, plan, max_sites, status, created_at)
             VALUES (?, ?, ?, ?, ?, "active", NOW())',
            [ $key, $order_id, $email, $plan, self::max_sites( $plan ) ]
        );
        return $key;
    }

    public static function find( string $key ): ?array {
        return DB::row( 'SELECT * FROM licenses WHERE license_key = ?', [ $key ] );
    }

    public static function activate( string $key, string $site_url, string $site_name ): array {
        $license = self::find( $key );
        if ( ! $license ) {
            return [ 'success' => false, 'message' => 'Invalid license key.' ];
        }
        if ( $license['status'] !== 'active' ) {
            return [ 'success' => false, 'message' => 'License is ' . $license['status'] . '.' ];
        }

        $site_url = rtrim( strtolower( $site_url ), '/' );

        // Already activated on this site?
        $existing = DB::row(
            'SELECT id FROM activations WHERE license_id = ? AND site_url = ?',
            [ $license['id'], $site_url ]
        );
        if ( $existing ) {
            DB::run(
                'UPDATE activations SET last_check = NOW(), site_name = ? WHERE id = ?',
                [ $site_name, $existing['id'] ]
            );
            return [
                'success'    => true,
                'message'    => 'Already activated.',
                'plan'       => $license['plan'],
                'max_sites'  => (int) $license['max_sites'],
                'sites_used' => self::sites_used( $license['id'] ),
            ];
        }

        $used = self::sites_used( $license['id'] );
        if ( $used >= $license['max_sites'] ) {
            return [
                'success' => false,
                'message' => 'Activation limit reached (' . $license['max_sites'] . ' sites). Deactivate an existing site first.',
            ];
        }

        DB::insert(
            'INSERT INTO activations (license_id, site_url, site_name, activated_at, last_check)
             VALUES (?, ?, ?, NOW(), NOW())',
            [ $license['id'], $site_url, $site_name ]
        );

        return [
            'success'    => true,
            'message'    => 'Activated successfully.',
            'plan'       => $license['plan'],
            'max_sites'  => (int) $license['max_sites'],
            'sites_used' => $used + 1,
        ];
    }

    public static function deactivate( string $key, string $site_url ): array {
        $license = self::find( $key );
        if ( ! $license ) {
            return [ 'success' => false, 'message' => 'Invalid license key.' ];
        }

        $site_url = rtrim( strtolower( $site_url ), '/' );

        $deleted = DB::run(
            'DELETE FROM activations WHERE license_id = ? AND site_url = ?',
            [ $license['id'], $site_url ]
        )->rowCount();

        return [
            'success' => true,
            'message' => $deleted ? 'Deactivated successfully.' : 'Site was not activated.',
        ];
    }

    public static function verify( string $key, string $site_url ): array {
        $license = self::find( $key );
        if ( ! $license ) {
            return [ 'valid' => false, 'message' => 'Invalid license key.' ];
        }
        if ( $license['status'] !== 'active' ) {
            return [ 'valid' => false, 'message' => 'License ' . $license['status'] . '.' ];
        }

        $site_url   = rtrim( strtolower( $site_url ), '/' );
        $activation = DB::row(
            'SELECT id FROM activations WHERE license_id = ? AND site_url = ?',
            [ $license['id'], $site_url ]
        );

        if ( ! $activation ) {
            return [ 'valid' => false, 'message' => 'Site not activated.' ];
        }

        DB::run( 'UPDATE activations SET last_check = NOW() WHERE id = ?', [ $activation['id'] ] );

        return [
            'valid'      => true,
            'plan'       => $license['plan'],
            'max_sites'  => (int) $license['max_sites'],
            'sites_used' => self::sites_used( $license['id'] ),
        ];
    }

    public static function sites_used( int $license_id ): int {
        return (int) DB::val( 'SELECT COUNT(*) FROM activations WHERE license_id = ?', [ $license_id ] );
    }

    public static function plan_label( string $plan ): string {
        return match ( $plan ) {
            'agency'     => 'Agency (5 sites)',
            'enterprise' => 'Enterprise (100 sites)',
            default      => 'Single Site',
        };
    }
}
