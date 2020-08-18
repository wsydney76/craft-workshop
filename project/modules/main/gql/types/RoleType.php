<?php

namespace project\modules\main\gql\types;

use craft\gql\interfaces\elements\Entry;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class RoleType extends ObjectType
{
    public function __construct()
    {
        $config = [
            'name' => 'Roles',
            'description' => 'Role Details',
            'fields' => [
                'id' => [
                    'type' => Type::int(),
                    'description' => 'Id of matrixblock'
                ],
                'roleName' => [
                    'type' => Type::string(),
                    'description' => 'Role Name'
                ],
                'film' => [
                    'type' => Entry::getType(),
                    'description' => 'The film the role occured in'
                ]
            ]
        ];
        parent::__construct($config);
    }
}
