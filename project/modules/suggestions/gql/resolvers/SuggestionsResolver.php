<?php

namespace project\modules\suggestions\gql\resolvers;

use craft\gql\base\Resolver;
use craft\helpers\Gql as GqlHelper;
use GraphQL\Type\Definition\ResolveInfo;
use project\modules\suggestions\models\SuggestionModel;


class SuggestionsResolver extends Resolver
{

    public static function resolve($source, array $arguments, $context, ResolveInfo $resolveInfo)
    {
        $query = SuggestionModel::find();

        foreach ($arguments as $key => $value) {
            $query->$key($value);
        }

        return $query->all();
        // return GqlHelper::applyDirectives($source, $resolveInfo, $query->all());
    }

}
