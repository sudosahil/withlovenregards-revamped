<?php
/**
 * PHPMailer SMTP configuration.
 *
 * Credentials are intentionally empty until live. settings.json (written by the
 * admin settings page) overrides these values at runtime when present.
 */

require_once __DIR__ . '/constants.php';

define('MAIL_HOST', '');             // e.g. smtp.hostinger.com
define('MAIL_PORT', 587);
define('MAIL_USERNAME', '');         // e.g. support@withlovenregards.com
define('MAIL_PASSWORD', '');
define('MAIL_ENCRYPTION', 'tls');    // tls | ssl
define('MAIL_FROM_ADDRESS', ADMIN_EMAIL);
define('MAIL_FROM_NAME', SITE_NAME);

/**
 * Build a configured PHPMailer instance.
 * Returns null if PHPMailer isn't installed yet (composer install pending).
 *
 * @return \PHPMailer\PHPMailer\PHPMailer|null
 */
function mailer()
{
    $autoload = ROOT_PATH . '/vendor/autoload.php';
    if (!file_exists($autoload)) {
        error_log('PHPMailer not installed: run composer install.');
        return null;
    }
    require_once $autoload;

    // Allow admin-saved settings to override the constants above.
    $settingsFile = ROOT_PATH . '/config/settings.json';
    $cfg = [
        'host' => MAIL_HOST,
        'port' => MAIL_PORT,
        'user' => MAIL_USERNAME,
        'pass' => MAIL_PASSWORD,
        'enc'  => MAIL_ENCRYPTION,
    ];
    if (file_exists($settingsFile)) {
        $saved = json_decode((string) file_get_contents($settingsFile), true);
        if (is_array($saved) && !empty($saved['smtp'])) {
            $cfg = array_merge($cfg, array_filter($saved['smtp']));
        }
    }

    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host       = $cfg['host'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $cfg['user'];
    $mail->Password   = $cfg['pass'];
    $mail->SMTPSecure = $cfg['enc'];
    $mail->Port       = (int) $cfg['port'];
    $mail->setFrom(MAIL_FROM_ADDRESS, MAIL_FROM_NAME);
    $mail->isHTML(true);
    $mail->CharSet = 'UTF-8';

    return $mail;
}
