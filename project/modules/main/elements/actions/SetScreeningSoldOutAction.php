<?php

namespace project\modules\main\elements\actions;

use Craft;
use craft\base\ElementAction;
use craft\elements\db\ElementQueryInterface;
use craft\elements\Entry;
use craft\errors\ElementNotFoundException;
use Throwable;
use yii\base\Exception;

class SetScreeningSoldOutAction extends ElementAction
{
    public function getTriggerLabel(): string
    {
        return Craft::t('main', 'Set Screening as Sold Out');
    }

    /**
     * @param ElementQueryInterface $query
     * @return bool
     * @throws Throwable
     * @throws ElementNotFoundException
     * @throws Exception
     */
    public function performAction(ElementQueryInterface $query): bool
    {
        $screeningSectionId = Craft::$app->sections->getSectionByHandle('screening')->id;
        if ($query->sectionId && !$query->sectionId == $screeningSectionId) {
            $this->setMessage(Craft::t('main', 'This makes only sense for screenings.'));
            return false;
        }
        $entries = $query->section('screening')->soldOut(false)->all();
        if (!$entries) {
            $this->setMessage(Craft::t('main', 'No matching not sold out screenings found'));
            return false;
        }

        $errorCount = 0;
        /** @var Entry $entry */
        foreach ($entries as $entry) {
            $entry->setFieldValue('soldOut', 1);
            if (!Craft::$app->elements->saveElement($entry)) {
                $errorCount++;
            }
        }

        if ($errorCount) {
            $this->setMessage($errorCount . ' ' . Craft::t('main', 'unexpected error(s) occurred.'));
            return false;
        }

        $this->setMessage(Craft::t('main', 'Screenings set as Sold Out'));
        return true;
    }
}
