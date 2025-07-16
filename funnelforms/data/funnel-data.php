<?php

return [
    [
        'title' => 'Schritt 1: Persönliche Daten',
        'elements' => [
            [
                'type' => 'text',
                'label' => 'Ihr Name',
                'name'  => 'name',
                'required' => true
            ],
            [
                'type' => 'email',
                'label' => 'Ihre E-Mail-Adresse',
                'name'  => 'email',
                'required' => true
            ]
        ]
    ],
    [
        'title' => 'Schritt 2: Projektinformationen',
        'elements' => [
            [
                'type' => 'select',
                'label' => 'Projektphase',
                'name'  => 'phase',
                'options' => ['Analyse', 'Umsetzung', 'Wartung']
            ],
            [
                'type' => 'textarea',
                'label' => 'Projektbeschreibung',
                'name'  => 'beschreibung'
            ]
        ]
    ],
    [
        'title' => 'Schritt 3: Bildauswahl',
        'elements' => [
            [
                'type' => 'image-choice',
                'label' => 'Wählen Sie ein Design',
                'name' => 'design',
                'options' => [
                    ['label' => 'Modern', 'src' => 'https://example.com/img1.jpg'],
                    ['label' => 'Klassisch', 'src' => 'https://example.com/img2.jpg']
                ]
            ]
        ]
    ]
];
