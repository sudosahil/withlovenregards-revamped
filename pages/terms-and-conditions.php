<?php
require_once __DIR__ . '/../includes/functions.php';
$legalTitle = 'Terms and Conditions';
$legalSlug = 'terms-and-conditions';
$legalIntro = 'By using this website and placing an order, you agree to the following terms and conditions.';
$legalSections = [
    ['Acceptance of Terms', 'Accessing or purchasing from WithLoveNRegards constitutes acceptance of these terms. If you do not agree, please do not use the site.'],
    ['Orders & Pricing', 'All prices are listed in Indian Rupees and are inclusive of applicable taxes unless stated otherwise. We reserve the right to correct pricing errors and to refuse or cancel orders affected by such errors.'],
    ['Payments', 'Payments are processed securely via CC Avenue. Your order is confirmed only after successful payment authorisation.'],
    ['Product Representation', 'Product images are indicative. Actual flowers and arrangements may vary slightly due to seasonal availability while maintaining equivalent value and theme.'],
    ['Intellectual Property', 'All content on this site, including images, text and logos, is the property of WithLoveNRegards and may not be reproduced without permission.'],
    ['Limitation of Liability', 'We are not liable for indirect or consequential losses arising from delays beyond our reasonable control, including weather, traffic or incorrect delivery information provided by the customer.'],
    ['Governing Law', 'These terms are governed by the laws of India, with jurisdiction in Pune, Maharashtra.'],
];
require __DIR__ . '/../includes/legal-page.php';
