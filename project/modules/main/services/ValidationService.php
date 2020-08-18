<?php

namespace project\modules\main\services;

use craft\base\Component;
use craft\elements\Entry;
use craft\elements\MatrixBlock;
use DateTime;
use Exception;

class ValidationService extends Component
{
    /**
     * @param Entry $entry
     * @throws Exception
     */
    public function validateEntry(Entry $entry)
    {
        if ($entry->scenario != Entry::SCENARIO_LIVE) {
            return;
        }

        $this->_validateGeneric($entry);

        switch ($entry->section->handle) {
            case 'person':
                $this->_validatePerson($entry);
                break;
            case 'film':
                $this->_validateFilm($entry);
                break;
        }
    }

    protected function _validateGeneric(Entry $entry)
    {
        if ($entry->folderName) {
            if (!@preg_match("/^[a-z][a-z0-9\-]+[a-z]$/", $entry->folderName)) {
                $entry->addError('folderName', 'Image Folder Name is invalid');
            }
            if (strlen($entry->folderName) < 4) {
                $entry->addError('folderName', 'Image Folder Name should contain at least 5 characters');
            }
        }

        if (!$entry->isFieldEmpty('bodyContent') && !$this->_validateBodyContent($entry)) {
            $entry->addError('bodyContent', 'There is an error with the correct nesting of heading levels');
        }
    }

    protected function _validateBodyContent(Entry $entry): bool
    {
        $isValid = true;
        $last = 1;

        foreach ($entry->bodyContent->all() as $block) {
            if ($block->type == 'heading') {
                $tag = (int)str_replace('h', '', $block->tag->value);
                if ($tag - $last > 1) {
                    $block->addError('tag', 'Invalid heading level: H' . $tag . ' cannot follow H' . $last);
                    $isValid = false;
                }
                $last = $tag;
            }
        }
        return $isValid;
    }

    protected function _validatePerson(Entry $entry)
    {
        $this->_checkBiographicalDate($entry, 'born');
        $this->_checkBiographicalDate($entry, 'died');

        if ($entry->born && $entry->died && $entry->born > $entry->died) {
            $entry->addError('died', 'You have to be born before you die.');
        }
    }

    /**
     * @param Entry $entry
     * @throws Exception
     */
    protected function _validateFilm(Entry $entry)
    {
        $castIsValid = true;
        /** @var MatrixBlock $role */
        foreach ($entry->cast->all() as $role) {
            foreach ($role->persons->anyStatus()->all() as $person) {
                if ($person->born && $entry->releaseYear && $person->born > new DateTime($entry->releaseYear . '-01-01')) {
                    $castIsValid = false;
                    $role->addError('persons', "In {$entry->releaseYear}, when the film was released, {$person->title} was not even born.");
                }
            }
        }
        if (!$castIsValid) {
            $entry->addError('cast', 'There is an error with one of the roles');
        }
    }

    protected function _checkBiographicalDate(Entry $entry, $attr)
    {
        if (!$entry->$attr) {
            return;
        }

        if ($entry->$attr < new DateTime('1895-01-01')) {
            $entry->addError($attr, 'This is very unlikely');
        } elseif ($entry->$attr > new DateTime()) {
            $entry->addError($attr, 'This is in the future');
        }
    }
}
