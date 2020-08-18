<?php

namespace project\modules\suggestions\gql\types;

use craft\gql\interfaces\elements\Entry;
use craft\gql\interfaces\elements\User;
use craft\gql\types\DateTime;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class SuggestionType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Suggestion',
            'description' => 'Suggestion',
            'fields' => [
                'id' => [
                    'type' => Type::int(),
                    'description' => 'ID'
                ],
                'title' => [
                    'type' => Type::string(),
                    'description' => 'Title of the suggestion'
                ],
                'name' => [
                    'type' => Type::string(),
                    'description' => 'Name of contributor'
                ],
                'email' => [
                    'type' => Type::string(),
                    'description' => 'Email of the suggestion'
                ],
                'description' => [
                    'type' => Type::string(),
                    'description' => 'Description of the suggestion'
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
                'history' => [
                    'type' => Type::listOf(new SuggestionHistoryType()),
                    'description' => 'History'
                ]
            ]
        ];
        parent::__construct($config);
    }
}
