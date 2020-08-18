<?php

namespace craft\contentmigrations;

use craft\db\Migration;
use project\modules\suggestions\records\SuggestionHistoryRecord;
use project\modules\suggestions\records\SuggestionRecord;

/**
 * m200305_135829_suggestions_create_tables migration.
 */
class m200305_135829_suggestions_create_tables extends Migration
{
    /**
     * @inheritdoc
     */
    public function safeUp()
    {
        $suggestionsTable = SuggestionRecord::tableName();
        $suggestionsHistoryTable = SuggestionHistoryRecord::tableName();

        $this->dropTableIfExists($suggestionsHistoryTable);
        $this->dropTableIfExists($suggestionsTable);

        $this->createTable($suggestionsTable, [
            'id' => 'pk',
            'name' => 'string NOT NULL',
            'email' => 'string NOT NULL',
            'title' => 'string NOT NULL',
            'description' => 'text NOT NULL',
            'status' => 'string',
            'notes' => 'text',
            'relatedEntryId' => 'integer',
            'userId' => 'integer',
            'site' => 'string',
            'dateCreated' => 'datetime NOT NULL',
            'dateUpdated' => 'datetime NOT NULL',
            'dateDeleted' => 'datetime',
            'uid' => 'string NOT NULL'
        ], 'ENGINE=InnoDB');

        $this->createTable($suggestionsHistoryTable, [
            'id' => 'pk',
            'suggestionId' => 'integer NOT NULL',
            'status' => 'string',
            'notes' => 'text',
            'relatedEntryId' => 'integer',
            'userId' => 'integer',
            'creatorId' => 'integer NOT NULL',
            'dateCreated' => 'datetime NOT NULL',
            'dateUpdated' => 'datetime NOT NULL',
            'uid' => 'string NOT NULL'
        ], 'ENGINE=InnoDB');


        $this->createIndex(null, $suggestionsHistoryTable, 'suggestionId', false);

        $this->addForeignKey(null,
            $suggestionsHistoryTable, 'suggestionId',
            $suggestionsTable, 'id',
            'CASCADE', 'CASCADE');
    }

    /**
     * @inheritdoc
     */
    public function safeDown()
    {
        $suggestionsTable = SuggestionRecord::tableName();
        $suggestionsHistoryTable = SuggestionHistoryRecord::tableName();

        $this->dropTableIfExists($suggestionsHistoryTable);
        $this->dropTableIfExists($suggestionsTable);

        return true;
    }
}
