<?php

return [
    'plugin_description' => "Plugin qui ajoute l'envoi de courriel à travers Mailgun",

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
