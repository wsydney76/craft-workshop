<?php

namespace apps\headless;

use Craft;
use craft\elements\Entry;
use craft\events\RegisterPreviewTargetsEvent;
use craft\events\RegisterTemplateRootsEvent;
use craft\web\View;
use yii\base\Event;

class HeadlessModule extends \yii\base\Module
{
    /**
     * Initializes the module.
     */
    public function init()
    {

        // Base template directory
        Event::on(
            View::class,
            View::EVENT_REGISTER_SITE_TEMPLATE_ROOTS, function(RegisterTemplateRootsEvent $e) {
            $e->roots['client'] = $this->getBasePath() . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . 'templates';
        });

        if (Craft::$app->request->isCpRequest) {
            Event::on(
                Entry::class,
                Entry::EVENT_REGISTER_PREVIEW_TARGETS, function(RegisterPreviewTargetsEvent $event) {
                /** @var Entry $entry */
                $entry = $event->sender;
                if (in_array($entry->section->handle, ['film', 'person', 'news'])) {
                    $event->previewTargets[] = [
                        'label' => "Headless preview: Entry page",
                        'url' => "/client#/{$entry->site->handle}/{$entry->section->handle}/{$entry->sourceId}/{$entry->slug}"
                    ];
                    $event->previewTargets[] = [
                        'label' => "Headless preview: {$entry->section->name} index page",
                        'url' => "/client#/{$entry->site->handle}/{$entry->section->handle}"
                    ];
                }
            }
            );
        }

        parent::init();
    }
}
