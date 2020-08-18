<?php

namespace project\modules\main\fields;

use Craft;
use craft\base\SortableFieldInterface;
use craft\fields\PlainText;
use yii\db\Schema;

class SortableTextField extends PlainText implements SortableFieldInterface
{
    public static function displayName(): string
    {
        return 'Sortable Text Field';
    }

    /**
     * @return string
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_STRING;
    }

    /**
     * @inheritdoc
     */
    public function getSettingsHtml()
    {
        return Craft::$app->getView()->renderTemplate('main/fields/sortabletextfield_settings.twig',
            [
                'field' => $this
            ]);
    }
}
