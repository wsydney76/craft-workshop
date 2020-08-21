<?php
/**
 * Yii Application Config
 *
 * Edit this file at your own risk!
 *
 * The array returned by this file will get merged with
 * vendor/craftcms/cms/src/config/app.php and app.[web|console].php, when
 * Craft's bootstrap script is defining the configuration for the entire
 * application.
 *
 * You can define custom modules and system components, and even override the
 * built-in system components.
 *
 * If you want to modify the application config for *only* web requests or
 * *only* console requests, create an app.web.php or app.console.php file in
 * your config/ folder, alongside this one.
 */

use apps\headless\HeadlessModule;
use project\modules\ads\AdsModule;
use project\modules\main\MainModule;
use project\modules\main\models\RequestDataModel;
use project\modules\solrsearch\SolrSearchModule;
use project\modules\suggestions\SuggestionModule;

return [
    'modules' => [
        'main' => MainModule::class,
        'client' => HeadlessModule::class,
        'suggestions' => SuggestionModule::class,
        'project_solrsearch' => SolrSearchModule::class,
        'ads' => AdsModule::class
    ],
    'bootstrap' => [
        'main',
        'client',
        'suggestions',
        'project_solrsearch',
        'ads'
    ],
    'components' => [
        'requestData' => [
            'class' => RequestDataModel::class
        ],
        'mutex' => function() {
            $config = craft\helpers\App::mutexConfig();
            $config['isWindows'] = true;
            return Craft::createObject($config);
        },
    ]
];
