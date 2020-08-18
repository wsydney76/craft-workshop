<?php

namespace project\modules\main;

use Craft;
use craft\base\Element;
use craft\commerce\elements\Order;
use craft\commerce\models\LineItem;
use craft\elements\Asset;
use craft\elements\db\MatrixBlockQuery;
use craft\elements\Entry;
use craft\events\DefineBehaviorsEvent;
use craft\events\DefineGqlTypeFieldsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterElementActionsEvent;
use craft\events\RegisterElementExportersEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\SetElementTableAttributeHtmlEvent;
use craft\gql\TypeManager;
use craft\i18n\PhpMessageSource;
use craft\services\Fields;
use craft\services\Utilities;
use craft\web\twig\variables\CraftVariable;
use craft\web\View;
use GraphQL\Type\Definition\Type;
use project\modules\main\behaviors\AssetBehavior;
use project\modules\main\behaviors\CartBehavior;
use project\modules\main\behaviors\EntryBehavior;
use project\modules\main\behaviors\LineItemBehavior;
use project\modules\main\behaviors\MatrixBlockQueryBehavior;
use project\modules\main\elements\actions\SetScreeningSoldOutAction;
use project\modules\main\exporters\ScreeningsExporter;
use project\modules\main\fields\BackgroundColorField;
use project\modules\main\fields\BackgroundImageField;
use project\modules\main\fields\SortableTextField;
use project\modules\main\gql\fields\RolesField;
use project\modules\main\gql\fields\ShortDescriptionField;
use project\modules\main\modules\drafts\DraftsModule;
use project\modules\main\modules\members\MembersModule;
use project\modules\main\services\ContentService;
use project\modules\main\services\MigrationService;
use project\modules\main\services\ValidationService;
use project\modules\main\twigextensions\TwigExtension;
use project\modules\main\utilities\ContentUtility;
use project\modules\main\variables\ContentVariable;
use project\modules\main\variables\GdprVariable;
use project\resources\cp\CpAssets;
use putyourlightson\blitz\drivers\warmers\BaseCacheWarmer;
use putyourlightson\blitz\events\RefreshCacheEvent;
use wsydney76\staticcache\events\PageCountEvent;
use wsydney76\staticcache\services\CacheService;
use yii\base\Event;
use yii\base\Module;
use function strpos;
use const DIRECTORY_SEPARATOR;

class MainModule extends Module
{

// Static
// Fields
    public static $services;

    protected $releaseYearFieldId;

    public function init()
    {

        // Set the controllerNamespace based on whether this is a console or web request
        if (Craft::$app->getRequest()->getIsConsoleRequest()) {
            $this->controllerNamespace = 'project\modules\\main\\console\\controllers';
        } else {
            $this->controllerNamespace = 'project\modules\\main\\controllers';
        }

        // Register services
        $this->setComponents([
            'validate' => ValidationService::class,
            'content' => ContentService::class,
            'migrate' => MigrationService::class
        ]);
        self::$services = $this;

        // Base template directory
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
            $e->roots['main'] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'main';
        });

        // Register translation category
        Craft::$app->i18n->translations['main'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'basePath' => __DIR__ . '/translations',
            'allowOverrides' => true,
        ];

        // Register Twig Extensions
        Craft::$app->view->registerTwigExtension(new TwigExtension());

        // Register Variable in Twig
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $e) {
            /** @var CraftVariable $variable */
            $variable = $e->sender;
            $variable->set('content', ContentVariable::class);
            $variable->set('gdpr', GdprVariable::class);
        });

        // Add Behaviors
        Event::on(
            Entry::class,
            Entry::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
            $event->behaviors['entryBehavior'] = EntryBehavior::class;
        }
        );

        Event::on(
            Asset::class,
            Asset::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
            $event->behaviors[] = AssetBehavior::class;
        }
        );

        Event::on(
            MatrixBlockQuery::class,
            MatrixBlockQuery::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
            $event->behaviors['matrixBlockQueryBehavior'] = MatrixBlockQueryBehavior::class;
        }
        );

        Event::on(
            Order::class,
            Order::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
            $event->behaviors['cartBehavior'] = CartBehavior::class;
        }
        );
        Event::on(
            LineItem::class,
            LineItem::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
            $event->behaviors['lineItemBehavior'] = LineItemBehavior::class;
        }
        );

        // Enable caching for paginated stuff
        // This relies on naming convention xxxIndex (Single) -> xxx (Channel)
        // See also config/staticcache.php
        if (Craft::$app->plugins->isPluginEnabled('staticcache')) {

            Event::on(
                CacheService::class,
                CacheService::EVENT_STATICCACHE_PAGECOUNT, function(PageCountEvent $event) {
                if (strpos($event->entry->section->handle, 'Index')) {
                    $event->pageCount = $this->content->getPageCountForIndex($event->entry, $event->perPage);
                }
            }
            );
        }

        // Register additional fields to graphql
        Event::on(
            TypeManager::class,
            TypeManager::EVENT_DEFINE_GQL_TYPE_FIELDS, function(DefineGqlTypeFieldsEvent $event) {

            if ($event->typeName == 'EntryInterface') {
                $event->fields['shortDescription'] = new ShortDescriptionField();
            }

            if ($event->typeName == 'person_person_Entry') {
                $event->fields['roles'] = new RolesField();
            }
        }
        );

        // Register field type.
        Event::on(
            Fields::class,
            Fields::EVENT_REGISTER_FIELD_TYPES,
            function(RegisterComponentTypesEvent $event) {
                $event->types[] = BackgroundColorField::class;
                $event->types[] = BackgroundImageField::class;
                $event->types[] = SortableTextField::class;
            }
        );

        // Register Blitz events
        if (Craft::$app->plugins->isPluginEnabled('blitz')) {
            Event::on(
                BaseCacheWarmer::class,
                BaseCacheWarmer::EVENT_BEFORE_WARM_CACHE, function(RefreshCacheEvent $event) {
                    $event->siteUris = $this->content->getCacheSiteUris($event->siteUris);
            }
            );
        }

        // Declare and load sub modules
        $this->modules = [
            'members' => MembersModule::class,
            'cpDrafts' => DraftsModule::class
        ];

        $this->getModule('members', true);

        // CP specifics
        if (Craft::$app->request->isCpRequest) {

            // Base template directory
            Event::on(
                View::class,
                View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
                $e->roots['main'] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'main';
            });

            // Register CSS Files
            Event::on(
                View::class,
                View::EVENT_BEFORE_RENDER_TEMPLATE, function() {
                Craft::$app->view->registerAssetBundle(CpAssets::class);
            }
            );

            $this->getModule('cpDrafts', true);

            // Register Exporters
            Event::on(
                Entry::class,
                Entry::EVENT_REGISTER_EXPORTERS, function(RegisterElementExportersEvent $event) {
                $event->exporters[] = ScreeningsExporter::class;
            });

            // Register Utilities
            Event::on(
                Utilities::class,
                Utilities::EVENT_REGISTER_UTILITY_TYPES, function(RegisterComponentTypesEvent $event) {
                $event->types[] = ContentUtility::class;
            }, null, false
            );

            // Register Element Actions
            Event::on(
                Entry::class,
                Entry::EVENT_REGISTER_ACTIONS, function(RegisterElementActionsEvent $event) {
                $event->actions[] = SetScreeningSoldOutAction::class;
            }
            );

            // Register custom field display for element indexes
            $this->releaseYearFieldId = Craft::$app->fields->getFieldByHandle('releaseYear')->id;
            Event::on(
                Entry::class,
                Element::EVENT_SET_TABLE_ATTRIBUTE_HTML, function(SetElementTableAttributeHtmlEvent $event) {
                $entry = $event->sender;
                if (($entry->section->handle == 'film') && $event->attribute == 'field:' . $this->releaseYearFieldId) {
                    $event->handled = true;
                    $event->html = $entry->releaseYear;
                }
            }
            );
        }
    }

}
