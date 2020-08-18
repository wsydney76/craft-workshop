<?php

namespace project\modules\main\exporters;

use craft\base\ElementExporter;
use craft\elements\db\ElementQuery;
use craft\elements\db\ElementQueryInterface;
use craft\elements\Entry;
use craft\helpers\ArrayHelper;

class ScreeningsExporter extends ElementExporter
{
    public static function displayName(): string
    {
        return 'Screenings Exporter';
    }

    public function export(ElementQueryInterface $query): array
    {
        $results = [];

        /** @var ElementQuery $query */
        $query->section('screening')
            ->with(['location', 'film', 'screeningLanguage', 'subtitlesLanguages'])
            ->orderBy('showtime');

        foreach ($query->each() as $entry) {
            /** @var Entry $entry */
            if ($entry->film && $entry->location) {
                $results[] = [
                    'id' => $entry->id,
                    'title' => $entry->title ?? '',
                    'url' => $entry->getUrl(),
                    'showtime' => $entry->showtime->format('Y-m-d G:i'),
                    'location' =>  $entry->location[0]->title,
                    'address' => $entry->location[0]->address,
                    'film' => $entry->film[0]->title,
                    'language' => count($entry->screeningLanguage) ? $entry->screeningLanguage[0]->languageCode : 'n/a',
                    'subtitleLanguages' => ArrayHelper::getColumn($entry->subtitlesLanguages, 'languageCode'),
                    'soldout' => $entry->soldOut
                ];
            }
        }

        return $results;
    }
}
