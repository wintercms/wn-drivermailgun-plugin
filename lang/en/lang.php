<?php

$name = 'Mailgun API key';

return [
    'plugin_description' => 'Mailgun mail driver plugin',

    'fields' => [
        'mailgun_api_key' => [
            'label' => $name,
            'comment' => 'Enter your ' . $name,
        ],
    ],
];
