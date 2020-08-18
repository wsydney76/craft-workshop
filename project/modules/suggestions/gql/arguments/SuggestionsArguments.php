<?php

namespace project\modules\suggestions\gql\arguments;

use craft\gql\base\Arguments;
use craft\gql\types\QueryArgument;
use GraphQL\Type\Definition\Type;

class SuggestionsArguments extends Arguments
{
    static function getArguments(): array
    {
        return [
            'id' => [
                'name' => 'id',
                'type' => Type::listOf(QueryArgument::getType()),
                'description' => 'ID of a suggestion'
            ],
            'status' => [
                'name' => 'status',
                'type' => Type::string(),
                'description' => 'Status of suggestions'
            ],
            'email' => [
                'name' => 'email',
                'type' => Type::string(),
                'description' => 'Email of suggesitons'
            ],
            'userId' => [
                'name' => 'userId',
                'type' => Type::int(),
                'description' => 'Assigned user of suggestions'
            ],
            'relatedEntryId' => [
                'name' => 'relatedEntryId',
                'type' => Type::int(),
                'description' => 'Related entry of suggestions'
            ],
            'search' => [
                'name' => 'search',
                'type' => Type::string(),
                'description' => 'Search term'
            ]
        ];
    }
}
