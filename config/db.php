<?php
/**
 * Database connection layer.
 *
 * Currently runs in placeholder mode: $use_placeholder = true makes every
 * data function in includes/functions.php read from data/placeholder_data.php
 * instead of issuing live queries. When the real DB is ready, fill the
 * credentials below and flip the flag — no other code changes required because
 * all data access already goes through the prepared-statement helpers.
 */

require_once __DIR__ . '/constants.php';

// --- .env-style credential block (empty until live) -----------------------
define('DB_HOST', '');
define('DB_NAME', '');
define('DB_USER', '');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Master switch. While true, no socket is opened.
$use_placeholder = true;

/**
 * Lazily create and reuse a single PDO connection.
 * Returns null in placeholder mode.
 *
 * @return PDO|null
 */
function db(): ?PDO
{
    global $use_placeholder;

    if ($use_placeholder) {
        return null;
    }

    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        // Never leak connection details to the client.
        error_log('DB connection failed: ' . $e->getMessage());
        http_response_code(500);
        exit('A database error occurred. Please try again later.');
    }

    return $pdo;
}
