<?php

namespace craft\contentmigrations;

use craft\db\Migration;
use craft\errors\ElementNotFoundException;
use project\modules\main\MainModule;
use project\modules\main\services\MigrationService;
use Throwable;
use yii\base\Exception;

/**
 * m191010_091406_gdprinfo migration.
 */
class m191010_091406_gdprinfo extends Migration
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

        $id = $service->createEntry('page', [
            'en' => [
                'title' => 'Privacy',
                'slug' => 'privacy',
                'fields' => [
                    'showInNav' => 'footer'
                ]
            ],
            'de' => [
                'title' => 'Datenschutz',
                'slug' => 'datenschutz',
                'fields' => []
            ]
        ]);

        $service->populateGlobal('gdprInfo', [
            'en' => [
                'privacyPage' => $id ? [$id] : [],
                'body' => 'Cookies facilitate the free provision of our offer. With the further use you agree with it.',
            ],
            'de' => [
                'body' => 'Cookies erleichtern uns die kostenfreie Bereitstellung unseres Angebots. Mit der weiteren Nutzung erklären Sie sich damit einverstanden.',
            ]
        ]);
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
