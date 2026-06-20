<?php
require_once __DIR__ . '/../core/functions.php';
$legalTitle = 'Privacy Policy';
$legalSlug = 'privacy-policy';
$legalIntro = 'Your privacy matters to us. This policy explains what information we collect, how we use it and the choices you have.';
$legalSections = [
    ['Information We Collect', 'We collect information you provide when placing an order or creating an account, including your name, email, phone number and delivery address. We also collect limited technical data such as your IP address and browsing activity to improve our service.'],
    ['How We Use Your Information', "We use your information to process and deliver orders, send order updates and invoices, provide customer support and, with your consent, share offers and promotions. We never sell your personal data to third parties."],
    ['Payment Security', 'All payments are processed through CC Avenue\'s secure, PCI-compliant gateway. We do not store your full card details on our servers.'],
    ['Cookies', 'We use cookies and similar technologies to keep your cart and session active, remember preferences and measure site performance. You can manage cookies through your browser settings.'],
    ['Data Retention & Your Rights', 'We retain order records as required for accounting and legal purposes. You may request access to, correction of, or deletion of your personal data by contacting us.'],
];
require __DIR__ . '/../core/legal-page.php';
