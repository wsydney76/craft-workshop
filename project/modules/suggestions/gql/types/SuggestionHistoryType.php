<?php

namespace project\modules\suggestions\gql\types;

use craft\gql\interfaces\elements\Entry;
use craft\gql\interfaces\elements\User;
use craft\gql\types\DateTime;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class SuggestionHistoryType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'SuggestionHistory',
            'description' => 'Suggestion History Record',
            'fields' => [
                'id' => [
                    'type' => Type::int(),
                    'description' => 'ID'
                ],
                'notes' => [
                    'type' => Type::string(),
                    'description' => 'Notes of the suggestion'
                ],
                'status' => [
                    'type' => Type::string(),
                    'description' => 'Status of the suggestion'
                ],
                'dateCreated' => [
                    'type' => DateTime::getType(),
                    'description' => 'Creation date of the suggestion'
                ],
                'entry' => [
                    'type' => Entry::getType(),
                    'description' => 'Related Entry'
                ],
                'user' => [
                    'type' => User::getType(),
                    'description' => 'Assigned User'
                ],
                'creator' => [
                    'type' => User::getType(),
                    'description' => 'Creator of history record'
                ]
            ]
        ];
        parent::__construct($config);
    }
}
