<?php

namespace project\modules\suggestions\services;

use craft;
use craft\base\Component;
use craft\db\Query;
use craft\elements\GlobalSet;
use craft\helpers\App;
use project\modules\suggestions\events\SuggestionEvent;
use project\modules\suggestions\jobs\SendSuggestionNotificationMail;
use project\modules\suggestions\models\SuggestionModel;
use project\modules\suggestions\records\SuggestionHistoryRecord;
use project\modules\suggestions\records\SuggestionRecord;
use project\modules\suggestions\SuggestionModule;
use Throwable;
use yii\base\InvalidConfigException;
use yii\db\StaleObjectException;

/**
 * Class SuggestionsService
 *
 * @package project\modules\suggestions
 *
 * @property mixed $statusCounts
 */
class SuggestionsService extends Component
{

// Constants
    /**
     * @event SuggestionEvent The event that is triggered after the element is saved
     */
    const EVENT_AFTER_SAVE = 'afterSave';

    /**
     * @return array
     */
    public function getStatusCounts(): array
    {
        return  (new Query())
            ->from(SuggestionRecord::tableName())
            ->select(['status', 'count(*) as cnt'])
            ->andWhere(['dateDeleted' => null])
            ->groupBy(['status'])
            ->indexBy('status')
            ->all();
    }

    /**
     * @param SuggestionModel $suggestion
     * @return bool
     */
    public function saveSuggestion(SuggestionModel $suggestion): bool
    {
        $isNew = $suggestion->id === null;

        if ($isNew) {
            $suggestionRecord = new SuggestionRecord();
            $suggestionRecord->setAttributes($suggestion->getAttributes(), false);
            $hasNewStatus = false;
        } else {
            $suggestionRecord = SuggestionRecord::findOne($suggestion->id);
            $suggestionRecord->setAttributes($suggestion->getAttributes(), false);
            $hasNewStatus = (bool)$suggestionRecord->getDirtyAttributes(['status']);
        }

        if (!$suggestion->validate()) {
            if(!$suggestionRecord->validate()) {
                $suggestion->addModelErrors($suggestionRecord);
            }
            return false;
        }
        if (!$suggestionRecord->save()) {
            $suggestion->addModelErrors($suggestionRecord);
            return false;
        }

        if ($isNew) {
            $suggestion->id = $suggestionRecord->id;
            $suggestion->uid = $suggestionRecord->uid;
        }

        // Fire an event and handle it in the same service does not make much sense,
        // but hey, we can learn something about events!
        if ($this->hasEventHandlers(self::EVENT_AFTER_SAVE)) {
            $this->trigger(self::EVENT_AFTER_SAVE, new SuggestionEvent([
                'isNew' => $isNew,
                'suggestion' => $suggestion,
                'statusUpdated' => $hasNewStatus
            ]));
        }

        return true;
    }

    /**
     * @param SuggestionModel $suggestion
     * @return bool|false|int
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function deleteSuggestion(SuggestionModel $suggestion)
    {
        $suggestionRecord = SuggestionRecord::findOne($suggestion->id);
        if (!$suggestionRecord) {
            return false;
        }
        return $suggestionRecord->softDelete();
    }

    /**
     * @param SuggestionModel $suggestion
     * @return bool
     */
    public function restoreSuggestion(SuggestionModel $suggestion)
    {
        $suggestionRecord = SuggestionRecord::findTrashed()->andWhere(['id' => $suggestion->id])->one();
        if (!$suggestionRecord) {
            return false;
        }
        return $suggestionRecord->restore();
    }

    /**
     * @param SuggestionModel $suggestion
     * @return bool|false|int
     * @throws StaleObjectException
     * @throws Throwable
     */
    public function hardDeleteSuggestion(SuggestionModel $suggestion)
    {
        $suggestionRecord = SuggestionRecord::findTrashed()->andWhere(['id' => $suggestion->id])->one();
        if (!$suggestionRecord) {
            return false;
        }
        return $suggestionRecord->delete();
    }

    /**
     * @param SuggestionModel $suggestion
     * @param bool $isNew
     * @param bool $statusUpdated
     * @throws InvalidConfigException
     */
    public function afterSaveSuggestion(SuggestionModel $suggestion, bool $isNew, bool $statusUpdated)
    {

        if ($isNew) {
            $this->_sendEmail($suggestion, 'suggestion_new');
        } elseif ($statusUpdated) {
            $this->_sendEmail($suggestion, 'suggestion_updatestatus');
        }

        $this->_createHistory($suggestion);
    }

    /**
     * @param SuggestionModel $suggestion
     * @param $key
     * @throws InvalidConfigException
     */
    protected function _sendEmail(SuggestionModel $suggestion, $key)
    {

        $mailSettings = App::mailSettings();

        /** @var craft\elements\Entry $entry */
        $entry = $suggestion->entry;
        $language = Craft::$app->sites->getSiteByHandle($suggestion->site)->language;

        $message = Craft::$app->mailer->composeFromKey($key,
            [
                'suggestion' => $suggestion->attributes,
                'url' => $suggestion->url,
                'entryTitle' => $entry ? $entry->title : '',
                'entryUrl' => $entry ? $entry->url : '',
                'statusLabel' => Craft::t('suggestions',
                    SuggestionModule::$suggestionStatus[$suggestion->status], [], $language)
            ])
            ->setFrom(Craft::parseEnv($mailSettings->fromEmail))
            ->setTo($suggestion->email);

        if ($key == 'suggestion_new') {
            $recipient = GlobalSet::find()->handle('siteInfo')->one()->suggestionUserForEmail->one();
            if ($recipient) {
                $message->setCc($recipient);
            }
        }

        Craft::$app->queue->push(new SendSuggestionNotificationMail([
            'message' => $message,
            'language' => $language
        ]));
    }

    protected function _createHistory(SuggestionModel $suggestion)
    {
        $historyRecord = new SuggestionHistoryRecord([
            'suggestionId' => $suggestion->id,
            'status' => $suggestion->status,
            'notes' => $suggestion->notes,
            'relatedEntryId' => $suggestion->relatedEntryId,
            'userId' => $suggestion->userId,
            'creatorId' => craft::$app->user->id ?? 0
        ]);
        $historyRecord->save();
    }

}
