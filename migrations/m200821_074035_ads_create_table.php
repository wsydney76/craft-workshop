<?php

namespace craft\contentmigrations;

use craft\db\Migration;
use project\modules\ads\records\AdRecord;

/**
 * m200821_074035_ads_create_table migration.
 */
class m200821_074035_ads_create_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $table = AdRecord::tableName();
        $this->dropTableIfExists($table);

        $this->createTable($table, [
            'id' => 'pk',
            'type' => 'string NOT NULL',
            'title' => 'string NOT NULL',
            'text' => 'text NOT NULL',
            'email' => 'string NOT NULL',
            'status' => 'string NOT NULL',
            'dateCreated' => 'datetime NOT NULL',
            'dateUpdated' => 'datetime NOT NULL',
            'uid' => 'string NOT NULL'
        ], 'ENGINE=InnoDB');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $table = AdRecord::tableName();
        $this->dropTableIfExists($table);
        return true;
    }
}
