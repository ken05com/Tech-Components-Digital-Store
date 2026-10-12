<?php

/**
 * Default dashboard content.
 *
 * These values can be overridden at runtime from the Settings screen (stored
 * in the "settings" table). The defaults contain only facts already present in
 * the project — anything personal that was never supplied is left blank and
 * marked for editing rather than invented.
 */
return [
    'author' => [
        'name' => env('AUTHOR_NAME', 'Shoby Keneth R. Daamo'),
        'address' => env('AUTHOR_ADDRESS', ''),
        'image' => env('AUTHOR_IMAGE', 'images/profile-avatar.jpg'),
    ],

    'reflections' => [
        [
            'key' => 'reflection_1',
            'title' => 'I chose this topic because…',
            'body' => 'Add your reflection in Settings.',
        ],
        [
            'key' => 'reflection_2',
            'title' => 'My 2nd topic is about… because…',
            'body' => 'Add your reflection in Settings.',
        ],
        [
            'key' => 'reflection_3',
            'title' => 'I am proud of my topic because…',
            'body' => 'Add your reflection in Settings.',
        ],
    ],
];
