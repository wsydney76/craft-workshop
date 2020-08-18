<?php

namespace project\modules\main\modules\members\variables;

use Craft;
use craft\base\Component;
use craft\elements\db\ElementQueryInterface;
use craft\elements\db\EntryQuery;
use craft\elements\User;

class WatchListVariable extends Component
{
    /**
     * @param null $user
     * @return ElementQueryInterface|EntryQuery
     */
    public function getEntries(User $user = null)
    {
        if (!$user) {
            $user = Craft::$app->user->identity;
        }

        return $user->watchList->site('*')->unique();
    }

    /**
     * @param $id
     * @return bool
     */
    public function getExists($id): bool
    {
        $user = Craft::$app->user->identity;

        return $user->watchList->id($id)->site('*')->anyStatus()->exists();
    }
}
