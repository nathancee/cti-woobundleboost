<?php
/**
 * CTI WooBundleBoost License Server — Configuration
 * All values are read from environment variables.
 * Copy .env.example → .env (local) or set vars in Vercel dashboard.
 */

// ── Database ──────────────────────────────────────────────────────────────────
define( 'DB_HOST',    getenv( 'DB_HOST' )    ?: 'localhost' );
define( 'DB_PORT',    getenv( 'DB_PORT' )    ?: '3306' );
define( 'DB_NAME',    getenv( 'DB_NAME' )    ?: 'cti_licenses' );
define( 'DB_USER',    getenv( 'DB_USER' )    ?: 'root' );
define( 'DB_PASS',    getenv( 'DB_PASS' )    ?: '' );
define( 'DB_CHARSET', getenv( 'DB_CHARSET' ) ?: 'utf8mb4' );

// ── Stripe ────────────────────────────────────────────────────────────────────
define( 'STRIPE_SECRET_KEY',     getenv( 'STRIPE_SECRET_KEY' )     ?: '' );
define( 'STRIPE_WEBHOOK_SECRET', getenv( 'STRIPE_WEBHOOK_SECRET' ) ?: '' );
define( 'STRIPE_CURRENCY',       getenv( 'STRIPE_CURRENCY' )       ?: 'usd' );

// ── Pricing ───────────────────────────────────────────────────────────────────
define( 'PRICE_SINGLE',     (float) ( getenv( 'PRICE_SINGLE' )     ?: 49 ) );
define( 'PRICE_AGENCY',     (float) ( getenv( 'PRICE_AGENCY' )     ?: 99 ) );
define( 'PRICE_ENTERPRISE', (float) ( getenv( 'PRICE_ENTERPRISE' ) ?: 197 ) );

define( 'SITES_SINGLE',     1 );
define( 'SITES_AGENCY',     5 );
define( 'SITES_ENTERPRISE', 100 );

// ── Email — Resend ────────────────────────────────────────────────────────────
define( 'RESEND_API_KEY',  getenv( 'RESEND_API_KEY' )  ?: '' );
define( 'MAIL_FROM',       getenv( 'MAIL_FROM' )       ?: 'support@yourdomain.com' );
define( 'MAIL_FROM_NAME',  getenv( 'MAIL_FROM_NAME' )  ?: 'CTI WooBundleBoost' );

// ── Admin ─────────────────────────────────────────────────────────────────────
define( 'ADMIN_USERNAME',      getenv( 'ADMIN_USERNAME' )      ?: 'admin' );
define( 'ADMIN_PASSWORD_HASH', getenv( 'ADMIN_PASSWORD_HASH' ) ?: '' );
define( 'ADMIN_SECRET',        getenv( 'ADMIN_SECRET' )        ?: '' );

// ── Site ──────────────────────────────────────────────────────────────────────
define( 'SITE_URL',  rtrim( getenv( 'SITE_URL' ) ?: 'https://localhost', '/' ) );
define( 'SITE_NAME', getenv( 'SITE_NAME' ) ?: 'CTI WooBundleBoost' );
