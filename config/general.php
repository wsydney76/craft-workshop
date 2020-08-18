<?php
/**
 * General Configuration
 *
 * All of your system's general configuration settings go in here. You can see a
 * list of the available settings in vendor/craftcms/cms/src/config/GeneralConfig.php.
 *
 * @see \craft\config\GeneralConfig
 */

use config\Env;

return [
    // Global settings
    '*' => [
        // Default Week Start Day (0 = Sunday, 1 = Monday...)
        'defaultWeekStartDay' => 1,

        // Whether generated URLs should omit "index.php"
        'omitScriptNameInUrls' => true,

        // Control Panel trigger word
        'cpTrigger' => 'cms',

        // The path to the root directory that should store published CP resources.
        'resourceBasePath' => '@webroot/cmsresources',
        'resourceBaseUrl' => '@web/cmsresources',

        // The string preceding a number which Craft will look for when determining if the current request is for a particular page in a paginated list of pages.
        // 'pageTrigger' => 'page/',

        // Whether to send the 'Powered by Craft' http header
        'sendPoweredByHeader' => false,

        // Whether Craft should create a database backup before applying a new system update
        'backupOnUpdate' => true,

        // The number of backups that Craft should make before it starts deleting the oldest backups.
        'maxBackups' => 5,

        // Whether to enable Craft's template {% cache %} tag on a global basis
        'enableTemplateCaching' => false,
        'cacheElementQueries' => false,

        // Whether to enable GraphQL
        'enableGql' => true,

        // Whether to enable caching of GraphQL queries
        'enableGraphQlCaching' => false,

        // Max No. of revisions
        'maxRevisions' => 10,

        // The amount of time to wait before Craft purges drafts of new elements that were never formally saved.
        'purgeUnsavedDraftsDuration' => 86400,

        // Whether uploaded filenames with non-ASCII characters should be converted to ASCII
        'convertFilenamesToAscii' => true,

        //Whether non-ASCII characters in auto-generated slugs should be converted to ASCII
        'limitAutoSlugsToAscii' => true,

        // Whether images transforms should be generated before page load.
        'generateTransformsBeforePageLoad' => true,

        //The prefix that should be prepended to HTTP error status codes when determining the path to look for an error’s template.
        'errorTemplatePrefix' => '_errors/',

        // path to login page
        'loginPath' => 'members/login',

        // path to password reset page
        'setPasswordPath' => 'members/setpassword',

        // path after resetting password
        'setPasswordSuccessPath' => 'members/myprogram',
        'activateAccountSuccessPath' => 'members/myprogram',

        // set password when activating the account, not in registration form
        'deferPublicRegistrationPassword' => true,

        // login after activation
        'autoLoginAfterAccountActivation' => true,

        // redired after loggin out on front end side
        // 'postLogoutRedirect' => 'members/myprogram',

        'aliases' => [
            // Prevent the @web alias from being set automatically (cache poisoning vulnerability)
            '@web' => Env::DEFAULT_SITE_URL,
            // Lets `./craft clear-caches all` clear CP resources cache
            '@webroot' => dirname(__DIR__) . '/web',
            // Let craft cli commands find controllers
            '@project' => '@root/project',
            //'@modules' => '@root/project/modules',
            //'@resources' => '@root/project/resources',
            '@apps' => '@root/apps',

            // Variables
            '@SYSTEM_NAME' => Env::SYSTEM_NAME,
            '@EMAIL_ADDRESS' => Env::EMAIL_ADDRESS,
            '@EMAIL_SENDER' => Env::EMAIL_SENDER,
            '@SMTP_HOST' => Env::SMTP_HOST,
            '@SMTP_PORT' => Env::SMTP_PORT,
            '@SMTP_USER' => Env::SMTP_USER,
            '@SMTP_PASSWORD' => Env::SMTP_PASSWORD,
            '@GOOGLE_API_KEY' => Env::GOOGLE_API_KEY,

            // Solr Integration
            '@SOLR_BASE_URL' => Env::SOLR_BASE_URL,
            '@SOLR_CORE' => Env::SOLR_CORE
        ],

        // The secure key Craft will use for hashing and encrypting data
        'securityKey' => Env::SECURITY_KEY,

        // Use session instead of cookie for CSRF Protection (which is enabled by default)
        'enableCsrfCookie' => false,

        // Whether to save the project config out to config/project.yaml
        // (see https://docs.craftcms.com/v3/project-config.html)
        'useProjectConfigFile' => true,

        // project specific settings
        'project' => [
            // How many entries shall be show on index pages
            'entriesPerPage' => 5,
            // Which sections shall be shown in wide image layout on index pages
            'wideLayoutSections' => ['person', 'location', 'topic'],
            // Which sections will have paginated Indexes (used for cache warming only)
            'paginatedSections' => ['filmIndex', 'personIndex', 'eventsectionIndex','newsIndex'],
            // Whether to show screening details in a modal window
            'showScreeningDetailsInModal' => true,
            // Whether to embed svgs in html code, set false to link to the image
            'embedSvgs' => true,
            // Sections for cookie confirmation
            'cookiePreferences' => [
                'necessary' => [
                    'fixed' => true,
                    'en' => ['title' => 'Necessary', 'description' => 'Required for the site to work properly'],
                    'de' => ['title' => 'Notwendig', 'description' => 'Benötigt für die ordnungsgemäße Funktion der Seite'],
                ],
                'preferences' => [
                    'fixed' => false,
                    'en' => ['title' => 'Site Preferences', 'description' => 'Required for saving your site preferences, e.g. remembering your username etc.'],
                    'de' => ['title' => 'Site Einstellungen', 'description' => 'Benötigt zum Speichern Ihrer persönlichen Einstellungen, z.B. Benutzername usw'],
                ],
                'analytics' => [
                    'fixed' => false,
                    'en' => ['title' => 'Analytics', 'description' => 'Required to collect site visits, browser types, etc.'],
                    'de' => ['title' => 'Analyse', 'description' => 'Benötigt für statistische Auswertungen'],
                ],
                'marketing' => [
                    'fixed' => false,
                    'en' => ['title' => 'Marketing', 'description' => 'Required to marketing, e.g. newsletters, social media, etc.'],
                    'de' => ['title' => 'Marketing', 'description' => 'Benötigt für Marketing, z.B, Newsletter, Social Media etc'],
                ],
                'media' => [
                    'fixed' => false,
                    'en' => ['title' => 'Media', 'description' => 'Required to embed external media instead of linking'],
                    'de' => ['title' => 'Medien', 'description' => 'Benötigt für das Einbetten externer Medien anstelle einer Verlinkung'],
                ],
            ]
        ]

    ],

    // Temporary Settings for installing or upgrading the site
    'install' => [
        'isSystemLive' => false,
    ],

    // Dev environment settings
    'dev' => [
        // Dev Mode (see https://craftcms.com/guides/what-dev-mode-does)
        'devMode' => true,
    ],

    // Staging environment settings
    'staging' => [
        // Set this to `false` to prevent administrative changes from being made on staging
        'allowAdminChanges' => false,

        // Disable development plugins
        'disabledPlugins' => ['cp-field-inspect', 'inventory'],

    ],

    // Production environment settings
    'production' => [
        // Set this to `false` to prevent administrative changes from being made on production
        'allowAdminChanges' => false,

        // Disable development plugins
        'disabledPlugins' => ['cp-field-inspect', 'inventory'],

        // Whether to enable Craft's template {% cache %} tag on a global basis
        'enableTemplateCaching' => true,
        'cacheElementQueries' => true,

        // Whether to enable caching of GraphQL queries
        'enableGraphQlCaching' => true
    ],
];
