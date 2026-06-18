<?php
/**
 * Static placeholder dataset.
 *
 * Every array is shaped exactly as the corresponding DB query would return it
 * (associative rows). Functions in includes/functions.php read from here while
 * $use_placeholder is true. Image paths are normalised: lowercase, hyphenated,
 * no spaces, .webp extension.
 *
 * @return array
 */

function wlnr_data(): array
{
    static $data = null;
    if ($data !== null) {
        return $data;
    }

    // ---------------------------------------------------------------------
    // CATEGORIES (8) — parent_id null = top level
    // ---------------------------------------------------------------------
    $categories = [
        ['id' => 1, 'name' => 'Flowers',     'slug' => 'flowers',     'parent_id' => null, 'description' => 'Fresh hand-tied bouquets and arrangements delivered across India.', 'meta_title' => 'Send Flowers Online | Fresh Bouquet Delivery', 'meta_description' => 'Order fresh flowers online with same-day delivery. Roses, lilies, carnations and mixed bouquets for every occasion.', 'image' => '/assets/img/categories/flowers.webp'],
        ['id' => 2, 'name' => 'Cakes',        'slug' => 'cakes',       'parent_id' => null, 'description' => 'Freshly baked cakes — eggless options available, delivered to your door.', 'meta_title' => 'Order Cakes Online | Same Day Cake Delivery', 'meta_description' => 'Send delicious cakes online. Birthday, theme and heart-shaped cakes with same-day delivery in Pune and beyond.', 'image' => '/assets/img/categories/cakes.webp'],
        ['id' => 3, 'name' => 'Chocolates',   'slug' => 'chocolates',  'parent_id' => null, 'description' => 'Premium chocolate bouquets and gift hampers.', 'meta_title' => 'Chocolate Gifts & Bouquets Online', 'meta_description' => 'Gift premium chocolate bouquets and hampers. Perfect for birthdays, anniversaries and celebrations.', 'image' => '/assets/img/categories/chocolates.webp'],
        ['id' => 4, 'name' => 'Combos',       'slug' => 'combos',      'parent_id' => null, 'description' => 'Curated flower and gift combos for double the joy.', 'meta_title' => 'Flower & Gift Combos Online', 'meta_description' => 'Send combo gifts — flowers with cakes or chocolates. Thoughtfully curated and delivered fresh.', 'image' => '/assets/img/categories/combos.webp'],
        ['id' => 5, 'name' => 'Occasions',    'slug' => 'occasions',   'parent_id' => null, 'description' => 'Gifts handpicked for every special occasion.', 'meta_title' => 'Occasion Gifts | Birthday, Anniversary & More', 'meta_description' => 'Find the perfect gift for every occasion — birthdays, anniversaries, Valentine\'s Day and more.', 'image' => '/assets/img/categories/occasions.webp'],
        ['id' => 6, 'name' => 'Roses',        'slug' => 'roses',       'parent_id' => 1,    'description' => 'Classic red, pink and mixed rose bouquets.', 'meta_title' => 'Rose Bouquets Online | Red & Mixed Roses', 'meta_description' => 'Send romantic rose bouquets. Red roses, pink roses and mixed arrangements with fast delivery.', 'image' => '/assets/img/categories/roses.webp'],
        ['id' => 7, 'name' => 'Carnations',   'slug' => 'carnations',  'parent_id' => 1,    'description' => 'Cheerful carnation arrangements in many colours.', 'meta_title' => 'Carnation Flowers Online', 'meta_description' => 'Order fresh carnation bouquets online. Bright, long-lasting blooms for any celebration.', 'image' => '/assets/img/categories/carnations.webp'],
        ['id' => 8, 'name' => 'Lilies',       'slug' => 'lilies',      'parent_id' => 1,    'description' => 'Elegant oriental and Asiatic lily bouquets.', 'meta_title' => 'Lily Bouquets Online | Fresh Lilies', 'meta_description' => 'Send elegant lily bouquets. Fragrant oriental and Asiatic lilies delivered fresh.', 'image' => '/assets/img/categories/lilies.webp'],
    ];

    // ---------------------------------------------------------------------
    // PRODUCTS (32) — across categories
    // ---------------------------------------------------------------------
    $now = '2026-05-01 10:00:00';
    $p = function (
        $id, $name, $slug, $cat, $subcat, $price, $sale, $stock, $type, $weight,
        $featured, $bestseller, $created, $short, $desc
    ) {
        return [
            'id' => $id, 'name' => $name, 'slug' => $slug,
            'category_id' => $cat, 'subcategory_id' => $subcat,
            'price' => $price, 'sale_price' => $sale, 'stock' => $stock,
            'description' => $desc, 'short_description' => $short,
            'image' => '/assets/img/products/' . $slug . '.webp',
            'weight' => $weight, 'type' => $type,
            'meta_title' => $name . ' | ' . SITE_NAME,
            'meta_description' => $short,
            'is_featured' => $featured, 'is_bestseller' => $bestseller,
            'created_at' => $created,
        ];
    };

    $products = [
        // Roses (cat 1 / sub 6)
        $p(1,  'Red Roses Bunch',          'red-roses-bunch',          1, 6, 599,  549,  40, 'Bouquet', '12 Roses', 1, 1, '2026-04-02 09:00:00', 'A classic bunch of 12 fresh red roses wrapped in premium paper.', 'Nothing says love like a dozen red roses. This timeless bouquet features twelve long-stemmed fresh red roses, hand-tied and finished with elegant wrapping and a satin ribbon. Ideal for anniversaries, proposals or simply saying "I love you".'),
        $p(2,  'Pink Rose Elegance',       'pink-rose-elegance',       1, 6, 749,  699,  30, 'Bouquet', '15 Roses', 1, 1, '2026-04-03 09:00:00', 'Fifteen blush-pink roses arranged for a soft, romantic statement.', 'A graceful arrangement of fifteen blush-pink roses, hand-tied with seasonal fillers. The soft tones make it perfect for birthdays, thank-you gestures and new beginnings.'),
        $p(3,  'Rainbow Rose Bouquet',     'rainbow-rose-bouquet',     1, 6, 1299, 1149, 18, 'Bouquet', '20 Roses', 1, 0, '2026-04-04 09:00:00', 'A vibrant mix of multi-coloured roses for a cheerful surprise.', 'Twenty roses in a riot of colour — red, pink, yellow, orange and white — brought together in one joyful bouquet. A show-stopping gift that brightens any room.'),
        $p(4,  'Red Roses Heart Box',      'red-roses-heart-box',      1, 6, 1499, null, 12, 'Box',     '24 Roses', 0, 1, '2026-04-05 09:00:00', 'Two dozen red roses arranged inside a heart-shaped keepsake box.', 'Twenty-four premium red roses nestled in a heart-shaped box — a luxurious romantic gesture that keeps its shape long after delivery.'),

        // Carnations (cat 1 / sub 7)
        $p(5,  'Pink Carnation Posy',      'pink-carnation-posy',      1, 7, 499,  null, 35, 'Bouquet', '15 Stems', 0, 1, '2026-04-06 09:00:00', 'A cheerful posy of fifteen pink carnations.', 'Long-lasting pink carnations gathered into a charming posy. Carnations symbolise admiration and gratitude, making this a heartfelt choice for any occasion.'),
        $p(6,  'Mixed Carnation Basket',   'mixed-carnation-basket',   1, 7, 899,  799,  20, 'Basket',  '25 Stems', 1, 0, '2026-04-07 09:00:00', 'A bright basket arrangement of mixed-colour carnations.', 'Twenty-five carnations in mixed shades arranged in a cane basket. A wholesome, long-lasting gift that needs no vase.'),

        // Lilies (cat 1 / sub 8)
        $p(7,  'White Lily Bouquet',       'white-lily-bouquet',       1, 8, 999,  899,  22, 'Bouquet', '6 Stems',  1, 1, '2026-04-08 09:00:00', 'Six fragrant white oriental lilies hand-tied with greens.', 'Six stems of pure white oriental lilies, prized for their elegant form and intoxicating fragrance. A sophisticated gift for milestones and tributes alike.'),
        $p(8,  'Pink Lily Arrangement',    'pink-lily-arrangement',    1, 8, 1149, null, 16, 'Bouquet', '8 Stems',  0, 0, '2026-04-09 09:00:00', 'Eight pink Asiatic lilies in a romantic hand-tie.', 'Eight pink Asiatic lilies arranged with lush foliage. Their open blooms make a generous, eye-catching gift.'),

        // Mixed flowers (cat 1, no subcat)
        $p(9,  'Seasonal Mixed Bouquet',   'seasonal-mixed-bouquet',   1, null, 849,  749,  28, 'Bouquet', 'Assorted', 1, 1, '2026-04-10 09:00:00', 'A florist-choice mix of the freshest seasonal blooms.', 'Let our florists hand-pick the freshest seasonal flowers into a balanced, colourful bouquet. Every arrangement is unique — a delightful surprise for the recipient.'),
        $p(10, 'Orchid Grace',             'orchid-grace',             1, null, 1399, 1249, 10, 'Bouquet', '10 Stems', 0, 0, '2026-04-11 09:00:00', 'Ten purple orchid stems for an exotic, modern statement.', 'Ten stems of striking purple orchids, hand-tied for a contemporary look. Long-lasting and effortlessly elegant.'),
        $p(11, 'Anniversary Special Bouquet','anniversary-special-bouquet',1, null, 1699, 1499, 14, 'Bouquet', '30 Roses', 1, 1, '2026-04-12 09:00:00', 'A grand bouquet of thirty red and pink roses for anniversaries.', 'Mark the occasion with thirty red and pink roses, hand-tied with premium foliage and finished in luxe wrapping. A memorable centrepiece for any anniversary.'),
        $p(12, 'Sunflower Sunshine',       'sunflower-sunshine',       1, null, 699,  649,  24, 'Bouquet', '5 Stems',  0, 1, '2026-04-13 09:00:00', 'Five bright sunflowers to bring instant cheer.', 'Five large sunflowers tied with complementary greens. A bold, happy bouquet that radiates warmth.'),

        // Cakes (cat 2)
        $p(13, 'Chocolate Truffle Cake',   'chocolate-truffle-cake',   2, null, 549,  499,  50, 'Eggless', '500 g',   1, 1, '2026-04-14 09:00:00', 'Rich half-kg chocolate truffle cake, freshly baked.', 'A decadent half-kilogram chocolate truffle cake layered with smooth ganache. Freshly baked and available eggless on request.'),
        $p(14, 'Black Forest Cake',        'black-forest-cake',        2, null, 599,  null, 45, 'With Egg','500 g',   1, 1, '2026-04-15 09:00:00', 'Classic black forest with cherries and cream.', 'The timeless black forest — moist chocolate sponge layered with whipped cream and cherries, topped with chocolate shavings.'),
        $p(15, 'Red Velvet Cake',          'red-velvet-cake',          2, null, 749,  699,  40, 'Eggless', '500 g',   1, 1, '2026-04-16 09:00:00', 'Velvety red sponge with cream-cheese frosting.', 'A half-kg red velvet cake with a tender crumb and tangy cream-cheese frosting. A crowd favourite for birthdays and celebrations.'),
        $p(16, 'Butterscotch Cake',        'butterscotch-cake',        2, null, 599,  549,  38, 'Eggless', '500 g',   0, 1, '2026-04-17 09:00:00', 'Half-kg butterscotch cake with crunchy praline.', 'Smooth butterscotch cream layered over soft sponge and finished with crunchy praline. A delightful eggless treat.'),
        $p(17, 'Heart Shape Red Velvet',   'heart-shape-red-velvet',   2, null, 999,  899,  20, 'Eggless', '1 Kg',    1, 0, '2026-04-18 09:00:00', 'A one-kg heart-shaped red velvet cake for romance.', 'Say it from the heart with a one-kilogram heart-shaped red velvet cake, frosted in cream cheese and finished with a rose accent.'),
        $p(18, 'Theme Cartoon Cake',       'theme-cartoon-cake',       2, null, 1299, null, 15, 'With Egg','1 Kg',    0, 0, '2026-04-19 09:00:00', 'A custom one-kg cartoon-themed cake for kids.', 'A one-kilogram themed cake decorated with a cheerful cartoon design — perfect for children\'s birthdays. Flavour customisable at checkout.'),
        $p(19, 'Pineapple Cake',           'pineapple-cake',           2, null, 499,  449,  42, 'Eggless', '500 g',   0, 1, '2026-04-20 09:00:00', 'Light pineapple cream cake, eggless and fresh.', 'A light, refreshing pineapple cake with whipped cream and pineapple chunks. Eggless and freshly baked.'),

        // Chocolates (cat 3)
        $p(20, 'Chocolate Bouquet Deluxe', 'chocolate-bouquet-deluxe', 3, null, 899,  799,  30, 'Bouquet', '12 Pcs',  1, 1, '2026-04-21 09:00:00', 'A bouquet of twelve assorted wrapped chocolates.', 'Twelve premium assorted chocolates arranged like a flower bouquet — a sweet, lasting alternative to fresh blooms.'),
        $p(21, 'Ferrero Rocher Hamper',    'ferrero-rocher-hamper',    3, null, 1199, 1099, 25, 'Hamper',  '16 Pcs',  1, 1, '2026-04-22 09:00:00', 'A gift hamper of sixteen Ferrero Rocher chocolates.', 'A luxurious hamper of sixteen Ferrero Rocher chocolates presented in a decorative box. An indulgent gift for any celebration.'),
        $p(22, 'Dairy Milk Gift Box',      'dairy-milk-gift-box',      3, null, 699,  null, 35, 'Box',     '10 Pcs',  0, 0, '2026-04-23 09:00:00', 'An assorted Cadbury Dairy Milk gift box.', 'A curated box of assorted Cadbury Dairy Milk chocolates — a familiar favourite, beautifully packaged.'),
        $p(23, 'Premium Chocolate Hamper', 'premium-chocolate-hamper', 3, null, 1799, 1599, 12, 'Hamper',  'Assorted',1, 0, '2026-04-24 09:00:00', 'A premium hamper of imported assorted chocolates.', 'An elegant hamper brimming with imported assorted chocolates and truffles. The ultimate sweet indulgence to mark a special moment.'),

        // Combos (cat 4)
        $p(24, 'Roses N Chocolate Cake',   'roses-n-chocolate-cake',   4, null, 1099, 999,  26, 'Combo',   'Combo',   1, 1, '2026-04-25 09:00:00', 'Ten red roses paired with a half-kg chocolate cake.', 'A perfect pairing — ten fresh red roses delivered alongside a half-kilogram chocolate truffle cake. Double the joy in one thoughtful gift.'),
        $p(25, 'Flowers N Chocolates Combo','flowers-n-chocolates-combo',4, null, 1249, 1149, 22, 'Combo',  'Combo',   1, 1, '2026-04-26 09:00:00', 'A mixed bouquet with a box of assorted chocolates.', 'A seasonal mixed bouquet teamed with a box of assorted chocolates. A balanced gift of blooms and sweetness.'),
        $p(26, 'Teddy Roses N Cake',       'teddy-roses-n-cake',       4, null, 1499, 1349, 18, 'Combo',   'Combo',   1, 0, '2026-04-27 09:00:00', 'Roses, a soft teddy and a half-kg cake together.', 'A delightful trio — a bunch of red roses, a cuddly teddy bear and a half-kilogram cake. A complete celebration delivered to the door.'),
        $p(27, 'Anniversary Combo Hamper', 'anniversary-combo-hamper', 4, null, 1999, 1799, 14, 'Combo',   'Combo',   1, 1, '2026-04-28 09:00:00', 'Roses, cake and chocolates curated for anniversaries.', 'An anniversary hamper bringing together a rose bouquet, a one-kg cake and a box of chocolates. A grand gesture for the ones who matter most.'),

        // Occasions (cat 5)
        $p(28, 'Birthday Surprise Box',    'birthday-surprise-box',    5, null, 1299, 1199, 20, 'Gift',    'Combo',   1, 1, '2026-04-29 09:00:00', 'A birthday box of flowers, balloons and a cake.', 'A festive birthday box featuring a bright bouquet, a half-kg cake and a balloon accent. Everything needed to make the day special.'),
        $p(29, 'Valentine Romance Combo',  'valentine-romance-combo',  5, null, 1599, 1449, 16, 'Gift',    'Combo',   1, 1, '2026-04-30 09:00:00', 'Red roses, a heart cake and chocolates for Valentine\'s.', 'A romantic Valentine\'s combo with red roses, a heart-shaped cake and a box of chocolates. Crafted to win hearts.'),
        $p(30, 'Mothers Day Bouquet',      'mothers-day-bouquet',      5, null, 899,  799,  24, 'Bouquet', 'Assorted',0, 1, '2026-05-01 09:00:00', 'A graceful mixed bouquet to celebrate Mum.', 'A tender mixed bouquet of pastel blooms to thank Mum for everything. A heartfelt Mother\'s Day tribute.'),
        $p(31, 'Fathers Day Special',      'fathers-day-special',      5, null, 999,  899,  20, 'Combo',   'Combo',   0, 0, '2026-05-02 09:00:00', 'Flowers with a cake to honour Dad on his day.', 'A handsome bouquet paired with a half-kg cake to celebrate Dad. A simple, sincere way to say thank you.'),
        $p(32, 'Womens Day Floral Treat',  'womens-day-floral-treat',  5, null, 749,  699,  26, 'Bouquet', 'Assorted',0, 1, '2026-05-03 09:00:00', 'A vibrant bouquet to celebrate the women in your life.', 'A vibrant mixed bouquet to mark International Women\'s Day. A bright way to show appreciation and respect.'),
    ];

    // ---------------------------------------------------------------------
    // CUSTOMERS (10)
    // ---------------------------------------------------------------------
    $customers = [
        ['id' => 1,  'name' => 'Aarav Sharma',    'email' => 'aarav.sharma@example.com',   'phone' => '+919812345671', 'city' => 'Pune',      'total_orders' => 6, 'total_spent' => 7894.00, 'last_order_date' => '2026-06-10', 'created_at' => '2025-09-12 14:20:00', 'notes' => 'Prefers eggless cakes. Repeat anniversary buyer.'],
        ['id' => 2,  'name' => 'Diya Patel',      'email' => 'diya.patel@example.com',     'phone' => '+919812345672', 'city' => 'Mumbai',    'total_orders' => 3, 'total_spent' => 3247.00, 'last_order_date' => '2026-06-05', 'created_at' => '2025-11-03 09:10:00', 'notes' => ''],
        ['id' => 3,  'name' => 'Vihaan Mehta',    'email' => 'vihaan.mehta@example.com',   'phone' => '+919812345673', 'city' => 'Delhi',     'total_orders' => 9, 'total_spent' => 14210.00,'last_order_date' => '2026-06-14', 'created_at' => '2025-06-21 18:45:00', 'notes' => 'VIP. Orders monthly for office.'],
        ['id' => 4,  'name' => 'Ananya Iyer',     'email' => 'ananya.iyer@example.com',    'phone' => '+919812345674', 'city' => 'Bangalore', 'total_orders' => 1, 'total_spent' => 549.00,  'last_order_date' => '2026-05-28', 'created_at' => '2026-05-28 11:00:00', 'notes' => 'New customer.'],
        ['id' => 5,  'name' => 'Kabir Nair',      'email' => 'kabir.nair@example.com',     'phone' => '+919812345675', 'city' => 'Hyderabad', 'total_orders' => 4, 'total_spent' => 5396.00, 'last_order_date' => '2026-06-01', 'created_at' => '2025-12-15 16:30:00', 'notes' => ''],
        ['id' => 6,  'name' => 'Saanvi Reddy',    'email' => 'saanvi.reddy@example.com',   'phone' => '+919812345676', 'city' => 'Pune',      'total_orders' => 2, 'total_spent' => 1898.00, 'last_order_date' => '2026-04-18', 'created_at' => '2026-01-09 13:05:00', 'notes' => 'At risk — no order in 2 months.'],
        ['id' => 7,  'name' => 'Arjun Gupta',     'email' => 'arjun.gupta@example.com',    'phone' => '+919812345677', 'city' => 'Kolkata',   'total_orders' => 5, 'total_spent' => 6745.00, 'last_order_date' => '2026-06-12', 'created_at' => '2025-08-02 10:15:00', 'notes' => ''],
        ['id' => 8,  'name' => 'Ishaan Verma',    'email' => 'ishaan.verma@example.com',   'phone' => '+919812345678', 'city' => 'Gurgaon',   'total_orders' => 7, 'total_spent' => 9320.00, 'last_order_date' => '2026-06-09', 'created_at' => '2025-07-19 12:40:00', 'notes' => 'Corporate gifting contact.'],
        ['id' => 9,  'name' => 'Myra Joshi',      'email' => 'myra.joshi@example.com',     'phone' => '+919812345679', 'city' => 'Pune',      'total_orders' => 2, 'total_spent' => 2148.00, 'last_order_date' => '2026-05-22', 'created_at' => '2026-02-27 15:50:00', 'notes' => ''],
        ['id' => 10, 'name' => 'Reyansh Rao',     'email' => 'reyansh.rao@example.com',    'phone' => '+919812345680', 'city' => 'Mumbai',    'total_orders' => 1, 'total_spent' => 1099.00, 'last_order_date' => '2026-06-03', 'created_at' => '2026-06-03 19:25:00', 'notes' => 'New customer.'],
    ];

    // ---------------------------------------------------------------------
    // ORDERS (15)
    // ---------------------------------------------------------------------
    $mkItem = fn($pid, $qty, $price) => ['product_id' => $pid, 'qty' => $qty, 'price' => $price];
    $orders = [
        ['id' => 1,  'order_number' => 'WLNR-2026-1001', 'customer_id' => 3,  'status' => 'delivered',  'total' => 1148.00, 'payment_status' => 'paid',    'payment_method' => 'CCAvenue', 'delivery_date' => '2026-06-14', 'delivery_slot' => 'Morning (8-12)',   'address' => '12 Connaught Place', 'city' => 'Delhi',     'created_at' => '2026-06-12 11:20:00', 'ccavenue_tracking_id' => '308812345671', 'items' => [$mkItem(1,1,549.00), $mkItem(5,1,499.00), $mkItem(19,1,100.00)]],
        ['id' => 2,  'order_number' => 'WLNR-2026-1002', 'customer_id' => 1,  'status' => 'delivered',  'total' => 1349.00, 'payment_status' => 'paid',    'payment_method' => 'CCAvenue', 'delivery_date' => '2026-06-10', 'delivery_slot' => 'Evening (4-8)',    'address' => '45 FC Road',         'city' => 'Pune',      'created_at' => '2026-06-09 16:05:00', 'ccavenue_tracking_id' => '308812345672', 'items' => [$mkItem(26,1,1349.00)]],
        ['id' => 3,  'order_number' => 'WLNR-2026-1003', 'customer_id' => 7,  'status' => 'dispatched', 'total' => 999.00,  'payment_status' => 'paid',    'payment_method' => 'CCAvenue', 'delivery_date' => '2026-06-18', 'delivery_slot' => 'Afternoon (12-4)', 'address' => '8 Park Street',      'city' => 'Kolkata',   'created_at' => '2026-06-17 10:00:00', 'ccavenue_tracking_id' => '308812345673', 'items' => [$mkItem(7,1,899.00), $mkItem(19,1,100.00)]],
        ['id' => 4,  'order_number' => 'WLNR-2026-1004', 'customer_id' => 8,  'status' => 'processing', 'total' => 1799.00, 'payment_status' => 'paid',    'payment_method' => 'CCAvenue', 'delivery_date' => '2026-06-19', 'delivery_slot' => 'Morning (8-12)',   'address' => 'Tower B, Cyber City', 'city' => 'Gurgaon',  'created_at' => '2026-06-17 14:30:00', 'ccavenue_tracking_id' => '308812345674', 'items' => [$mkItem(27,1,1799.00)]],
        ['id' => 5,  'order_number' => 'WLNR-2026-1005', 'customer_id' => 2,  'status' => 'pending',    'total' => 1149.00, 'payment_status' => 'pending', 'payment_method' => 'CCAvenue', 'delivery_date' => '2026-06-20', 'delivery_slot' => 'Evening (4-8)',    'address' => '21 Marine Drive',    'city' => 'Mumbai',    'created_at' => '2026-06-18 09:15:00', 'ccavenue_tracking_id' => '', 'items' => [$mkItem(25,1,1149.00)]],
        ['id' => 6,  'order_number' => 'WLNR-2026-1006', 'customer_id' => 5,  'status' => 'delivered',  'total' => 1349.00, 'payment_status' => 'paid',    'payment_method' => 'CCAvenue', 'delivery_date' => '2026-06-01', 'delivery_slot' => 'Afternoon (12-4)', 'address' => '5 Banjara Hills',   'city' => 'Hyderabad', 'created_at' => '2026-05-31 12:00:00', 'ccavenue_tracking_id' => '308812345676', 'items' => [$mkItem(24,1,999.00), $mkItem(5,1,350.00)]],
        ['id' => 7,  'order_number' => 'WLNR-2026-1007', 'customer_id' => 1,  'status' => 'delivered',  'total' => 699.00,  'payment_status' => 'paid',    'payment_method' => 'CCAvenue', 'delivery_date' => '2026-05-20', 'delivery_slot' => 'Morning (8-12)',   'address' => '45 FC Road',         'city' => 'Pune',      'created_at' => '2026-05-19 15:45:00', 'ccavenue_tracking_id' => '308812345677', 'items' => [$mkItem(12,1,649.00), $mkItem(19,1,50.00)]],
        ['id' => 8,  'order_number' => 'WLNR-2026-1008', 'customer_id' => 3,  'status' => 'delivered',  'total' => 2298.00, 'payment_status' => 'paid',    'payment_method' => 'CCAvenue', 'delivery_date' => '2026-05-25', 'delivery_slot' => 'Evening (4-8)',    'address' => '12 Connaught Place', 'city' => 'Delhi',     'created_at' => '2026-05-24 11:10:00', 'ccavenue_tracking_id' => '308812345678', 'items' => [$mkItem(23,1,1599.00), $mkItem(20,1,699.00)]],
        ['id' => 9,  'order_number' => 'WLNR-2026-1009', 'customer_id' => 9,  'status' => 'cancelled',  'total' => 899.00,  'payment_status' => 'refunded','payment_method' => 'CCAvenue', 'delivery_date' => '2026-05-22', 'delivery_slot' => 'Afternoon (12-4)', 'address' => '9 Koregaon Park',   'city' => 'Pune',      'created_at' => '2026-05-21 13:20:00', 'ccavenue_tracking_id' => '308812345679', 'items' => [$mkItem(30,1,799.00), $mkItem(19,1,100.00)]],
        ['id' => 10, 'order_number' => 'WLNR-2026-1010', 'customer_id' => 10, 'status' => 'delivered',  'total' => 1099.00, 'payment_status' => 'paid',    'payment_method' => 'CCAvenue', 'delivery_date' => '2026-06-03', 'delivery_slot' => 'Evening (4-8)',    'address' => '33 Bandra West',     'city' => 'Mumbai',    'created_at' => '2026-06-02 18:30:00', 'ccavenue_tracking_id' => '308812345680', 'items' => [$mkItem(24,1,999.00), $mkItem(19,1,100.00)]],
        ['id' => 11, 'order_number' => 'WLNR-2026-1011', 'customer_id' => 8,  'status' => 'dispatched', 'total' => 1599.00, 'payment_status' => 'paid',    'payment_method' => 'CCAvenue', 'delivery_date' => '2026-06-18', 'delivery_slot' => 'Midnight (11-1)',  'address' => 'Tower B, Cyber City', 'city' => 'Gurgaon',  'created_at' => '2026-06-16 21:05:00', 'ccavenue_tracking_id' => '308812345681', 'items' => [$mkItem(29,1,1449.00), $mkItem(19,1,150.00)]],
        ['id' => 12, 'order_number' => 'WLNR-2026-1012', 'customer_id' => 7,  'status' => 'delivered',  'total' => 1149.00, 'payment_status' => 'paid',    'payment_method' => 'CCAvenue', 'delivery_date' => '2026-06-08', 'delivery_slot' => 'Morning (8-12)',   'address' => '8 Park Street',      'city' => 'Kolkata',   'created_at' => '2026-06-07 09:40:00', 'ccavenue_tracking_id' => '308812345682', 'items' => [$mkItem(21,1,1099.00), $mkItem(19,1,50.00)]],
        ['id' => 13, 'order_number' => 'WLNR-2026-1013', 'customer_id' => 5,  'status' => 'processing', 'total' => 999.00,  'payment_status' => 'paid',    'payment_method' => 'CCAvenue', 'delivery_date' => '2026-06-19', 'delivery_slot' => 'Afternoon (12-4)', 'address' => '5 Banjara Hills',   'city' => 'Hyderabad', 'created_at' => '2026-06-18 08:50:00', 'ccavenue_tracking_id' => '308812345683', 'items' => [$mkItem(17,1,899.00), $mkItem(19,1,100.00)]],
        ['id' => 14, 'order_number' => 'WLNR-2026-1014', 'customer_id' => 6,  'status' => 'delivered',  'total' => 999.00,  'payment_status' => 'paid',    'payment_method' => 'CCAvenue', 'delivery_date' => '2026-04-18', 'delivery_slot' => 'Evening (4-8)',    'address' => '9 Koregaon Park',   'city' => 'Pune',      'created_at' => '2026-04-17 17:15:00', 'ccavenue_tracking_id' => '308812345684', 'items' => [$mkItem(11,1,999.00)]],
        ['id' => 15, 'order_number' => 'WLNR-2026-1015', 'customer_id' => 2,  'status' => 'pending',    'total' => 549.00,  'payment_status' => 'pending', 'payment_method' => 'CCAvenue', 'delivery_date' => '2026-06-21', 'delivery_slot' => 'Morning (8-12)',   'address' => '21 Marine Drive',    'city' => 'Mumbai',    'created_at' => '2026-06-18 10:25:00', 'ccavenue_tracking_id' => '', 'items' => [$mkItem(13,1,499.00), $mkItem(19,1,50.00)]],
    ];

    // ---------------------------------------------------------------------
    // ABANDONED CARTS (8)
    // ---------------------------------------------------------------------
    $abandoned = [
        ['id' => 1, 'customer_email' => 'rohit.kale@example.com',   'customer_phone' => '+919800000001', 'cart_items' => [$mkItem(1,2,549.00), $mkItem(13,1,499.00)], 'total_value' => 1597.00, 'last_activity' => '2026-06-18 07:42:00', 'recovery_status' => 'new'],
        ['id' => 2, 'customer_email' => 'neha.singh@example.com',   'customer_phone' => '+919800000002', 'cart_items' => [$mkItem(11,1,1499.00)], 'total_value' => 1499.00, 'last_activity' => '2026-06-17 22:10:00', 'recovery_status' => 'new'],
        ['id' => 3, 'customer_email' => 'amit.desai@example.com',   'customer_phone' => '+919800000003', 'cart_items' => [$mkItem(21,1,1099.00), $mkItem(20,1,799.00)], 'total_value' => 1898.00, 'last_activity' => '2026-06-17 18:55:00', 'recovery_status' => 'contacted'],
        ['id' => 4, 'customer_email' => 'pooja.shah@example.com',   'customer_phone' => '+919800000004', 'cart_items' => [$mkItem(7,1,899.00)], 'total_value' => 899.00, 'last_activity' => '2026-06-16 14:30:00', 'recovery_status' => 'recovered'],
        ['id' => 5, 'customer_email' => 'sanjay.kumar@example.com', 'customer_phone' => '+919800000005', 'cart_items' => [$mkItem(27,1,1799.00), $mkItem(22,1,699.00)], 'total_value' => 2498.00, 'last_activity' => '2026-06-16 09:12:00', 'recovery_status' => 'contacted'],
        ['id' => 6, 'customer_email' => 'kavya.menon@example.com',  'customer_phone' => '+919800000006', 'cart_items' => [$mkItem(30,1,799.00)], 'total_value' => 799.00, 'last_activity' => '2026-06-15 20:05:00', 'recovery_status' => 'lost'],
        ['id' => 7, 'customer_email' => 'rahul.jain@example.com',   'customer_phone' => '+919800000007', 'cart_items' => [$mkItem(29,1,1449.00), $mkItem(13,1,499.00)], 'total_value' => 1948.00, 'last_activity' => '2026-06-15 11:48:00', 'recovery_status' => 'new'],
        ['id' => 8, 'customer_email' => 'sneha.rao@example.com',    'customer_phone' => '+919800000008', 'cart_items' => [$mkItem(24,1,999.00)], 'total_value' => 999.00, 'last_activity' => '2026-06-14 16:20:00', 'recovery_status' => 'recovered'],
    ];

    // ---------------------------------------------------------------------
    // ANALYTICS SUMMARY
    // ---------------------------------------------------------------------
    $revenueByDay = [];
    // Deterministic 30-day series ending 2026-06-18 (no random — stable output).
    $base = ['2026-05-20' => 4200,'2026-05-21' => 3850,'2026-05-22' => 5100,'2026-05-23' => 2900,'2026-05-24' => 6200,'2026-05-25' => 7400,'2026-05-26' => 3300,'2026-05-27' => 4100,'2026-05-28' => 2750,'2026-05-29' => 5600,'2026-05-30' => 4800,'2026-05-31' => 6900,'2026-06-01' => 7100,'2026-06-02' => 3900,'2026-06-03' => 4500,'2026-06-04' => 3200,'2026-06-05' => 5300,'2026-06-06' => 6100,'2026-06-07' => 4700,'2026-06-08' => 5900,'2026-06-09' => 6400,'2026-06-10' => 7200,'2026-06-11' => 4300,'2026-06-12' => 5500,'2026-06-13' => 4900,'2026-06-14' => 8100,'2026-06-15' => 3600,'2026-06-16' => 5200,'2026-06-17' => 6700,'2026-06-18' => 3100];
    foreach ($base as $day => $rev) {
        $revenueByDay[] = ['date' => $day, 'revenue' => $rev];
    }

    $analytics = [
        'today_orders'        => 3,
        'today_revenue'       => 3100.00,
        'this_month_orders'   => 11,
        'this_month_revenue'  => 58940.00,
        'pending_orders'      => 2,
        'abandoned_carts_count' => 3, // status = new
        'total_customers'     => count($customers),
        'top_products'        => [
            ['product_id' => 1,  'name' => 'Red Roses Bunch',        'units_sold' => 84, 'revenue' => 46116.00],
            ['product_id' => 13, 'name' => 'Chocolate Truffle Cake', 'units_sold' => 72, 'revenue' => 35928.00],
            ['product_id' => 24, 'name' => 'Roses N Chocolate Cake', 'units_sold' => 58, 'revenue' => 57942.00],
            ['product_id' => 21, 'name' => 'Ferrero Rocher Hamper',  'units_sold' => 41, 'revenue' => 45059.00],
            ['product_id' => 9,  'name' => 'Seasonal Mixed Bouquet', 'units_sold' => 37, 'revenue' => 27713.00],
        ],
        'orders_by_status'    => ['pending' => 2, 'processing' => 2, 'dispatched' => 2, 'delivered' => 8, 'cancelled' => 1],
        'revenue_by_day'      => $revenueByDay,
    ];

    // ---------------------------------------------------------------------
    // FAQ (homepage + category)
    // ---------------------------------------------------------------------
    $faqs = [
        ['q' => 'Do you offer same-day flower delivery?', 'a' => 'Yes. Orders placed before 5:00 PM IST are eligible for same-day delivery across our serviceable cities. Orders after the cutoff are scheduled for the next available day.'],
        ['q' => 'Which cities do you deliver to?', 'a' => 'We deliver flowers, cakes and gifts across Pune, Mumbai, Delhi, Bangalore, Hyderabad, Kolkata and Gurgaon, with more cities added regularly.'],
        ['q' => 'Are the cakes eggless?', 'a' => 'Most of our cakes are available in an eggless option. You can choose your preference on the product page before adding to cart.'],
        ['q' => 'How do I track my order?', 'a' => 'Once your order is dispatched you will receive an email and SMS with tracking details. You can also view order status in the My Account section.'],
        ['q' => 'What payment methods do you accept?', 'a' => 'We accept all major credit cards, debit cards, net banking, UPI and wallets securely through CC Avenue.'],
        ['q' => 'Can I send a personalised message with my gift?', 'a' => 'Absolutely. Add your personal message in the special instructions field at checkout and we will include a complimentary message card.'],
    ];

    $data = [
        'categories'        => $categories,
        'products'          => $products,
        'customers'         => $customers,
        'orders'            => $orders,
        'abandoned_carts'   => $abandoned,
        'analytics'         => $analytics,
        'faqs'              => $faqs,
    ];

    return $data;
}
