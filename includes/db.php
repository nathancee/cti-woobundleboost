<?php
defined( 'CTI_LS' ) || die;

class DB {
    private static ?PDO $pdo = null;

    public static function connect(): PDO {
        if ( self::$pdo ) return self::$pdo;

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST, DB_PORT, DB_NAME, DB_CHARSET
        );
        self::$pdo = new PDO( $dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ] );
        return self::$pdo;
    }

    public static function run( string $sql, array $params = [] ): PDOStatement {
        $stmt = self::connect()->prepare( $sql );
        $stmt->execute( $params );
        return $stmt;
    }

    public static function row( string $sql, array $params = [] ): ?array {
        $row = self::run( $sql, $params )->fetch();
        return $row ?: null;
    }

    public static function rows( string $sql, array $params = [] ): array {
        return self::run( $sql, $params )->fetchAll();
    }

    public static function val( string $sql, array $params = [] ): mixed {
        $row = self::run( $sql, $params )->fetch( PDO::FETCH_NUM );
        return $row ? $row[0] : null;
    }

    public static function insert( string $sql, array $params = [] ): string {
        self::run( $sql, $params );
        return self::connect()->lastInsertId();
    }
}
