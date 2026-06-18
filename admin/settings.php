<?php
/**
 * Site settings — CC Avenue keys, SMTP, WhatsApp, social URLs, announcement bar.
 * Writes config/settings.json which config/mail.php and the CC Avenue endpoints
 * read at runtime.
 */
require_once __DIR__ . '/../includes/functions.php';
require_admin();

$settingsFile = ROOT_PATH . '/config/settings.json';
$settings = site_settings();
$flash = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_verify($_POST['csrf_token'] ?? null)) {
    $new = [
        'ccavenue' => [
            'merchant_id' => trim((string) ($_POST['cca_merchant_id'] ?? '')),
            'access_code' => trim((string) ($_POST['cca_access_code'] ?? '')),
            'working_key' => trim((string) ($_POST['cca_working_key'] ?? '')),
        ],
        'smtp' => [
            'host' => trim((string) ($_POST['smtp_host'] ?? '')),
            'port' => (int) ($_POST['smtp_port'] ?? 587),
            'user' => trim((string) ($_POST['smtp_user'] ?? '')),
            'pass' => trim((string) ($_POST['smtp_pass'] ?? '')),
            'enc'  => trim((string) ($_POST['smtp_enc'] ?? 'tls')),
        ],
        'whatsapp' => trim((string) ($_POST['whatsapp'] ?? '')),
        'social' => [
            'facebook'  => trim((string) ($_POST['social_facebook'] ?? '')),
            'instagram' => trim((string) ($_POST['social_instagram'] ?? '')),
            'twitter'   => trim((string) ($_POST['social_twitter'] ?? '')),
            'pinterest' => trim((string) ($_POST['social_pinterest'] ?? '')),
        ],
        'announcement' => trim((string) ($_POST['announcement'] ?? '')),
    ];
    if (file_put_contents($settingsFile, json_encode($new, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) !== false) {
        $settings = $new;
        $flash = 'Settings saved.';
    } else {
        $flash = 'Could not write config/settings.json — check file permissions.';
    }
}

$cca = $settings['ccavenue'] ?? [];
$smtp = $settings['smtp'] ?? [];
$social = $settings['social'] ?? [];
$mask = fn($v) => $v ? str_repeat('•', max(4, strlen((string) $v) - 4)) . substr((string) $v, -4) : '';

$adminTitle = 'Settings';
$adminActive = 'settings';
require __DIR__ . '/includes/admin-header.php';
?>
<?php if ($flash): ?><div class="alert-inline alert-inline--success"><?= e($flash) ?></div><?php endif; ?>
<form method="post">
    <?= csrf_field() ?>
    <div class="panel-grid">
        <div class="panel">
            <div class="panel__head"><h2>CC Avenue</h2></div>
            <div class="panel__body">
                <p style="font-size:.82rem;color:#8a8a9a;">Enter live credentials to enable payments. Leave blank to keep the gateway in placeholder mode.</p>
                <div class="afield"><label>Merchant ID</label><input type="text" name="cca_merchant_id" value="<?= e($cca['merchant_id'] ?? '') ?>"></div>
                <div class="afield"><label>Access Code</label><input type="text" name="cca_access_code" value="<?= e($cca['access_code'] ?? '') ?>"></div>
                <div class="afield"><label>Working Key</label><input type="password" name="cca_working_key" value="<?= e($cca['working_key'] ?? '') ?>" placeholder="<?= e($mask($cca['working_key'] ?? '')) ?>"></div>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head"><h2>Email (SMTP)</h2></div>
            <div class="panel__body">
                <div class="afield"><label>Host</label><input type="text" name="smtp_host" value="<?= e($smtp['host'] ?? '') ?>"></div>
                <div class="afield-row">
                    <div class="afield"><label>Port</label><input type="number" name="smtp_port" value="<?= e((string) ($smtp['port'] ?? 587)) ?>"></div>
                    <div class="afield"><label>Encryption</label>
                        <select name="smtp_enc">
                            <option value="tls" <?= ($smtp['enc'] ?? 'tls') === 'tls' ? 'selected' : '' ?>>TLS</option>
                            <option value="ssl" <?= ($smtp['enc'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
                        </select>
                    </div>
                </div>
                <div class="afield"><label>Username</label><input type="text" name="smtp_user" value="<?= e($smtp['user'] ?? '') ?>"></div>
                <div class="afield"><label>Password</label><input type="password" name="smtp_pass" value="<?= e($smtp['pass'] ?? '') ?>"></div>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head"><h2>Contact &amp; Social</h2></div>
            <div class="panel__body">
                <div class="afield"><label>WhatsApp Number</label><input type="text" name="whatsapp" value="<?= e($settings['whatsapp'] ?? WHATSAPP_NUMBER) ?>"></div>
                <div class="afield"><label>Facebook URL</label><input type="url" name="social_facebook" value="<?= e($social['facebook'] ?? SOCIAL_FACEBOOK) ?>"></div>
                <div class="afield"><label>Instagram URL</label><input type="url" name="social_instagram" value="<?= e($social['instagram'] ?? SOCIAL_INSTAGRAM) ?>"></div>
                <div class="afield"><label>Twitter URL</label><input type="url" name="social_twitter" value="<?= e($social['twitter'] ?? SOCIAL_TWITTER) ?>"></div>
                <div class="afield"><label>Pinterest URL</label><input type="url" name="social_pinterest" value="<?= e($social['pinterest'] ?? SOCIAL_PINTEREST) ?>"></div>
            </div>
        </div>

        <div class="panel">
            <div class="panel__head"><h2>Announcement Bar</h2></div>
            <div class="panel__body">
                <div class="afield"><label>Text</label><input type="text" name="announcement" value="<?= e($settings['announcement'] ?? 'Same Day Delivery Available') ?>"></div>
            </div>
        </div>
    </div>
    <button class="abtn abtn--primary" type="submit" style="justify-content:center;"><i class="fa-solid fa-floppy-disk"></i> Save Settings</button>
</form>
<?php require __DIR__ . '/includes/admin-footer.php'; ?>
