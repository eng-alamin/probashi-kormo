<?php

return [
    'name' => 'Probashi Kormo Overseas & Travels',
    'short_name' => 'Probashi Kormo',
    'tagline' => 'Skill training and trusted manpower recruitment for jobs abroad.',

    'phone' => env('SITE_PHONE'),
    'email' => env('SITE_EMAIL'),
    'address' => env('SITE_ADDRESS'),
    'whatsapp' => env('SITE_WHATSAPP'),
    // Shown on the About page only when set in .env (SITE_LICENSE_NO).
    'license_no' => env('SITE_LICENSE_NO'),
    // Shown on the Contact page only when set in .env
    'hours' => env('SITE_HOURS'),
    // Google Maps > Share > Embed a map: put only the src="..." URL in SITE_MAP_EMBED
    'map_embed' => env('SITE_MAP_EMBED'),
    // Home page defaults. The admin can change them in Settings; blank there means "use these".
    'home' => [
        'title' => 'Your Skills.',
        'highlight' => 'Your Future Abroad.',
        'text' => 'We train skilled workers and connect them with trusted employers overseas — safe, legal and transparent recruitment from start to finish.',
        'stats' => [
            ['500+', 'Workers Sent'],
            ['10+', 'Destination Countries'],
            ['20+', 'Training Trades'],
            ['100%', 'Legal Process'],
        ],
    ],

    'facebook' => 'https://www.facebook.com/probashikormoot',

    // Main navigation. "route" is a named route; the link stays "#" until that route exists.
    'nav' => [
        ['label' => 'Home', 'route' => 'home'],
        ['label' => 'About', 'route' => 'about'],
        ['label' => 'Services', 'route' => 'services'],
        ['label' => 'Training', 'route' => 'training'],
        ['label' => 'Jobs', 'route' => 'jobs'],
        ['label' => 'Countries', 'route' => 'countries'],
        ['label' => 'Gallery', 'route' => 'gallery'],
        ['label' => 'Contact', 'route' => 'contact', 'cta' => true],
    ],

    'footer' => [
        'quick' => [
            ['label' => 'About', 'route' => 'about'],
            ['label' => 'Services', 'route' => 'services'],
            ['label' => 'Training', 'route' => 'training'],
            ['label' => 'Jobs', 'route' => 'jobs'],
            ['label' => 'Countries', 'route' => 'countries'],
        ],
        'more' => [
            ['label' => 'Apply Online', 'route' => 'apply'],
            ['label' => 'Success Stories', 'route' => 'success-stories'],
            ['label' => 'News & Notice', 'route' => 'news'],
            ['label' => 'Gallery', 'route' => 'gallery'],
            ['label' => 'Contact', 'route' => 'contact'],
        ],
        'legal' => [
            ['label' => 'Privacy Policy', 'route' => 'privacy-policy'],
            ['label' => 'Terms', 'route' => 'terms'],
        ],
    ],
];
