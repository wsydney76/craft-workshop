<?php

namespace craft\contentmigrations;

use craft\db\Migration;
use craft\errors\ElementNotFoundException;
use project\modules\main\MainModule;
use project\modules\main\services\MigrationService;
use Throwable;
use yii\base\Exception;

/**
 * m191004_174653_initialcontent migration.
 */
class m191004_174653_initialcontent extends Migration
{

    /**
     * @return bool|void
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function safeUp()
    {
        /** @var MigrationService $service */
        $service = MainModule::$services->migrate;

        $service->populateSingle('home', [
            'en' => [
                'title' => 'Craft Festival',
                'fields' => [
                    'teaser' => 'The Ultimate Template'
                ]
            ],
            'de' => [
                'title' => 'Craft Festival',
                'fields' => [
                    'teaser' => 'Die Mutter aller Templates'
                ]
            ],
        ]);

        $service->populateSingle('eventsectionIndex', [
            'en' => [
                'title' => 'Sections',
                'fields' => [
                ]
            ],
            'de' => [
                'title' => 'Reihen',
                'fields' => [
                ]
            ],
        ]);

        $service->populateSingle('filmIndex', [
            'en' => [
                'title' => 'Films',
                'fields' => [
                ]
            ],
            'de' => [
                'title' => 'Filme',
                'fields' => [
                ]
            ],
        ]);

        $service->populateSingle('personIndex', [
            'en' => [
                'title' => 'Stars',
                'fields' => [
                ]
            ],
            'de' => [
                'title' => 'Stars',
                'fields' => [
                ]
            ],
        ]);

        $service->populateSingle('newsIndex', [
            'en' => [
                'title' => 'News',
                'fields' => [
                ]
            ],
            'de' => [
                'title' => 'Nachrichten',
                'fields' => [
                ]
            ],
        ]);

        $service->populateSingle('topicIndex', [
            'en' => [
                'title' => 'Topics',
                'fields' => [
                ]
            ],
            'de' => [
                'title' => 'Themen',
                'fields' => [
                ]
            ],
        ]);

        $service->populateSingle('locationIndex', [
            'en' => [
                'title' => 'Locations',
                'fields' => [
                ]
            ],
            'de' => [
                'title' => 'Orte',
                'fields' => [
                ]
            ],
        ]);

        $service->populateSingle('screeningIndex', [
            'en' => [
                'title' => 'Program',
                'fields' => [
                ]
            ],
            'de' => [
                'title' => 'Programm',
                'fields' => [
                ]
            ],
        ]);

        $service->populateSingle('search', [
            'en' => [
                'title' => 'Search',
                'fields' => [
                ]
            ],
            'de' => [
                'title' => 'Suche',
                'fields' => [
                ]
            ],
        ]);

        $service->populateSingle('suggestion', [
            'en' => [
                'title' => 'Suggestion',
                'fields' => [
                    'teaser' => 'Which film shoud we add? Give us a suggestion!'
                ]
            ],
            'de' => [
                'title' => 'Vorschlag',
                'fields' => [
                    'teaser' => 'Welchen Film sollen wir aufnehmen? Machen Sie Ihren Vorschlag!'
                ]
            ],
        ]);

        $service->createEntry('page', [
            'en' => [
                'title' => 'About',
                'slug' => 'about-us',
                'fields' => [
                    'showInNav' => 'primary'
                ]
            ],
            'de' => [
                'title' => 'Über uns',
                'slug' => 'ueber-uns',
                'fields' => []
            ]
        ]);

        $service->createEntry('page', [
            'en' => [
                'title' => 'Impressum',
                'slug' => 'impressum',
                'fields' => [
                    'showInNav' => 'footer'
                ]
            ],
            'de' => [
                'title' => 'Impressum',
                'slug' => 'impressum',
                'fields' => []
            ]
        ]);

        $service->populateGlobal('siteInfo', [
            'en' => [
                'siteName' => 'Craft Starter',
                'copyright' => 'The Starter Inc.',
            ],
            'de' => [
                'siteName' => 'Craft Starter',
                'copyright' => 'The Starter Inc.',
            ]
        ]);

        $service->populateGlobal('siteInfo', [
            'en' => [
                'siteName' => 'Craft Starter',
                'copyright' => 'The Starter Inc.',
            ],
            'de' => [
                'siteName' => 'Craft Starter',
                'copyright' => 'The Starter Inc.',
            ]
        ]);



        $service->populateFirstUser('Sabine','Mustermann');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        echo "There is no need to revert something .\n";
        return true;
    }
}
