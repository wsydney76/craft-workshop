<?php

namespace project\modules\ads;

use Craft;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\i18n\PhpMessageSource;
use craft\services\UserPermissions;
use craft\web\twig\variables\Cp;
use craft\web\UrlManager;
use craft\web\View;
use project\modules\ads\records\AdsRecord;
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
            $event->rules['ads/new'] = 'ads/ads/new';
            $event->rules['ads/list'] = 'ads/ads/index';
            $event->rules['ads/list/<type:.*>'] = 'ads/ads/index';
        }
        );

        // Register CP Urls
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['ads/edit/<id:[\d]+>'] = 'ads/ads/edit';
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
                    'url' => 'ads',
                    'label' => 'Ads',
                    'icon' => '@app/icons/search.svg',
                    'badgeCount' => AdsRecord::find()->where(['status' => 'open'])->count()
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


        parent::init();
    }
}
