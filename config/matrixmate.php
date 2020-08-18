<?php

return [
    'fields' => [
        'contentBuilder' => [
            'types' => [
                'textImage' => [
                    'tabs' => [
                        [
                            'label' => \Craft::t('site', 'Content'),
                            'fields' => ['heading', 'text', 'image'],
                        ],
                        [
                            'label' => \Craft::t('site', 'Button'),
                            'fields' => ['buttonTarget', 'buttonCaption'],
                        ],
                        [
                            'label' => \Craft::t('site', 'Layout'),
                            'fields' => ['backgroundImageData','backgroundStyle','reusableBackgroundColor']
                        ]
                    ]
                ]
            ],
        ],
        'bodyContent' => [
            'types' => [
                'text' => [
                    'tabs' => [
                        [
                            'label' => \Craft::t('site', 'Content'),
                            'fields' => ['text']
                        ],
                        [
                            'label' => \Craft::t('site', 'Layout'),
                            'fields' => ['highlightBlock']
                        ]
                    ]
                ],
                'image' => [
                    'tabs' => [
                        [
                            'label' => \Craft::t('site', 'Content'),
                            'fields' => ['image', 'caption']
                        ],
                        [
                            'label' => \Craft::t('site', 'Layout'),
                            'fields' => ['options']
                        ]
                    ]
                ],
                'button' => [
                    'tabs' => [
                        [
                            'label' => \Craft::t('site', 'Content'),
                            'fields' => ['caption', 'target']
                        ],
                        [
                            'label' => \Craft::t('site', 'Layout'),
                            'fields' => ['color']
                        ]
                    ]
                ],
                'heading' => [
                    'tabs' => [
                        [
                            'label' => \Craft::t('site', 'Content'),
                            'fields' => ['text']
                        ],
                        [
                            'label' => \Craft::t('site', 'Layout'),
                            'fields' => ['tag']
                        ]
                    ]
                ]
            ]
        ]
    ],
];
