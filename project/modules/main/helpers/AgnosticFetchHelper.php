<?php

namespace project\modules\main\helpers;

use craft\elements\db\ElementQueryInterface;

class AgnosticFetchHelper
{
    public static function all($stuff)
    {
        if ($stuff === null) {
            return [];
        }
        if (is_array($stuff)) {
            return ($stuff);
        }
        /** @var ElementQueryInterface $stuff */
        return $stuff->all();
    }

    public static function one($stuff)
    {
        if ($stuff === null) {
            return null;
        }
        if (is_array($stuff)) {
            return (count($stuff) ? $stuff[0] : null);
        }
        /** @var ElementQueryInterface $stuff */
        return $stuff->one();
    }
}
