<?php
require_once __DIR__ . '/../core/functions.php';
$legalTitle = 'Shipping Disclaimer';
$legalSlug = 'shipping-disclaimer';
$legalIntro = 'Please review our delivery terms so we can serve you better.';
$legalSections = [
    ['Delivery Areas', 'We currently deliver across Pune, Mumbai, Delhi, Bangalore, Hyderabad, Kolkata and Gurgaon, with serviceability depending on the recipient\'s pincode.'],
    ['Same-Day Delivery & Cutoff', 'Orders placed before 5:00 PM IST are eligible for same-day delivery. Orders placed after the cutoff are scheduled for the next available delivery date.'],
    ['Delivery Slots', 'You may choose from Morning (8 AM–12 PM), Afternoon (12–4 PM), Evening (4–8 PM) and Midnight (11 PM–1 AM) slots. Slots are indicative; exact timing may vary due to traffic, weather or local conditions.'],
    ['Product Substitution', 'Fresh flowers are seasonal. In rare cases we may substitute flowers or containers of equal or greater value while preserving the overall look and theme of your order.'],
    ['Failed Delivery', 'If the recipient is unavailable or the address is incorrect, our delivery partner will attempt to contact them. Re-delivery may incur additional charges. We are not liable for delays caused by incorrect address details.'],
];
require __DIR__ . '/../core/legal-page.php';
