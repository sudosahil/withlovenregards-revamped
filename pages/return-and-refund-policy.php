<?php
require_once __DIR__ . '/../includes/functions.php';
$legalTitle = 'Return and Refund Policy';
$legalSlug = 'return-and-refund-policy';
$legalIntro = 'We want you to be delighted with every order. Because our products are perishable, the following return and refund terms apply.';
$legalSections = [
    ['Perishable Goods', 'Flowers, cakes and chocolates are perishable and cannot be returned once delivered. We encourage you to report any issue on the day of delivery.'],
    ['Quality Concerns', 'If your order arrives damaged or not as described, please contact us within 24 hours of delivery with photographs. We will arrange a replacement or a refund at our discretion.'],
    ['Cancellations', 'Orders may be cancelled free of charge up to 24 hours before the scheduled delivery date. Cancellations made within 24 hours of delivery may not be eligible for a full refund as preparation may already be underway.'],
    ['Refund Method & Timeline', 'Approved refunds are processed to your original payment method via CC Avenue and typically reflect within 5–7 business days, depending on your bank.'],
    ['Non-Delivery', 'In the rare event we are unable to deliver your order, you will be offered a re-delivery or a full refund.'],
];
require __DIR__ . '/../includes/legal-page.php';
