<?php
// =============================================================================
// Murli Creations - Database Configuration & PDO Connection
// Fully compatible with Localhost (XAMPP/MAMP/Homebrew) & Hostinger Shared Hosting
// =============================================================================

// Define Database Credentials
// For Hostinger: update DB_NAME, DB_USER, DB_PASS as per your Hostinger hPanel MySQL settings
defined('DB_HOST') or define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
defined('DB_PORT') or define('DB_PORT', getenv('DB_PORT') ?: '3306');
defined('DB_NAME') or define('DB_NAME', getenv('DB_NAME') ?: 'murli_creations_db');
defined('DB_USER') or define('DB_USER', getenv('DB_USER') ?: 'root');
defined('DB_PASS') or define('DB_PASS', getenv('DB_PASS') ?: '');
defined('DB_CHARSET') or define('DB_CHARSET', 'utf8mb4');

/**
 * Get the PDO Database Connection instance
 * Returns PDO on success, or null if connection cannot be established
 */
function get_db_connection() {
    static $pdo = null;
    static $attempted = false;

    if ($pdo !== null) {
        return $pdo;
    }

    if ($attempted) {
        return null;
    }

    $attempted = true;

    try {
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $connErr) {
            // Automatic Hostinger socket/TCP fallback between localhost and 127.0.0.1
            $altHost = (DB_HOST === '127.0.0.1') ? 'localhost' : ((DB_HOST === 'localhost') ? '127.0.0.1' : null);
            if ($altHost !== null) {
                $altDsn = sprintf('mysql:host=%s;port=%s;dbname=%s;charset=%s', $altHost, DB_PORT, DB_NAME, DB_CHARSET);
                $pdo = new PDO($altDsn, DB_USER, DB_PASS, $options);
            } else {
                throw $connErr;
            }
        }

        $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
        return $pdo;
    } catch (PDOException $e) {
        // If murli_creations_db doesn't exist yet, try connecting without dbname and create it
        if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
            try {
                $rootDsn = sprintf('mysql:host=%s;port=%s;charset=%s', DB_HOST, DB_PORT, DB_CHARSET);
                $rootPdo = new PDO($rootDsn, DB_USER, DB_PASS);
                $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                
                // Retry connection now
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                return $pdo;
            } catch (Exception $inner) {
                error_log('Database Auto-Create Failed: ' . $inner->getMessage());
            }
        }

        error_log('MySQL Connection Warning: ' . $e->getMessage());
        return null;
    }
}

/**
 * Check if Database is connected and healthy
 */
function is_db_connected() {
    $db = get_db_connection();
    return $db instanceof PDO;
}
