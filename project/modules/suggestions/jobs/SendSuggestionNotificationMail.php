<?php

namespace project\modules\suggestions\jobs;

use Craft;
use craft\queue\BaseJob;

class SendSuggestionNotificationMail extends BaseJob

{

    public $message;
    public $language;

    public function execute($queue)
    {

        Craft::info('Sending Mail', 'Suggestions');

        Craft::$app->language = $this->language;
        $this->message->language = $this->language;
        $this->message->send();
    }

    protected function defaultDescription()
    {
        return 'Sending Suggestion Notification Mail';
    }
}
