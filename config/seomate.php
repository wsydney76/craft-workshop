<?php

use config\Env;

return [
    '*' => [
        'cacheEnabled' => false,
        'defaultProfile' => 'standard',

        'defaultMeta' => [
            'description' => ['globalSeo.globalseofields:settings.description'],
            'image' => ['globalSeo.globalseofields:settings.image'],
        ],

        'fieldProfiles' => [
            'standard' => [
                'title' => ['seoFields:settings.alternativeTitle', 'title'],
                'description' => ['seoFields:settings.description', 'teaser'],
                'image' => ['seoFields:settings.image', 'featuredImage'],
                'robots' => ['seoFields:settings.robots'],
            ],
        ],

        'sitemapEnabled' => true,
        'sitemapLimit' => 100,
        'sitemapConfig' => [
            'elements' => [
                'news' => ['changefreq' => 'daily', 'priority' => 1],
                'film' => ['changefreq' => 'weekly', 'priority' => 0.5],
                'person' => ['changefreq' => 'weekly', 'priority' => 0.5],
                'eventsection' => ['changefreq' => 'weekly', 'priority' => 0.5],
                'screening' => ['changefreq' => 'daily', 'priority' => 0.5],
            ],
        ],

        'siteName' => Env::ENVIRONMENT . ' ' . Env::SYSTEM_NAME,
        'sitenameSeparator' => ' - '
    ],

    'production' => [
        'cacheEnabled' => true,
        'siteName' => Env::SYSTEM_NAME,
    ]

];
