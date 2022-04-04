<?php

return [
    'plugin_description' => "Ajoute le pilote de courriel Mailgun pour Winter CMS",

    'fields' => [
        'mailgun_domain' => [
            'label' => "Domaine Mailgun",
            'comment' => "Veuillez entrer le nom de domaine pour Mailgun",
        ],
        'mailgun_secret' => [
            'label' => "Secret Mailgun",
            'comment' => "Entrez votre secret Mailgun",
        ],
    ],
];
