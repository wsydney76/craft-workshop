<?php

namespace project\modules\ads;

use Craft;
use craft\events\DefineBehaviorsEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\i18n\PhpMessageSource;
use craft\services\UserPermissions;
use craft\web\twig\variables\Cp;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\web\View;
use project\modules\ads\behaviors\CraftVariableBehavior;
use project\modules\ads\models\AdModel;
use project\modules\ads\models\SettingsModel;
use yii\base\Event;
use yii\base\Module;

class AdsModule extends Module
{

    public function init()
    {

        // Register translation category
        Craft::$app->i18n->translations['ads'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'basePath' => __DIR__ . '/translations',
            'allowOverrides' => true,
        ];

        // Register Site Urls
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES, function(RegisterUrlRulesEvent $event) {

            // Route directly to template, skip controller
            $event->rules['ads/<id:[\d]+>'] = ['template' => '_ads/show'];

            // Route to controller action
            $event->rules['ads'] = 'ads/ads/index';
            $event->rules['ads/new'] = 'ads/ads/new';
            $event->rules['ads/withdraw/<id:[\d]+>'] = 'ads/ads/withdraw';
            $event->rules['ads/<type:.*>'] = 'ads/ads/index';
        }
        );

        // Register CP Urls
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['ads/<id:[\d]+>'] = 'ads/ads/edit';
        });

        // Register Templates directory
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
            $e->roots['ads'] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates';
        });

        // Create Permissions
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
            $event->permissions['Ads Module'] = [
                'editAds' => [
                    'label' => 'View and edit User Ads'
                ]
            ];
        }
        );

        // Set Nav
        Event::on(Cp::class, Cp::EVENT_REGISTER_CP_NAV_ITEMS, function(RegisterCpNavItemsEvent $event) {
            if (Craft::$app->user->checkPermission('editAds')) {
                $nav = [
                    'url' => SettingsModel::MANAGEADSURL,
                    'label' => 'Ads',
                    'icon' => '@app/icons/search.svg',
                    'badgeCount' => AdModel::find()->status('active')->count()
                ];

                $i = 0;
                foreach ($event->navItems as $i => $navItem) {
                    if ($navItem['url'] == 'assets') {
                        break;
                    }
                }
                array_splice($event->navItems, $i + 1, 0, [$nav]);
            }
        });

        // Register Craft variable
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
            $event->behaviors[] = CraftVariableBehavior::class;
        }
        );

        parent::init();
    }
}
