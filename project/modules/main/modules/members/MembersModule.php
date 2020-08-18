<?php

namespace project\modules\main\modules\members;

use craft\events\RegisterUrlRulesEvent;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use project\modules\main\modules\members\variables\MembersVariable;
use project\modules\main\modules\members\variables\WatchListVariable;
use yii\base\Event;
use yii\base\Module;
use yii\db\Exception;

class MembersModule extends Module
{
    /**
     * @throws Exception
     */
    public function init()
    {

        // Register routes
        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_SITE_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['watchlist/add/<id:[\d]+>'] = 'main/members/members/add-to-watchlist';
            $event->rules['watchlist/delete/<id:[\d]+>'] = 'main/members/members/delete-from-watchlist';
        });

        // Set craft.xxx variables for twig templates
        Event::on(
            CraftVariable::class,
            CraftVariable::EVENT_INIT, function(Event $event) {
            $event->sender->set('members', MembersVariable::class);
            $event->sender->set('watchlist', WatchListVariable::class);
        });


        parent::init();

    }

}
