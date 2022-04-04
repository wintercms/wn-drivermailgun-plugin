<?php

return [
    'plugin_name' => 'Mailgun mail driver',
    'plugin_description' => 'Mailgun mail driver functionality for Winter CMS',

    'fields' => [
        'mailgun_domain' => [
            'label' => 'Mailgun domain',
            'comment' => 'Please specify the Mailgun domain name.',
        ],
        'mailgun_secret' => [
            'label' => 'Mailgun secret',
            'comment' => 'Enter your Mailgun secret',
        ],
    ],
];
