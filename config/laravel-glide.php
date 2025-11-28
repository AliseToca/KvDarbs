<?php

return [

    /*
     * The driver that will be used to create images. Can be set to gd or imagick.
     */
    'driver' => 'gd',

    'output_disk_name' => 'public',

    'modifiable_image_extensions' => ['jpeg', 'jpg', 'png', 'webp'],

    'presets' => [

        'large' => [
            'w' => 800,
            'h' => 800,
            'fit' => 'crop'
        ],
    ],

    // Responsive image configuration consumed by App\Services\ImageService
    // You can adjust these to match your design system / performance goals.
    'image_variations' => [320, 640, 960, 1280, 1920],

    'responsive' => [
        // Default fallback width (capped by original width)
        'fallback_width' => 1280,

        // Output quality per format (overrides internal defaults)
        'quality' => [
            'webp' => 90,
            'jpg' => 90,
            'jpeg' => 90,
            'png' => 90, // Only used if you explicitly request png
        ],
    ],
];
