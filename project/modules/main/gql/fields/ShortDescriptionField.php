<?php

namespace project\modules\main\gql\fields;

use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\Type;
use project\modules\main\gql\types\RoleType;
use project\modules\main\Services\ContentService;

class ShortDescriptionField extends FieldDefinition
{
    public function __construct()
    {
        $config =  [
            'name' => 'shortDescription',
            'type' => Type::string(),
            'resolve' => function($source) {
                // Probably not the smartest idea, though, as this will perform a query for each entry
                return $source->shortDescription;
            }
        ];
        parent::__construct($config);
    }
}
