<?php

namespace project\modules\suggestions\events;

use craft\events\ModelEvent;

class SuggestionEvent extends ModelEvent
{
    public $suggestion;
    public $statusUpdated = false;
}
