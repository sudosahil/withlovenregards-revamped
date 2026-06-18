<?php
require_once __DIR__ . '/../includes/functions.php';

$sent = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_verify($_POST['csrf_token'] ?? null)) {
        $error = 'Your session expired. Please try again.';
    } else {
        $name = trim((string) ($_POST['name'] ?? ''));
        $email = filter_var($_POST['email'] ?? '', FILTER_VALIDATE_EMAIL);
        $message = trim((string) ($_POST['message'] ?? ''));
        if ($name === '' || !$email || $message === '') {
            $error = 'Please complete all required fields with a valid email.';
        } else {
            // Live: send via PHPMailer to ADMIN_EMAIL. Placeholder: acknowledge.
            $sent = true;
        }
    }
}

$seo = [
    'title'       => 'Contact Us | ' . SITE_NAME,
    'description' => 'Get in touch with WithLoveNRegards. Call, email or message us on WhatsApp for orders and support.',
    'canonical'   => BASE_URL . '/contact-us/',
    'schema'      => ['organization'],
];
require __DIR__ . '/../includes/header.php';
?>
<main class="container section">
    <h1 class="section__title">Contact Us</h1>
    <p class="section__subtitle">We'd love to hear from you</p>

    <div class="checkout-layout">
        <div class="card-box">
            <h3>Send us a message</h3>
            <?php if ($sent): ?>
                <div class="alert alert--success">Thank you! Your message has been received. We'll get back to you shortly.</div>
            <?php elseif ($error): ?>
                <div class="alert alert--error"><?= e($error) ?></div>
            <?php endif; ?>
            <form method="post">
                <?= csrf_field() ?>
                <div class="form-grid">
                    <div class="form-field"><label>Name *</label><input type="text" name="name" required></div>
                    <div class="form-field"><label>Email *</label><input type="email" name="email" required></div>
                    <div class="form-field form-field--full"><label>Subject</label><input type="text" name="subject"></div>
                    <div class="form-field form-field--full"><label>Message *</label><textarea name="message" rows="5" required></textarea></div>
                </div>
                <button type="submit" class="btn btn--primary" style="margin-top:16px;">Send Message</button>
            </form>
        </div>

        <aside class="card-box">
            <h3>Get in touch</h3>
            <ul style="list-style:none;padding:0;line-height:2.2;">
                <li><i class="fa-solid fa-location-dot" style="color:var(--primary);width:24px;"></i> Pune, Maharashtra, India</li>
                <li><i class="fa-solid fa-phone" style="color:var(--primary);width:24px;"></i> <a href="tel:<?= e(CONTACT_PHONE_TEL) ?>"><?= e(CONTACT_PHONE) ?></a></li>
                <li><i class="fa-solid fa-envelope" style="color:var(--primary);width:24px;"></i> <a href="mailto:<?= e(ADMIN_EMAIL) ?>"><?= e(ADMIN_EMAIL) ?></a></li>
                <li><i class="fa-brands fa-whatsapp" style="color:var(--primary);width:24px;"></i> <a href="https://wa.me/<?= e(WHATSAPP_NUMBER) ?>" target="_blank" rel="noopener">Chat on WhatsApp</a></li>
            </ul>
            <p style="color:var(--muted);font-size:.88rem;margin-top:14px;">Support hours: 9 AM – 8 PM IST, all days.</p>
        </aside>
    </div>
</main>
<?php require __DIR__ . '/../includes/footer.php'; ?>
