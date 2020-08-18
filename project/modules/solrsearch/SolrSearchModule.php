<?php

namespace project\modules\solrsearch;

use Craft;
use craft\base\ElementInterface;
use craft\commerce\elements\Product;
use craft\elements\Entry;
use craft\helpers\ArrayHelper;
use project\modules\solrsearch\behaviors\SolrSearchEntryBehavior;
use wsydney76\solrsearch\events\GetAllElementsForSolrSearchEvent;
use wsydney76\solrsearch\events\GetSolrDocForElementEvent;
use wsydney76\solrsearch\services\SearchService;
use yii\base\Event;
use yii\base\Module;

class SolrSearchModule extends Module
{
    public $sections = ['film', 'person', 'news', 'screening', 'location', 'award', 'eventsection','page'];

    public function init()
    {
        if (!Craft::$app->plugins->isPluginEnabled('solrsearch')) {
            return;
        }

        Event::on(
            SearchService::class,
            SearchService::EVENT_GET_SOLR_DOC_FOR_ELEMENT, function(GetSolrDocForElementEvent $event) {

            /** @var ElementInterface $entry */
            $element = $event->element;
            /*if (!in_array($entry->section->handle, $this->sections)) {
                $event->cancel = true;
                return;
            }*/

            if ($element instanceof Product) {
                if($element->site->handle == 'kids') {
                    $event->cancel = true;
                    return;
                }
            }

            // Return the Solr doc, using a method that fits best for your project

            $element->attachBehavior('', SolrSearchEntryBehavior::class);

            $event->doc = $element->getSolrDoc();
            if (!$event->doc) {
                $event->cancel = true;
            }
        }
        );

        Event::on(
            SearchService::class,
            SearchService::EVENT_GET_ALL_ELEMENTS_FOR_SOLR_SEARCH, function(GetAllElementsForSolrSearchEvent $event) {
            // Get entries, eg. via a service
            $entries = [];
            foreach (Craft::$app->sites->getAllSites() as $site) {

                $entriesForSite = Entry::find()
                    // ->section($this->sections)
                    ->site($site)
                    ->with([
                        ['eventSection', ['site' => $site]],
                        ['cast', ['site' => $site]],
                        ['cast.role:persons', ['site' => '*', 'preferSites' => ['de']]],
                        ['crew', ['site' => $site]],
                        ['crew.role:persons', ['site' => '*', 'preferSites' => ['de']]],
                        ['crew.role:departments', ['site' => '*', 'preferSites' => ['de']]],
                        ['bodyContent', ['site' => $site->handle]],
                        ['film', ['site' => '*', 'preferSites' => ['de']]],
                        ['bodyContent', ['site' => $site->handle]],
                        ['contentBuilder', ['site' => $site->handle]],
                        ['trivia', ['site' => $site->handle]],
                        ['genre', ['site' => $site->handle]],
                        ['producedIn', ['site' => $site->handle]],
                    ])
                    ->all();

                $entries = ArrayHelper::merge($entries, $entriesForSite);

                if ($site->handle != 'kids') {
                    $products = Product::find()->site($site)->all();
                    $entries = ArrayHelper::merge($entries, $products);
                }
            }
            $event->elements = $entries;
        }
        );
    }
}
