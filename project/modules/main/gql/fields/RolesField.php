<?php

namespace project\modules\main\gql\fields;

use GraphQL\Type\Definition\FieldDefinition;
use GraphQL\Type\Definition\Type;
use project\modules\main\gql\types\RoleType;

class RolesField extends FieldDefinition
{
    public function __construct()
    {
        $config = [
            'name' => 'roles',
            'description' => 'Roles a person played',
            'type' => Type::listOf(new RoleType()),
            'args' => [
                [
                    'name' => 'orderBy',
                    'type' => Type::string(),
                    'description' => 'Sort criteria',
                    'default' => ''
                ]
            ],
            'resolve' => function($entry, $args) {
                return $entry->getGqlRolesForPerson($args);
            }
        ];
        parent::__construct($config);
    }
}
