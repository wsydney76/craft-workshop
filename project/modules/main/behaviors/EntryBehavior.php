<?php

namespace project\modules\main\behaviors;

use Craft;
use craft\elements\db\EntryQuery;
use craft\elements\db\MatrixBlockQuery;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\helpers\ArrayHelper;
use project\modules\main\MainModule;
use whoisjuan\craftcolormixer\twigextensions\CraftColorMixerTwigExtension;
use yii\base\Behavior;

class EntryBehavior extends Behavior
{

    public $filmIds = [];

    public function events()
    {
        return [
            Entry::EVENT_AFTER_VALIDATE => '_validate'
        ];
    }

    public function _validate()
    {

        /** @var Entry $entry */
        $entry = $this->owner;
        if ($entry->scenario != Entry::SCENARIO_LIVE) {
            return;
        }

        MainModule::$services->validate->validateEntry($entry);
    }

    public function getAllRelatedEntries()
    {
        return Entry::find()
            ->relatedTo([
                'or',
                ['sourceElement' => $this->owner, 'field' => 'relatedEntries'],
                ['targetElement' => $this->owner, 'field' => 'relatedEntries']
            ]);
    }

    public function getCreditedWith(): EntryQuery
    {
        $filmIds = $this->_getFilmIdsForPerson();

        $query = Entry::find()
            ->section('person')
            ->relatedTo(['sourceElement' => $filmIds, 'field' => 'cast.persons'])
            ->id("not " . $this->owner->sourceId)
            ->orderBy('title');

        return $query;
    }

    protected function _getFilmIdsForPerson()
    {
        if (!$this->filmIds) {
            $this->filmIds = Entry::find()
                ->section('film')
                ->site('*')
                ->unique()
                ->relatedTo(['targetElement' => $this->owner->sourceId, 'field' => 'cast.persons'])
                ->ids();
        }

        return $this->filmIds;
    }

    public function getFilms()
    {
        return Entry::find()
            ->section('film')
            ->relatedTo(
                ['targetElement' => $this->owner]
            );
    }

    public function getNews()
    {
        return Entry::find()
            ->section('news')
            ->relatedTo([
                'or',
                $this->owner,
                ['targetElement' => $this->owner, 'field' => 'bodyContent.target']
            ]);
    }

    public function getPersonsForSection()
    {
        $filmIds = Entry::find()
            ->section('film')
            ->relatedTo(['targetElement' => $this->owner, 'field' => 'eventSection'])
            ->ids();
        return Entry::find()
            ->section('person')
            ->relatedTo(['sourceElement' => $filmIds, 'field' => 'cast.persons']);
    }

    public function getRolesForPerson(): MatrixBlockQuery
    {

        /*
         * This uses our custom MatrixBlockQueryBehavior for easier use.
         * If you want to go without it, do it like this:
         * Be sure you do not pass an empty array to ->ownerId, as this will will mean 'all'
         *
         $fieldId = Craft::$app->fields->getFieldByHandle('cast')->id;
         $filmIds = Entry::find()->section('film')->ids();
         $siteId = Craft::$app->sites->currentSite->id;
         $query = MatrixBlock::find()
            ->fieldId($fieldId)
            ->ownerId($filmIds)
            ->relatedTo($this->owner->sourceId)
            ->leftJoin('{{%content}} film', 'matrixblocks.ownerId = film.elementId AND film.siteId=' . $siteId)
            ->orderBy('film.field_releaseYear desc');
         */

        $query = MatrixBlock::find()
            ->field('cast')
            ->site('*')
            ->unique()
            ->ownerQuery(Entry::find()->section('film')->site('*')->unique())
            ->relatedTo($this->owner->sourceId)
            ->orderByOwnerContent('owner.field_releaseYear desc');

        return $query;
    }

    public function getGqlRolesForPerson($args = []): array
    {

        $entry = $this->owner;

        $orderBy = $args['orderBy'] ?? 'owner.title, owner.id, sortOrder';

        $blocks = MatrixBlock::find()
            ->siteId($entry->siteId)
            ->field('cast')
            ->ownerId(Entry::find()->site($entry->site)->section('film')->ids())
            ->persons($entry)
            ->leftJoin('{{%content}} owner', 'matrixblocks.ownerId = owner.elementId and owner.siteId=' . $entry->site->id)
            ->orderBy($orderBy)
            ->all();

        $films = Entry::find()
            ->siteId($entry->siteId)
            ->section('film')
            ->id(array_unique(array_column($blocks, 'ownerId')))
            ->indexBy('id')
            ->all();

        $roles = [];
        foreach ($blocks as $block) {
            $roles[] = [
                'id' => $block->id,
                'roleName' => $block->roleName,
                'film' => $films[$block->ownerId]
            ];
        }

        return $roles;
    }

    public function getDepartmentsForPerson(): MatrixBlockQuery
    {
        $query = MatrixBlock::find()
            ->field('crew')
            ->site('*')
            ->unique()
            ->ownerQuery(Entry::find()->section('film')->site('*')->unique())
            ->relatedTo($this->owner->sourceId)
            ->orderByOwnerContent('owner.title asc, sortOrder');

        return $query;
    }

    /**
     * @return EntryQuery
     */
    public function getScreenings(): EntryQuery
    {

        /*
        * Trying to avoid complex queries in twig templates
        * and create a common interface for all sections to get their related screenings.
        */

        /** @var Entry $entry */
        $entry = $this->owner;

        $query = Entry::find()->section('screening')->site('*')->unique();

        // Make sure we do not show screenings where film/location is not enabled.
        switch ($entry->section->handle) {
            case 'person':
                $filmIds = $this->_getFilmIdsForPerson();
                $locationIds = Entry::find()->section('location')->ids();
                break;
            case 'film':
                $filmIds = $entry->sourceId;
                $locationIds = Entry::find()->section('location')->ids();
                break;
            case 'location':
                $locationIds = $entry->sourceId;
                $filmIds = Entry::find()->section('film')->site('*')->unique()->ids();
                break;
            case 'eventsection':
                $locationIds = Entry::find()->section('location')->ids();
                $filmIds = Entry::find()->section('film')->relatedTo($entry)->ids();
                break;
            default:
                // show all
                $filmIds = Entry::find()->section('film')->ids();
                $locationIds = Entry::find()->section('location')->ids();
        }

        return $query->relatedTo([
            'and',
            ['targetElement' => $filmIds, 'field' => 'film'],
            ['targetElement' => $locationIds, 'field' => 'location']

        ]);
    }

    /**
     * @return EntryQuery
     */
    public function getAwards($includeFilmsForPerson = true): EntryQuery
    {
        /** @var Entry $entry */
        $entry = $this->owner;
        $query = Entry::find()
            ->section('award')
            ->site('*')
            ->unique();
        switch ($entry->section->handle) {
            case 'film':
            {
                $query = $query
                    ->film($entry)
                    ->with([
                        ['person', ['site' => '*', 'unique' => true]]
                    ]);
                break;
            }
            case 'person':
            {
                $ids = [$entry->SourceId];

                if ($includeFilmsForPerson) {
                    $ids = ArrayHelper::merge($ids, $this->_getFilmIdsForPerson());
                }

                $query = $query
                    ->relatedTo($ids)
                    ->with([
                        ['film', ['site' => '*', 'unique' => true]]
                    ]);
                break;
            }
        }
        return $query;
    }

    public function getShortDescription()
    {
        /** @var Entry $entry */
        $entry = $this->owner;

        $teaser = $entry->teaser;
        switch ($entry->section->handle) {
            case 'film' :
                $genre = $entry->genre->one();
                if ($genre) {
                    $teaser = $genre->title;
                }

                $country = $entry->producedIn->one();
                if ($country) {
                    $teaser .= ', ' . $country->title;
                }
                $teaser .= ' ' . $entry->releaseYear;

                $roles = $entry->cast->limit(2)->all();
                $sep = '';
                $cast = '';
                foreach ($roles as $role) {
                    $persons = $role->persons->site('*')->unique()->all();
                    foreach ($persons as $person) {
                        $cast .= $sep . $person->lastName;
                        $sep = ', ';
                    }
                }
                if ($cast) {
                    $teaser .= ' (' . $cast . ')';
                }

                break;
            case 'person':
                $teaser = '';
                $sep = '';
                $roles = $entry->rolesForPerson->limit(2)->all();
                foreach ($roles as $role) {
                    $teaser .= $sep . $role->owner->title;
                    $sep = ', ';
                }
        }
        return $teaser;
    }

    public function showInModal()
    {
        if (Craft::$app->sites->currentSite->handle == 'kids') {
            return false;
        }
        return Craft::$app->config->general->project['showScreeningDetailsInModal'] ?? false;
    }

    public function getClass()
    {
        /** @var Entry $entry */
        $entry = $this->owner;

        $bgColor = $entry->backgroundColor;
        if (!$bgColor) {
            return '';
        }
        $ext = new CraftColorMixerTwigExtension();
        return $ext->isDark($bgColor, $entry->darkColorThreshold) ? 'background-isdark' : '';
    }

    /**
     * @return string
     */
    public function getStyle()
    {
        /** @var Entry $entry */
        $entry = $this->owner;

        if ($entry->useGradient) {
            $ext = new CraftColorMixerTwigExtension();

            $secondaryColor = $entry->gradientSecondaryColor;

            if ($secondaryColor) {
                $style = $ext->gradient($entry->backgroundColor, $entry->gradientDirection,
                    $secondaryColor->hex, $entry->darkColorThreshold);
            } else {
                $style = $ext->gradient($entry->backgroundColor, $entry->gradientDirection,
                    $entry->gradientAmount, $entry->darkColorThreshold);
            }
        } else {
            $style = "background-color:{$entry->backgroundColor}";
        }

        return $style;
    }

    public function getJsonLd()
    {
        /** @var Entry $entry */
        $entry = $this->owner;
        $data = [];
        switch ($entry->section->handle) {
            case 'film':
                $cast = Craft::$app->requestData->get('cast');
                if (!$cast) {
                    $cast = $entry->cast->with(['role:persons'])->all();
                }
                $crew = Craft::$app->requestData->get('crew');
                if (!$crew) {
                    $crew = $entry->crew->with(['role:persons'])->all();
                }

                $data['@context'] = 'http://schema.org';
                $data['@type'] = 'Movie';
                $data['name'] = $entry->title;
                $data['description'] = $entry->teaser;
                $data['actor'] = [];
                foreach ($cast as $block) {
                    foreach ($block->persons as $person) {
                        $data['actor'][] = ['@type' => 'Person', 'name' => $person->title, 'url' => $person->url];
                    }
                }
                foreach ($crew as $block) {
                    $departments = $block->departments;
                    foreach ($departments as $department) {
                        if ($department->jsonLdProperty) {
                            $data[$department->jsonLdProperty] = [];
                            foreach ($block->persons as $person) {
                                $data[$department->jsonLdProperty][] = ['@type' => 'Person', 'name' => $person->title, 'url' => $person->url];;
                            }
                            if ($block->names) {
                                foreach ($block->names as $name) {
                                    $data[$department->jsonLdProperty][] = ['@type' => 'Person', 'name' => $name['name']];;
                                }
                            }
                        }
                    }
                }
        }
        return $data;
    }
}
