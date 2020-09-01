<?php

namespace project\modules\main\services;

use Craft;
use craft\base\Component;
use craft\db\Table;
use craft\elements\Entry;
use craft\helpers\ArrayHelper;
use project\modules\main\variables\ContentVariable;
use putyourlightson\blitz\models\SiteUriModel;
use Throwable;
use yii\db\Exception;
use yii\db\Expression;
use function var_dump;

class ContentService extends Component
{
    /**
     * @param bool $nav
     * @return array
     */
    public function getFeaturedSectionEntries($nav = ''): array
    {
        $sections = ['eventsectionIndex', 'filmIndex', 'personIndex', 'newsIndex'];

        switch ($nav) {
            case 'featured':
            {
                $sections[] = 'topicIndex';
                $sections[] = 'shop';
                break;
            }
            case 'primary-nav':
            {
                $sections[] = 'topicIndex';
                $sections[] = 'awardIndex';
                $sections[] = 'shop';
                break;
            }
        }

        $entries = [];
        foreach ($sections as $section) {
            $entry = Entry::find()->section($section)->one();
            if ($entry) {
                $entries[] = $entry;
            }
        }
        return $entries;
    }

    /**
     * @param $entry
     * @param $perPage
     * @return false|float
     */
    public function getPageCountForIndex($entry, $perPage)
    {
        $section = str_replace('Index', '', $entry->section->handle);
        switch ($section) {
            case 'person':
            {
                $count = (new ContentVariable())->getCast()->site($entry->site)->count();
                break;
            }
            case 'award':
            case 'screening':
            {
                $count = 1;
                break;
            }

            default:
            {
                $count = Entry::find()->section($section)->site($entry->site)->count();
            }
        }

        return ceil($count / $perPage);
    }

    /**
     * Emulates Craft::$app->relations->saveRelations, as this only accepts a BaseRelationField
     *
     * @param $element
     * @param $targetIds
     * @throws Throwable
     */
    public function saveRelations($field, $element, $targetIds)
    {
        $transaction = Craft::$app->getDb()->beginTransaction();

        try {
            // Delete the existing relations
            $oldRelationConditions = [
                'and',
                [
                    'fieldId' => $field->id,
                    'sourceId' => $element->id,
                ]
            ];

            Craft::$app->getDb()->createCommand()
                ->delete(Table::RELATIONS, $oldRelationConditions)
                ->execute();

            // Add the new ones
            if (!empty($targetIds)) {
                $values = [];

                foreach ($targetIds as $sortOrder => $targetId) {
                    $values[] = [
                        $field->id,
                        $element->id,
                        null,
                        $targetId,
                        $sortOrder + 1
                    ];
                }

                $columns = [
                    'fieldId',
                    'sourceId',
                    'sourceSiteId',
                    'targetId',
                    'sortOrder'
                ];
                Craft::$app->getDb()->createCommand()
                    ->batchInsert(Table::RELATIONS, $columns, $values)
                    ->execute();
            }

            $transaction->commit();
        } catch (Throwable $e) {
            $transaction->rollBack();

            throw $e;
        }
    }

    /**
     * @param int $days
     * @return bool
     */
    public function addDaysToShowtime($days = 30)
    {
        try {
            Craft::$app->db->createCommand()->update(
                Table::CONTENT,
                ['field_showtime' => new Expression("adddate(field_showtime,{$days})")],
                ['not', ['field_showtime' => null]]
            )->execute();
        } catch (Exception $e) {
            return false;
        }
        return true;
    }

    public function getCacheSiteUris($siteUris)
    {
        foreach (Craft::$app->sites->getAllSiteIds() as $siteId) {
            $urisForSite = ArrayHelper::where($siteUris, 'siteId', $siteId);
            foreach (Craft::$app->config->general->project['paginatedSections'] as $section) {
                $entry = Entry::find()->siteId($siteId)->section($section)->one();
                if ($entry) {
                    if (ArrayHelper::contains($urisForSite, 'uri', $entry->uri)) {
                        $pageCount = $this->getPageCountForIndex($entry, Craft::$app->config->general->project['entriesPerPage']);
                        for ($i = 2; $i <= $pageCount; $i++) {
                            $uri = $entry->uri . '/' . Craft::$app->config->general->pageTrigger . $i;
                            if (!ArrayHelper::contains($urisForSite, 'uri', $uri)) {
                                $siteUris[] = new SiteUriModel(['siteId' => $siteId, 'uri' => $uri]);
                            }
                        }
                    }
                }
            }

            $screenings = Entry::find()->section('screening')->siteId($siteId)->all();
            foreach ($screenings as $screening) {
                $siteUris[] = new SiteUriModel(['siteId' => $siteId, 'uri' => "ajax/screening/{$screening->id}"]);
            }

        }
        return $siteUris;
    }

}
