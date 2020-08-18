<?php

namespace project\modules\suggestions;

use Craft;
use craft\elements\Entry;
use craft\elements\User;
use craft\events\DefineBehaviorsEvent;
use craft\events\DefineGqlTypeFieldsEvent;
use craft\events\RegisterComponentTypesEvent;
use craft\events\RegisterCpNavItemsEvent;
use craft\events\RegisterElementSourcesEvent;
use craft\events\RegisterEmailMessagesEvent;
use craft\events\RegisterGqlQueriesEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\events\RegisterUrlRulesEvent;
use craft\events\RegisterUserPermissionsEvent;
use craft\gql\TypeManager;
use craft\i18n\PhpMessageSource;
use craft\services\Dashboard;
use craft\services\Gc;
use craft\services\Gql;
use craft\services\SystemMessages;
use craft\services\UserPermissions;
use craft\web\twig\variables\Cp;
use craft\web\twig\variables\CraftVariable;
use craft\web\UrlManager;
use craft\web\View;
use project\modules\main\gql\fields\RolesField;
use project\modules\main\gql\fields\ShortDescriptionField;
use project\modules\suggestions\behaviors\CraftVariableBehavior;
use project\modules\suggestions\behaviors\EntryBehavior;
use project\modules\suggestions\events\SuggestionEvent;
use project\modules\suggestions\gql\fields\DateTime;
use project\modules\suggestions\gql\queries\SuggestionsQueries;
use project\modules\suggestions\models\SuggestionModel;
use project\modules\suggestions\records\SuggestionRecord;
use project\modules\suggestions\services\PdfService;
use project\modules\suggestions\services\SuggestionsService;
use project\modules\suggestions\variables\SuggestionsVariable;
use project\modules\suggestions\widgets\SuggestionWidget;
use yii\base\Event;
use yii\base\Module;
use yii\base\NotSupportedException;
use yii\db\Exception;
use const DIRECTORY_SEPARATOR;

/**
 * Class SuggestionModule
 *
 * @package project\modules\suggestions
 *
 * @property suggestionsService $suggestionService
 */
class SuggestionModule extends Module
{

// Static
    public static $services;

    public static $suggestionStatus = [
        'open' => 'Open',
        'atwork' => 'At Work',
        'rejected' => 'Rejected',
        'accepted' => 'Accepted',
    ];

    public static $suggestionSections = ['film', 'person'];

    /**
     * @throws Exception
     * @throws NotSupportedException
     */
    public function init()
    {

        if (!Craft::$app->db->tableExists(SuggestionRecord::tableName())) {
            // Run migration first to setup database tables
            return;
        }

        self::$services = $this;

        $this->controllerNamespace = 'project\modules\\suggestions\\controllers';

        // Make service available
        $this->setComponents([
            'suggestionsService' => SuggestionsService::class,
            'pdfService' => PdfService::class
        ]);

        // Base template directory
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
            $e->roots['suggestions'] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'suggestions';
        });
        Event::on(
            View::class,
            View::EVENT_REGISTER_CP_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
            $e->roots['suggestions'] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'templates' . DIRECTORY_SEPARATOR . 'suggestions';
        });

        // Register translation category
        Craft::$app->i18n->translations['suggestions'] = [
            'class' => PhpMessageSource::class,
            'sourceLanguage' => 'en',
            'basePath' => __DIR__ . '/translations',
            'allowOverrides' => true,
        ];

        // Set Routes

        Event::on(
            UrlManager::class,
            UrlManager::EVENT_REGISTER_CP_URL_RULES, function(RegisterUrlRulesEvent $event) {
            $event->rules['suggestions'] = 'suggestions/suggestions/index';
            $event->rules['suggestions/<id:[\d]+>'] = 'suggestions/suggestions/show';
            $event->rules['suggestions/<id:[\d]+>/delete'] = 'suggestions/suggestions/delete';
            $event->rules['suggestions/pdf'] = 'suggestions/pdf/create';
            $event->rules['suggestions/data'] = 'suggestions/suggestions/data';
        });

        // Create Permissions
        Event::on(
            UserPermissions::class,
            UserPermissions::EVENT_REGISTER_PERMISSIONS, function(RegisterUserPermissionsEvent $event) {
            $event->permissions['Suggestions Module'] = [
                'viewSuggestions' => [
                    'label' => 'View and edit User Suggestions'
                ]
            ];
        }
        );

        // Set Nav
        Event::on(Cp::class, Cp::EVENT_REGISTER_CP_NAV_ITEMS, function(RegisterCpNavItemsEvent $event) {
            if (Craft::$app->user->checkPermission('viewSuggestions')) {
                $nav = [
                    'url' => 'suggestions',
                    'label' => Craft::t('suggestions', 'Suggestions'),
                    'icon' => '@app/icons/envelope.svg',
                    'badgeCount' => SuggestionModel::find()->status('open')->count()
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

        // After save Suggestion
        Event::on(
            SuggestionsService::class,
            SuggestionsService::EVENT_AFTER_SAVE, function(SuggestionEvent $event) {
            $this->suggestionsService->afterSaveSuggestion($event->suggestion, $event->isNew, $event->statusUpdated);
        }
        );

        // Attach behavior

        Event::on(
            Entry::class,
            Entry::EVENT_DEFINE_BEHAVIORS, function(DefineBehaviorsEvent $event) {
            $event->behaviors[] = EntryBehavior::class;
        }
        );

        // Register Edit Screen extensions

        Craft::$app->view->hook('cp.entries.edit.details', function(&$context) {
            if (Craft::$app->user->checkPermission('viewSuggestions') && $context['entry']->id) {
                return Craft::$app->view->renderTemplate('suggestions/entry_suggestions.twig', ['entry' => $context['entry']]);
            }
            return '';
        });

        Craft::$app->view->hook('cp.users.edit.details', function(&$context) {
            if (Craft::$app->user->checkPermission('viewSuggestions') && $context['user']->id) {
                return Craft::$app->view->renderTemplate('suggestions/user_suggestions.twig', ['user' => $context['user']]);
            }
            return '';
        });

        // set craft.suggestions variable for twig templates
        Event::on(CraftVariable::class, CraftVariable::EVENT_INIT, function(Event $event) {
            /** @var CraftVariable $variable */
            $variable = $event->sender;

            $variable->attachBehaviors([
                CraftVariableBehavior::class,
            ]);

            $variable->set('suggestionModule', SuggestionsVariable::class);
        });

        // Register Widget
        Event::on(
            Dashboard::class,
            Dashboard::EVENT_REGISTER_WIDGET_TYPES, function(RegisterComponentTypesEvent $event) {
            $event->types[] = SuggestionWidget::class;
        }
        );

        // Register System Messages
        Event::on(
            SystemMessages::class,
            SystemMessages::EVENT_REGISTER_MESSAGES, function(RegisterEmailMessagesEvent $event) {
            $event->messages[] = [
                'key' => 'suggestion_new',
                'heading' => Craft::t('suggestions', 'If a new suggestion is created'),
                'subject' => Craft::t('suggestions', 'New Suggestion: {{ suggestion.title }}'),
                'body' => Craft::t('suggestions', 'suggestion_new_body')
            ];
            $event->messages[] = [
                'key' => 'suggestion_updatestatus',
                'heading' => Craft::t('suggestions', 'If the status of a suggestion changes'),
                'subject' => Craft::t('suggestions', 'New Suggestion Status: {{ suggestion.title }} {{ statusLabel }}'),
                'body' => Craft::t('suggestions', 'suggestion_updatestatus_body')
            ];
        }
        );

        // Register element source for editors with permission to edit suggestions
        Event::on(
            User::class,
            User::EVENT_REGISTER_SOURCES, function(RegisterElementSourcesEvent $event) {
            $event->sources[] = [
                'key' => 'suggestions_editors',
                'label' => Craft::t('suggestions', 'Suggestion Editors'),
                'criteria' => [
                    'can' => 'viewSuggestions',
                    'admin' => false
                ],
                'defaultSort' => [
                    0 => 'dateCreated',
                    1 => 'desc'
                ]
            ];
        }
        );

        // Hard delete
        Event::on(Gc::class, Gc::EVENT_RUN, function() {
            Craft::$app->gc->hardDelete(SuggestionRecord::tableName());
        });

        // Register GraphQL queries
        Event::on(
            Gql::class,
            Gql::EVENT_REGISTER_GQL_QUERIES, function(RegisterGqlQueriesEvent $event) {
            $event->queries = array_merge($event->queries, SuggestionsQueries::getQueries());
        }
        );

        parent::init();
    }

}
