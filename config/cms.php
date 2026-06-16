<?php

/*
|--------------------------------------------------------------------------
| Website CMS field schema
|--------------------------------------------------------------------------
| Single source of truth for the editable homepage + contact content.
| The admin editor (Admin\Cms\Edit) renders fields from here, and the
| public blades read values via cms()/cms_image()/cms_array() using the
| `default` defined below — so unset keys fall back to the original copy.
|
| Field types: text | textarea | image | repeater
| Each field key is dotted ("home.hero_image"); a dot-safe binding `name`
| is derived automatically for Livewire wire:model.
*/

$sections = [
    'homepage' => [
        'label' => 'Homepage',
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

            ['key' => 'home.jos_title', 'label' => '“Explore Jos” heading', 'type' => 'text', 'default' => 'Beyond the stay Explore Jos City'],
            ['key' => 'home.jos_text', 'label' => '“Explore Jos” paragraph', 'type' => 'textarea', 'default' => 'Experience the beauty and calm that make Jos truly unforgettable. From breathtaking rock landscapes and cool weather to peaceful nature trails and rich local culture, every moment invites discovery. Whether you seek adventure, relaxation, or quiet reflection, Jos offers a refreshing escape where nature, serenity, and memorable experiences come together beautifully.'],
            ['key' => 'home.jos_image_1', 'label' => '“Explore Jos” image 1', 'type' => 'image', 'default' => 'images/IMG_2625 1.jpg'],
            ['key' => 'home.jos_image_2', 'label' => '“Explore Jos” image 2', 'type' => 'image', 'default' => 'images/IMG_2620 2.jpg'],
            ['key' => 'home.jos_image_3', 'label' => '“Explore Jos” image 3', 'type' => 'image', 'default' => 'images/IMG_2627 2.jpg'],

            ['key' => 'home.wellness_title', 'label' => '“Wellness lifestyle” heading', 'type' => 'text', 'default' => 'Explore our wellness lifestyle'],
            ['key' => 'home.wellness_image', 'label' => '“Wellness lifestyle” image', 'type' => 'image', 'default' => 'images/image 14.jpg'],

            ['key' => 'home.train_title', 'label' => '“Train. Recover.” heading', 'type' => 'text', 'default' => 'Train. Recover. Recharge.'],
            ['key' => 'home.train_text', 'label' => '“Train. Recover.” paragraph', 'type' => 'textarea', 'default' => 'Stay active and restore your balance in a space designed for movement, wellness, and recovery. Whether you’re maintaining your routine, starting your day with energy, or unwinding after a long one, our fitness and wellness experience is designed to help you feel refreshed, focused, and recharged throughout your stay.'],
            ['key' => 'home.train_image', 'label' => '“Train. Recover.” image', 'type' => 'image', 'default' => 'images/image 13.jpg'],
        ],
    ],

    'contact' => [
        'label' => 'Contact Page',
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
foreach ($sections as $sectionKey => &$section) {
    foreach ($section['fields'] as &$field) {
        $field['name'] = str_replace('.', '__', $field['key']);
        $defaults[$field['key']] = $field['default'] ?? null;
    }
    unset($field);
}
unset($section);

return [
    'sections' => $sections,
    'defaults' => $defaults,
];
