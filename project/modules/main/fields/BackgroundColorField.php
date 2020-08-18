<?php

namespace project\modules\main\fields;

use Craft;
use craft\base\Element;
use craft\base\ElementInterface;
use craft\base\Field;
use craft\base\PreviewableFieldInterface;
use craft\base\SortableFieldInterface;
use craft\helpers\Json;
use project\modules\main\models\BackgroundColorModel;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use yii\base\Exception;
use yii\db\Schema;
use function is_string;

class BackgroundColorField extends Field implements PreviewableFieldInterface, SortableFieldInterface
{

    public $data = null;

    /**
     * @return string
     */
    public static function displayName(): string
    {
        return 'Background Color';
    }

    /**
     * @return string
     */
    public function getContentColumnType(): string
    {
        return Schema::TYPE_TEXT;
    }

    /**
     * @param mixed $value
     * @param ElementInterface|null $element
     * @return mixed
     */
    public function normalizeValue($value, ElementInterface $element = null)
    {

        if (empty($value)) {
            $value = [];
        } else if (is_string($value)) {
            $value = Json::decode($value);
        }
        $this->data = new BackgroundColorModel($value);
        return $this->data;
    }

    /**
     * @return bool
     */
    public function isSearchable(): bool
    {
        return false;
    }

    /**
     * @param mixed $value
     * @param ElementInterface $element
     * @return string
     */
    public function getSearchKeywords($value, ElementInterface $element): string
    {
        return '';
    }

    /**
     * @param mixed $value
     * @param ElementInterface|null $element
     * @return string
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getInputHtml($value, ElementInterface $element = null): string
    {

        return Craft::$app->getView()->renderTemplate('main/fields/backgroundimagefield_input', [
            'field' => $this,
            'value' => $value,
            'mode' => 'color'
        ]);
    }

    /**
     * @param mixed $value
     * @param ElementInterface $element
     * @return string
     * @throws Exception
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function getTableAttributeHtml($value, ElementInterface $element): string
    {
        return Craft::$app->getView()->renderTemplate('main/fields/backgroundcolorfield_tableattribute', [
            'value' => $value,
        ]);
    }

    public function getElementValidationRules(): array
    {
        return [
            [$this->handle, 'checkValue', 'on' => Element::SCENARIO_LIVE],
        ];
    }

    public function checkValue(ElementInterface $element)
    {
        /** @var BackgroundColorModel $data */
        $data = $this->data;
        if (!$data->useGradient) {
            $data->scenario = BackgroundColorModel::SCENARIO_NOGRADIENT;
        }
        if (!$data->validate()) {
            $element->addError($this->handle, Craft::t('main', 'There are errors in the field'));
        }
    }

}
