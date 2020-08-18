<?php

namespace project\modules\solrsearch\behaviors;

use Craft;
use craft\base\ElementInterface;
use craft\commerce\elements\Product;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use craft\helpers\UrlHelper;
use yii\base\Behavior;
use function is_array;
use function var_dump;

class SolrSearchEntryBehavior extends Behavior
{
    public function getSolrDoc()
    {
        /** @var Entry|Product $element */
        $element = $this->owner;

        $doc = [
            'id' => $element->id,
            'title' => $element->title,
            'site' => $element->site->handle,
            'content' => $this->_getContent($element),
            'type' => $element instanceof Entry ? Craft::$app->sections->getSectionByHandle($element->section->handle)->name : 'product',
            'slug' => $element->slug,
            'url' => $element->url,
        ];

        if ($element instanceof Entry) {
            switch ($element->section->handle) {
                case 'film':
                {
                    $eventSection = $this->one($element->eventSection);
                    $roles = [];
                    $acting = [];
                    $crew = [];
                    $cast = $this->all($element->cast);
                    $genre = $this->one($element->genre);
                    $country = $this->one($element->producedIn);
                    $crews = $this->all($element->crew);
                    $trivia = $this->all($element->trivia);
                    if ($cast) {
                        foreach ($cast as $role) {
                            foreach ($this->all($role->persons) as $person) {
                                $roles[] = $role->roleName . ' (' . $person->title . ')';
                                $acting[] = $person->title;
                            }
                        }
                    }
                    if ($crews) {
                        foreach ($crews as $role) {
                            $roleName = '';
                            $departments = $this->all($role->departments);
                            if ($departments) {
                                foreach ($departments as $department) {
                                    $roleName .= $department->title . ' ';
                                }
                            }
                            $persons = $this->all($role->persons);
                            if ($persons) {
                                foreach ($persons as $person) {
                                    $crew[] = $roleName . ' (' . $person->title . ')';
                                }
                            }
                        }
                    }

                    if ($trivia) {
                        foreach ($trivia as $block) {
                            $doc['content'] .= ' ' . $block->text;
                        }
                    }

                    $doc['sectiontitle'] = $eventSection ? $eventSection->title : '';
                    $doc['sectionid'] = $eventSection ? $eventSection->id : 0;
                    $doc['releaseyear'] = $element->releaseYear;
                    $doc['role'] = $roles;
                    $doc['crew'] = $crew;
                    $doc['acting'] = $acting;
                    $doc['genre'] = $genre ? $genre->title : '';
                    $doc['country'] = $country ? $country->title : '';

                    break;
                }
                case 'eventSection':
                {
                    break;
                }
                case 'award':
                {
                    $awardsEntry = Entry::find()->section('awardIndex')->siteId($element->siteId)->one();
                    $doc['url'] = $awardsEntry ? $awardsEntry->url . '#award-' . $element->id : UrlHelper::url('');
                    break;
                }
                case 'person':
                {
                    $filmEntries = Entry::find()->section('film')
                        ->relatedTo(['targetElement' => $element, 'field' => 'cast.persons'])
                        ->orderBy('title')
                        ->all();
                    $films = [];
                    foreach ($filmEntries as $filmEntry) {
                        $films[] = $filmEntry->title;
                    }

                    $thumb = $this->one($element->photo);

                    $doc['imagefile'] = $thumb ? $thumb->filename : '';
                    $doc['film'] = $films;
                }
            }
        } else {
            // var_dump($doc); die('X');
        }

        return $doc;
    }

    protected function _getContent(ElementInterface $element)
    {
        $content = '';
        if ($element instanceof Entry) {
            switch ($element->section->handle) {
                case 'person':
                {
                    $content = $element->bio;
                    break;
                }
                case 'award':
                {
                    $content = '';
                    $film = $this->one($element->film);
                    $person = $this->one($element->person);
                    if ($film) {
                        $content .= ' ' . $film->title;
                    }
                    if ($person) {
                        $content .= ' ' . $person->title;
                    }
                    break;
                }
                case 'page': {
                    $blocks = $this->all($element->contentBuilder);
                    if ($blocks) {
                        foreach ($blocks as $block) {
                            $content .= ' ' . $block->text;
                            $content .= ' ' . $block->heading;
                        }
                    }
                    break;
                }
                default:
                {
                    $content = '';
                }
            }
        }

        if ($element->bodyContent) {
            /** @var MatrixBlock $block */
            foreach ($this->all($element->bodyContent) as $block) {
                switch ($block->type) {
                    case 'text':
                    case 'heading':
                    {
                        $content .= ' ' . $block->text;
                        break;
                    }
                }
            }
        }
        return $content;
    }

    protected function all($stuff)
    {
        if (!$stuff) {
            return null;
        }
        if (is_array($stuff)) {
            return $stuff;
        }
        return $stuff->all();
    }

    protected function one($stuff)
    {
        if (!$stuff) {
            return null;
        }
        if (is_array($stuff)) {
            return $stuff[0];
        }
        return $stuff->one();
    }
}
