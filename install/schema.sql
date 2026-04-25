-- CTI WooBundleBoost License Server — Database Schema
-- Run once during setup. Safe to re-run (uses IF NOT EXISTS).

CREATE TABLE IF NOT EXISTS orders (
    id         INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    paypal_id  VARCHAR(50)     NOT NULL UNIQUE COMMENT 'PayPal order ID',
    email      VARCHAR(254)    NOT NULL,
    plan       ENUM('single','agency','enterprise') NOT NULL,
    amount     DECIMAL(10,2)   NOT NULL,
    currency   VARCHAR(8)      NOT NULL DEFAULT 'USD',
    status     ENUM('pending','completed','refunded') NOT NULL DEFAULT 'completed',
    created_at DATETIME        NOT NULL,
    paypal_raw MEDIUMTEXT      COMMENT 'Full PayPal capture JSON',
    PRIMARY KEY (id),
    KEY idx_email (email),
    KEY idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS licenses (
    id          INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    license_key VARCHAR(24)     NOT NULL UNIQUE COMMENT 'Format: CTI-XXXX-XXXX-XXXX-XXXX',
    order_id    INT UNSIGNED    NOT NULL,
    email       VARCHAR(254)    NOT NULL,
    plan        ENUM('single','agency','enterprise') NOT NULL,
    max_sites   TINYINT UNSIGNED NOT NULL DEFAULT 1,
    status      ENUM('active','suspended','revoked') NOT NULL DEFAULT 'active',
    notes       TEXT,
    created_at  DATETIME        NOT NULL,
    PRIMARY KEY (id),
    KEY idx_email (email),
    KEY idx_status (status),
    CONSTRAINT fk_license_order FOREIGN KEY (order_id) REFERENCES orders (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS activations (
    id           INT UNSIGNED NOT NULL AUTO_INCREMENT,
    license_id   INT UNSIGNED NOT NULL,
    site_url     VARCHAR(500) NOT NULL,
    site_name    VARCHAR(255) NOT NULL DEFAULT '',
    activated_at DATETIME     NOT NULL,
    last_check   DATETIME     NOT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY uq_license_site (license_id, site_url(255)),
    CONSTRAINT fk_activation_license FOREIGN KEY (license_id) REFERENCES licenses (id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
