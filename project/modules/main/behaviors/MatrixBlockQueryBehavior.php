<?php

namespace project\modules\main\behaviors;

use Craft;
use craft\behaviors\CustomFieldBehavior;
use craft\elements\db\EntryQuery;
use craft\elements\db\MatrixBlockQuery;

class MatrixBlockQueryBehavior extends CustomFieldBehavior
{

    public function ownerQuery(EntryQuery $ownerQuery): MatrixBlockQuery
    {
        /** @var MatrixBlockQuery $query */
        $query = $this->owner;

        $ids = $ownerQuery->ids();
        if (!$ids) {
            $ids = [0];
        }
        $query->ownerId($ids);
        return $query;
    }

    public function joinOwnerContent(): MatrixBlockQuery
    {
        /** @var MatrixBlockQuery $query */
        $query = $this->owner;

        $siteId = Craft::$app->sites->currentSite->id;
        $query->leftJoin('{{%content}} owner', 'matrixblocks.ownerId = owner.elementId AND owner.siteId=' . $siteId);

        return $query;
    }

    /**
     * @param $criteria
     * @return MatrixBlockQuery
     */
    public function orderByOwnerContent($criteria): MatrixBlockQuery
    {
        /** @var MatrixBlockQuery $query */
        $query = $this->owner;

        $query->joinOwnerContent()
            ->orderBy($criteria);

        return $query;
    }
}
