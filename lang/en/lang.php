<?php

$name = 'Mailgun secret';

return [
    'plugin_description' => 'Mailgun mail driver plugin',

    'fields' => [
        'mailgun_secret' => [
            'label' => $name,
            'comment' => 'Enter your ' . $name,
        ],
    ],
];
