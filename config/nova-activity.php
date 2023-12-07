<?php

return [
    'table_name' => 'nova_activity',

    'quick_replies' => true,

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
