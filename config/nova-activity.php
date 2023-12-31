<?php

return [
    'table_name' => 'nova_activity',

    'use_quick_replies' => true,

    'use_comments' => true,

    'use_file_uploads' => false,

    'dates' => [
        'format' => 'd M, Y \a\t H:i',
        // moment.js formatting
        'js_format' => 'Do MMM, YYYY',
        'use_human_readable' => true,
    ],

    'comment_validation' => [
        'type' => 'required',
    ],

    'quick_replies' => [
        'exited' => [
            'name' => 'Exited',
            'background' => '#ef4444',
            'icon' => 'fire',
            'solid_icon' => false,
        ],
        'loved' => [
            'name' => 'Loved',
            'background' => '#f472b6',
            'icon' => 'heart',
            'solid_icon' => false,
        ],
        'happy' => [
            'name' => 'Happy',
            'background' => '#4ade80',
            'icon' => 'emoji-happy',
            'solid_icon' => false,
        ],
        'sad' => [
            'name' => 'Sad',
            'background' => '#facc15',
            'icon' => 'emoji-sad',
            'solid_icon' => false,
        ],
        'thumbsy' => [
            'name' => 'Thumbsy',
            'background' => '#3b82f6',
            'icon' => 'thumb-up',
            'solid_icon' => false,
        ],
    ],
];
