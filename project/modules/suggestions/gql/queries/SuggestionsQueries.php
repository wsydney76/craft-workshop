<?php

namespace project\modules\suggestions\gql\queries;

use craft\gql\base\Query;
use GraphQL\Type\Definition\Type;
use project\modules\suggestions\gql\arguments\SuggestionsArguments;
use project\modules\suggestions\gql\resolvers\SuggestionsResolver;
use project\modules\suggestions\gql\types\SuggestionType;

class SuggestionsQueries extends Query
{

    public static function getQueries($checkToken = true): array
    {
        return [
            'suggestions' => [
                'type' => Type::listOf(new SuggestionType()),
                'args' => SuggestionsArguments::getArguments(),
                'resolve' => SuggestionsResolver::class . '::resolve',
                'description' => 'List of user supplied Suggestions'
            ]
        ];
    }
}
