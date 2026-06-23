<?php

/*
|--------------------------------------------------------------------------
| Website CMS — page & field schema
|--------------------------------------------------------------------------
| Single source of truth for every editable area of the public website.
| Content is grouped into "pages" (the cards on Admin → Website CMS). Each
| page has a category (core | system), section "chips", and a flat list of
| fields. The per-page editor (Admin\Cms\Edit) renders fields from here, and
| the public blades read values via cms()/cms_image()/cms_array() using the
| `default` below — so unset keys fall back to the original copy.
|
| Field types: text | textarea | image | repeater
| A dot-safe binding `name` is derived for each field automatically.
*/

$pages = [
    'landing' => [
        'label' => 'Landing Page',
        'category' => 'core',
        'chips' => ['Hero', 'Stillness', 'Offers', 'Member', 'Explore Jos', 'Wellness'],
        'preview' => '/',
        'fields' => [
            ['key' => 'home.hero_image', 'label' => 'Hero image', 'type' => 'image', 'default' => 'images/image 1.jpg'],

            ['key' => 'home.stillness_title', 'label' => '“Stillness” heading', 'type' => 'text', 'default' => 'Where stillness finds you'],
            ['key' => 'home.stillness_text', 'label' => '“Stillness” paragraph', 'type' => 'textarea', 'default' => 'Retiro Del Rocio blends modern hospitality with intentional living. From intelligent room experiences and personalized comfort to curated wellness spaces and attentive service, every part of your journey is designed to feel effortless.'],

            ['key' => 'home.offers_heading', 'label' => 'Offers section heading', 'type' => 'text', 'default' => 'Explore our exclusive offers'],

            ['key' => 'home.destination_title', 'label' => '“Destination” heading', 'type' => 'text', 'default' => 'More than a destination'],
            ['key' => 'home.destination_text', 'label' => '“Destination” paragraph', 'type' => 'textarea', 'default' => 'Surrounded by calming architecture and refined hospitality, every experience is thoughtfully crafted to help you slow down and reconnect. From personalized room experiences and wellness-centered spaces to seamless service and quiet luxury, every detail is designed to make your stay feel effortless.'],
            ['key' => 'home.destination_image', 'label' => '“Destination” image', 'type' => 'image', 'default' => 'images/image 5.png'],

            ['key' => 'home.member_title', 'label' => '“Member” heading', 'type' => 'text', 'default' => 'Become a member of Retiro Del Rocio'],
            ['key' => 'home.member_text', 'label' => '“Member” paragraph', 'type' => 'textarea', 'default' => 'Get exclusive discounts on services, experiences, and curated destinations across Jos and beyond. Enjoy member-only perks designed to help you explore more for less.'],
            ['key' => 'home.member_image', 'label' => '“Member” image', 'type' => 'image', 'default' => 'images/image 47.jpg'],
            ['key' => 'home.member_cta_label', 'label' => '“Member” button text', 'type' => 'text', 'default' => 'Subscribe'],
            ['key' => 'home.member_cta_url', 'label' => '“Member” button link', 'type' => 'text', 'default' => '#'],
            ['key' => 'home.member_link_label', 'label' => '“Member” secondary link text', 'type' => 'text', 'default' => 'Contact Us'],
            ['key' => 'home.member_link_url', 'label' => '“Member” secondary link', 'type' => 'text', 'default' => '/contact-us'],

            ['key' => 'home.jos_title', 'label' => '“Explore Jos” heading', 'type' => 'text', 'default' => 'Beyond the stay Explore Jos City'],
            ['key' => 'home.jos_text', 'label' => '“Explore Jos” paragraph', 'type' => 'textarea', 'default' => 'Experience the beauty and calm that make Jos truly unforgettable. From breathtaking rock landscapes and cool weather to peaceful nature trails and rich local culture, every moment invites discovery. Whether you seek adventure, relaxation, or quiet reflection, Jos offers a refreshing escape where nature, serenity, and memorable experiences come together beautifully.'],
            ['key' => 'home.jos_image_1', 'label' => '“Explore Jos” image 1', 'type' => 'image', 'default' => 'images/IMG_2625 1.jpg'],
            ['key' => 'home.jos_image_2', 'label' => '“Explore Jos” image 2', 'type' => 'image', 'default' => 'images/IMG_2620 2.jpg'],
            ['key' => 'home.jos_image_3', 'label' => '“Explore Jos” image 3', 'type' => 'image', 'default' => 'images/IMG_2627 2.jpg'],

            ['key' => 'home.wellness_title', 'label' => '“Wellness lifestyle” heading', 'type' => 'text', 'default' => 'Explore our wellness lifestyle'],
            ['key' => 'home.wellness_image', 'label' => '“Wellness lifestyle” image', 'type' => 'image', 'default' => 'images/image 14.jpg'],
            ['key' => 'home.wellness_cta_label', 'label' => '“Wellness” button text', 'type' => 'text', 'default' => 'Explore'],
            ['key' => 'home.wellness_cta_url', 'label' => '“Wellness” button link', 'type' => 'text', 'default' => '#'],

            ['key' => 'home.train_title', 'label' => '“Train. Recover.” heading', 'type' => 'text', 'default' => 'Train. Recover. Recharge.'],
            ['key' => 'home.train_text', 'label' => '“Train. Recover.” paragraph', 'type' => 'textarea', 'default' => 'Stay active and restore your balance in a space designed for movement, wellness, and recovery. Whether you’re maintaining your routine, starting your day with energy, or unwinding after a long one, our fitness and wellness experience is designed to help you feel refreshed, focused, and recharged throughout your stay.'],
            ['key' => 'home.train_image', 'label' => '“Train. Recover.” image', 'type' => 'image', 'default' => 'images/image 13.jpg'],
        ],
    ],

    'spa' => [
        'label' => 'Spa & Wellness',
        'category' => 'core',
        'chips' => ['Hero', 'Services', 'Discover', 'Why Us'],
        'preview' => '/spa-wellness',
        'fields' => [
            ['key' => 'spa.hero_image', 'label' => 'Hero image', 'type' => 'image', 'default' => 'images/spabg.jpg'],
            ['key' => 'spa.hero_title', 'label' => 'Hero heading', 'type' => 'text', 'default' => 'Rejuvenate Mind, Body & Soul'],
            ['key' => 'spa.hero_text', 'label' => 'Hero subtext', 'type' => 'textarea', 'default' => 'Escape the demands of everyday life and immerse yourself in a world of relaxation.'],
            ['key' => 'spa.hero_cta_label', 'label' => 'Hero button text', 'type' => 'text', 'default' => 'Book Session'],
            ['key' => 'spa.hero_cta_url', 'label' => 'Hero button link', 'type' => 'text', 'default' => '/contact-us'],

            ['key' => 'spa.services_title', 'label' => 'Services heading', 'type' => 'text', 'default' => 'Explore our Spa Services'],
            ['key' => 'spa.services_text', 'label' => 'Services subtext', 'type' => 'textarea', 'default' => 'From therapeutic massages to revitalizing skincare treatments, our wellness experiences are tailored to help you unwind, recharge, and feel your absolute best.'],

            ['key' => 'spa.service_1_image', 'label' => 'Service 1 image', 'type' => 'image', 'default' => 'images/skincare.png'],
            ['key' => 'spa.service_1_title', 'label' => 'Service 1 title', 'type' => 'text', 'default' => 'Skin Care'],
            ['key' => 'spa.service_2_image', 'label' => 'Service 2 image', 'type' => 'image', 'default' => 'images/manicure.png'],
            ['key' => 'spa.service_2_title', 'label' => 'Service 2 title', 'type' => 'text', 'default' => 'Manicure & Pedicure'],
            ['key' => 'spa.service_3_image', 'label' => 'Service 3 image', 'type' => 'image', 'default' => 'images/sauna.jpg'],
            ['key' => 'spa.service_3_title', 'label' => 'Service 3 title', 'type' => 'text', 'default' => 'Sauna Baths'],
            ['key' => 'spa.service_4_image', 'label' => 'Service 4 image', 'type' => 'image', 'default' => 'images/massage.png'],
            ['key' => 'spa.service_4_title', 'label' => 'Service 4 title', 'type' => 'text', 'default' => 'Massage'],

            ['key' => 'spa.discover_image', 'label' => 'Discover band image', 'type' => 'image', 'default' => 'images/image 1.jpg'],
            ['key' => 'spa.discover_title', 'label' => 'Discover heading', 'type' => 'text', 'default' => 'Discover a Healthier Way to Relax'],
            ['key' => 'spa.discover_text', 'label' => 'Discover paragraph', 'type' => 'textarea', 'default' => 'Wellness is more than a treatment—it’s a lifestyle. Experience holistic care that nurtures your body, mind, and spirit.'],

            ['key' => 'spa.why_title', 'label' => '“Why us” heading', 'type' => 'text', 'default' => 'WHY Retiro Del Rocio'],
            ['key' => 'spa.features', 'label' => '“Why us” features', 'type' => 'repeater', 'item' => ['title' => 'Title', 'text' => 'Description'], 'default' => [
                ['title' => 'Premium Products', 'text' => 'We use carefully selected, high-quality products to ensure exceptional results and comfort.'],
                ['title' => 'Certified Professionals', 'text' => 'Our experienced wellness specialists are dedicated to delivering personalized care and outstanding service.'],
                ['title' => 'Personalized Treatments', 'text' => 'Every guest receives tailored recommendations and treatments designed around their unique needs.'],
                ['title' => 'Serene Environment', 'text' => 'Enjoy a peaceful atmosphere thoughtfully designed to help you relax, recharge, and reconnect.'],
            ]],
        ],
    ],

    'navigation' => [
        'label' => 'Navigation Menu',
        'category' => 'system',
        'chips' => ['Logo', 'Desktop Nav', 'Mobile Nav', 'Links'],
        'preview' => '/',
        'fields' => [
            ['key' => 'nav.logo', 'label' => 'Navbar logo', 'type' => 'image', 'default' => 'images/Hotel Logo 1.png'],
            ['key' => 'nav.links', 'label' => 'Menu links', 'type' => 'repeater', 'item' => ['label' => 'Label', 'url' => 'Link (URL)'], 'default' => [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'Rooms & Apartment', 'url' => '/rooms-apartment'],
                ['label' => 'Gym', 'url' => '#'],
                ['label' => 'Cinema', 'url' => '#'],
                ['label' => 'Restaurant', 'url' => '#'],
                ['label' => 'Spa/Wellness', 'url' => '/spa-wellness'],
            ]],
            ['key' => 'nav.cta_label', 'label' => 'Button text', 'type' => 'text', 'default' => 'Get in touch'],
            ['key' => 'nav.cta_url', 'label' => 'Button link', 'type' => 'text', 'default' => '/contact-us'],
        ],
    ],

    'footer' => [
        'label' => 'Footer Editor',
        'category' => 'system',
        'chips' => ['Brand', 'Links', 'Contact', 'Legal'],
        'preview' => '/',
        'fields' => [
            ['key' => 'footer.logo', 'label' => 'Footer logo', 'type' => 'image', 'default' => 'images/Logo footer.png'],
            ['key' => 'footer.brand_text', 'label' => 'Brand description', 'type' => 'textarea', 'default' => 'Experience the elegance of stay at Retiro Del Rocio, where luxury meets world-class comfort in every detail.'],
            ['key' => 'footer.links', 'label' => 'Helpful links', 'type' => 'repeater', 'item' => ['label' => 'Label', 'url' => 'Link (URL)'], 'default' => [
                ['label' => 'Home', 'url' => '/'],
                ['label' => 'About Us', 'url' => '#'],
                ['label' => 'Rooms & Apartments', 'url' => '/rooms-apartment'],
            ]],
            ['key' => 'footer.email', 'label' => 'Contact email', 'type' => 'text', 'default' => 'hello@retirodelrocio.com'],
            ['key' => 'footer.address', 'label' => 'Address', 'type' => 'textarea', 'default' => 'No. 1, Off Liberty Boulevard, Millionaire Quarters, Jos, Plateau State'],
            ['key' => 'footer.phone', 'label' => 'Phone number', 'type' => 'text', 'default' => '(+234) 7012623680'],
            ['key' => 'footer.copyright', 'label' => 'Copyright line', 'type' => 'text', 'default' => '2026 Retiro Del Rocio. All Rights Reserved.'],
            ['key' => 'footer.policy_links', 'label' => 'Policy links (bottom bar)', 'type' => 'repeater', 'item' => ['label' => 'Label', 'url' => 'Link (URL)'], 'default' => [
                ['label' => 'Privacy Policy', 'url' => '#'],
                ['label' => 'Terms of Service', 'url' => '#'],
                ['label' => 'Booking Policy', 'url' => '#'],
            ]],
        ],
    ],

    'pickup' => [
        'label' => 'Vehicle Pickup',
        'category' => 'core',
        'chips' => ['Service', 'Locations', 'Vehicles'],
        'preview' => '/rooms-apartment',
        'fields' => [
            ['key' => 'pickup.title', 'label' => 'Popup title', 'type' => 'text', 'default' => 'Vehicle Pickup Service'],
            ['key' => 'pickup.heading', 'label' => 'Feature heading', 'type' => 'text', 'default' => 'Premium Chauffeur Experience'],
            ['key' => 'pickup.text', 'label' => 'Feature paragraph', 'type' => 'textarea', 'default' => 'Combine luxury accommodation with premium transportation services and enjoy special packages designed to enhance your stay from the moment you arrive.'],
            ['key' => 'pickup.image_1', 'label' => 'Popup image (left)', 'type' => 'image', 'default' => 'images/airportpickup image popup1.jpg'],
            ['key' => 'pickup.image_2', 'label' => 'Popup image (right)', 'type' => 'image', 'default' => 'images/airportpickup image popup2.jpg'],
            ['key' => 'pickup.locations', 'label' => 'Pickup locations', 'type' => 'repeater', 'item' => ['name' => 'Location name'], 'default' => [
                ['name' => 'Airport Pickup'],
                ['name' => 'Valgee'],
                ['name' => 'Nengee'],
                ['name' => 'Plateau Riders'],
            ]],
        ],
    ],

    'checkout' => [
        'label' => 'Checkout',
        'category' => 'core',
        'chips' => ['Booking Summary', 'Payment', 'Confirmation'],
        'preview' => '/rooms-apartment',
        'fields' => [
            ['key' => 'checkout.summary_title', 'label' => 'Summary heading', 'type' => 'text', 'default' => 'Summary'],
            ['key' => 'checkout.edit_label', 'label' => '“Edit selection” link text', 'type' => 'text', 'default' => 'Edit selection'],
            ['key' => 'checkout.guest_title', 'label' => 'Guest section heading', 'type' => 'text', 'default' => "Who's Checking in?"],
            ['key' => 'checkout.secure_note', 'label' => 'Secure-payment note', 'type' => 'textarea', 'default' => 'Card details are entered securely in the Paystack window.'],
            ['key' => 'checkout.pay_label', 'label' => 'Pay button text', 'type' => 'text', 'default' => 'Make reservation'],
        ],
    ],

    'spareservation' => [
        'label' => 'Spa Reservation',
        'category' => 'core',
        'chips' => ['Popup', 'Summary'],
        'preview' => '/spa-wellness',
        'fields' => [
            ['key' => 'spares.title', 'label' => 'Popup heading', 'type' => 'text', 'default' => 'Reservation'],
            ['key' => 'spares.intro', 'label' => 'Popup intro text', 'type' => 'textarea', 'default' => 'Choose your spa services, the number of guests and a preferred date & time. We’ll confirm your reservation after a secure payment.'],
            ['key' => 'spares.service_label', 'label' => '“Select service” label', 'type' => 'text', 'default' => 'Select Spa Service'],
            ['key' => 'spares.special_label', 'label' => '“Special request” label', 'type' => 'text', 'default' => 'Special Request'],
            ['key' => 'spares.summary_title', 'label' => 'Reservation Summary heading', 'type' => 'text', 'default' => 'Reservation Summary'],
            ['key' => 'spares.summary_text', 'label' => 'Reservation Summary text', 'type' => 'textarea', 'default' => 'Review your selected services and reservation details below, then complete your secure payment to confirm your booking.'],
            ['key' => 'spares.cta_label', 'label' => 'Popup button text', 'type' => 'text', 'default' => 'Complete Reservation'],
        ],
    ],

    'spacheckout' => [
        'label' => 'Spa Checkout',
        'category' => 'core',
        'chips' => ['Checkout', 'Payment', 'Success'],
        'preview' => '/spa-wellness',
        'fields' => [
            // Checkout page
            ['key' => 'spacheckout.checkout_heading', 'label' => 'Checkout heading', 'type' => 'text', 'default' => 'Complete your spa reservation securely in under 2 minutes.'],
            ['key' => 'spacheckout.customer_title', 'label' => 'Customer section heading', 'type' => 'text', 'default' => 'Customer Details'],
            ['key' => 'spacheckout.cancellation_title', 'label' => 'Cancellation policy heading', 'type' => 'text', 'default' => 'Cancellation Policy'],
            ['key' => 'spacheckout.cancellation_text', 'label' => 'Cancellation policy text', 'type' => 'textarea', 'default' => 'Reschedule or cancel up to 24 hours before your appointment for a full refund. Within 24 hours, the convenience fee is non-refundable.'],
            ['key' => 'spacheckout.secure_note', 'label' => 'Secure-payment note', 'type' => 'textarea', 'default' => 'Card details are entered securely in the Paystack window.'],
            ['key' => 'spacheckout.pay_label', 'label' => 'Pay button text', 'type' => 'text', 'default' => 'Make reservation'],

            // Success page
            ['key' => 'spacheckout.success_title', 'label' => 'Success heading', 'type' => 'text', 'default' => 'Reservation Successful!'],
            ['key' => 'spacheckout.success_text', 'label' => 'Success message', 'type' => 'textarea', 'default' => 'Your spa reservation is confirmed.'],
        ],
    ],

    'contact' => [
        'label' => 'Contact Page',
        'category' => 'core',
        'chips' => ['Form', 'Enquiries', 'FAQs'],
        'preview' => '/contact-us',
        'fields' => [
            ['key' => 'contact.form_title', 'label' => 'Form heading', 'type' => 'text', 'default' => 'Get in Touch'],
            ['key' => 'contact.form_subtitle', 'label' => 'Form subheading', 'type' => 'text', 'default' => 'You can reach us anytime'],

            ['key' => 'contact.enquiry_eyebrow', 'label' => 'Enquiries eyebrow', 'type' => 'text', 'default' => 'Let’s Start a Conversation'],
            ['key' => 'contact.enquiry_title', 'label' => 'Enquiries heading', 'type' => 'text', 'default' => 'General Enquires'],
            ['key' => 'contact.enquiry_text', 'label' => 'Enquiries paragraph', 'type' => 'textarea', 'default' => 'For services inquiries, project discussions, or partnerships opportunities, please reach out using the contact details or form. A member of our team will get back to you shortly.'],

            ['key' => 'contact.address', 'label' => 'Address', 'type' => 'textarea', 'default' => 'No. 1, Off Liberty Boulevard, Millionaire Quarters, Jos, Plateau State'],
            ['key' => 'contact.email', 'label' => 'Contact email', 'type' => 'text', 'default' => 'hello@retirodelrocio.com'],
            ['key' => 'contact.phone', 'label' => 'Phone number', 'type' => 'text', 'default' => '(+234) 7012623680'],
            ['key' => 'contact.phone_note', 'label' => 'Phone note', 'type' => 'text', 'default' => 'Get in touch with us. Speak to one of our business reps'],
            ['key' => 'contact.hours', 'label' => 'Opening hours', 'type' => 'text', 'default' => 'Mon - Fri  from 9am - 4pm'],

            ['key' => 'contact.facebook', 'label' => 'Facebook URL', 'type' => 'text', 'default' => '#'],
            ['key' => 'contact.x', 'label' => 'X (Twitter) URL', 'type' => 'text', 'default' => '#'],
            ['key' => 'contact.instagram', 'label' => 'Instagram URL', 'type' => 'text', 'default' => '#'],
            ['key' => 'contact.linkedin', 'label' => 'LinkedIn URL', 'type' => 'text', 'default' => '#'],

            ['key' => 'contact.faqs', 'label' => 'Frequently asked questions', 'type' => 'repeater', 'item' => ['q' => 'Question', 'a' => 'Answer'], 'default' => [
                ['q' => 'Can I change my room booking?', 'a' => 'Yes. You can adjust or reschedule your booking from your confirmation email or by contacting our team — changes are subject to availability and our cancellation policy.'],
                ['q' => 'Can I modify my selection after making reservations?', 'a' => 'Absolutely. Reach out to our reservations team before your stay and we will update your selection where possible.'],
                ['q' => 'Can I modify my seat selection after booking a ticket?', 'a' => 'Cinema and experience seats can be changed up to a few hours before the session, depending on availability.'],
                ['q' => 'Is my payment information secure on Retiro Del Rocio?', 'a' => 'Yes. All payments are processed over encrypted, secure channels and we never store your full card details.'],
                ['q' => 'What if I have trouble booking tickets?', 'a' => 'Our support team is available Mon–Fri, 9am–4pm. Call or email us and we will help you complete your booking.'],
            ]],
        ],
    ],
];

// Derive a dot-safe binding name for each field + a flat defaults map.
$defaults = [];
foreach ($pages as $pageKey => &$page) {
    foreach ($page['fields'] as &$field) {
        $field['name'] = str_replace('.', '__', $field['key']);
        $defaults[$field['key']] = $field['default'] ?? null;
    }
    unset($field);
}
unset($page);

return [
    'pages' => $pages,
    'defaults' => $defaults,
];
