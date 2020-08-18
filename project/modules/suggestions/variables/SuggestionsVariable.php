<?php

namespace project\modules\suggestions\variables;

use Craft;
use craft\base\Component;
use project\modules\suggestions\models\SuggestionModel;
use project\modules\suggestions\SuggestionModule;

/**
 *
 * @property array $userSources
 * @property SuggestionModel $newSuggestion
 * @property mixed $suggestionStatus
 * @property array $entrySources
 */
class SuggestionsVariable extends Component
{
    public function getSuggestionStatus()
    {
        // Status for suggestions
        return SuggestionModule::$suggestionStatus;
    }

    public function getEntrySources()
    {
        $sources = [];
        foreach (SuggestionModule::$suggestionSections as $section) {
            $sources[] = 'section:' . Craft::$app->sections->getSectionByHandle($section)->uid;
        }
        return $sources;
    }

    public function getUserSources()
    {
        $sources = [];
        //$sources[] = 'admins';
        //$sources[] = 'group:' . Craft::$app->userGroups->getGroupByHandle('editors')->uid;
        $sources[] = 'suggestions_editors';
        return $sources;
    }

    public function getNewSuggestion() {
       $suggestion = new SuggestionModel();
       $user = Craft::$app->user->identity;
       if ($user) {
           $suggestion->name = $user->fullName;
           $suggestion->email = $user->email;
       }
       return $suggestion;
    }
}
